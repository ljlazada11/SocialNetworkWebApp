<?php
// app/controllers/ReportController.php
// Controller handling system analytics and report generation requests

require_once __DIR__ . '/../models/ReportModel.php';

class ReportController {
    private $reportModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->reportModel = new ReportModel();
    }

    /**
     * Enforce authentication for reports dashboard.
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Please log in to view system reports and analytics.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
    }

    /**
     * Display reports dashboard with filtering and SQL metrics.
     */
    public function index() {
        $this->requireAuth();

        // Retrieve and sanitize inputs
        $search = trim($_GET['search'] ?? '');
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');
        $sort = trim($_GET['sort'] ?? 'posts_desc');
        $activeTab = trim($_GET['tab'] ?? 'summary');

        // Validate date strings format (YYYY-MM-DD)
        if (!empty($startDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = '';
        }
        if (!empty($endDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = '';
        }

        // Validate sorting order
        $validSorts = ['posts_desc', 'posts_asc', 'username_asc'];
        if (!in_array($sort, $validSorts, true)) {
            $sort = 'posts_desc';
        }

        // Validate active tab
        $validTabs = ['summary', 'user_posts', 'post_likes', 'comments'];
        if (!in_array($activeTab, $validTabs, true)) {
            $activeTab = 'summary';
        }

        // Execute report queries via ReportModel using prepared statements
        $summaryData = $this->reportModel->getSystemSummary();
        $postsPerUser = $this->reportModel->getPostsPerUser($search, $sort);
        $mostActiveUsers = $this->reportModel->getMostActiveUsers(5);
        $recentPosts = $this->reportModel->getRecentPostsReport($startDate, $endDate, $search, 15);
        $mostLikedPosts = $this->reportModel->getMostLikedPosts(10);
        $commentsReport = $this->reportModel->getCommentsReport();

        $pageTitle = 'Reports & SQL Analytics';

        // Render view wrapped in layout headers and footers
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/reports/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
