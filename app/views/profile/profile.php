<?php
// app/views/profile/profile.php
// User Profile View - Redesigned with Modern Dashboard Card Layout

require_once __DIR__ . '/../layouts/header.php';

// Prepare profile image URL or fallback placeholder
$avatarUrl = null;
if (!empty($user['profile_image'])) {
    $uploadedPath = __DIR__ . '/../../../public/uploads/avatars/' . basename($user['profile_image']);
    if (file_exists($uploadedPath)) {
        $avatarUrl = BASE_URL . '/public/uploads/avatars/' . htmlspecialchars(basename($user['profile_image']));
    }
    // If file does not exist on disk, leave $avatarUrl = null → initials fallback will show
}

// Prepare cover photo URL or fallback placeholder
$coverUrl = null;
if (!empty($user['cover_photo'])) {
    $uploadedCoverPath = __DIR__ . '/../../../public/uploads/covers/' . basename($user['cover_photo']);
    if (file_exists($uploadedCoverPath)) {
        $coverUrl = BASE_URL . '/public/uploads/covers/' . htmlspecialchars(basename($user['cover_photo']));
    }
    // If file does not exist on disk, leave $coverUrl = null → gradient background fallback
}

$formattedDate = !empty($user['created_at']) 
    ? date('F j, Y', strtotime($user['created_at'])) 
    : 'N/A';

$userInitials = 'U';
if (!empty($user['full_name'])) {
    $parts = explode(' ', trim($user['full_name']));
    if (count($parts) >= 2) {
        $userInitials = strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts)-1], 0, 1));
    } else {
        $userInitials = strtoupper(mb_substr($user['full_name'], 0, 2));
    }
}
?>

