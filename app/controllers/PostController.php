<?php
// app/controllers/PostController.php
// Controller handling posts, comments, and likes

require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class PostController {
    private $postModel;
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->postModel = new PostModel();
        $this->userModel = new UserModel();
    }

    /**
     * Enforce authentication.
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'You must be logged in to perform that action.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
    }

    /**
     * Create a post.
     */
    public function create() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');
            $userId  = (int)$_SESSION['user_id'];
            $imageName = null;

            if (empty($content) && (!isset($_FILES['post_image']) || $_FILES['post_image']['error'] === UPLOAD_ERR_NO_FILE)) {
                $_SESSION['error_message'] = 'Post content or image cannot be empty.';
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }

            // Handle optional image upload
            if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath  = $_FILES['post_image']['tmp_name'];
                $fileName = $_FILES['post_image']['name'];
                $fileSize = $_FILES['post_image']['size'];

                $maxSize = 5 * 1024 * 1024; // 5MB
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if ($fileSize <= $maxSize && in_array($extension, $allowedExtensions, true)) {
                    $uploadDir = __DIR__ . '/../../public/uploads/posts/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $safeFileName = 'post_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                    $destPath = $uploadDir . $safeFileName;

                    if (move_uploaded_file($tmpPath, $destPath)) {
                        $imageName = $safeFileName;
                    }
                }
            }

            $postId = $this->postModel->createPost($userId, $content, $imageName);
            if ($postId) {
                $_SESSION['success_message'] = 'Post published successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to publish post.';
            }
        }

        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }

    /**
     * Add comment.
     */
    public function comment() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId  = (int)($_POST['post_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            $userId  = (int)$_SESSION['user_id'];

            if ($postId > 0 && !empty($content)) {
                $this->postModel->addComment($postId, $userId, $content);
                $_SESSION['success_message'] = 'Comment added!';
            }
        }

        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }

    /**
     * Toggle like.
     */
    public function like() {
        $this->requireAuth();

        $postId = (int)($_POST['post_id'] ?? $_GET['post_id'] ?? 0);
        $userId = (int)$_SESSION['user_id'];

        if ($postId > 0) {
            $this->postModel->toggleLike($postId, $userId);
        }

        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }

    /**
     * Delete post.
     */
    public function delete() {
        $this->requireAuth();

        $postId = (int)($_POST['post_id'] ?? $_GET['post_id'] ?? 0);
        $userId = (int)$_SESSION['user_id'];

        if ($postId > 0) {
            $post = $this->postModel->getPostById($postId);
            if ($post && $post['user_id'] == $userId) {
                if (!empty($post['image'])) {
                    $imgFile = __DIR__ . '/../../public/uploads/posts/' . basename($post['image']);
                    if (file_exists($imgFile)) {
                        @unlink($imgFile);
                    }
                }
                $this->postModel->deletePost($postId, $userId);
                $_SESSION['success_message'] = 'Post deleted successfully.';
            }
        }

        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
