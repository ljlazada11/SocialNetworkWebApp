<?php
// app/views/admin/dashboard.php
// Admin Dashboard View - Modern layout matching SMCC Connect theme

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- ADMIN DASHBOARD HERO HEADER -->
<div class="card-modern p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill">
                    <i class="bi bi-shield-lock-fill me-1"></i> Administrator Access
                </span>
                <span class="badge bg-white bg-opacity-20 text-white px-3 py-1 rounded-pill">
                    <i class="bi bi-database me-1"></i> Database: social_app
                </span>
            </div>
            <h2 class="fw-bold mb-1 text-white">Admin Control Center</h2>
            <p class="mb-0 text-white-50 small">
                Manage platform users, moderate posts and comments, inspect platform activity, and enforce server-side security.
            </p>
        </div>
        <div class="text-md-end">
            <a href="<?= BASE_URL ?>/" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Newsfeed
            </a>
        </div>
    </div>
</div>

<!-- FLASH MESSAGES -->
<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- 1. DASHBOARD OVERVIEW SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <!-- Total Users Card -->
    <div class="col-6 col-md-3">
        <div class="card-modern p-3 h-100 border-0 shadow-sm" style="border-left: 4px solid #3b82f6 !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total Users</span>
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?= (int)($summaryStats['total_users'] ?? 0); ?></h3>
            <span class="text-muted micro-text">
                <i class="bi bi-shield-check text-warning me-1"></i> <?= (int)($summaryStats['admin_users'] ?? 0); ?> Admins &bull; <?= (int)($summaryStats['normal_users'] ?? 0); ?> Users
            </span>
        </div>
    </div>

    <!-- Total Posts Card -->
    <div class="col-6 col-md-3">
        <div class="card-modern p-3 h-100 border-0 shadow-sm" style="border-left: 4px solid #10b981 !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total Posts</span>
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                    <i class="bi bi-file-earmark-post-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?= (int)($summaryStats['total_posts'] ?? 0); ?></h3>
            <span class="text-muted micro-text">
                <i class="bi bi-bar-chart-line text-success me-1"></i> Avg <?= htmlspecialchars($summaryStats['avg_posts_per_user'] ?? 0); ?> posts/user
            </span>
        </div>
    </div>

    <!-- Total Comments Card -->
    <div class="col-6 col-md-3">
        <div class="card-modern p-3 h-100 border-0 shadow-sm" style="border-left: 4px solid #f59e0b !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total Comments</span>
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                    <i class="bi bi-chat-left-text-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?= (int)($summaryStats['total_comments'] ?? 0); ?></h3>
            <span class="text-muted micro-text">
                <i class="bi bi-chat-dots text-warning me-1"></i> Avg <?= htmlspecialchars($summaryStats['avg_comments_per_post'] ?? 0); ?> comments/post
            </span>
        </div>
    </div>

    <!-- Total Likes Card -->
    <div class="col-6 col-md-3">
        <div class="card-modern p-3 h-100 border-0 shadow-sm" style="border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total Likes</span>
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                    <i class="bi bi-heart-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?= (int)($summaryStats['total_likes'] ?? 0); ?></h3>
            <span class="text-muted micro-text">
                <i class="bi bi-hand-thumbs-up text-danger me-1"></i> Engagements logged
            </span>
        </div>
    </div>
</div>

<!-- NAVIGATION TABS -->
<ul class="nav nav-tabs nav-fill mb-4 border-bottom-0 gap-1">
    <li class="nav-item">
        <a class="nav-link rounded-3 fw-semibold <?= ($tab === 'overview') ? 'active bg-white shadow-sm border' : 'bg-light text-secondary'; ?>" href="<?= BASE_URL ?>/index.php?action=admin&tab=overview">
            <i class="bi bi-speedometer2 me-1 text-primary"></i> Overview
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-3 fw-semibold <?= ($tab === 'users') ? 'active bg-white shadow-sm border' : 'bg-light text-secondary'; ?>" href="<?= BASE_URL ?>/index.php?action=admin&tab=users">
            <i class="bi bi-people-fill me-1 text-success"></i> User Management
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-3 fw-semibold <?= ($tab === 'posts') ? 'active bg-white shadow-sm border' : 'bg-light text-secondary'; ?>" href="<?= BASE_URL ?>/index.php?action=admin&tab=posts">
            <i class="bi bi-file-earmark-post me-1 text-info"></i> Post Management
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-3 fw-semibold <?= ($tab === 'comments') ? 'active bg-white shadow-sm border' : 'bg-light text-secondary'; ?>" href="<?= BASE_URL ?>/index.php?action=admin&tab=comments">
            <i class="bi bi-chat-left-dots-fill me-1 text-warning"></i> Comment Moderation
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-3 fw-semibold <?= ($tab === 'stats') ? 'active bg-white shadow-sm border' : 'bg-light text-secondary'; ?>" href="<?= BASE_URL ?>/index.php?action=admin&tab=stats">
            <i class="bi bi-graph-up-arrow me-1 text-danger"></i> Platform Activity Stats
        </a>
    </li>