<div class="feed-layout-container mx-auto">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-8">

        <!-- Flash Success Message -->
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Navigation Back Bar -->
        <div class="d-flex align-items-center mb-3 px-1 profile-nav-bar">
            <a href="<?= BASE_URL ?>/" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1 shadow-sm" title="Return to Feed">
                <i class="bi bi-arrow-left"></i>
                <span class="fw-semibold">Back to Feed</span>
            </a>
        </div>

        <!-- Profile Card -->
        <div class="card-modern overflow-hidden mb-4">
            <!-- Header Banner / Cover Photo -->
            <div style="<?= $coverUrl ? "background: url('" . $coverUrl . "') center center / cover no-repeat;" : "background: linear-gradient(135deg, #07193d 0%, #1e3a8a 100%);" ?> height: 200px;" class="position-relative">
                <?php if (!empty($isOwnProfile)): ?>
                    <div class="profile-cover-actions">
                        <a href="<?= BASE_URL ?>/index.php?action=edit_profile" class="btn btn-sm btn-cover-photo-action shadow" title="Edit Cover Photo">
                            <i class="bi bi-camera-fill me-1"></i> Edit Cover<span class="d-none d-sm-inline"> Photo</span>
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?action=edit_profile" class="btn btn-sm btn-cover-photo-action shadow" title="Edit Profile">
                            <i class="bi bi-pencil-square me-1"></i> Edit<span class="d-none d-sm-inline"> Profile</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-4 p-md-5 pt-0 text-center">
                <!-- Avatar -->
                <div class="d-inline-block position-relative mb-3" style="margin-top: -65px;">
                    <?php if ($avatarUrl): ?>
                        <img 
                            src="<?= $avatarUrl ?>" 
                            alt="<?= htmlspecialchars($user['full_name']); ?>" 
                            class="rounded-circle border border-4 border-white shadow bg-white"
                            style="width: 120px; height: 120px; object-fit: cover;"
                            onerror="this.onerror=null; this.outerHTML='<div class=\'rounded-circle border border-4 border-white shadow text-white d-inline-flex align-items-center justify-content-center fs-2 fw-bold\' style=\'width: 120px; height: 120px; background-color: #07193d;\'><?= htmlspecialchars($userInitials) ?></div>';"
                        >
                    <?php else: ?>
                        <div 
                            class="rounded-circle border border-4 border-white shadow text-white d-inline-flex align-items-center justify-content-center fs-2 fw-bold"
                            style="width: 120px; height: 120px; background-color: #07193d;"
                        >
                            <?= htmlspecialchars($userInitials); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- User Names -->
                <h2 class="fw-bold mb-1 text-themed"><?= htmlspecialchars($user['full_name']); ?></h2>
                <p class="text-muted fw-medium mb-3">@<?= htmlspecialchars($user['username']); ?></p>

                <!-- Follower / Following Stats Badges -->
                <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
                    <button type="button" class="btn p-0 border-0 bg-transparent text-center follow-stat-btn" onclick="openFollowListModal('followers', <?= (int)$user['id'] ?>)" title="Click to view Followers" style="cursor: pointer; min-width: 75px; text-decoration: none;">
                        <div class="fs-5 fw-bold text-themed follow-stat-num" id="profileFollowersCount"><?= (int)($followersCount ?? 0) ?></div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Followers</small>
                    </button>
                    <div class="border-end" style="height: 24px;"></div>
                    <button type="button" class="btn p-0 border-0 bg-transparent text-center follow-stat-btn" onclick="openFollowListModal('following', <?= (int)$user['id'] ?>)" title="Click to view Following" style="cursor: pointer; min-width: 75px; text-decoration: none;">
                        <div class="fs-5 fw-bold text-themed follow-stat-num" id="profileFollowingCount"><?= (int)($followingCount ?? 0) ?></div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Following</small>
                    </button>
                    <div class="border-end" style="height: 24px;"></div>
                    <div class="text-center" style="min-width: 75px;">
                        <div class="fs-5 fw-bold text-themed"><?= !empty($posts) ? count($posts) : 0; ?></div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Posts</small>
                    </div>
                </div>

                <!-- Profile Action Buttons: Follow & Message (other users only) -->
                <?php if (empty($isOwnProfile)): ?>
                <div class="profile-actions-group mb-4">
                        <!-- Follow / Unfollow AJAX Button -->
                        <form action="<?= BASE_URL ?>/index.php?action=follow_user" method="POST" id="profileFollowForm">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="user_id" value="<?= (int)$user['id']; ?>">
                            <button type="submit" id="btnProfileFollow" class="btn <?= !empty($isFollowing) ? 'btn-outline-secondary' : (!empty($isFollowedBy) ? 'btn-primary' : 'btn-navy') ?> fw-semibold rounded-pill profile-action-btn" data-is-followed-by="<?= !empty($isFollowedBy) ? '1' : '0' ?>">
                                <i class="bi <?= !empty($isFollowing) ? 'bi-person-check-fill' : (!empty($isFollowedBy) ? 'bi-arrow-repeat' : 'bi-person-plus-fill') ?> me-2"></i>
                                <span><?= !empty($isFollowing) ? 'Following' : (!empty($isFollowedBy) ? 'Follow Back' : 'Follow') ?></span>
                            </button>
                        </form>

                        <!-- Message Button: opens conversation with this profile owner -->
                        <a href="<?= BASE_URL ?>/index.php?action=conversation&user_id=<?= (int)$user['id']; ?>"
                           class="btn btn-outline-primary rounded-pill fw-semibold profile-action-btn"
                           title="Send a message to <?= htmlspecialchars($user['full_name']); ?>">
                            <i class="bi bi-chat-dots-fill me-2"></i> <span>Message</span>
                        </a>
                </div>
                <?php endif; ?>

                <!-- Bio -->
                <div class="col-md-9 col-lg-8 mx-auto mb-4 text-center">
                    <?php if (!empty($user['bio'])): ?>
                        <p class="text-secondary mb-0" style="line-height: 1.6; font-size: 0.95rem;">
                            <?= nl2br(htmlspecialchars($user['bio'])); ?>
                        </p>
                    <?php else: ?>
                        <p class="text-muted fst-italic mb-0 small">
                            <?= !empty($isOwnProfile) ? 'No bio provided yet. Tell others about yourself by updating your profile!' : 'No bio provided.'; ?>
                        </p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <?php
        // Extract all photos from the profile posts
        $allProfilePhotos = [];
        if (!empty($posts)) {
            foreach ($posts as $p) {
                $pImgs = !empty($p['images']) ? $p['images'] : PostModel::parseImages($p['image'] ?? null);
                if (!empty($pImgs)) {
                    foreach ($pImgs as $imgIdx => $imgName) {
                        $allProfilePhotos[] = [
                            'image' => $imgName,
                            'post_id' => (int)$p['id'],
                            'post_content' => $p['content'],
                            'created_at' => $p['created_at'],
                            'likes_count' => (int)($p['likes_count'] ?? 0),
                            'comments_count' => (int)($p['comments_count'] ?? 0),
                            'index_in_post' => $imgIdx + 1
                        ];
                    }
                }
            }
        }
        $totalPhotosCount = count($allProfilePhotos);
        ?>

        <!-- Profile Navigation Tabs: All Posts & Photos -->
        <div class="card-modern p-2 mb-4 profile-tabs-card">
            <ul class="nav nav-pills nav-fill profile-nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" 
                            id="tab-posts-btn" 
                            data-bs-toggle="pill" 
                            data-bs-target="#tab-posts-pane" 
                            type="button" 
                            role="tab" 
                            aria-controls="tab-posts-pane" 
                            aria-selected="true">
                        <i class="bi bi-file-earmark-post"></i>
                        <span>All Posts</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" 
                            id="tab-photos-btn" 
                            data-bs-toggle="pill" 
                            data-bs-target="#tab-photos-pane" 
                            type="button" 
                            role="tab" 
                            aria-controls="tab-photos-pane" 
                            aria-selected="false">
                        <i class="bi bi-images"></i>
                        <span>Photos</span>
                    </button>
                </li>

            </ul>
        </div>

        <div class="tab-content" id="profileTabsContent">
            <!-- ==============================================
                 PANE 1: USER'S POSTS SECTION
                 ============================================== -->
            <div class="tab-pane fade show active" id="tab-posts-pane" role="tabpanel" aria-labelledby="tab-posts-btn">
                <div class="user-posts-section mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                <h4 class="fw-bold mb-0 text-themed d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-post text-primary"></i>
                    <span><?= !empty($isOwnProfile) ? 'My Posts' : 'Posts'; ?></span>
                </h4>
            </div>

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
                            <?php if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$post['user_id']): ?>
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
                                <div class="post-image-container <?= $imgOrientationClass ?> mb-3">
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

                                                    <?php if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$comment['user_id']): ?>
                                                        <a href="<?= BASE_URL ?>/index.php?action=edit_comment&comment_id=<?= $comment['id'] ?>" class="comment-btn-action text-primary" title="Edit comment">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>
                                                        <form action="<?= BASE_URL ?>/index.php?action=delete_comment" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this comment? This action cannot be undone.');">
                                                            <?= csrf_field(); ?>
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
                            <?php if (!empty($_SESSION['user_id'])): ?>
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
                <!-- Empty State -->
                <div class="card-modern p-5 text-center text-muted">
                    <i class="bi bi-chat-square-text fs-1 mb-3 text-secondary"></i>
                    <h5 class="fw-semibold text-dark">No posts yet.</h5>
                    <p class="small mb-0 text-muted">
                        <?= !empty($isOwnProfile) ? "You haven't published any posts yet. Head over to the feed to share what's happening!" : "This user hasn't shared any posts yet."; ?>
                    </p>
                </div>
            <?php endif; ?>
                </div>
            </div> <!-- End tab-posts-pane -->

            <!-- ==============================================
                 PANE 2: USER'S PHOTOS SECTION
                 ============================================== -->
            <div class="tab-pane fade" id="tab-photos-pane" role="tabpanel" aria-labelledby="tab-photos-btn">
                <div class="card-modern p-4 mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-themed mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-images text-primary"></i>
                                <span><?= !empty($isOwnProfile) ? 'Photos' : htmlspecialchars($user['full_name']) . "'s Photos"; ?></span>
                            </h5>
                            <p class="text-muted small mb-0"><?= !empty($isOwnProfile) ? 'All photos you have shared in your posts' : 'All photos shared by ' . htmlspecialchars($user['full_name']); ?></p>
                        </div>
                        
                    </div>

                    <?php if ($totalPhotosCount > 0): ?>
                        <div class="album-photos-grid">
                            <?php foreach ($allProfilePhotos as $photo): ?>
                                <a href="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($photo['image'])) ?>" 
                                   class="album-photo-tile post-lightbox-trigger text-decoration-none" 
                                   data-gallery="profile-photos" 
                                   title="<?= htmlspecialchars(mb_substr($photo['post_content'], 0, 60)) ?>">
                                    <img src="<?= BASE_URL ?>/public/uploads/posts/<?= htmlspecialchars(basename($photo['image'])) ?>" alt="Photo" loading="lazy">
                                    <div class="album-photo-overlay">
                                        <span><i class="bi bi-heart-fill me-1 text-danger"></i> <?= $photo['likes_count'] ?></span>
                                        <span><i class="bi bi-chat-fill me-1 text-white"></i> <?= $photo['comments_count'] ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-5 bg-light rounded-3 text-center border text-muted">
                            <i class="bi bi-image fs-1 mb-2 d-block opacity-50"></i>
                            <h6 class="fw-semibold text-themed mb-1">No photos yet</h6>
                            <span class="small"><?= !empty($isOwnProfile) ? 'Photos you share in your posts will appear here.' : 'This user has not shared any photos yet.'; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div> <!-- End tab-photos-pane -->


        </div> <!-- End tab-content -->

        </div>
    </div>
