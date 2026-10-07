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

// Get current user avatar if logged in
$userInitials = 'U';
$userAvatarPath = null;
if (isset($_SESSION['user_id'])) {
    $name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        $userInitials = strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts)-1], 0, 1));
    } else {
        $userInitials = strtoupper(mb_substr($name, 0, 2));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - SMCC Connect' : 'SMCC Connect'; ?></title>

    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Modern Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body>

<div class="app-layout">

    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- LEFT SIDEBAR -->
    <aside class="app-sidebar" id="appSidebar">
        <!-- Sidebar Brand / Logo -->
        <div class="sidebar-header">
            <a href="<?= BASE_URL ?>/" class="brand-link">
                <div class="brand-icon-box">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span>SMCC Connect</span>
            </a>
        </div>

        <!-- Sidebar Navigation Menu -->
        <div class="sidebar-nav">
            <a href="<?= BASE_URL ?>/" class="nav-link <?= ($currentAction === 'home') ? 'active' : '' ?>">
                <i class="bi bi-house-door-fill"></i>
                <span>Home</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=people" class="nav-link <?= ($currentAction === 'people') ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span>People</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=groups" class="nav-link <?= ($currentAction === 'groups') ? 'active' : '' ?>">
                <i class="bi bi-people-gear"></i>
                <span>Groups</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=events" class="nav-link <?= ($currentAction === 'events') ? 'active' : '' ?>">
                <i class="bi bi-calendar-event"></i>
                <span>Events</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=messages" class="nav-link <?= ($currentAction === 'messages') ? 'active' : '' ?>">
                <i class="bi bi-chat-dots"></i>
                <span>Messages</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=saved" class="nav-link <?= ($currentAction === 'saved') ? 'active' : '' ?>">
                <i class="bi bi-bookmark"></i>
                <span>Saved</span>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>/index.php?action=profile" class="nav-link <?= ($currentAction === 'profile') ? 'active' : '' ?>">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/index.php?action=edit_profile" class="nav-link <?= ($currentAction === 'edit_profile') ? 'active' : '' ?>">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </div>

        <!-- Sidebar Footer (Sign Out / Sign In) -->
        <div class="sidebar-footer">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>/index.php?action=logout" class="nav-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/index.php?action=login" class="nav-link text-white">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Sign In</span>
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA WRAPPER -->
    <div class="app-main">

        <!-- TOP HEADER -->
        <header class="app-header">
            <div class="header-container">
                <!-- Mobile Toggle Button -->
                <button class="btn btn-sm d-lg-none p-1 text-dark" onclick="toggleSidebar()" aria-label="Toggle Navigation">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Search Bar -->
                <div class="header-search">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" placeholder="Search posts, people, or topics..." aria-label="Search">
                    <span class="search-shortcut">⌘K</span>
                </div>

                <!-- Right Header Actions -->
                <div class="header-actions">
                    <button class="header-icon-btn" title="Notifications" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                    </button>
                    <button class="header-icon-btn" title="Messages" aria-label="Messages">
                        <i class="bi bi-chat-text"></i>
                    </button>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Profile Dropdown -->
                        <div class="dropdown">
                            <a href="#" class="header-user-btn dropdown-toggle" id="headerUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar-sm">
                                    <?= htmlspecialchars($userInitials); ?>
                                </div>
                                <span class="header-user-name d-none d-sm-inline"><?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="headerUserDropdown">
                                <li>
                                    <div class="px-3 py-2 border-bottom">
                                        <p class="mb-0 fw-bold small text-dark"><?= htmlspecialchars($_SESSION['full_name'] ?? ''); ?></p>
                                        <p class="mb-0 text-muted small">@<?= htmlspecialchars($_SESSION['username'] ?? ''); ?></p>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>/index.php?action=profile">
                                        <i class="bi bi-person text-muted"></i>
                                        <span>My Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>/index.php?action=edit_profile">
                                        <i class="bi bi-pencil-square text-muted"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="<?= BASE_URL ?>/index.php?action=logout">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Sign Out</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-outline-primary btn-sm px-3 rounded-pill">Log In</a>
                        <a href="<?= BASE_URL ?>/index.php?action=register" class="btn btn-accent btn-sm rounded-pill">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT CONTAINER -->
        <main class="p-3 p-md-4 flex-grow-1">
