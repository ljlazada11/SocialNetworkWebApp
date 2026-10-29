<?php
// app/controllers/MessageController.php
// Handles inbox listing, conversation viewing, and message sending for CLIQ

require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/FileHelper.php';

class MessageController {
    private $messageModel;
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->messageModel = new MessageModel();
        $this->userModel    = new UserModel();
    }

    /**
     * Require the user to be authenticated. Redirects to login if not.
     */
    private function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'You must be logged in to access messages.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
    }

    /**
     * Display the messages inbox — a list of all conversations for the logged-in user.
     * In the unified split-screen layout, if conversations exist and 'list' is not requested,
     * it automatically opens the most recent conversation.
     */
    public function inbox(): void {
        $this->requireAuth();

        $currentUserId = (int)$_SESSION['user_id'];
        $conversations = $this->messageModel->getInbox($currentUserId);

        // Resolve avatar URLs for each conversation partner
        foreach ($conversations as &$conv) {
            $conv['partner_avatar_url'] = $this->resolveAvatarUrl($conv['partner_profile_image'] ?? null);
            $parts = explode(' ', trim($conv['partner_full_name'] ?? ''));
            $conv['partner_initials'] = count($parts) >= 2
                ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1))
                : strtoupper(mb_substr($conv['partner_full_name'] ?? 'U', 0, 2));
        }
        unset($conv);

        // Render main messages inbox view: lists all conversations with placeholder on right when no partner is selected
        $partner              = null;
        $messages             = [];
        $partnerAvatarUrl     = null;
        $partnerInitials      = '';
        $currentUser          = $this->userModel->findById($currentUserId);
        $currentUserAvatarUrl = $this->resolveAvatarUrl($currentUser['profile_image'] ?? null);
        $currentParts         = explode(' ', trim($currentUser['full_name'] ?? ''));
        $currentUserInitials  = count($currentParts) >= 2
            ? strtoupper(mb_substr($currentParts[0], 0, 1) . mb_substr($currentParts[count($currentParts) - 1], 0, 1))
            : strtoupper(mb_substr($currentUser['full_name'] ?? 'U', 0, 2));

        $pageTitle = 'Messages';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/messages/conversation.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Display the conversation between the current user and a specific partner.
     * Also loads all conversations for the left sidebar in the split-screen view.
     */
    public function conversation(): void {
        $this->requireAuth();

        $currentUserId = (int)$_SESSION['user_id'];
        $partnerId     = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

        // Must have a valid partner who is not themselves
        if ($partnerId <= 0 || $partnerId === $currentUserId) {
            $_SESSION['error_message'] = 'Invalid conversation target.';
            header('Location: ' . BASE_URL . '/index.php?action=messages');
            exit;
        }

        // Verify the partner actually exists in the database
        $partner = $this->userModel->findById($partnerId);
        if (!$partner) {
            ErrorHandler::renderErrorPage(404, 'User Not Found', 'The user you are trying to message does not exist.');
            exit;
        }

        // Mark all messages from partner → current user as read
        $this->messageModel->markAsRead($partnerId, $currentUserId);

        // Load conversation history
        $messages = $this->messageModel->getConversation($currentUserId, $partnerId);

        // Resolve avatar for partner
        $partnerAvatarUrl = $this->resolveAvatarUrl($partner['profile_image'] ?? null);

        // Compute partner initials
        $parts = explode(' ', trim($partner['full_name'] ?? ''));
        $partnerInitials = count($parts) >= 2
            ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1))
            : strtoupper(mb_substr($partner['full_name'] ?? 'U', 0, 2));

        // Resolve avatar for current user (for outgoing message bubbles)
        $currentUser = $this->userModel->findById($currentUserId);
        $currentUserAvatarUrl = $this->resolveAvatarUrl($currentUser['profile_image'] ?? null);
        $currentParts = explode(' ', trim($currentUser['full_name'] ?? ''));
        $currentUserInitials = count($currentParts) >= 2
            ? strtoupper(mb_substr($currentParts[0], 0, 1) . mb_substr($currentParts[count($currentParts) - 1], 0, 1))
            : strtoupper(mb_substr($currentUser['full_name'] ?? 'U', 0, 2));

        // Load all conversations for the left sidebar
        $conversations = $this->messageModel->getInbox($currentUserId);
        foreach ($conversations as &$conv) {
            $conv['partner_avatar_url'] = $this->resolveAvatarUrl($conv['partner_profile_image'] ?? null);
            $cParts = explode(' ', trim($conv['partner_full_name'] ?? ''));
            $conv['partner_initials'] = count($cParts) >= 2
                ? strtoupper(mb_substr($cParts[0], 0, 1) . mb_substr($cParts[count($cParts) - 1], 0, 1))
                : strtoupper(mb_substr($conv['partner_full_name'] ?? 'U', 0, 2));
        }
        unset($conv);

        // If active partner is not yet in conversations list (e.g. brand-new conversation), prepend them
        $found = false;
        foreach ($conversations as $c) {
            if ((int)$c['partner_id'] === $partnerId) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            array_unshift($conversations, [
                'partner_id'            => $partner['id'],
                'partner_full_name'     => $partner['full_name'],
                'partner_username'      => $partner['username'],
                'partner_profile_image' => $partner['profile_image'],
                'partner_avatar_url'    => $partnerAvatarUrl,
                'partner_initials'      => $partnerInitials,
                'last_message'          => '',
                'last_at'               => null,
                'unread_count'          => 0,
            ]);
        }

        $pageTitle = 'Chat with ' . htmlspecialchars($partner['full_name']);
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/messages/conversation.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Handle POST request to send a message.
     * Validates input, sends the message, and redirects back to the conversation.
     */
    public function send(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?action=messages');
            exit;
        }

        // CSRF validation
        if (!validate_csrf_token()) {
            $_SESSION['error_message'] = 'Security error: invalid request. Please try again.';
            header('Location: ' . BASE_URL . '/index.php?action=messages');
            exit;
        }

        $currentUserId = (int)$_SESSION['user_id'];
        $receiverId    = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
        $messageText   = trim($_POST['message'] ?? '');

        // Validate receiver
        if ($receiverId <= 0 || $receiverId === $currentUserId) {
            $_SESSION['error_message'] = 'Invalid message recipient.';
            header('Location: ' . BASE_URL . '/index.php?action=messages');
            exit;
        }

        // Verify receiver exists
        $receiver = $this->userModel->findById($receiverId);
        if (!$receiver) {
            $_SESSION['error_message'] = 'The user you are messaging does not exist.';
            header('Location: ' . BASE_URL . '/index.php?action=messages');
            exit;
        }

        // Validate message content
        if ($messageText === '') {
            $_SESSION['error_message'] = 'Message cannot be empty.';
            header('Location: ' . BASE_URL . '/index.php?action=conversation&user_id=' . $receiverId);
            exit;
        }

        if (mb_strlen($messageText) > 2000) {
            $_SESSION['error_message'] = 'Message is too long (maximum 2000 characters).';
            header('Location: ' . BASE_URL . '/index.php?action=conversation&user_id=' . $receiverId);
            exit;
        }

        $result = $this->messageModel->sendMessage($currentUserId, $receiverId, $messageText);

        if ($result === false) {
            $_SESSION['error_message'] = 'Failed to send message. Please try again.';
        }

        // Redirect back to the conversation
        header('Location: ' . BASE_URL . '/index.php?action=conversation&user_id=' . $receiverId . '#msg-bottom');
        exit;
    }

    /**
     * Handle POST request to delete a message (only sender can delete).
     * Returns JSON for AJAX callers, or redirects for non-AJAX.
     */
    public function deleteMessage(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        if (!validate_csrf_token()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
            exit;
        }

        $currentUserId = (int)$_SESSION['user_id'];
        $messageId     = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;
        $partnerId     = isset($_POST['partner_id']) ? (int)$_POST['partner_id'] : 0;

        if ($messageId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid message ID.']);
            exit;
        }

        // Verify the message exists and belongs to the current user
        $msg = $this->messageModel->findById($messageId);
        if (!$msg || (int)$msg['sender_id'] !== $currentUserId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'You are not authorized to delete this message.']);
            exit;
        }

        $deleted = $this->messageModel->deleteMessage($messageId, $currentUserId);

        header('Content-Type: application/json');
        echo json_encode([
            'success'    => $deleted,
            'message_id' => $messageId,
        ]);
        exit;
    }

    /**
     * AJAX endpoint: return the unread message count for the current user.
     */
    public function getUnreadCount(): void {
        $this->requireAuth();
        $currentUserId = (int)$_SESSION['user_id'];
        $count = $this->messageModel->getUnreadCount($currentUserId);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'unread_count' => $count]);
        exit;
    }

    // -----------------------------------------------------------------------
    // Private Helpers
    // -----------------------------------------------------------------------

    /**
     * Resolve a profile_image filename to a full URL or null.
     *
     * @param string|null $profileImage  Filename stored in the DB
     * @return string|null
     */
    private function resolveAvatarUrl(?string $profileImage): ?string {
        if (empty($profileImage)) {
            return null;
        }
        $path = __DIR__ . '/../../public/uploads/avatars/' . basename($profileImage);
        if (file_exists($path)) {
            return BASE_URL . '/public/uploads/avatars/' . htmlspecialchars(basename($profileImage));
        }
        return null;
    }
}
