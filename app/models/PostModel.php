<?php
// app/models/PostModel.php
// Model handling all database operations for posts, comments, and likes

require_once __DIR__ . '/../../config/database.php';

class PostModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Get all posts with author info, like counts, and comment counts.
     *
     * @param int|null $currentUserId To check if the current user liked each post
     * @return array
     */
    public function getAllPosts($currentUserId = null) {
        $sql = "SELECT p.*, 
                       u.username, 
                       u.full_name, 
                       u.profile_image,
                       COUNT(DISTINCT l.id) AS likes_count,
                       COUNT(DISTINCT c.id) AS comments_count,
                       MAX(CASE WHEN l.user_id = :current_user_id THEN 1 ELSE 0 END) AS is_liked
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                GROUP BY p.id
                ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':current_user_id' => $currentUserId ?? 0]);
        $posts = $stmt->fetchAll();

        // Attach comments to each post
        foreach ($posts as &$post) {
            $post['comments'] = $this->getCommentsByPostId($post['id']);
        }

        return $posts;
    }

    /**
     * Get all comments for a specific post.
     *
     * @param int $postId
     * @return array
     */
    public function getCommentsByPostId($postId) {
        require_once __DIR__ . '/CommentModel.php';
        $commentModel = new CommentModel();
        return $commentModel->getCommentsByPost($postId);
    }

    /**
     * Create a new post.
     *
     * @param int $userId
     * @param string $content
     * @param string|null $image
     * @return int|bool
     */
    public function createPost($userId, $content, $image = null) {
        $sql = "INSERT INTO posts (user_id, content, image, created_at) VALUES (:user_id, :content, :image, NOW())";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':user_id' => $userId,
            ':content' => $content,
            ':image'   => $image
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    /**
     * Add a comment to a post.
     *
     * @param int $postId
     * @param int $userId
     * @param string $content
     * @return bool
     */
    public function addComment($postId, $userId, $content) {
        require_once __DIR__ . '/CommentModel.php';
        $commentModel = new CommentModel();
        return (bool)$commentModel->createComment($postId, $userId, $content);
    }

    /**
     * Toggle like on a post.
     *
     * @param int $postId
     * @param int $userId
     * @return bool True if liked, False if unliked
     */
    public function toggleLike($postId, $userId) {
        $checkSql = "SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($checkSql);
        $stmt->execute([':post_id' => $postId, ':user_id' => $userId]);
        $like = $stmt->fetch();

        if ($like) {
            $delSql = "DELETE FROM likes WHERE id = :id";
            $delStmt = $this->db->prepare($delSql);
            $delStmt->execute([':id' => $like['id']]);
            return false;
        } else {
            $insSql = "INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)";
            $insStmt = $this->db->prepare($insSql);
            $insStmt->execute([':post_id' => $postId, ':user_id' => $userId]);
            return true;
        }
    }

    /**
     * Delete a post (only allowed if post belongs to user).
     *
     * @param int $postId
     * @param int $userId
     * @return bool
     */
    public function deletePost($postId, $userId) {
        $sql = "DELETE FROM posts WHERE id = :post_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':post_id' => $postId, ':user_id' => $userId]);
    }

    /**
     * Get a post by its ID (including author information).
     *
     * @param int $postId
     * @return array|false
     */
    public function getPostById($postId) {
        $sql = "SELECT p.*, 
                       u.username, 
                       u.full_name, 
                       u.profile_image
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $postId]);
        return $stmt->fetch();
    }

    /**
     * Update an existing post (only if user is author).
     *
     * @param int $postId
     * @param int $userId
     * @param string $content
     * @param string|null $image
     * @return bool
     */
    public function updatePost($postId, $userId, $content, $image = null) {
        $sql = "UPDATE posts 
                SET content = :content, image = :image 
                WHERE id = :post_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':content' => $content,
            ':image'   => $image,
            ':post_id' => $postId,
            ':user_id' => $userId
        ]);
    }
}