</div>

<style>
.follow-stat-btn {
    transition: transform 0.15s ease, opacity 0.15s ease;
}
.follow-stat-btn:hover {
    transform: translateY(-2px);
    opacity: 0.85;
}
.follow-stat-btn:hover .follow-stat-num {
    text-decoration: underline;
    color: var(--accent-yellow-hover, #0d6efd) !important;
}
</style>



<!-- Followers & Following Modal -->
<div class="modal fade" id="followListModal" tabindex="-1" aria-labelledby="followListModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 34px; height: 34px;">
                        <i class="bi bi-people-fill" id="followListModalIcon"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-themed mb-0" id="followListModalTitle">Followers</h6>
                        <small class="text-muted" id="followListModalSubtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="followListModalBody" style="max-height: 420px; min-height: 160px;">
                <!-- Dynamically loaded users -->
            </div>
        </div>
    </div>
</div>

<script>
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function openFollowListModal(type, userId) {
    const modalEl = document.getElementById('followListModal');
    if (!modalEl) return;

    const modalTitle = document.getElementById('followListModalTitle');
    const modalSubtitle = document.getElementById('followListModalSubtitle');
    const modalIcon = document.getElementById('followListModalIcon');
    const modalBody = document.getElementById('followListModalBody');

    const isFollowing = (type === 'following');
    if (modalTitle) modalTitle.textContent = isFollowing ? 'Following' : 'Followers';
    if (modalSubtitle) modalSubtitle.textContent = isFollowing ? 'People followed by this user' : 'People following this user';
    if (modalIcon) modalIcon.className = isFollowing ? 'bi bi-person-check-fill' : 'bi bi-people-fill';

    if (modalBody) {
        modalBody.innerHTML = `
            <div class="p-5 text-center text-muted">
                <div class="spinner-border text-primary spinner-border-sm mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="small">Loading ${isFollowing ? 'following' : 'followers'}...</div>
            </div>
        `;
    }

    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInstance.show();

    fetch(window.BASE_URL + '/index.php?action=get_follow_list&user_id=' + encodeURIComponent(userId) + '&type=' + encodeURIComponent(type), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!modalBody) return;

        if (data.is_private) {
            modalBody.innerHTML = `
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-lock-fill fs-1 text-warning mb-2 d-block"></i>
                    <h6 class="fw-bold text-themed mb-1">Private List</h6>
                    <p class="small text-muted mb-0">${escapeHtml(data.message || 'This account has made their follower and following list private.')}</p>
                </div>
            `;
            return;
        }

        if (!data.success) {
            modalBody.innerHTML = `
                <div class="p-5 text-center text-danger">
                    <i class="bi bi-exclamation-triangle fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">Could not load list</h6>
                    <p class="small mb-0">${escapeHtml(data.error || 'Please try again later.')}</p>
                </div>
            `;
            return;
        }

        const users = data.users || [];
        if (users.length === 0) {
            modalBody.innerHTML = `
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-people fs-1 opacity-50 mb-2 d-block"></i>
                    <h6 class="fw-bold text-themed mb-1">No ${isFollowing ? 'following' : 'followers'} yet</h6>
                    <p class="small text-muted mb-0">${isFollowing ? 'Not following anyone yet.' : 'No one is following this user yet.'}</p>
                </div>
            `;
            return;
        }

        let html = '<div class="list-group list-group-flush">';
        users.forEach(u => {
            const profileUrl = window.BASE_URL + '/index.php?action=profile&user_id=' + encodeURIComponent(u.id);
            const avatarHtml = u.profile_image
                ? `<img src="${window.BASE_URL}/public/uploads/avatars/${encodeURIComponent(u.profile_image.split('/').pop())}" alt="${escapeHtml(u.full_name)}" class="rounded-circle" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<div class=\\'rounded-circle d-flex align-items-center justify-content-center text-white fw-bold\\' style=\\'width: 44px; height: 44px; background-color: #07193d; font-size: 0.9rem;\\'>${escapeHtml(u.initials)}</div>';">`
                : `<div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 44px; height: 44px; background-color: #07193d; font-size: 0.9rem;">${escapeHtml(u.initials)}</div>`;

            html += `
                <div class="list-group-item list-group-item-action p-3 d-flex align-items-center justify-content-between gap-3">
                    <a href="${profileUrl}" class="d-flex align-items-center gap-3 text-decoration-none text-reset flex-grow-1 min-w-0" title="View ${escapeHtml(u.full_name)}'s profile">
                        <div class="flex-shrink-0">
                            ${avatarHtml}
                        </div>
                        <div class="min-w-0">
                            <h6 class="fw-semibold text-themed mb-0 text-truncate">${escapeHtml(u.full_name)}</h6>
                            <span class="text-muted small text-truncate d-block">@${escapeHtml(u.username)}</span>
                        </div>
                    </a>
                    ${!u.is_self ? `
                        <form action="${window.BASE_URL}/index.php?action=follow_user" method="POST" class="m-0 d-inline flex-shrink-0" onsubmit="handleFollowInModal(event, this)">
                            <input type="hidden" name="user_id" value="${u.id}">
                            <button type="submit" class="btn btn-sm ${u.is_following ? 'btn-outline-secondary' : (u.is_followed_by ? 'btn-primary' : 'btn-navy')} rounded-pill px-3 fw-semibold" style="font-size: 0.78rem;" data-is-followed-by="${u.is_followed_by ? '1' : '0'}">
                                <i class="bi ${u.is_following ? 'bi-person-check-fill' : (u.is_followed_by ? 'bi-arrow-repeat' : 'bi-person-plus-fill')} me-1"></i>
                                <span>${u.is_following ? 'Following' : (u.is_followed_by ? 'Follow Back' : 'Follow')}</span>
                            </button>
                        </form>
                    ` : ''}
                </div>
            `;
        });
        html += '</div>';

        modalBody.innerHTML = html;
    })
    .catch(err => {
        if (!modalBody) return;
        modalBody.innerHTML = `
            <div class="p-5 text-center text-danger">
                <i class="bi bi-exclamation-triangle fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold mb-1">Failed to load list</h6>
                <p class="small mb-0">Please check your connection and try again.</p>
            </div>
        `;
    });
}

