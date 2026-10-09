<?php
// app/controllers/SearchController.php
// Controller handling search for users and posts, with filtering

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';

class SearchController {
    private $userModel;
    private $postModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
    }

    /**
     * Enforce authentication for search.
     * Prevents guest users from accessing protected social search results.
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'You must be logged in to search and view social content.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
    }

    /**
     * Main search and filter handler.
     */
    public function index() {
        $this->requireAuth();

        $currentUserId = (int)$_SESSION['user_id'];
        $rawQuery = $_GET['q'] ?? '';
        $query = trim($rawQuery);
        $type = trim($_GET['type'] ?? 'all'); // 'all', 'users', 'posts'
        $filter = trim($_GET['filter'] ?? 'newest'); // 'newest', 'oldest'

        // Valid types
        $validTypes = ['all', 'users', 'posts'];
        if (!in_array($type, $validTypes, true)) {
            $type = 'all';
        }

        // Valid post sorting filters
        $validFilters = ['newest', 'oldest', 'all'];
        if (!in_array($filter, $validFilters, true)) {
            $filter = 'newest';
        }

        $users = [];
        $posts = [];
        $isEmptySearch = ($query === '');

        if (!$isEmptySearch) {
            // Execute search queries inside models using prepared statements
            if ($type === 'all' || $type === 'users') {
                $users = $this->userModel->searchUsers($query);
            }

            if ($type === 'all' || $type === 'posts') {
                $posts = $this->postModel->searchPosts($query, $filter, $currentUserId);
            }
        }

        $pageTitle = $isEmptySearch ? 'Search' : 'Search: ' . $query;

        // Render search view wrapped in header and footer
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/search/results.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
