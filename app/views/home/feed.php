<?php
// app/views/home/feed.php
// Main Social Feed and Dashboard view matching reference design

$currentUserId = $_SESSION['user_id'] ?? null;
?>

<div class="feed-layout-container mx-auto">

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

    <div class="row g-4">
        <!-- ==============================================
             CENTER COLUMN: POST COMPOSER & SOCIAL FEED
             ============================================== -->
        <div class="col-lg-7 col-xl-8">

            <!-- MOBILE UPCOMING EVENTS STRIP (Visible on mobile/tablet < 992px) -->
            <section class="mobile-events-section d-lg-none mb-3" aria-label="Upcoming Events">
                <div class="mes-header">
                    <div class="mes-header-left">
                        <span class="mes-icon"><i class="bi bi-calendar-event-fill"></i></span>
                        <span class="mes-title">Upcoming Events</span>
                        <?php if (!empty($upcomingEvents)): ?>
                            <span class="mes-count"><?= count($upcomingEvents); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($upcomingEvents)): ?>
                        <button type="button" class="mes-view-all" data-bs-toggle="modal" data-bs-target="#modalAllUpcomingEvents">
                            View All <i class="bi bi-arrow-right"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (!empty($upcomingEvents)): ?>
                    <div class="mes-scroll-wrap">
                        <div class="mes-scroll" tabindex="0" role="region" aria-label="Upcoming Events Carousel">
                            <?php foreach ($upcomingEvents as $evt): ?>
                                <?php
                                    $evtTs   = !empty($evt['event_date']) ? strtotime($evt['event_date']) : time();
                                    $evtMon  = strtoupper(date('M', $evtTs));
                                    $evtDay  = date('j', $evtTs);
                                    $evtDow  = date('D', $evtTs);
                                    $todayD  = date('Y-m-d');
                                    $tmrwD   = date('Y-m-d', strtotime('+1 day'));
                                    $isToday    = ($evt['event_date'] === $todayD);
                                    $isTomorrow = ($evt['event_date'] === $tmrwD);
                                    $badgeClass = $isToday ? 'mes-badge-today' : ($isTomorrow ? 'mes-badge-soon' : '');
                                    $badgeText  = $isToday ? 'Today' : ($isTomorrow ? 'Tomorrow' : '');
                                ?>
                                <div class="mes-card" role="button" tabindex="0"
                                     data-bs-toggle="modal" data-bs-target="#modalEventDetails<?= (int)$evt['id']; ?>">
                                    <div class="mes-date-col">
                                        <span class="mes-month"><?= htmlspecialchars($evtMon); ?></span>
                                        <span class="mes-day"><?= htmlspecialchars($evtDay); ?></span>
                                        <span class="mes-dow"><?= htmlspecialchars($evtDow); ?></span>
                                    </div>
                                    <div class="mes-info-col">
                                        <?php if ($badgeText): ?>
                                            <span class="mes-badge <?= $badgeClass; ?>"><?= $badgeText; ?></span>
                                        <?php endif; ?>
                                        <div class="mes-event-title" title="<?= htmlspecialchars($evt['title']); ?>">
                                            <?= htmlspecialchars($evt['title']); ?>
                                        </div>
                                        <div class="mes-meta">
                                            <?php if (!empty($evt['event_time'])): ?>
                                                <span><i class="bi bi-clock"></i> <?= htmlspecialchars($evt['event_time']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($evt['location'])): ?>
                                                <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($evt['location']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mes-fade-right" aria-hidden="true"></div>
                    </div>
                <?php else: ?>
                    <div class="mes-empty">
                        <i class="bi bi-calendar-check"></i>
                        <span>No upcoming events yet</span>
                    </div>
                <?php endif; ?>
            </section>

            <?php if (!$currentUserId): ?>
                <!-- Guest Landing Home Page (No social posts exposed to guests) -->
                <div class="card-modern p-5 text-center mb-4">
                    <div class="mb-3">
                        <div class="mb-3">
                        <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-light.png" alt="CLIQ" class="cliq-logo-full cliq-logo-auth cliq-logo-light">
                        <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-dark.png" alt="CLIQ" class="cliq-logo-full cliq-logo-auth cliq-logo-dark">
                    </div>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">Welcome to CLIQ</h2>
                    <p class="text-muted fs-6 mb-4 col-md-10 mx-auto">
                        The SMCC Community Hub. Sign in or create an account to view campus posts, connect with fellow students, comment, and engage with the community.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-medium">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?action=register" class="btn btn-accent px-4 py-2 rounded-pill fw-medium">
                            <i class="bi bi-person-plus-fill me-1"></i> Create Account
                        </a>
                    </div>
                </div>

                <!-- Campus Highlights Cards for Guests -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card-modern p-4 text-center h-100">
                            <i class="bi bi-chat-heart text-primary fs-2 mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark">Social Newsfeed</h6>
                            <p class="small text-muted mb-0">Share your thoughts, photos, and updates with fellow students.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-modern p-4 text-center h-100">
                            <i class="bi bi-search text-warning fs-2 mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark">Search & Discover</h6>
                            <p class="small text-muted mb-0">Easily find peers, professors, and discussions across campus.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-modern p-4 text-center h-100">
                            <i class="bi bi-shield-check text-success fs-2 mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark">Protected Community</h6>
                            <p class="small text-muted mb-0">Private to verified members of the SMCC collegiate network.</p>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- 1. Post Creation Card -->
                <div class="composer-card">
                    <form action="<?= BASE_URL ?>/index.php?action=create_post" method="POST" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <div class="composer-input-row">
                            <?php if (!empty($userAvatarPath)): ?>
                                <img 
                                    src="<?= $userAvatarPath ?>" 
                                    alt="<?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>" 
                                    class="user-avatar-sm" 
                                    style="width: 40px; height: 40px; object-fit: cover;"
                                    onerror="this.onerror=null; this.outerHTML='<div class=&quot;user-avatar-sm&quot; style=&quot;width: 40px; height: 40px; font-size: 0.9rem;&quot;><?= htmlspecialchars($userInitials ?? 'U') ?></div>';"
                                >
                            <?php else: ?>
                                <div class="user-avatar-sm" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                    <?= htmlspecialchars($userInitials ?? 'U'); ?>
                                </div>
                            <?php endif; ?>
                            <textarea 
                                name="content" 
                                id="composerTextarea"
                                class="composer-input" 
                                rows="2" 
                                placeholder="What's happening on campus?"
                            ></textarea>
                        </div>

                        <!-- Multi-Image Preview Container -->
                        <div id="imagePreviewContainer" class="mt-2 ps-2 ps-sm-5 d-none">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" id="imageCountBadge">
                                    0 photos selected
                                </span>
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none p-0 small" onclick="clearAllSelectedImages()">
                                    <i class="bi bi-trash3 me-1"></i>Clear all
                                </button>
                            </div>
                            <div id="imagePreviewGrid" class="d-flex flex-wrap gap-2"></div>
                        </div>

                        <div class="composer-actions-row">
                            <div class="d-flex align-items-center gap-2">
                                <label for="postImageInput" class="composer-tool-btn mb-0 d-inline-flex align-items-center gap-1" title="Upload Photos (multiple allowed)" style="cursor: pointer;">
                                    <i class="bi bi-images text-secondary"></i>
                                    <span class="small text-muted d-none d-sm-inline">Photos</span>
                                </label>
                                <input type="file" id="postImageInput" name="post_images[]" accept="image/jpeg,image/png,image/gif,image/webp" class="d-none" multiple onchange="previewImages(this)">
                            </div>
                            <button type="submit" class="btn btn-accent btn-sm px-4 fw-semibold">
                                Post
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Feed Header and Filter Bar -->
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-rss-fill text-warning me-2"></i>Campus Feed
                    </h6>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Feed Filter">
                        <a href="<?= BASE_URL ?>/index.php?filter=newest" class="btn <?= (!isset($_GET['filter']) || $_GET['filter'] === 'newest') ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                            Newest
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?filter=oldest" class="btn <?= (isset($_GET['filter']) && $_GET['filter'] === 'oldest') ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                            Oldest
                        </a>
                    </div>
                </div>

                <!-- 2. Social Posts Feed -->
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                    <?php
                    // Compute author initials
                    $authorParts = explode(' ', trim($post['full_name']));
                    $authorInitials = count($authorParts) >= 2
                        ? strtoupper(mb_substr($authorParts[0], 0, 1) . mb_substr($authorParts[count($authorParts)-1], 0, 1))
                        : strtoupper(mb_substr($post['full_name'], 0, 2));

                    // Time formatting
                    $postTime = date('M j, Y \a\t g:i a', strtotime($post['created_at']));
                    $timeBadge = format_time_ago($post['created_at']);
                    ?>
                    <article class="post-card" id="post-<?= $post['id'] ?>">
                        <!-- Post Author Header -->
                        <div class="post-header">
                            <div class="post-author-box">
                                <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$post['user_id']; ?>" class="text-decoration-none flex-shrink-0" title="View <?= htmlspecialchars($post['full_name']); ?>'s profile">
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
                                    <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$post['user_id']; ?>" class="post-author-name" title="View <?= htmlspecialchars($post['full_name']); ?>'s profile"><?= htmlspecialchars($post['full_name']); ?></a>
                                    <div class="post-meta">
                                        <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$post['user_id']; ?>" class="text-muted text-decoration-none">@<?= htmlspecialchars($post['username']); ?></a> &bull; <?= htmlspecialchars($timeBadge); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Post Menu Dropdown -->
                            <?php if ($currentUserId && $currentUserId == $post['user_id']): ?>
                            <div class="dropdown">
                                <button type="button" class="btn post-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Post actions" title="Post actions">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end post-dropdown-menu">
                                    <li>
                                        <a class="dropdown-item action-item-edit" href="<?= BASE_URL ?>/index.php?action=edit_post&post_id=<?= $post['id'] ?>">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit post</span>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="<?= BASE_URL ?>/index.php?action=delete_post" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                            <button type="submit" class="dropdown-item action-item-delete">
                                                <i class="bi bi-trash"></i>
                                                <span>Delete post</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Post Text Content -->
                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($post['content'])); ?>
                        </div>

                        <!-- Optional Post Image(s) -->
                        <?php
                        $postImages = !empty($post['images']) ? $post['images'] : PostModel::parseImages($post['image'] ?? null);
                        $totalPostImages = count($postImages);
                        ?>
                        <?php if ($totalPostImages === 1): ?>
                            <?php
                            $singleImg = $postImages[0];
                            $postImgFile = __DIR__ . '/../../../public/uploads/posts/' . basename($singleImg);
                            $hasRealImage = file_exists($postImgFile);
                            $imgSize = $hasRealImage ? @getimagesize($postImgFile) : false;
                            $imgOrientationClass = ($imgSize && $imgSize[1] > $imgSize[0]) ? 'is-portrait' : 'is-landscape';
                            ?>
                            <?php if ($hasRealImage): ?>
                                <div class="post-image-container <?= $imgOrientationClass ?>">
                                    <a href="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($singleImg)) ?>" class="post-lightbox-trigger" data-gallery="post-<?= (int)$post['id'] ?>" title="View full image">
                                        <img src="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($singleImg)) ?>" alt="Post attachment" loading="lazy" onload="this.naturalHeight > this.naturalWidth ? (this.closest('.post-image-container')?.classList.add('is-portrait'), this.closest('.post-image-container')?.classList.remove('is-landscape')) : (this.closest('.post-image-container')?.classList.add('is-landscape'), this.closest('.post-image-container')?.classList.remove('is-portrait'));">
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="post-image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($totalPostImages > 1): ?>
                            <div class="post-gallery post-gallery-<?= min($totalPostImages, 4) ?> mb-3" data-post-id="<?= (int)$post['id'] ?>">
                                <?php foreach (array_slice($postImages, 0, 4) as $idx => $imgFile): ?>
                                    <?php
                                    $isLastQuadrant = ($idx === 3 && $totalPostImages > 4);
                                    $remainingCount = $totalPostImages - 4;
                                    ?>
                                    <div class="post-gallery-item">
                                        <a href="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($imgFile)) ?>" class="post-lightbox-trigger" data-gallery="post-<?= (int)$post['id'] ?>" title="View image <?= $idx + 1 ?>">
                                            <img src="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($imgFile)) ?>" alt="Post image <?= $idx + 1 ?>" loading="lazy">
                                            <?php if ($isLastQuadrant): ?>
                                                <div class="post-gallery-overlay">
                                                    <span>+<?= $remainingCount ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($totalPostImages > 4): ?>
                                    <?php foreach (array_slice($postImages, 4) as $extraIdx => $extraImg): ?>
                                        <a href="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($extraImg)) ?>" class="post-lightbox-trigger d-none" data-gallery="post-<?= (int)$post['id'] ?>" title="View image <?= 5 + $extraIdx ?>"></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Post Engagement Actions (Like / Comment) -->
                        <div class="post-footer-actions d-flex align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <form action="<?= BASE_URL ?>/index.php?action=like_post" method="POST" class="d-inline">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="post-action-btn <?= !empty($post['is_liked']) ? 'liked' : '' ?>" title="Like">
                                        <i class="bi <?= !empty($post['is_liked']) ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                        <span class="post-likes-count" data-post-id="<?= (int)$post['id']; ?>" title="View reactions"><?= (int)$post['likes_count']; ?></span>
                                    </button>
                                </form>

                                <button type="button" class="post-action-btn" onclick="toggleComments(<?= $post['id'] ?>)" title="Comments">
                                    <i class="bi bi-chat"></i>
                                    <span><?= (int)$post['comments_count']; ?></span>
                                </button>
                            </div>
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

                                    $cTimeBadge = format_time_ago($comment['created_at']);
                                    $cFullTime = date('M j, Y \a\t g:i a', strtotime($comment['created_at']));
                                    ?>
                                    <div class="comment-bubble" id="comment-<?= $comment['id'] ?>">
                                        <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$comment['user_id']; ?>" class="text-decoration-none flex-shrink-0" title="View <?= htmlspecialchars($comment['full_name']); ?>'s profile">
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
                                        </a>

                                        <div class="comment-content-box">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                <div>
                                                    <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$comment['user_id']; ?>" class="comment-author text-decoration-none" title="View <?= htmlspecialchars($comment['full_name']); ?>'s profile"><?= htmlspecialchars($comment['full_name']); ?></a>
                                                    <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$comment['user_id']; ?>" class="text-muted small text-decoration-none">@<?= htmlspecialchars($comment['username']); ?></a>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted" title="<?= htmlspecialchars($cFullTime); ?>">
                                                        <?= htmlspecialchars($cTimeBadge); ?>
                                                    </small>

                                                    <?php if ($currentUserId && (int)$currentUserId === (int)$comment['user_id']): ?>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn comment-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Comment options" title="Comment options">
                                                                <i class="bi bi-three-dots"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end post-dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item action-item-edit" href="<?= BASE_URL ?>/index.php?action=edit_comment&comment_id=<?= $comment['id'] ?>">
                                                                        <i class="bi bi-pencil-square"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <form action="<?= BASE_URL ?>/index.php?action=delete_comment" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this comment? This action cannot be undone.');">
                                                                        <?= csrf_field(); ?>
                                                                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                                        <button type="submit" class="dropdown-item action-item-delete">
                                                                            <i class="bi bi-trash"></i>
                                                                            <span>Delete</span>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
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
                                    <?= csrf_field(); ?>
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
                            <?php else: ?>
                                <div class="text-center py-2">
                                    <a href="<?= BASE_URL ?>/index.php?action=login" class="text-muted small text-decoration-none">
                                        Sign in to comment
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card-modern p-5 text-center text-muted">
                    <i class="bi bi-chat-square-text fs-1 mb-3 text-secondary"></i>
                    <h5>No posts yet</h5>
                    <p class="small mb-0">Be the first to share an update on CLIQ!</p>
                </div>
            <?php endif; ?>
            <?php endif; // End if (!$currentUserId) ?>
        </div>

        <!-- ==============================================
             RIGHT SIDEBAR: UPCOMING EVENTS
             ============================================== -->
        <div class="col-lg-5 col-xl-4 d-none d-lg-block">

            <!-- 1. Upcoming Events Card -->
            <div class="widget-card">
                <div class="widget-header d-flex justify-content-between align-items-center">
                    <h6 class="widget-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event text-primary"></i>
                        <span>Upcoming Events</span>
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($upcomingEvents)): ?>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none micro-text fw-semibold text-primary" data-bs-toggle="modal" data-bs-target="#modalAllUpcomingEvents">
                                <i class="bi bi-eye me-1"></i>View All
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($upcomingEvents)): ?>
                    <div class="events-list">
                        <?php foreach ($upcomingEvents as $evt): ?>
                            <?php
                                $evtTimestamp = !empty($evt['event_date']) ? strtotime($evt['event_date']) : time();
                                $evtMonth = strtoupper(date('M', $evtTimestamp));
                                $evtDay = date('d', $evtTimestamp);
                                $dateFormatted = date('M d', $evtTimestamp);
                                if (!empty($evt['event_time'])) {
                                    $dateFormatted .= ' &bull; ' . htmlspecialchars($evt['event_time']);
                                }

                                $todayDate = date('Y-m-d');
                                $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
                                $isToday = ($evt['event_date'] === $todayDate);
                                $isTomorrow = ($evt['event_date'] === $tomorrowDate);
                            ?>
                            <div class="event-item event-item-clickable" role="button" data-bs-toggle="modal" data-bs-target="#modalEventDetails<?= (int)$evt['id']; ?>" title="Click to view full event details">
                                <div class="event-date-box">
                                    <span class="event-month"><?= htmlspecialchars($evtMonth); ?></span>
                                    <span class="event-day"><?= htmlspecialchars($evtDay); ?></span>
                                </div>
                                <div class="event-info">
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <div class="event-title"><?= htmlspecialchars($evt['title']); ?></div>
                                        <?php if ($isToday): ?>
                                            <span class="badge bg-success micro-text">Today</span>
                                        <?php elseif ($isTomorrow): ?>
                                            <span class="badge bg-primary micro-text">Tomorrow</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="event-meta">
                                        <span class="text-dark fw-medium"><?= $dateFormatted; ?></span><br>
                                        <span class="text-secondary"><i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($evt['location']); ?></span>
                                        <div class="mt-1">
                                            <span class="badge bg-primary-subtle text-primary border-0 micro-text fw-medium py-1 px-2">
                                                <i class="bi bi-eye me-1"></i>Click for full details
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted opacity-50 ms-1"></i>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted small">
                        <i class="bi bi-calendar-check fs-2 d-block mb-2 text-primary opacity-50"></i>
                        <span class="fw-semibold d-block text-dark mb-1">No Upcoming Events</span>
                        No campus events are currently scheduled. Check back soon for student activities, seminars, and sportsfest announcements!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ==============================================
         SHARED CAMPUS EVENT MODALS (Accessible to both Mobile and Desktop)
         ============================================== -->
    <?php if (!empty($upcomingEvents)): ?>
        <?php foreach ($upcomingEvents as $evt): ?>
            <?php
                $evtTimestamp = !empty($evt['event_date']) ? strtotime($evt['event_date']) : time();
                $todayDate = date('Y-m-d');
                $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
                $isToday = ($evt['event_date'] === $todayDate);
                $isTomorrow = ($evt['event_date'] === $tomorrowDate);
            ?>
            <!-- EVENT FULL DETAILS MODAL -->
            <div class="modal fade ve-detail-modal" id="modalEventDetails<?= (int)$evt['id']; ?>" tabindex="-1" aria-labelledby="eventModalLabel<?= (int)$evt['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header border-bottom py-3 px-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="ve-header-badge">
                                    <i class="bi bi-calendar-event-fill"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title fw-bold mb-0 text-themed" id="eventModalLabel<?= (int)$evt['id']; ?>">
                                        <?= htmlspecialchars($evt['title']); ?>
                                    </h5>
                                    <span class="micro-text text-muted">Official SMCC Campus Event</span>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Status Banner -->
                            <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
                                <?php if ($isToday): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-1.5"><i class="bi bi-broadcast me-1"></i>Happening Today</span>
                                <?php elseif ($isTomorrow): ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-1.5"><i class="bi bi-clock-history me-1"></i>Happening Tomorrow</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-info-emphasis border rounded-pill px-3 py-1.5"><i class="bi bi-calendar-check me-1"></i>Upcoming Campus Event</span>
                                <?php endif; ?>
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-1.5"><i class="bi bi-people me-1"></i>Campus Community</span>
                            </div>

                            <!-- Info Cards Grid -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6 col-md-4">
                                    <div class="p-3 rounded-3 event-info-card border h-100">
                                        <div class="text-muted small fw-semibold mb-1"><i class="bi bi-calendar3 text-primary me-1"></i> Date</div>
                                        <div class="fw-bold text-themed"><?= date('l, F j, Y', $evtTimestamp); ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="p-3 rounded-3 event-info-card border h-100">
                                        <div class="text-muted small fw-semibold mb-1"><i class="bi bi-clock text-warning me-1"></i> Time</div>
                                        <div class="fw-bold text-themed"><?= !empty($evt['event_time']) ? htmlspecialchars($evt['event_time']) : 'Schedule to be announced'; ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="p-3 rounded-3 event-info-card border h-100">
                                        <div class="text-muted small fw-semibold mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Venue / Location</div>
                                        <div class="fw-bold text-themed text-break"><?= htmlspecialchars($evt['location']); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Full Description / Guidelines Section -->
                            <div class="p-4 rounded-3 event-desc-card border shadow-sm">
                                <h6 class="fw-bold mb-2 d-flex align-items-center gap-2 text-themed">
                                    <i class="bi bi-file-text-fill text-primary"></i>
                                    <span>Full Description & Event Details</span>
                                </h6>
                                <div class="text-secondary event-desc-text" style="line-height: 1.7; font-size: 0.95rem;">
                                    <?php if (!empty($evt['description'])): ?>
                                        <?= nl2br(htmlspecialchars($evt['description'])); ?>
                                    <?php else: ?>
                                        <em class="text-muted">No additional instructions or agenda provided for this event yet. Please check campus announcements or inquire at the Student Affairs Office.</em>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($evt['organizer_name'])): ?>
                                <div class="mt-3 text-muted micro-text text-end">
                                    <i class="bi bi-person-check me-1"></i>Published by <strong><?= htmlspecialchars($evt['organizer_name']); ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="backToAllEvents('modalEventDetails<?= (int)$evt['id']; ?>')">
                                    <i class="bi bi-arrow-left me-1"></i> All Events
                                </button>
                                <span class="micro-text text-muted d-none d-sm-inline">
                                    <i class="bi bi-shield-check text-success me-1"></i>Official CLIQ Announcement
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- MODAL: ALL UPCOMING CAMPUS EVENTS -->
        <div class="modal fade ve-modal" id="modalAllUpcomingEvents" tabindex="-1" aria-labelledby="allEventsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="ve-modal-header py-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ve-header-badge">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="ve-modal-title mb-0" id="allEventsModalLabel">SMCC Campus Events</h5>
                                    <span class="badge bg-primary-subtle text-primary border-0 rounded-pill px-2 py-0.5 micro-text fw-semibold">
                                        <?= count($upcomingEvents); ?> Upcoming
                                    </span>
                                </div>
                                <p class="ve-modal-subtitle mb-0">Full schedule of campus activities, deadlines, and community gatherings</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="<?= BASE_URL ?>/index.php?action=events" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1 micro-text d-none d-sm-inline-flex align-items-center gap-1" title="Open as full dedicated page">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span>Full Page</span>
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-3 p-md-4">
                        <div class="ve-events-list">
                            <?php foreach ($upcomingEvents as $evt): ?>
                                <?php
                                    $evtTimestamp = !empty($evt['event_date']) ? strtotime($evt['event_date']) : time();
                                    $evtMon  = strtoupper(date('M', $evtTimestamp));
                                    $evtDay  = date('j', $evtTimestamp);
                                    $evtDow  = date('D', $evtTimestamp);
                                    $todayDate = date('Y-m-d');
                                    $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
                                    $isToday = ($evt['event_date'] === $todayDate);
                                    $isTomorrow = ($evt['event_date'] === $tomorrowDate);
                                    $statusClass = $isToday ? 've-status-today' : ($isTomorrow ? 've-status-soon' : 've-status-upcoming');
                                    $statusLabel = $isToday ? 'Happening Today' : ($isTomorrow ? 'Tomorrow' : 'Upcoming Event');
                                ?>
                                <div class="ve-card" role="button" tabindex="0" onclick="openEventDetailFromAll(<?= (int)$evt['id']; ?>)">
                                    <div class="ve-date-badge">
                                        <span class="ve-date-month"><?= htmlspecialchars($evtMon); ?></span>
                                        <span class="ve-date-day"><?= htmlspecialchars($evtDay); ?></span>
                                        <span class="ve-date-dow"><?= htmlspecialchars($evtDow); ?></span>
                                    </div>
                                    <div class="ve-body">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <span class="ve-status-pill <?= $statusClass; ?>">
                                                <?php if ($isToday): ?><i class="bi bi-broadcast me-1"></i><?php endif; ?>
                                        </div>
                                        <?php if (!empty($evt['description'])): ?>
                                            <div class="ve-description text-truncate">
                                                <?= htmlspecialchars($evt['description']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="ve-footer-action d-sm-none">
                                            <span>View full schedule & details</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                        <span class="micro-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>All times are in Philippine Standard Time (PST)
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <a href="<?= BASE_URL ?>/index.php?action=events" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-calendar3 me-1"></i> Full Events Page
                            </a>
                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<script>
function toggleComments(postId) {
    const box = document.getElementById('comments-box-' + postId);
    if (!box) return;
    if (box.classList.contains('d-none')) {
        box.classList.remove('d-none');
        const input = box.querySelector('input[name="content"], textarea[name="content"]');
        if (input) setTimeout(() => input.focus(), 60);
    } else {
        box.classList.add('d-none');
    }
}

// Multi-Image Preview Handler with Crop/Adjust using Cropper.js
let postFilesDataTransfer = new DataTransfer();
let cropQueue = [];
let currentCropper = null;
let cropModalInstance = null;

function previewImages(input) {
    if (!input.files || input.files.length === 0) return;

    // Build the queue of new files to crop
    cropQueue = [];
    for (let i = 0; i < input.files.length; i++) {
        if (postFilesDataTransfer.items.length + cropQueue.length >= 10) {
            alert('You can upload a maximum of 10 photos per post.');
            break;
        }
        cropQueue.push(input.files[i]);
    }

    // Reset the raw input (we'll manage files via DataTransfer)
    input.value = '';

    // Start processing the queue
    processNextCropItem();
}

function processNextCropItem() {
    if (cropQueue.length === 0) {
        // All done - update the file input and render previews
        const input = document.getElementById('postImageInput');
        if (input) input.files = postFilesDataTransfer.files;
        renderSelectedPreviews();
        return;
    }

    const file = cropQueue.shift();
    openCropModal(file);
}

function openCropModal(file) {
    const modalEl = document.getElementById('imageCropModal');
    const cropperImg = document.getElementById('cropperImage');
    if (!modalEl || !cropperImg) {
        // Fallback: no modal available, add file as-is
        postFilesDataTransfer.items.add(file);
        processNextCropItem();
        return;
    }

    if (!cropModalInstance) {
        cropModalInstance = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
    }

    // Update modal title with queue info
    const label = document.getElementById('imageCropModalLabel');
    const remaining = cropQueue.length;
    if (label) {
        label.textContent = remaining > 0 ? `Adjust Photo (${remaining + 1} remaining)` : 'Adjust Photo';
    }

    // Load image into cropper
    const reader = new FileReader();
    reader.onload = function(e) {
        cropperImg.src = e.target.result;
        cropModalInstance.show();

        // Wait for modal fully shown to init cropper
        modalEl.addEventListener('shown.bs.modal', function onShown() {
            modalEl.removeEventListener('shown.bs.modal', onShown);

            if (currentCropper) {
                currentCropper.destroy();
                currentCropper = null;
            }

            currentCropper = new Cropper(cropperImg, {
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                background: true,
            });
        }, { once: true });
    };
    reader.readAsDataURL(file);

    // Store file reference for skip
    modalEl._currentFile = file;

    // Wire up buttons (remove old listeners by cloning)
    wireUpCropButtons(file);
}

function wireUpCropButtons(originalFile) {
    const applyBtn = document.getElementById('cropApplyBtn');
    const skipBtn = document.getElementById('cropSkipBtn');
    const cancelBtn = document.getElementById('cropCancelBtn');
    const rotateLeftBtn = document.getElementById('cropRotateLeft');
    const rotateRightBtn = document.getElementById('cropRotateRight');
    const flipHBtn = document.getElementById('cropFlipH');
    const resetBtn = document.getElementById('cropReset');

    // Clone buttons to remove old event listeners
    function replaceBtn(btn) {
        if (!btn) return btn;
        const clone = btn.cloneNode(true);
        btn.parentNode.replaceChild(clone, btn);
        return clone;
    }

    const newApply = replaceBtn(applyBtn);
    const newSkip = replaceBtn(skipBtn);
    const newCancel = replaceBtn(cancelBtn);
    const newRotateLeft = replaceBtn(rotateLeftBtn);
    const newRotateRight = replaceBtn(rotateRightBtn);
    const newFlipH = replaceBtn(flipHBtn);
    const newReset = replaceBtn(resetBtn);

    // Rotate/Flip/Reset
    if (newRotateLeft) newRotateLeft.addEventListener('click', () => { if (currentCropper) currentCropper.rotate(-90); });
    if (newRotateRight) newRotateRight.addEventListener('click', () => { if (currentCropper) currentCropper.rotate(90); });
    if (newFlipH) {
        let flipped = false;
        newFlipH.addEventListener('click', () => {
            if (currentCropper) {
                flipped = !flipped;
                currentCropper.scaleX(flipped ? -1 : 1);
            }
        });
    }
    if (newReset) newReset.addEventListener('click', () => { if (currentCropper) currentCropper.reset(); });

    // Apply: crop the image and add as new file
    if (newApply) {
        newApply.addEventListener('click', () => {
            if (!currentCropper) return;

            const canvas = currentCropper.getCroppedCanvas({
                maxWidth: 2048,
                maxHeight: 2048,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) {
                postFilesDataTransfer.items.add(originalFile);
                closeCropAndNext();
                return;
            }

            canvas.toBlob(function(blob) {
                if (!blob) {
                    postFilesDataTransfer.items.add(originalFile);
                } else {
                    // Create a new file from the cropped blob
                    const ext = originalFile.name.split('.').pop() || 'jpg';
                    const croppedFile = new File([blob], originalFile.name, {
                        type: blob.type || originalFile.type || 'image/jpeg',
                        lastModified: Date.now()
                    });
                    postFilesDataTransfer.items.add(croppedFile);
                }
                closeCropAndNext();
            }, originalFile.type || 'image/jpeg', 0.92);
        });
    }

    // Skip: add original file without cropping
    if (newSkip) {
        newSkip.addEventListener('click', () => {
            postFilesDataTransfer.items.add(originalFile);
            closeCropAndNext();
        });
    }

    // Cancel: discard this image entirely
    if (newCancel) {
        newCancel.addEventListener('click', () => {
            // Don't add the file, also clear the rest of the queue
            cropQueue = [];
            closeCropAndNext();
        });
    }
}

function closeCropAndNext() {
    if (currentCropper) {
        currentCropper.destroy();
        currentCropper = null;
    }
    if (cropModalInstance) {
        cropModalInstance.hide();
    }

    // Small delay to let modal close animation finish
    setTimeout(() => {
        processNextCropItem();
    }, 300);
}

function removeSelectedImage(index) {
    const newDt = new DataTransfer();
    const files = postFilesDataTransfer.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            newDt.items.add(files[i]);
        }
    }
    postFilesDataTransfer = newDt;
    const input = document.getElementById('postImageInput');
    if (input) input.files = postFilesDataTransfer.files;
    renderSelectedPreviews();
}

