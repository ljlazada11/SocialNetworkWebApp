<?php
// app/views/search/results.php
// Centered Modern Search View for SMCC Connect

$currentUserId = $_SESSION['user_id'] ?? null;
$currentUserInitials = 'U';
if ($currentUserId) {
    $name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';
    $parts = explode(' ', trim($name));
    $currentUserInitials = count($parts) >= 2
        ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts)-1], 0, 1))
        : strtoupper(mb_substr($name, 0, 2));
}

$userCount = count($users ?? []);
$postCount = count($posts ?? []);
?>

<div class="search-view-container">

    <!-- Flash Notifications -->
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

    <!-- 1. Search Title -->
    <div class="search-hero">
        <h3 class="search-hero-title">Search</h3>
        <p class="search-hero-subtitle">Discover users and posts across SMCC Connect</p>
    </div>

    <!-- 2. Search Input Box (No Search Button, Enter submits) -->
    <form action="<?= BASE_URL ?>/index.php" method="GET" class="search-box-centered" role="search">
        <input type="hidden" name="action" value="search">
        <input type="hidden" name="type" value="<?= htmlspecialchars($type); ?>">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter); ?>">
        <i class="bi bi-search search-icon"></i>
        <input 
            type="text" 
            name="q" 
            class="search-input-centered" 
            placeholder="Search users or posts..." 
            value="<?= htmlspecialchars($query ?? ''); ?>" 
            autocomplete="off"
        >
        <?php if (!empty($query)): ?>
            <a href="<?= BASE_URL ?>/index.php?action=search" class="search-clear-btn" title="Clear search">&times;</a>
        <?php endif; ?>
    </form>

    <!-- 3. Filter Controls: [ All ] [ Users ] [ Posts ] & [ Newest ] [ Oldest ] -->
    <div class="search-filter-group">
        <div class="filter-pills-wrap">
            <a href="<?= BASE_URL ?>/index.php?action=search&q=<?= urlencode($query); ?>&type=all&filter=<?= urlencode($filter); ?>" 
               class="filter-pill <?= ($type === 'all') ? 'active' : ''; ?>">
                All
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=search&q=<?= urlencode($query); ?>&type=users&filter=<?= urlencode($filter); ?>" 
               class="filter-pill <?= ($type === 'users') ? 'active' : ''; ?>">
                Users
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=search&q=<?= urlencode($query); ?>&type=posts&filter=<?= urlencode($filter); ?>" 
               class="filter-pill <?= ($type === 'posts') ? 'active' : ''; ?>">
                Posts
            </a>
        </div>

        <?php if ($type !== 'users'): ?>
            <div class="sort-pills-wrap">
                <a href="<?= BASE_URL ?>/index.php?action=search&q=<?= urlencode($query); ?>&type=<?= urlencode($type); ?>&filter=newest" 
                   class="sort-pill <?= ($filter === 'newest') ? 'active' : ''; ?>">
                    Newest
                </a>
                <span class="text-muted" style="font-size: 0.7rem;">&bull;</span>
                <a href="<?= BASE_URL ?>/index.php?action=search&q=<?= urlencode($query); ?>&type=<?= urlencode($type); ?>&filter=oldest" 
                   class="sort-pill <?= ($filter === 'oldest') ? 'active' : ''; ?>">
                    Oldest
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 4. Search Results Content -->
    <?php if ($isEmptySearch): ?>
        <!-- Empty Search Guidance -->
        <div class="search-empty-state">
            <div class="search-empty-icon">
                <i class="bi bi-search"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Find Users and Posts</h5>
            <p class="small text-muted mb-0 col-md-9 mx-auto">
                Type a username, full name, or keywords from a post in the search box above and press <kbd class="px-2 py-1 bg-light text-dark border rounded">Enter</kbd>.
            </p>
        </div>

    <?php elseif (($type === 'all' && empty($users) && empty($posts)) || ($type === 'users' && empty($users)) || ($type === 'posts' && empty($posts))): ?>
        <!-- No Results Found State -->
        <div class="search-empty-state">
            <div class="search-empty-icon">
                <i class="bi bi-slash-circle"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">No results found</h5>
            <p class="small text-muted mb-0 col-md-9 mx-auto">
                No matching results were found for "<strong><?= htmlspecialchars($query); ?></strong>".
                <br>Try another search query or switch filters.
            </p>
        </div>

    <?php else: ?>

        <!-- USERS SECTION -->
        <?php if (($type === 'all' || $type === 'users') && !empty($users)): ?>
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-people-fill text-primary me-2"></i>Users (<?= count($users); ?>)
                    </h6>
                </div>

                <div class="d-flex flex-column gap-3">
                    <?php foreach ($users as $user): ?>
                        <?php
                        $uParts = explode(' ', trim($user['full_name']));
                        $uInitials = count($uParts) >= 2
                            ? strtoupper(mb_substr($uParts[0], 0, 1) . mb_substr($uParts[count($uParts)-1], 0, 1))
                            : strtoupper(mb_substr($user['full_name'], 0, 2));
                        ?>
                        <div class="search-user-card">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3 min-w-0">
                                    <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $user['id']; ?>" class="text-decoration-none">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img 
                                                src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars(basename($user['profile_image'])) ?>" 
                                                alt="<?= htmlspecialchars($user['full_name']); ?>" 
                                                class="post-avatar"
                                                style="width: 44px; height: 44px; object-fit: cover;"
                                                onerror="this.onerror=null; this.outerHTML='<div class=\'post-avatar\' style=\'width: 44px; height: 44px; font-size: 1rem;\'><?= htmlspecialchars($uInitials) ?></div>';"
                                            >
                                        <?php else: ?>
                                            <div class="post-avatar" style="width: 44px; height: 44px; font-size: 1rem;">
                                                <?= htmlspecialchars($uInitials) ?>
                                            </div>
                                        <?php endif; ?>
                                    </a>

                                    <div class="min-w-0">
                                        <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $user['id']; ?>" class="fw-bold text-dark text-decoration-none d-block text-truncate">
                                            <?= htmlspecialchars($user['full_name']); ?>
                                        </a>
                                        <div class="text-muted small text-truncate">
                                            @<?= htmlspecialchars($user['username']); ?>
                                        </div>
                                        <?php if (!empty($user['bio'])): ?>
                                            <div class="text-muted small text-truncate mt-1" style="max-width: 380px;">
                                                <?= htmlspecialchars($user['bio']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $user['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 flex-shrink-0">
                                    <i class="bi bi-person me-1"></i> Profile
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- POSTS SECTION -->
        <?php if (($type === 'all' || $type === 'posts') && !empty($posts)): ?>
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-chat-square-text-fill text-warning me-2"></i>Posts (<?= count($posts); ?>)
                    </h6>
                </div>

                <?php foreach ($posts as $post): ?>
                    <?php
                    $authorParts = explode(' ', trim($post['full_name']));
                    $authorInitials = count($authorParts) >= 2
                        ? strtoupper(mb_substr($authorParts[0], 0, 1) . mb_substr($authorParts[count($authorParts)-1], 0, 1))
                        : strtoupper(mb_substr($post['full_name'], 0, 2));

                    $postTime = date('M j, Y \a\t g:i a', strtotime($post['created_at']));
                    $diffHours = round((time() - strtotime($post['created_at'])) / 3600);
                    $timeBadge = ($diffHours < 24 && $diffHours >= 1) ? $diffHours . 'h ago' : ($diffHours < 1 ? 'Just now' : date('M j', strtotime($post['created_at'])));
                    ?>
                    <article class="post-card" id="post-<?= $post['id'] ?>">
                        <!-- Post Author Header -->
                        <div class="post-header">
                            <div class="post-author-box">
                                <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $post['user_id']; ?>" class="text-decoration-none">
                                    <?php if (!empty($post['profile_image'])): ?>
                                        <img 
                                            src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars(basename($post['profile_image'])) ?>" 
                                            alt="<?= htmlspecialchars($post['full_name']); ?>" 
                                            class="post-avatar"
                                            onerror="this.onerror=null; this.outerHTML='<div class=\'post-avatar\'><?= htmlspecialchars($authorInitials) ?></div>';"
                                        >
                                    <?php else: ?>
                                        <div class="post-avatar">
                                            <?= htmlspecialchars($authorInitials) ?>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <div>
                                    <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $post['user_id']; ?>" class="post-author-name">
                                        <?= htmlspecialchars($post['full_name']); ?>
                                    </a>
                                    <div class="post-meta">
                                        @<?= htmlspecialchars($post['username']); ?> &bull; <?= htmlspecialchars($timeBadge); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Post Menu Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted p-0 text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Post actions">
                                    <i class="bi bi-three-dots fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <?php if ($currentUserId && (int)$currentUserId === (int)$post['user_id']): ?>
                                        <li>
                                            <a class="dropdown-item small text-primary" href="<?= BASE_URL ?>/index.php?action=edit_post&post_id=<?= $post['id'] ?>">
                                                <i class="bi bi-pencil-square me-2"></i>Edit post
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item small text-danger" href="<?= BASE_URL ?>/index.php?action=delete_post&post_id=<?= $post['id'] ?>" onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                                                <i class="bi bi-trash me-2"></i>Delete post
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li>
                                        <a class="dropdown-item small" href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $post['user_id']; ?>">
                                            <i class="bi bi-person me-2"></i>View author profile
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Post Text Content -->
                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($post['content'])); ?>
                        </div>

                        <!-- Optional Post Image -->
                        <?php if (!empty($post['image'])): ?>
                            <?php
                            $postImgFile = __DIR__ . '/../../../public/uploads/posts/' . basename($post['image']);
                            $hasRealImage = file_exists($postImgFile);
                            ?>
                            <?php if ($hasRealImage): ?>
                                <div class="post-image-container">
                                    <img src="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($post['image'])) ?>" alt="Post attachment">
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Post Engagement Actions (Like / Comment / Edit / Delete) -->
                        <div class="post-footer-actions d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <form action="<?= BASE_URL ?>/index.php?action=like_post" method="POST" class="d-inline">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="post-action-btn <?= !empty($post['is_liked']) ? 'liked' : '' ?>" title="Like">
                                        <i class="bi <?= !empty($post['is_liked']) ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                        <span><?= (int)$post['likes_count']; ?></span>
                                    </button>
                                </form>

                                <button type="button" class="post-action-btn" onclick="toggleComments(<?= $post['id'] ?>)" title="Comments">
                                    <i class="bi bi-chat"></i>
                                    <span><?= (int)$post['comments_count']; ?></span>
                                </button>
                            </div>

                            <?php if ($currentUserId && (int)$currentUserId === (int)$post['user_id']): ?>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="<?= BASE_URL ?>/index.php?action=edit_post&post_id=<?= $post['id'] ?>" class="btn btn-sm btn-light text-primary border-0 rounded-pill px-2 py-1 small" title="Edit your post">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=delete_post&post_id=<?= $post['id'] ?>" class="btn btn-sm btn-light text-danger border-0 rounded-pill px-2 py-1 small" onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.');" title="Delete your post">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Comments Section -->
                        <div class="comments-container d-none" id="comments-box-<?= $post['id'] ?>">
                            <?php if (!empty($post['comments'])): ?>
                                <?php foreach ($post['comments'] as $comment): ?>
                                    <?php
                                    $cParts = explode(' ', trim($comment['full_name'] ?? 'User'));
                                    $cInitials = count($cParts) >= 2
                                        ? strtoupper(mb_substr($cParts[0], 0, 1) . mb_substr($cParts[count($cParts)-1], 0, 1))
                                        : strtoupper(mb_substr($comment['full_name'] ?? 'U', 0, 2));

                                    $cTimeDiff = time() - strtotime($comment['created_at']);
                                    if ($cTimeDiff < 60) {
                                        $cTimeBadge = 'Just now';
                                    } elseif ($cTimeDiff < 3600) {
                                        $cTimeBadge = floor($cTimeDiff / 60) . 'm ago';
                                    } elseif ($cTimeDiff < 86400) {
                                        $cTimeBadge = floor($cTimeDiff / 3600) . 'h ago';
                                    } else {
                                        $cTimeBadge = date('M j, Y', strtotime($comment['created_at']));
                                    }
                                    ?>
                                    <div class="comment-bubble" id="comment-<?= $comment['id'] ?>">
                                        <?php if (!empty($comment['profile_image'])): ?>
                                            <img 
                                                src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars(basename($comment['profile_image'])) ?>" 
                                                alt="<?= htmlspecialchars($comment['full_name']); ?>" 
                                                class="comment-avatar"
                                                onerror="this.onerror=null; this.outerHTML='<div class=\'comment-avatar\'><?= htmlspecialchars($cInitials) ?></div>';"
                                            >
                                        <?php else: ?>
                                            <div class="comment-avatar">
                                                <?= htmlspecialchars($cInitials) ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="comment-content-box">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                <div>
                                                    <span class="comment-author"><?= htmlspecialchars($comment['full_name']); ?></span>
                                                    <span class="text-muted small">@<?= htmlspecialchars($comment['username']); ?></span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted"><?= htmlspecialchars($cTimeBadge); ?></small>

                                                    <?php if ($currentUserId && (int)$currentUserId === (int)$comment['user_id']): ?>
                                                        <a href="<?= BASE_URL ?>/index.php?action=edit_comment&comment_id=<?= $comment['id'] ?>" class="comment-btn-action text-primary" title="Edit comment">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>
                                                        <form action="<?= BASE_URL ?>/index.php?action=delete_comment" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this comment? This action cannot be undone.');">
                                                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                            <button type="submit" class="comment-btn-action text-danger" title="Delete comment">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="comment-text">
                                                <?= nl2br(htmlspecialchars($comment['content'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Add Comment Form -->
                            <?php if ($currentUserId): ?>
                                <form action="<?= BASE_URL ?>/index.php?action=create_comment" method="POST" class="comment-input-row">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input 
                                        type="text" 
                                        name="content" 
                                        class="form-control" 
                                        placeholder="Write a comment..." 
                                        maxlength="1000" 
                                        required
                                    >
                                    <button type="submit" class="btn btn-sm btn-accent rounded-pill px-3" title="Post comment">
                                        <i class="bi bi-send-fill"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<script>
function toggleComments(postId) {
    const box = document.getElementById('comments-box-' + postId);
    if (box) {
        box.classList.toggle('d-none');
    }
}
</script>
