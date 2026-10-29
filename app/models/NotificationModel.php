<?php
// app/models/NotificationModel.php
// Handles creating, fetching, counting, and marking notifications as read for CLIQ

require_once __DIR__ . '/../../config/database.php';

class NotificationModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Create a new notification.
     * Prevents self-notifications and duplicate unread notifications for repeated actions.
     *
     * @param int $recipientId User receiving the notification
     * @param int $actorId User performing the action
     * @param string $type Action category ('follow', 'like', 'comment')
     * @param string $message Action description
     * @param int|null $relatedPostId Post ID if applicable
     * @param int|null $relatedCommentId Comment ID if applicable
     * @return bool|int Returns insert ID on success, false on failure or self/duplicate
     */
    public function create(int $recipientId, int $actorId, string $type, string $message, ?int $relatedPostId = null, ?int $relatedCommentId = null) {
        $recipientId = (int)$recipientId;
        $actorId = (int)$actorId;

        // Prevent self-notifications
        if ($recipientId <= 0 || $actorId <= 0 || $recipientId === $actorId) {
            return false;
        }

        // Duplicate prevention for follow and like notifications
        if ($type === 'follow') {
            $checkSql = "SELECT id FROM notifications 
                         WHERE recipient_id = :recipient_id 
                           AND actor_id = :actor_id 
                           AND type = 'follow' 
                         LIMIT 1";
            $stmt = $this->db->prepare($checkSql);
            $stmt->execute([
                ':recipient_id' => $recipientId,
                ':actor_id'     => $actorId
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                // If a follow notification already exists, update it to unread and refresh timestamp
                $updateSql = "UPDATE notifications 
                              SET is_read = 0, message = :message, created_at = NOW() 
                              WHERE id = :id";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([
                    ':message' => $message,
                    ':id'      => (int)$existing['id']
                ]);
                return (int)$existing['id'];
            }
        } elseif ($type === 'like' && $relatedPostId) {
            $checkSql = "SELECT id FROM notifications 
                         WHERE recipient_id = :recipient_id 
                           AND actor_id = :actor_id 
                           AND type = 'like' 
                           AND related_post_id = :post_id 
                         LIMIT 1";
            $stmt = $this->db->prepare($checkSql);
            $stmt->execute([
                ':recipient_id' => $recipientId,
                ':actor_id'     => $actorId,
                ':post_id'      => $relatedPostId
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                // If a like notification already exists, update it to unread and refresh timestamp
                $updateSql = "UPDATE notifications 
                              SET is_read = 0, message = :message, created_at = NOW() 
                              WHERE id = :id";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([
                    ':message' => $message,
                    ':id'      => (int)$existing['id']
                ]);
                return (int)$existing['id'];
            }
        }

        $sql = "INSERT INTO notifications (recipient_id, actor_id, type, message, related_post_id, related_comment_id, is_read, created_at)
                VALUES (:recipient_id, :actor_id, :type, :message, :related_post_id, :related_comment_id, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
            ':recipient_id'        => $recipientId,
            ':actor_id'            => $actorId,
            ':type'                => $type,
            ':message'             => $message,
            ':related_post_id'     => $relatedPostId ? (int)$relatedPostId : null,
            ':related_comment_id'  => $relatedCommentId ? (int)$relatedCommentId : null
        ]);

        return $res ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Get unread notification count for a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int {
        $sql = "SELECT COUNT(*) FROM notifications WHERE recipient_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => (int)$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get paginated list of notifications for a user, with actor details.
     *
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getNotifications(int $userId, int $limit = 30, int $offset = 0): array {
        $sql = "SELECT n.id, n.recipient_id, n.actor_id, n.type, n.message, n.related_post_id, n.related_comment_id, n.is_read, n.created_at,
                       u.username AS actor_username, u.full_name AS actor_full_name, u.profile_image AS actor_profile_image,
                       CASE WHEN p.id IS NOT NULL THEN 1 ELSE 0 END AS post_exists,
                       CASE WHEN c.id IS NOT NULL THEN 1 ELSE 0 END AS comment_exists
                FROM notifications n
                JOIN users u ON n.actor_id = u.id
                LEFT JOIN posts p ON n.related_post_id = p.id
                LEFT JOIN comments c ON n.related_comment_id = c.id
                WHERE n.recipient_id = :user_id
                ORDER BY n.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark an individual notification as read.
     * Requires userId to ensure authorization.
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId): bool {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND recipient_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'      => (int)$notificationId,
            ':user_id' => (int)$userId
        ]);
    }

    /**
     * Mark all unread notifications as read for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool {
        $sql = "UPDATE notifications SET is_read = 1 WHERE recipient_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => (int)$userId]);
    }

    /**
     * Find a single notification by ID and recipient.
     *
     * @param int $notificationId
     * @param int $userId
     * @return array|false
     */
    public function findById(int $notificationId, int $userId) {
        $sql = "SELECT * FROM notifications WHERE id = :id AND recipient_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'      => (int)$notificationId,
            ':user_id' => (int)$userId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete follow notification when a user unfollows another user.
     *
     * @param int $recipientId User who was being followed
     * @param int $actorId User who unfollowed
     * @return bool
     */
    public function deleteFollowNotification(int $recipientId, int $actorId): bool {
        $sql = "DELETE FROM notifications 
                WHERE recipient_id = :recipient_id 
                  AND actor_id = :actor_id 
                  AND type = 'follow'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':recipient_id' => (int)$recipientId,
            ':actor_id'     => (int)$actorId
        ]);
    }

    /**
     * Delete like notification when a user unlikes a post.
     *
     * @param int $recipientId
     * @param int $actorId
     * @param int $postId
     * @return bool
     */
    public function deleteLikeNotification(int $recipientId, int $actorId, int $postId): bool {
        $sql = "DELETE FROM notifications 
                WHERE recipient_id = :recipient_id 
                  AND actor_id = :actor_id 
                  AND type = 'like' 
                  AND related_post_id = :post_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':recipient_id' => (int)$recipientId,
            ':actor_id'     => (int)$actorId,
            ':post_id'      => (int)$postId
        ]);
    }

    /**
     * Delete all notifications for a specific user.
     *
     * @param int $userId
     * @return bool
     */
    public function deleteAllByUserId(int $userId): bool {
        $sql = "DELETE FROM notifications WHERE recipient_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => (int)$userId]);
    }

    /**
     * Delete a single notification for a user.
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function deleteNotification(int $notificationId, int $userId): bool {
        $sql = "DELETE FROM notifications WHERE id = :id AND recipient_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'      => (int)$notificationId,
            ':user_id' => (int)$userId
        ]);
    }
}