function clearAllSelectedImages() {
    postFilesDataTransfer = new DataTransfer();
    const input = document.getElementById('postImageInput');
    if (input) input.value = '';
    renderSelectedPreviews();
}

function renderSelectedPreviews() {
    const container = document.getElementById('imagePreviewContainer');
    const grid = document.getElementById('imagePreviewGrid');
    const badge = document.getElementById('imageCountBadge');
    if (!container || !grid) return;

    const files = postFilesDataTransfer.files;
    if (files.length === 0) {
        container.classList.add('d-none');
        grid.innerHTML = '';
        return;
    }

    container.classList.remove('d-none');
    grid.innerHTML = '';
    if (badge) {
        badge.textContent = files.length === 1 ? '1 photo selected' : `${files.length} photos selected`;
    }

    Array.from(files).forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const card = document.createElement('div');
            card.className = 'position-relative d-inline-block';
            card.style.margin = '4px';
            card.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${idx + 1}" style="width: 76px; height: 76px; object-fit: cover; border-radius: 8px;" class="border shadow-sm">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0" style="width: 20px; height: 20px; line-height: 18px; transform: translate(30%, -30%); font-size: 13px;" onclick="removeSelectedImage(${idx})" title="Remove photo">
                    &times;
                </button>
            `;
            grid.appendChild(card);
        };
        reader.readAsDataURL(file);
    });
}

// Expose clear handlers globally for AJAX composer reset
window.clearAllSelectedImages = clearAllSelectedImages;
window.clearSelectedImage = clearAllSelectedImages;

// Clean modal transition helper for Event Details without backdrop glitches
function openEventDetailFromAll(eventId) {
    const allModalEl = document.getElementById('modalAllUpcomingEvents');
    const targetModalEl = document.getElementById('modalEventDetails' + eventId);
    if (!targetModalEl) return;
    
    if (allModalEl && allModalEl.classList.contains('show')) {
        const bsAll = bootstrap.Modal.getInstance(allModalEl) || new bootstrap.Modal(allModalEl);
        const onHidden = function() {
            allModalEl.removeEventListener('hidden.bs.modal', onHidden);
            const bsTarget = bootstrap.Modal.getOrCreateInstance(targetModalEl);
            bsTarget.show();
        };
        allModalEl.addEventListener('hidden.bs.modal', onHidden);
        bsAll.hide();
    } else {
        const bsTarget = bootstrap.Modal.getOrCreateInstance(targetModalEl);
        bsTarget.show();
    }
}

function backToAllEvents(currentModalId) {
    const currentEl = document.getElementById(currentModalId);
    const allModalEl = document.getElementById('modalAllUpcomingEvents');
    if (currentEl) {
        const bsCurrent = bootstrap.Modal.getInstance(currentEl) || new bootstrap.Modal(currentEl);
        if (allModalEl) {
            const onHidden = function() {
                currentEl.removeEventListener('hidden.bs.modal', onHidden);
                const bsAll = bootstrap.Modal.getOrCreateInstance(allModalEl);
                bsAll.show();
            };
            currentEl.addEventListener('hidden.bs.modal', onHidden);
        }
        bsCurrent.hide();
    }
}
</script>
