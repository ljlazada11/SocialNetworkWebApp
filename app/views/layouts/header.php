<?php
// app/views/layouts/header.php
// Reusable Modern HTML Header & Navigation Layout matching the Reference Design

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = preg_replace('#/public.*$#', '', $scriptDir);
    define('BASE_URL', rtrim($base, '/'));
}

$currentAction = $_GET['action'] ?? 'home';
$isLoggedIn = isset($_SESSION['user_id']);
$isLandingPage = !$isLoggedIn && ($currentAction === 'welcome' || $currentAction === 'home' || empty($_GET['action']));

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    if (function_exists('csrf_token')) {
        csrf_token();
    } else {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Get current user avatar if logged in
$userInitials = 'U';
$userAvatarPath = null;
if ($isLoggedIn) {
    // Sync latest profile details if UserModel available or query DB to keep session fresh
    if (!isset($_SESSION['profile_image_synced']) || $_SESSION['profile_image_synced'] < (time() - 60)) {
        require_once __DIR__ . '/../../models/UserModel.php';
        $headerUserModel = new UserModel();
        $currentUserData = $headerUserModel->findById((int)$_SESSION['user_id']);
        if ($currentUserData) {
            $_SESSION['profile_image'] = $currentUserData['profile_image'] ?? null;
            $_SESSION['full_name'] = $currentUserData['full_name'] ?? $_SESSION['full_name'];
            $_SESSION['profile_image_synced'] = time();
        }
    }

    $name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        $userInitials = strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts)-1], 0, 1));
    } else {
        $userInitials = strtoupper(mb_substr($name, 0, 2));
    }

    if (!empty($_SESSION['profile_image'])) {
        $uploadedPath = __DIR__ . '/../../../public/uploads/avatars/' . basename($_SESSION['profile_image']);
        if (file_exists($uploadedPath)) {
            $userAvatarPath = BASE_URL . '/public/uploads/avatars/' . htmlspecialchars(basename($_SESSION['profile_image']));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <title><?= isset($pageTitle) ? (strpos($pageTitle, 'CLIQ') !== false ? htmlspecialchars($pageTitle) : htmlspecialchars($pageTitle) . ' - CLIQ') : 'CLIQ'; ?></title>

    <!-- CLIQ Official Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/public/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/favicon.png">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/public/assets/images/cliq-icon-light.png">

    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Theme Controller (Loads early to prevent flicker & provide window.toggleTheme) -->
    <script>window.BASE_URL = "<?= BASE_URL ?>";</script>
    <script src="<?= BASE_URL ?>/public/assets/js/theme.js"></script>

    <!-- Cropper.js CSS (Image Crop/Adjust) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">

    <!-- Custom Modern Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
</head>
<body class="<?= $isLoggedIn ? 'app-logged-in' : 'app-logged-out' ?>">

<div class="app-layout">

    <!-- MAIN CONTENT AREA WRAPPER -->
    <div class="app-main">

        <!-- TOP HEADER -->
        <header class="app-header">
            <div class="header-container">
                <?php if ($isLoggedIn): ?>
                    <!-- Header Left: CLIQ Brand Logo and Name -->
                    <div class="header-brand-group d-flex align-items-center me-3">
                        <a href="<?= BASE_URL ?>/" class="brand-link d-inline-flex align-items-center" id="headerLogoBtn" title="CLIQ Home">
                            <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-light.png" alt="CLIQ" class="cliq-logo-full cliq-logo-header cliq-logo-light">
                            <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-dark.png" alt="CLIQ" class="cliq-logo-full cliq-logo-header cliq-logo-dark">
                        </a>
                    </div>

                    <!-- Search Bar (Desktop only, hidden on Search page to avoid duplicate) -->
                    <?php if ($currentAction !== 'search'): ?>
                    <form action="<?= BASE_URL ?>/index.php" method="GET" class="header-search d-none d-md-block" id="headerSearchForm" role="search">
                        <input type="hidden" name="action" value="search">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="q" id="headerSearchInput" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search..." aria-label="Search" autocomplete="off">
                        <span class="search-shortcut">⌘K</span>
                    </form>
                    <?php endif; ?>

                    <!-- Right Header Actions:
                         Screen View (Desktop): Messages -> Notifications -> Dark Mode Toggle -> Profile Dropdown
                         Mobile View: Notifications -> Dark Mode Toggle
                    -->
                    <div class="header-actions">
                        <!-- 1. Messages Icon (Desktop only: on mobile it is in the fixed bottom navigation bar) -->
                        <?php if ($isLoggedIn): ?>
                        <div class="position-relative d-none d-md-block">
                            <a href="<?= BASE_URL ?>/index.php?action=messages" class="header-icon-btn" title="Messages" aria-label="Messages" id="headerMsgBtn">
                                <i class="bi bi-chat-dots-fill"></i>
                                <?php
                                require_once __DIR__ . '/../../models/MessageModel.php';
                                $headerMsgModel = new MessageModel();
                                $headerMsgUnread = $headerMsgModel->getUnreadCount((int)$_SESSION['user_id']);
                                if ($headerMsgUnread > 0): ?>
                                    <span class="notif-badge-count" id="headerMsgBadge"><?= $headerMsgUnread > 99 ? '99+' : $headerMsgUnread ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- 2. Notifications Bell (Follows Messages on desktop, 1st on mobile) -->
                        <?php if ($isLoggedIn): ?>
                        <div class="position-relative header-notif-wrap">
                            <a href="<?= BASE_URL ?>/index.php?action=notifications" class="header-icon-btn notif-bell-btn" title="Notifications" aria-label="Notifications" id="headerNotifBtn">
                                <i class="bi bi-bell-fill"></i>
                                <?php
                                require_once __DIR__ . '/../../models/NotificationModel.php';
                                $headerNotifModel = new NotificationModel();
                                $headerUnreadCount = $headerNotifModel->getUnreadCount((int)$_SESSION['user_id']);
                                if ($headerUnreadCount > 0): ?>
                                    <span class="notif-badge-count" id="headerNotifBadge"><?= $headerUnreadCount > 99 ? '99+' : $headerUnreadCount ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- 3. Dark Mode / Light Mode Toggle (Follows Notifications on desktop, last control on mobile) -->
                        <button class="header-icon-btn theme-toggle-btn" type="button" onclick="toggleTheme()" title="Switch Theme" aria-label="Switch Theme">
                            <i class="theme-toggle-icon bi bi-moon-fill"></i>
                        </button>

                        <!-- Facebook-Style Profile Dropdown (Desktop only: on mobile Profile & Menu are in the fixed bottom nav) -->
                        <div class="dropdown fb-profile-dropdown d-none d-md-block">
                            <button class="fb-profile-trigger" 
                                    type="button" 
                                    id="headerUserDropdown" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside"
                                    aria-expanded="false" 
                                    title="Account Menu" 
                                    aria-label="Account Menu">
                                <?php if (!empty($userAvatarPath)): ?>
                                    <img src="<?= $userAvatarPath ?>" alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>" class="fb-avatar-trigger" onerror="this.onerror=null; this.outerHTML='<div class=\'fb-avatar-trigger\'><?= htmlspecialchars($userInitials) ?></div>';">
                                <?php else: ?>
                                    <div class="fb-avatar-trigger">
                                        <?= htmlspecialchars($userInitials); ?>
                                    </div>
                                <?php endif; ?>
                                <span class="header-user-name d-none d-md-inline text-truncate"><?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User'); ?></span>
                                <i class="bi bi-chevron-down fb-chevron ms-1 d-none d-sm-inline"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end fb-dropdown-menu shadow border-0" aria-labelledby="headerUserDropdown">
                                <!-- Top Profile Card / Header -->
                                <a href="<?= BASE_URL ?>/index.php?action=profile" class="fb-user-card text-decoration-none">
                                    <?php if (!empty($userAvatarPath)): ?>
                                        <img src="<?= $userAvatarPath ?>" alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>" class="fb-avatar-card" onerror="this.onerror=null; this.outerHTML='<div class=\'fb-avatar-card\'><?= htmlspecialchars($userInitials) ?></div>';">
                                    <?php else: ?>
                                        <div class="fb-avatar-card">
                                            <?= htmlspecialchars($userInitials); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex flex-column min-w-0 flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span class="fb-user-fullname text-truncate"><?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User'); ?></span>
                                        </div>
                                        <span class="fb-user-subtext text-truncate">@<?= htmlspecialchars($_SESSION['username'] ?? 'user'); ?></span>
                                    </div>
                                </a>

                                <div class="fb-menu-divider"></div>

                                <!-- Option 1: View Profile -->
                                <a class="dropdown-item fb-menu-item" href="<?= BASE_URL ?>/index.php?action=profile">
                                    <div class="fb-menu-item-left">
                                        <div class="fb-icon-circle">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <span class="fb-item-label">View Profile</span>
                                    </div>
                                    <i class="bi bi-chevron-right fb-arrow-right"></i>
                                </a>

                                <!-- Option: Friends -->
                                <a class="dropdown-item fb-menu-item" href="<?= BASE_URL ?>/index.php?action=friends">
                                    <div class="fb-menu-item-left">
                                        <div class="fb-icon-circle">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <span class="fb-item-label">Friends</span>
                                    </div>
                                    <i class="bi bi-chevron-right fb-arrow-right"></i>
                                </a>

                                <!-- Option 2: Account Settings -->
                                <a class="dropdown-item fb-menu-item" href="<?= BASE_URL ?>/index.php?action=settings" title="Account Settings">
                                    <div class="fb-menu-item-left">
                                        <div class="fb-icon-circle">
                                            <i class="bi bi-gear-fill"></i>
                                        </div>
                                        <span class="fb-item-label">Account Settings</span>
                                    </div>
                                    <i class="bi bi-chevron-right fb-arrow-right"></i>
                                </a>

                                

                                <div class="fb-menu-divider"></div>

                                <!-- Option 3: Sign Out -->
                                <a class="dropdown-item fb-menu-item fb-menu-signout" href="<?= BASE_URL ?>/index.php?action=logout">
                                    <div class="fb-menu-item-left">
                                        <div class="fb-icon-circle fb-icon-signout">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </div>
                                        <span class="fb-item-label text-danger">Sign Out</span>
                                    </div>
                                    <i class="bi bi-chevron-right fb-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Public / Unauthenticated Header -->
                    <div class="d-flex align-items-center">
                        <a href="<?= BASE_URL ?>/" class="brand-link d-inline-flex align-items-center" title="CLIQ">
                            <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-light.png" alt="CLIQ" class="cliq-logo-full cliq-logo-header cliq-logo-light">
                            <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-dark.png" alt="CLIQ" class="cliq-logo-full cliq-logo-header cliq-logo-dark">
                        </a>
                    </div>

                    <div class="header-actions d-flex align-items-center gap-2">
                        <!-- Dark Mode / Light Mode Toggle -->
                        <button class="header-icon-btn theme-toggle-btn" type="button" onclick="toggleTheme()" title="Switch Theme" aria-label="Switch Theme">
                            <i class="theme-toggle-icon bi bi-moon-fill"></i>
                        </button>

                        <?php if (!$isLandingPage && $currentAction !== 'login' && $currentAction !== 'register' && $currentAction !== 'forgot_password' && $currentAction !== 'reset_password'): ?>
                            <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                            </a>
                        <?php endif; ?>

                        <?php if (!$isLandingPage && $currentAction !== 'register' && $currentAction !== 'login' && $currentAction !== 'forgot_password' && $currentAction !== 'reset_password'): ?>
                            <a href="<?= BASE_URL ?>/index.php?action=register" class="btn btn-sm btn-navy rounded-pill px-3 fw-semibold d-none d-sm-inline-flex align-items-center">
                                <i class="bi bi-person-plus-fill me-1"></i> Register
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- PAGE CONTENT CONTAINER -->
        <main class="<?= $isLoggedIn ? 'p-3 p-md-4 flex-grow-1' : 'auth-main flex-grow-1 d-flex align-items-center justify-content-center p-3 p-md-4' ?>">

            <!-- Global Flash Messages -->
            <?php if (!empty($_SESSION['error_message']) && $currentAction !== 'home' && $currentAction !== 'insights' && $currentAction !== 'search' && $currentAction !== 'welcome' && $currentAction !== 'login' && $currentAction !== 'forgot_password' && $currentAction !== 'reset_password'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 mx-auto" style="max-width: 900px;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success_message']) && $currentAction !== 'home' && $currentAction !== 'insights' && $currentAction !== 'search' && $currentAction !== 'welcome' && $currentAction !== 'login' && $currentAction !== 'forgot_password' && $currentAction !== 'reset_password'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 mx-auto" style="max-width: 900px;" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= htmlspecialchars($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
