<?php
// public/index.php
// Main entry point and front controller routing for SocialNetworkWebApp

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Compute BASE_URL for clean routing and asset linking
if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = preg_replace('#/public.*$#', '', $scriptDir);
    define('BASE_URL', rtrim($base, '/'));
}

// Action-based routing
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'register':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $authController = new AuthController();
        $authController->register();
        break;

    case 'login':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $authController = new AuthController();
        $authController->login();
        break;

    case 'logout':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $authController = new AuthController();
        $authController->logout();
        break;

    case 'profile':
        require_once __DIR__ . '/../app/controllers/ProfileController.php';
        $profileController = new ProfileController();
        $profileController->view();
        break;

    case 'edit_profile':
        require_once __DIR__ . '/../app/controllers/ProfileController.php';
        $profileController = new ProfileController();
        $profileController->edit();
        break;

    // Post, Comment, Like actions
    case 'create_post':
        require_once __DIR__ . '/../app/controllers/PostController.php';
        $postController = new PostController();
        $postController->create();
        break;

    case 'edit_post':
        require_once __DIR__ . '/../app/controllers/PostController.php';
        $postController = new PostController();
        $postController->edit();
        break;

    case 'update_post':
        require_once __DIR__ . '/../app/controllers/PostController.php';
        $postController = new PostController();
        $postController->update();
        break;

    case 'create_comment':
    case 'add_comment':
        require_once __DIR__ . '/../app/controllers/CommentController.php';
        $commentController = new CommentController();
        $commentController->create();
        break;

    case 'edit_comment':
        require_once __DIR__ . '/../app/controllers/CommentController.php';
        $commentController = new CommentController();
        $commentController->edit();
        break;

    case 'update_comment':
        require_once __DIR__ . '/../app/controllers/CommentController.php';
        $commentController = new CommentController();
        $commentController->update();
        break;

    case 'delete_comment':
        require_once __DIR__ . '/../app/controllers/CommentController.php';
        $commentController = new CommentController();
        $commentController->delete();
        break;

    case 'like_post':
        require_once __DIR__ . '/../app/controllers/PostController.php';
        $postController = new PostController();
        $postController->like();
        break;

    case 'delete_post':
        require_once __DIR__ . '/../app/controllers/PostController.php';
        $postController = new PostController();
        $postController->delete();
        break;

    // Additional Navigation Pages
    case 'people':
    case 'groups':
    case 'events':
    case 'messages':
    case 'saved':
        $pageTitle = ucfirst($action);
        require_once __DIR__ . '/../app/views/layouts/header.php';
        ?>
        <div class="card-modern p-5 text-center">
            <div class="mb-3 text-primary">
                <?php
                $iconMap = [
                    'people' => 'bi-people',
                    'groups' => 'bi-people-gear',
                    'events' => 'bi-calendar-event',
                    'messages' => 'bi-chat-dots',
                    'saved' => 'bi-bookmark'
                ];
                $icon = $iconMap[$action] ?? 'bi-grid';
                ?>
                <i class="bi <?= $icon ?> fs-1" style="color: var(--accent-yellow);"></i>
            </div>
            <h3 class="fw-bold text-dark"><?= htmlspecialchars(ucfirst($action)); ?></h3>
            <p class="text-muted col-md-6 mx-auto mb-4">
                Explore campus <?= htmlspecialchars(strtolower($action)); ?> and connect with members of the SMCC community.
            </p>
            <a href="<?= BASE_URL ?>/" class="btn btn-outline-primary px-4 rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Back to Feed
            </a>
        </div>
        <?php
        require_once __DIR__ . '/../app/views/layouts/footer.php';
        break;

    case 'home':
    default:
        $pageTitle = 'Home';
        require_once __DIR__ . '/../app/models/PostModel.php';
        require_once __DIR__ . '/../app/models/UserModel.php';

        $postModel = new PostModel();
        $userModel = new UserModel();

        $currentUserId = $_SESSION['user_id'] ?? null;
        $posts = $postModel->getAllPosts($currentUserId);

        // Fetch suggested users (excluding current user if logged in)
        $db = Database::connect();
        if ($currentUserId) {
            $stmt = $db->prepare("SELECT id, username, full_name, profile_image FROM users WHERE id != :user_id LIMIT 3");
            $stmt->execute([':user_id' => $currentUserId]);
        } else {
            $stmt = $db->query("SELECT id, username, full_name, profile_image FROM users LIMIT 3");
        }
        $suggestedUsers = $stmt->fetchAll();

        require_once __DIR__ . '/../app/views/layouts/header.php';
        require_once __DIR__ . '/../app/views/home/feed.php';
        require_once __DIR__ . '/../app/views/layouts/footer.php';
        break;
}
