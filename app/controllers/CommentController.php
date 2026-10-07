<?php
// app/controllers/CommentController.php
// Controller handling comments CRUD operations and server-side authorization

require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class CommentController {
    private $commentModel;
    private $postModel;
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->commentModel = new CommentModel();
        $this->postModel    = new PostModel();
        $this->userModel    = new UserModel();
    }

    /**
     * Enforce authentication for comment actions.
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'You must be logged in to perform that action.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
    }

    /**
     * Create / Add a comment to a post.
     */
    public function create() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $postId  = (int)($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $userId  = (int)$_SESSION['user_id'];

        // Validate post ID
        if ($postId <= 0) {
            $_SESSION['error_message'] = 'Invalid post ID.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Verify that the post exists
        $post = $this->postModel->getPostById($postId);
        if (!$post) {
            $_SESSION['error_message'] = 'The post you are trying to comment on does not exist or has been deleted.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Validate content
        if ($content === '') {
            $_SESSION['error_message'] = 'Comment cannot be empty.';
            header('Location: ' . BASE_URL . '/index.php#post-' . $postId);
            exit;
        }

        if (mb_strlen($content) > 1000) {
            $_SESSION['error_message'] = 'Comment cannot exceed 1,000 characters.';
            header('Location: ' . BASE_URL . '/index.php#post-' . $postId);
            exit;
        }

        $newCommentId = $this->commentModel->createComment($postId, $userId, $content);
        if ($newCommentId) {
            $_SESSION['success_message'] = 'Comment added successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to post comment. Please try again.';
        }

        header('Location: ' . BASE_URL . '/index.php#post-' . $postId);
        exit;
    }

    /**
     * Show edit form for a comment owned by the logged-in user.
     */
    public function edit() {
        $this->requireAuth();

        $commentId = (int)($_GET['comment_id'] ?? $_GET['id'] ?? 0);
        $userId    = (int)$_SESSION['user_id'];

        if ($commentId <= 0) {
            $_SESSION['error_message'] = 'Invalid comment ID.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $comment = $this->commentModel->getCommentById($commentId);
        if (!$comment) {
            $_SESSION['error_message'] = 'Comment not found or has been deleted.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Strict Server-Side Ownership Check
        if ((int)$comment['user_id'] !== $userId) {
            $_SESSION['error_message'] = 'Unauthorized: You can only edit your own comments.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $pageTitle = 'Edit Comment';
        $formData  = [
            'content' => $comment['content']
        ];
        $errors = [];

        require __DIR__ . '/../views/comments/edit.php';
    }

    /**
     * Process comment update submission.
     */
    public function update() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $commentId = (int)($_POST['comment_id'] ?? 0);
        $userId    = (int)$_SESSION['user_id'];

        if ($commentId <= 0) {
            $_SESSION['error_message'] = 'Invalid comment ID.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $comment = $this->commentModel->getCommentById($commentId);
        if (!$comment) {
            $_SESSION['error_message'] = 'Comment not found or has been deleted.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Strict Server-Side Ownership Check
        if ((int)$comment['user_id'] !== $userId) {
            $_SESSION['error_message'] = 'Unauthorized: You can only edit your own comments.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $content  = trim($_POST['content'] ?? '');
        $errors   = [];
        $formData = ['content' => $content];

        if ($content === '') {
            $errors[] = 'Comment cannot be empty.';
        } elseif (mb_strlen($content) > 1000) {
            $errors[] = 'Comment cannot exceed 1,000 characters.';
        }

        if (!empty($errors)) {
            $pageTitle = 'Edit Comment';
            require __DIR__ . '/../views/comments/edit.php';
            return;
        }

        $updated = $this->commentModel->updateComment($commentId, $userId, $content);
        if ($updated) {
            $_SESSION['success_message'] = 'Comment updated successfully!';
            header('Location: ' . BASE_URL . '/index.php#post-' . $comment['post_id']);
            exit;
        } else {
            $_SESSION['error_message'] = 'Failed to update comment. Please try again.';
            header('Location: ' . BASE_URL . '/index.php?action=edit_comment&comment_id=' . $commentId);
            exit;
        }
    }

    /**
     * Delete an existing comment owned by the logged-in user.
     */
    public function delete() {
        $this->requireAuth();

        $commentId = (int)($_POST['comment_id'] ?? $_GET['comment_id'] ?? $_GET['id'] ?? 0);
        $userId    = (int)$_SESSION['user_id'];

        if ($commentId <= 0) {
            $_SESSION['error_message'] = 'Invalid comment ID.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $comment = $this->commentModel->getCommentById($commentId);
        if (!$comment) {
            $_SESSION['error_message'] = 'Comment not found or has already been deleted.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Strict Server-Side Ownership Check
        if ((int)$comment['user_id'] !== $userId) {
            $_SESSION['error_message'] = 'Unauthorized: You can only delete your own comments.';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $deleted = $this->commentModel->deleteComment($commentId, $userId);
        if ($deleted) {
            $_SESSION['success_message'] = 'Comment deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete comment.';
        }

        header('Location: ' . BASE_URL . '/index.php#post-' . $comment['post_id']);
        exit;
    }
}