function handleFollowInModal(e, form) {
    e.preventDefault();
    e.stopPropagation();
    const btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;

    const formData = new FormData(form);
    formData.append('ajax', '1');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    if (csrfToken && !formData.has('csrf_token')) {
        formData.append('csrf_token', csrfToken);
    }

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (btn) btn.disabled = false;
        if (data && data.success) {
            const icon = btn.querySelector('i');
            const textSpan = btn.querySelector('span');
            const isFollowedBy = (typeof data.is_followed_by !== 'undefined') ? Boolean(data.is_followed_by) : (btn.getAttribute('data-is-followed-by') === '1');
            btn.setAttribute('data-is-followed-by', isFollowedBy ? '1' : '0');

            if (data.is_following) {
                btn.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold';
                if (icon) icon.className = 'bi bi-person-check-fill me-1';
                if (textSpan) textSpan.textContent = 'Following';
            } else {
                if (isFollowedBy) {
                    btn.className = 'btn btn-sm btn-primary rounded-pill px-3 fw-semibold';
                    if (icon) icon.className = 'bi bi-arrow-repeat me-1';
                    if (textSpan) textSpan.textContent = 'Follow Back';
                } else {
                    btn.className = 'btn btn-sm btn-navy rounded-pill px-3 fw-semibold';
                    if (icon) icon.className = 'bi bi-person-plus-fill me-1';
                    if (textSpan) textSpan.textContent = 'Follow';
                }
            }
        }
    })
    .catch(() => {
        if (btn) btn.disabled = false;
    });
}

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

// Synchronize profile tabs with URL hash (#photos or #posts)
document.addEventListener('DOMContentLoaded', function() {
    function checkHashTab() {
        if (window.location.hash === '#photos' || window.location.hash === '#album' || window.location.hash === '#albums') {
            const photosTabBtn = document.getElementById('tab-photos-btn');
            if (photosTabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                bootstrap.Tab.getOrCreateInstance(photosTabBtn).show();
            }
        } else if (window.location.hash === '#posts') {
            const postsTabBtn = document.getElementById('tab-posts-btn');
            if (postsTabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                bootstrap.Tab.getOrCreateInstance(postsTabBtn).show();
            }
        }
    }
    checkHashTab();
    window.addEventListener('hashchange', checkHashTab);

    // Update hash when tabs are clicked
    const photosBtn = document.getElementById('tab-photos-btn');
    if (photosBtn) {
        photosBtn.addEventListener('shown.bs.tab', function() {
            history.replaceState(null, null, '#photos');
        });
    }
    const postsBtn = document.getElementById('tab-posts-btn');
    if (postsBtn) {
        postsBtn.addEventListener('shown.bs.tab', function() {
            history.replaceState(null, null, '#posts');
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
