-- =====================================================================
-- Database Schema for CLIQ (Social Network Web Application)
-- Target Database: social_app
-- Server: MySQL / MariaDB (XAMPP)
-- Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `social_app` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `social_app`;

-- ---------------------------------------------------------------------
-- 1. users table
-- Core user accounts, authentication credentials, and profile settings
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `bio` TEXT DEFAULT NULL,
    `profile_image` VARCHAR(255) DEFAULT NULL,
    `cover_photo` VARCHAR(255) DEFAULT NULL,
    `role` VARCHAR(20) NOT NULL DEFAULT 'user',
    `privacy_followers` ENUM('public', 'private') NOT NULL DEFAULT 'public',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. posts table
-- Feed posts with text content and optional image path / JSON photo array
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `image` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `fk_posts_user` (`user_id`),
    CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. comments table
-- User comments associated with specific posts
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `fk_comments_post` (`post_id`),
    KEY `fk_comments_user` (`user_id`),
    CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) 
        REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. likes table
-- Unique user likes on individual posts
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `likes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    UNIQUE KEY `unique_like_post_user` (`post_id`, `user_id`),
    KEY `fk_likes_user` (`user_id`),
    CONSTRAINT `fk_likes_post` FOREIGN KEY (`post_id`) 
        REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. follows table
-- Follower and following relationships between users
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `follows` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `follower_id` INT NOT NULL,
    `following_id` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_follower_following` (`follower_id`, `following_id`),
    KEY `fk_follows_following` (`following_id`),
    CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_follows_following` FOREIGN KEY (`following_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. events table
-- Campus events displayed on the feed and events calendar
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `event_date` DATE NOT NULL,
    `event_time` VARCHAR(50) DEFAULT NULL,
    `location` VARCHAR(255) NOT NULL,
    `created_by` INT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `fk_events_creator` (`created_by`),
    CONSTRAINT `fk_events_creator` FOREIGN KEY (`created_by`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. messages table
-- Direct peer-to-peer messages between users
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_messages_sender` (`sender_id`),
    KEY `idx_messages_receiver` (`receiver_id`),
    KEY `idx_messages_conversation` (`sender_id`, `receiver_id`),
    CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. notifications table
-- User notifications for follows, likes, and comments
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `recipient_id` INT NOT NULL COMMENT 'User who receives the notification',
    `actor_id` INT NOT NULL COMMENT 'User who performed the action',
    `type` VARCHAR(40) NOT NULL COMMENT 'follow | like | comment',
    `message` VARCHAR(255) DEFAULT NULL COMMENT 'Human-readable action description',
    `related_post_id` INT DEFAULT NULL,
    `related_comment_id` INT DEFAULT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_notif_recipient` (`recipient_id`),
    KEY `idx_notif_unread` (`recipient_id`, `is_read`),
    KEY `idx_notif_created` (`created_at`),
    KEY `idx_notif_actor` (`actor_id`),
    KEY `idx_notif_post` (`related_post_id`),
    KEY `idx_notif_comment` (`related_comment_id`),
    CONSTRAINT `fk_notif_recipient` FOREIGN KEY (`recipient_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_notif_actor` FOREIGN KEY (`actor_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_notif_post` FOREIGN KEY (`related_post_id`) 
        REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_notif_comment` FOREIGN KEY (`related_comment_id`) 
        REFERENCES `comments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. post_images table
-- Individual post image attachments for grid/album compatibility
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_post_images_post_id` (`post_id`),
    CONSTRAINT `fk_post_images_post` FOREIGN KEY (`post_id`) 
        REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
