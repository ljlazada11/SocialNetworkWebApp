<?php
// app/models/MessageModel.php
// Handles all database operations for private messages in CLIQ

require_once __DIR__ . '/../../config/database.php';

class MessageModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Send a new private message from sender to receiver.
     * Returns the inserted message ID on success, false on failure.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param string $message
     * @return int|false
     */
    public function sendMessage(int $senderId, int $receiverId, string $message) {
        $senderId   = (int)$senderId;
        $receiverId = (int)$receiverId;
        $message    = trim($message);

        // Prevent self-messaging
        if ($senderId <= 0 || $receiverId <= 0 || $senderId === $receiverId) {
            return false;
        }

        if ($message === '') {
            return false;
        }

        $sql = "INSERT INTO messages (sender_id, receiver_id, message, is_read, created_at)
                VALUES (:sender_id, :receiver_id, :message, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
            ':sender_id'   => $senderId,
            ':receiver_id' => $receiverId,
            ':message'     => $message,
        ]);

        return $res ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Get all messages in the conversation between two users, oldest first.
     *
     * @param int $userA
     * @param int $userB
     * @return array
     */
    public function getConversation(int $userA, int $userB): array {
        $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.is_read, m.created_at,
                       u.full_name AS sender_name, u.username AS sender_username,
                       u.profile_image AS sender_profile_image
                FROM messages m
                JOIN users u ON u.id = m.sender_id
                WHERE (m.sender_id = :a1 AND m.receiver_id = :b1)
                   OR (m.sender_id = :b2 AND m.receiver_id = :a2)
                ORDER BY m.created_at ASC, m.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':a1' => (int)$userA,
            ':b1' => (int)$userB,
            ':b2' => (int)$userB,
            ':a2' => (int)$userA,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark all messages FROM a specific sender TO the current user as read.
     *
     * @param int $senderId   The person who sent messages
     * @param int $receiverId The logged-in user reading them
     * @return bool
     */
    public function markAsRead(int $senderId, int $receiverId): bool {
        $sql = "UPDATE messages
                SET is_read = 1
                WHERE sender_id = :sender_id
                  AND receiver_id = :receiver_id
                  AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sender_id'   => (int)$senderId,
            ':receiver_id' => (int)$receiverId,
        ]);
    }

    /**
     * Get the inbox: a list of unique conversation partners for the current user,
     * with the latest message and unread count for each conversation.
     *
     * @param int $userId  The logged-in user
     * @return array
     */
    public function getInbox(int $userId): array {
        $userId = (int)$userId;

        $sql = "SELECT 
                    m.id,
                    m.sender_id,
                    m.receiver_id,
                    m.message AS last_message,
                    m.created_at AS last_at,
                    u.id AS partner_id,
                    u.username AS partner_username,
                    u.full_name AS partner_full_name,
                    u.profile_image AS partner_profile_image,
                    COALESCE(unread.unread_count, 0) AS unread_count
                FROM messages m
                INNER JOIN (
                    SELECT MAX(id) AS max_id
                    FROM messages
                    WHERE sender_id = :me1 OR receiver_id = :me2
                    GROUP BY IF(sender_id = :me3, receiver_id, sender_id)
                ) latest ON m.id = latest.max_id
                INNER JOIN users u ON u.id = IF(m.sender_id = :me4, m.receiver_id, m.sender_id)
                LEFT JOIN (
                    SELECT sender_id AS partner_id, COUNT(*) AS unread_count
                    FROM messages
                    WHERE receiver_id = :me5 AND is_read = 0
                    GROUP BY sender_id
                ) unread ON unread.partner_id = u.id
                ORDER BY m.created_at DESC, m.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':me1' => $userId,
            ':me2' => $userId,
            ':me3' => $userId,
            ':me4' => $userId,
            ':me5' => $userId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of unread messages for the logged-in user across all conversations.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int {
        $sql = "SELECT COUNT(*) FROM messages WHERE receiver_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => (int)$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Delete a message — only the sender may delete their own message.
     *
     * @param int $messageId
     * @param int $senderId
     * @return bool
     */
    public function deleteMessage(int $messageId, int $senderId): bool {
        $sql = "DELETE FROM messages WHERE id = :id AND sender_id = :sender_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'        => (int)$messageId,
            ':sender_id' => (int)$senderId,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Check whether a conversation exists between two users (any direction).
     *
     * @param int $userA
     * @param int $userB
     * @return bool
     */
    public function conversationExists(int $userA, int $userB): bool {
        $sql = "SELECT 1 FROM messages
                WHERE (sender_id = :a1 AND receiver_id = :b1)
                   OR (sender_id = :b2 AND receiver_id = :a2)
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':a1' => (int)$userA, ':b1' => (int)$userB,
            ':b2' => (int)$userB, ':a2' => (int)$userA,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Fetch a single message by its ID. Used for ownership verification.
     *
     * @param int $messageId
     * @return array|false
     */
    public function findById(int $messageId) {
        $sql = "SELECT * FROM messages WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => (int)$messageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
