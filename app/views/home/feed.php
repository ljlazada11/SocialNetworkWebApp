<?php
// app/views/home/feed.php
// Main Social Feed and Dashboard view matching reference design

$currentUserId = $_SESSION['user_id'] ?? null;
?>

<div class="container-fluid p-0">

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
        <div class="col-lg-8 col-xl-8">

            <!-- 1. Post Creation Card -->
            <div class="composer-card">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="<?= BASE_URL ?>/index.php?action=create_post" method="POST" enctype="multipart/form-data">
                        <div class="composer-input-row">
                            <div class="user-avatar-sm" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                <?= htmlspecialchars($userInitials ?? 'U'); ?>
                            </div>
                            <textarea 
                                name="content" 
                                class="composer-input" 
                                rows="2" 
                                placeholder="What's happening on campus?"
                                required
                            ></textarea>
                        </div>

                        <!-- Image Preview Box (Hidden until selected) -->
                        <div id="imagePreviewContainer" class="mt-2 ps-5 d-none">
                            <div class="position-relative d-inline-block">
                                <img id="imagePreview" src="#" alt="Preview" style="max-height: 120px; border-radius: 8px;" class="border">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width: 20px; height: 20px; line-height: 18px;" onclick="clearSelectedImage()">
                                    &times;
                                </button>
                            </div>
                        </div>

                        <div class="composer-actions-row">
                            <div class="d-flex align-items-center gap-2">
                                <label for="postImageInput" class="composer-tool-btn mb-0" title="Upload Photo" style="cursor: pointer;">
                                    <i class="bi bi-image text-secondary"></i>
                                </label>
                                <input type="file" id="postImageInput" name="post_image" accept="image/*" class="d-none" onchange="previewImage(this)">
                            </div>
                            <button type="submit" class="btn btn-accent btn-sm px-4">
                                Post
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="composer-input-row align-items-center">
                        <div class="user-avatar-sm" style="width: 40px; height: 40px; font-size: 0.9rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <input type="text" class="composer-input" placeholder="Sign in to share what's happening on campus..." onclick="window.location.href='<?= BASE_URL ?>/index.php?action=login'" readonly style="cursor: pointer;">
                        <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-accent btn-sm px-4">
                            Sign In
                        </a>
                    </div>
                <?php endif; ?>
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
                    $diffHours = round((time() - strtotime($post['created_at'])) / 3600);
                    $timeBadge = ($diffHours < 24 && $diffHours >= 1) ? $diffHours . 'h ago' : ($diffHours < 1 ? 'Just now' : date('M j', strtotime($post['created_at'])));
                    ?>
                    <article class="post-card" id="post-<?= $post['id'] ?>">
                        <!-- Post Author Header -->
                        <div class="post-header">
                            <div class="post-author-box">
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

                                <div>
                                    <a href="#" class="post-author-name"><?= htmlspecialchars($post['full_name']); ?></a>
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
                                    <li><a class="dropdown-item small" href="#"><i class="bi bi-bookmark me-2"></i>Save post</a></li>
                                    <li><a class="dropdown-item small" href="#"><i class="bi bi-link-45deg me-2"></i>Copy link</a></li>
                                    <?php if ($currentUserId && $currentUserId == $post['user_id']): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item small text-danger" href="<?= BASE_URL ?>/index.php?action=delete_post&post_id=<?= $post['id'] ?>" onclick="return confirm('Are you sure you want to delete this post?');">
                                                <i class="bi bi-trash me-2"></i>Delete post
                                            </a>
                                        </li>
                                    <?php endif; ?>
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
                            <?php else: ?>
                                <div class="post-image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Post Engagement Actions (Like / Comment) -->
                        <div class="post-footer-actions">
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

                        <!-- Comments Section -->
                        <div class="comments-container" id="comments-box-<?= $post['id'] ?>">
                            <?php if (!empty($post['comments'])): ?>
                                <?php foreach ($post['comments'] as $comment): ?>
                                    <?php
                                    $cParts = explode(' ', trim($comment['full_name']));
                                    $cInitials = count($cParts) >= 2
                                        ? strtoupper(mb_substr($cParts[0], 0, 1) . mb_substr($cParts[count($cParts)-1], 0, 1))
                                        : strtoupper(mb_substr($comment['full_name'], 0, 2));
                                    ?>
                                    <div class="comment-bubble">
                                        <div class="comment-avatar">
                                            <?= htmlspecialchars($cInitials) ?>
                                        </div>
                                        <div class="comment-content-box">
                                            <span class="comment-author"><?= htmlspecialchars($comment['full_name']); ?></span>
                                            <?= htmlspecialchars($comment['content']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Add Comment Form -->
                            <?php if ($currentUserId): ?>
                                <form action="<?= BASE_URL ?>/index.php?action=add_comment" method="POST" class="comment-input-row">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input 
                                        type="text" 
                                        name="content" 
                                        class="form-control" 
                                        placeholder="Write a comment..." 
                                        required
                                    >
                                    <button type="submit" class="btn btn-sm btn-accent rounded-pill px-3">
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
                    <p class="small mb-0">Be the first to share an update on SMCC Connect!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ==============================================
             RIGHT SIDEBAR: UPCOMING EVENTS, TRENDING, WHO TO FOLLOW
             ============================================== -->
        <div class="col-lg-4 col-xl-4">

            <!-- 1. Upcoming Events Card -->
            <div class="widget-card">
                <div class="widget-header">
                    <h6 class="widget-title">Upcoming Events</h6>
                    <a href="#" class="widget-link">View all</a>
                </div>

                <div class="event-item">
                    <div class="event-date-box">
                        <span class="event-month">MAY</span>
                        <span class="event-day">24</span>
                    </div>
                    <div class="event-info">
                        <div class="event-title">Intramurals Opening</div>
                        <div class="event-meta">May 24 &bull; 8:00 AM<br>SMCC Gymnasium</div>
                    </div>
                    <i class="bi bi-calendar3 event-icon"></i>
                </div>

                <div class="event-item">
                    <div class="event-date-box">
                        <span class="event-month">MAY</span>
                        <span class="event-day">27</span>
                    </div>
                    <div class="event-info">
                        <div class="event-title">Finals Week Tips</div>
                        <div class="event-meta">May 27 &bull; 2:00 PM<br>Audio Visual Room</div>
                    </div>
                    <i class="bi bi-calendar3 event-icon"></i>
                </div>

                <div class="event-item">
                    <div class="event-date-box">
                        <span class="event-month">JUN</span>
                        <span class="event-day">03</span>
                    </div>
                    <div class="event-info">
                        <div class="event-title">Blood Donation Drive</div>
                        <div class="event-meta">June 3 &bull; 9:00 AM<br>SMCC Covered Court</div>
                    </div>
                    <i class="bi bi-calendar3 event-icon"></i>
                </div>
            </div>

            <!-- 2. Trending Topics Card -->
            <div class="widget-card">
                <div class="widget-header">
                    <h6 class="widget-title">Trending Topics</h6>
                </div>

                <div class="trending-item">
                    <a href="#" class="trending-tag"># FinalsWeek</a>
                    <span class="trending-count">128 posts</span>
                </div>
                <div class="trending-item">
                    <a href="#" class="trending-tag"># Intramurals2024</a>
                    <span class="trending-count">96 posts</span>
                </div>
                <div class="trending-item">
                    <a href="#" class="trending-tag"># SMCCPride</a>
                    <span class="trending-count">75 posts</span>
                </div>

                <div class="mt-2 pt-2 border-top">
                    <a href="#" class="widget-link small">View all</a>
                </div>
            </div>

            <!-- 3. Who to Follow Card -->
            <div class="widget-card">
                <div class="widget-header">
                    <h6 class="widget-title">Who to Follow</h6>
                    <a href="#" class="widget-link">View all</a>
                </div>

                <?php if (!empty($suggestedUsers)): ?>
                    <?php foreach ($suggestedUsers as $sUser): ?>
                        <?php
                        $sParts = explode(' ', trim($sUser['full_name']));
                        $sInitials = count($sParts) >= 2
                            ? strtoupper(mb_substr($sParts[0], 0, 1) . mb_substr($sParts[count($sParts)-1], 0, 1))
                            : strtoupper(mb_substr($sUser['full_name'], 0, 2));
                        ?>
                        <div class="follow-item">
                            <div class="follow-user">
                                <?php if (!empty($sUser['profile_image'])): ?>
                                    <img 
                                        src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars(basename($sUser['profile_image'])) ?>" 
                                        alt="<?= htmlspecialchars($sUser['full_name']); ?>" 
                                        class="follow-avatar"
                                        onerror="this.onerror=null; this.outerHTML='<div class=\'follow-avatar\'><?= htmlspecialchars($sInitials) ?></div>';"
                                    >
                                <?php else: ?>
                                    <div class="follow-avatar">
                                        <?= htmlspecialchars($sInitials) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="follow-info">
                                    <span class="follow-name"><?= htmlspecialchars($sUser['full_name']); ?></span>
                                    <span class="follow-username">@<?= htmlspecialchars($sUser['username']); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-follow" type="button" onclick="this.textContent = (this.textContent === 'Follow' ? 'Following' : 'Follow')">Follow</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Campus accounts fallback matching reference mockup style -->
                    <div class="follow-item">
                        <div class="follow-user">
                            <div class="follow-avatar bg-warning text-dark">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div class="follow-info">
                                <span class="follow-name">SMCC Official</span>
                                <span class="follow-username">@smcc_official</span>
                            </div>
                        </div>
                        <button class="btn btn-follow" type="button">Follow</button>
                    </div>

                    <div class="follow-item">
                        <div class="follow-user">
                            <div class="follow-avatar bg-primary text-white">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <div class="follow-info">
                                <span class="follow-name">The Torch</span>
                                <span class="follow-username">@thetorchsmcc</span>
                            </div>
                        </div>
                        <button class="btn btn-follow" type="button">Follow</button>
                    </div>

                    <div class="follow-item">
                        <div class="follow-user">
                            <div class="follow-avatar bg-secondary text-white">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="follow-info">
                                <span class="follow-name">SMCC Library</span>
                                <span class="follow-username">@smcclibrary</span>
                            </div>
                        </div>
                        <button class="btn btn-follow" type="button">Follow</button>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
function toggleComments(postId) {
    const box = document.getElementById('comments-box-' + postId);
    if (box) {
        box.classList.toggle('d-none');
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            if (preview && container) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearSelectedImage() {
    const input = document.getElementById('postImageInput');
    const container = document.getElementById('imagePreviewContainer');
    if (input) input.value = '';
    if (container) container.classList.add('d-none');
}
</script>