</ul>

<!-- TAB CONTENT 1: OVERVIEW -->
<?php if ($tab === 'overview'): ?>
    <div class="row g-4 mb-4">
        <!-- Quick Stats & Top Active Users -->
        <div class="col-12 col-lg-7">
            <div class="card-modern p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-trophy-fill text-warning me-2"></i> Top Active Contributors
                    </h5>
                    <span class="badge bg-light text-dark border">Ranked by Posts</span>
                </div>

                <?php if (!empty($mostActiveUsers)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($mostActiveUsers as $idx => $u): ?>
                            <?php
                            $initials = strtoupper(mb_substr($u['full_name'] ?: $u['username'], 0, 2));
                            ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-3 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-primary rounded-circle" style="width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">
                                        <?= $idx + 1; ?>
                                    </span>
                                    <div class="user-avatar-sm" style="width:36px; height:36px; font-size:0.85rem;">
                                        <?= htmlspecialchars($initials); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($u['full_name']); ?>
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-warning text-dark micro-text ms-1">Admin</span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-muted small">@<?= htmlspecialchars($u['username']); ?></span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                        <?= (int)$u['total_posts']; ?> <?= $u['total_posts'] == 1 ? 'post' : 'posts'; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">No contributors found yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Most Liked Posts Preview -->
        <div class="col-12 col-lg-5">
            <div class="card-modern p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-heart-fill text-danger me-2"></i> Most Liked Content
                    </h5>
                    <span class="badge bg-light text-dark border">Engagements</span>
                </div>

                <?php if (!empty($mostLikedPosts)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($mostLikedPosts as $lp): ?>
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">@<?= htmlspecialchars($lp['author_username']); ?></span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                        <i class="bi bi-heart-fill me-1"></i> <?= (int)$lp['like_count']; ?>
                                    </span>
                                </div>
                                <p class="text-secondary small mb-1 text-truncate" style="max-width: 100%;">
                                    "<?= htmlspecialchars($lp['content']); ?>"
                                </p>
                                <span class="text-muted micro-text"><?= date('M d, Y h:i A', strtotime($lp['created_at'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">No liked posts recorded.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SQL CONCEPTS DEMONSTRATION BOX -->
    <div class="card-modern p-4 shadow-sm mb-4 bg-light border">
        <h5 class="fw-bold text-dark mb-3">
            <i class="bi bi-code-slash text-primary me-2"></i> Step 12 SQL Database Implementation Verification
        </h5>
        <div class="row g-3 small">
            <div class="col-md-4">
                <div class="p-3 bg-white rounded-3 border h-100">
                    <strong class="text-primary d-block mb-1"><i class="bi bi-check-circle-fill me-1"></i> SELECT & WHERE</strong>
                    <p class="text-muted mb-0">Used for search filters, role verification, and record lookups.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white rounded-3 border h-100">
                    <strong class="text-success d-block mb-1"><i class="bi bi-check-circle-fill me-1"></i> JOIN & LEFT JOIN</strong>
                    <p class="text-muted mb-0">Combines users, posts, comments, and likes safely without excluding zero-item records.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white rounded-3 border h-100">
                    <strong class="text-warning text-dark d-block mb-1"><i class="bi bi-check-circle-fill me-1"></i> Aggregate & GROUP BY</strong>
                    <p class="text-muted mb-0">Calculates summary stats using <code>COUNT(DISTINCT)</code> and <code>GROUP BY</code> clauses.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- TAB CONTENT 2: USER MANAGEMENT (REQUIREMENT 4) -->
<?php if ($tab === 'users'): ?>
    <div class="card-modern p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-people-fill text-success me-2"></i> Platform Registered Users
                </h5>
                <p class="text-muted small mb-0">
                    Manage member accounts, review user roles, and adjust permissions securely. Sensitive security credentials (password hashes) are excluded.
                </p>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-2 align-self-start align-self-md-auto">
                Total Matches: <?= (int)$pagination['total_items']; ?>
            </span>
        </div>

        <!-- SEARCH AND ROLE FILTER TOOLBAR -->
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 mb-4 align-items-center">
            <input type="hidden" name="action" value="admin">
            <input type="hidden" name="tab" value="users">

            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Search by username or full name..." value="<?= htmlspecialchars($search); ?>">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="admin" <?= ($roleFilter === 'admin') ? 'selected' : ''; ?>>Administrators Only</option>
                    <option value="user" <?= ($roleFilter === 'user') ? 'selected' : ''; ?>>Regular Users Only</option>
                </select>
            </div>

            <div class="col-6 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Apply Filter
                </button>
                <a href="<?= BASE_URL ?>/index.php?action=admin&tab=users" class="btn btn-outline-secondary btn-sm rounded-pill">
                    Reset
                </a>
            </div>
        </form>

        <!-- USERS TABLE -->
        <?php if (!empty($usersData)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User Details</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th class="text-center">Activity</th>
                            <th>Registered Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usersData as $u): ?>
                            <?php
                            $initials = strtoupper(mb_substr($u['full_name'] ?: $u['username'], 0, 2));
                            $isSelf = ((int)$u['id'] === (int)$_SESSION['user_id']);
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar-sm" style="width:36px; height:36px; font-size:0.85rem;">
                                            <?= htmlspecialchars($initials); ?>
                                        </div>
                                        <div>
                                            <a href="<?= BASE_URL ?>/index.php?action=profile&id=<?= (int)$u['id']; ?>" class="fw-bold text-dark text-decoration-none">
                                                <?= htmlspecialchars($u['full_name']); ?>
                                            </a>
                                            <?php if ($isSelf): ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle micro-text ms-1">You</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-secondary fw-semibold">@<?= htmlspecialchars($u['username']); ?></span></td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="bi bi-shield-check me-1"></i> Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border me-1"><?= (int)$u['post_count']; ?> posts</span>
                                    <span class="badge bg-light text-dark border"><?= (int)$u['comment_count']; ?> comments</span>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y', strtotime($u['created_at'])); ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <!-- CHANGE ROLE FORM -->
                                        <form action="<?= BASE_URL ?>/index.php?action=admin_change_role" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                            <input type="hidden" name="user_id" value="<?= (int)$u['id']; ?>">
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <input type="hidden" name="role" value="user">
                                                <button type="submit" class="btn btn-outline-warning btn-sm" title="Demote to User" <?= $isSelf ? 'disabled' : ''; ?> onclick="return confirm('Are you sure you want to demote this admin to regular user?');">
                                                    <i class="bi bi-arrow-down-circle"></i> Demote
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="role" value="admin">
                                                <button type="submit" class="btn btn-outline-success btn-sm" title="Promote to Admin" onclick="return confirm('Are you sure you want to promote this user to Administrator?');">
                                                    <i class="bi bi-arrow-up-circle"></i> Promote
                                                </button>
                                            <?php endif; ?>
                                        </form>

                                        <!-- DELETE USER FORM -->
                                        <form action="<?= BASE_URL ?>/index.php?action=admin_delete_user" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                            <input type="hidden" name="user_id" value="<?= (int)$u['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete User Account" <?= $isSelf ? 'disabled' : ''; ?> onclick="return confirm('WARNING: Are you sure you want to delete account @<?= htmlspecialchars($u['username']); ?>? All their posts and comments will also be removed.');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION LINKS -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?= ($pagination['current_page'] <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=users&q=<?= urlencode($search); ?>&role=<?= urlencode($roleFilter); ?>&page=<?= $pagination['current_page'] - 1; ?>">Previous</a>
                        </li>
                        <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                            <li class="page-item <?= ($p == $pagination['current_page']) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=users&q=<?= urlencode($search); ?>&role=<?= urlencode($roleFilter); ?>&page=<?= $p; ?>"><?= $p; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($pagination['current_page'] >= $pagination['total_pages']) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=users&q=<?= urlencode($search); ?>&role=<?= urlencode($roleFilter); ?>&page=<?= $pagination['current_page'] + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-2 d-block mb-2"></i>
                No users found matching your criteria.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- TAB CONTENT 3: POST MANAGEMENT (REQUIREMENT 5) -->
<?php if ($tab === 'posts'): ?>
    <div class="card-modern p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-file-earmark-post text-info me-2"></i> Platform Posts Overview & Moderation
                </h5>
                <p class="text-muted small mb-0">
                    Review and moderate user posts across the network. Authorized administrators can remove inappropriate content.
                </p>
            </div>
            <span class="badge bg-info rounded-pill px-3 py-2 align-self-start align-self-md-auto text-white">
                Total Posts: <?= (int)$pagination['total_items']; ?>
            </span>
        </div>

        <!-- SEARCH TOOLBAR -->
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 mb-4">
            <input type="hidden" name="action" value="admin">
            <input type="hidden" name="tab" value="posts">

            <div class="col-12 col-md-8">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Search post content or author name..." value="<?= htmlspecialchars($search); ?>">
                </div>
            </div>

            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Search
                </button>
                <a href="<?= BASE_URL ?>/index.php?action=admin&tab=posts" class="btn btn-outline-secondary btn-sm rounded-pill">
                    Reset
                </a>
            </div>
        </form>

        <!-- POSTS TABLE -->
        <?php if (!empty($postsData)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Author</th>
                            <th>Content Preview</th>
                            <th class="text-center">Image</th>
                            <th class="text-center">Engagements</th>
                            <th>Posted Date</th>
                            <th class="text-end">Moderation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postsData as $p): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= (int)$p['post_id']; ?></td>
                                <td>
                                    <div>
                                        <a href="<?= BASE_URL ?>/index.php?action=profile&id=<?= (int)$p['author_id']; ?>" class="fw-bold text-dark text-decoration-none">
                                            <?= htmlspecialchars($p['author_fullname']); ?>
                                        </a>
                                        <span class="d-block text-muted micro-text">@<?= htmlspecialchars($p['author_username']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark small text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($p['content']); ?>">
                                        <?= htmlspecialchars($p['content']); ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($p['image'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                            <i class="bi bi-image me-1"></i> Image
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted micro-text">None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger rounded-pill me-1"><i class="bi bi-heart-fill"></i> <?= (int)$p['like_count']; ?></span>
                                    <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-chat-left-dots-fill"></i> <?= (int)$p['comment_count']; ?></span>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($p['created_at'])); ?></td>
                                <td class="text-end">
                                    <form action="<?= BASE_URL ?>/index.php?action=admin_delete_post" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                        <input type="hidden" name="post_id" value="<?= (int)$p['post_id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete post #<?= (int)$p['post_id']; ?> by @<?= htmlspecialchars($p['author_username']); ?>?');">
                                            <i class="bi bi-trash me-1"></i> Remove Post
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?= ($pagination['current_page'] <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=posts&q=<?= urlencode($search); ?>&page=<?= $pagination['current_page'] - 1; ?>">Previous</a>
                        </li>
                        <?php for ($pg = 1; $pg <= $pagination['total_pages']; $pg++): ?>
                            <li class="page-item <?= ($pg == $pagination['current_page']) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=posts&q=<?= urlencode($search); ?>&page=<?= $pg; ?>"><?= $pg; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($pagination['current_page'] >= $pagination['total_pages']) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=posts&q=<?= urlencode($search); ?>&page=<?= $pagination['current_page'] + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                No posts found matching search filter.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- TAB CONTENT 4: COMMENT MODERATION (REQUIREMENT 6) -->
<?php if ($tab === 'comments'): ?>
    <div class="card-modern p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-chat-left-dots-fill text-warning me-2"></i> Comment Moderation Control
                </h5>
                <p class="text-muted small mb-0">
                    Inspect user comments across the platform and remove inappropriate or abusive remarks.
                </p>
            </div>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 align-self-start align-self-md-auto">
                Total Comments: <?= (int)$pagination['total_items']; ?>
            </span>
        </div>

        <!-- SEARCH TOOLBAR -->
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 mb-4">
            <input type="hidden" name="action" value="admin">
            <input type="hidden" name="tab" value="comments">

            <div class="col-12 col-md-8">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Search comment content, commenter, or post text..." value="<?= htmlspecialchars($search); ?>">
                </div>
            </div>

            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Search
                </button>
                <a href="<?= BASE_URL ?>/index.php?action=admin&tab=comments" class="btn btn-outline-secondary btn-sm rounded-pill">
                    Reset
                </a>
            </div>
        </form>

        <!-- COMMENTS TABLE -->
        <?php if (!empty($commentsData)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Commenter</th>
                            <th>Comment Text</th>
                            <th>Parent Post</th>
                            <th>Date</th>
                            <th class="text-end">Moderation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commentsData as $c): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= (int)$c['comment_id']; ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/index.php?action=profile&id=<?= (int)$c['commenter_id']; ?>" class="fw-bold text-dark text-decoration-none">
                                        <?= htmlspecialchars($c['commenter_fullname']); ?>
                                    </a>
                                    <span class="d-block text-muted micro-text">@<?= htmlspecialchars($c['commenter_username']); ?></span>
                                </td>
                                <td>
                                    <div class="bg-light p-2 rounded-3 small text-dark" style="max-width: 300px;">
                                        "<?= htmlspecialchars($c['comment_content']); ?>"
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small text-truncate" style="max-width: 220px;">
                                        Post #<?= (int)$c['post_id']; ?> by @<?= htmlspecialchars($c['post_author_username']); ?>:
                                        "<?= htmlspecialchars($c['post_content']); ?>"
                                    </div>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($c['comment_date'])); ?></td>
                                <td class="text-end">
                                    <form action="<?= BASE_URL ?>/index.php?action=admin_delete_comment" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                        <input type="hidden" name="comment_id" value="<?= (int)$c['comment_id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete comment #<?= (int)$c['comment_id']; ?>?');">
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?= ($pagination['current_page'] <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=comments&q=<?= urlencode($search); ?>&page=<?= $pagination['current_page'] - 1; ?>">Previous</a>
                        </li>
                        <?php for ($cg = 1; $cg <= $pagination['total_pages']; $cg++): ?>
                            <li class="page-item <?= ($cg == $pagination['current_page']) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=comments&q=<?= urlencode($search); ?>&page=<?= $cg; ?>"><?= $cg; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($pagination['current_page'] >= $pagination['total_pages']) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/index.php?action=admin&tab=comments&q=<?= urlencode($search); ?>&page=<?= $pagination['current_page'] + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-left-x fs-2 d-block mb-2"></i>
                No comments found matching filter.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- TAB CONTENT 5: PLATFORM ACTIVITY STATS (REQUIREMENT 7) -->
<?php if ($tab === 'stats'): ?>
    <div class="row g-4 mb-4">
        <!-- Most Active Users -->
        <div class="col-12 col-lg-6">
            <div class="card-modern p-4 h-100 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i class="bi bi-graph-up text-primary me-2"></i> Most Active Users (Post Counts)
                </h5>
                <?php if (!empty($mostActiveUsers)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th class="text-center">Total Posts</th>
                                    <th class="text-end">Last Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mostActiveUsers as $index => $u): ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?= $index + 1; ?></td>
                                        <td>
                                            <span class="fw-bold text-dark">@<?= htmlspecialchars($u['username']); ?></span>
                                            <span class="d-block text-muted micro-text"><?= htmlspecialchars($u['full_name']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill px-3"><?= (int)$u['total_posts']; ?></span>
                                        </td>
                                        <td class="text-end micro-text text-muted">
                                            <?= !empty($u['last_post_at']) ? date('M d, Y', strtotime($u['last_post_at'])) : 'N/A'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">No activity records.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Most Liked Posts -->
        <div class="col-12 col-lg-6">
            <div class="card-modern p-4 h-100 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i class="bi bi-heart-fill text-danger me-2"></i> Most Liked Posts Ranking
                </h5>
                <?php if (!empty($mostLikedPosts)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($mostLikedPosts as $lp): ?>
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">@<?= htmlspecialchars($lp['author_username']); ?></span>
                                    <span class="badge bg-danger rounded-pill px-3 py-1">
                                        <i class="bi bi-heart-fill me-1"></i> <?= (int)$lp['like_count']; ?> Likes
                                    </span>
                                </div>
                                <p class="text-secondary small mb-1 text-truncate">
                                    "<?= htmlspecialchars($lp['content']); ?>"
                                </p>
                                <span class="text-muted micro-text"><?= date('M d, Y h:i A', strtotime($lp['created_at'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">No liked posts available.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
