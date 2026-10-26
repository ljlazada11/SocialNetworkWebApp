<?php
// app/views/friends/index.php
// Dedicated Friends & Connections Management View for CLIQ — Unified Single Card Design

$currentUserId = (int)($_SESSION['user_id'] ?? 0);
?>

<div class="friends-page-container mx-auto py-4" style="max-width: 820px;">

    <!-- Navigation Back to Feed Bar -->
    <div class="d-flex align-items-center justify-content-between mb-3 px-1 profile-nav-bar">
        <a href="<?= BASE_URL ?>/" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1 shadow-sm" title="Return to Feed">
            <i class="bi bi-arrow-left"></i>
            <span class="fw-semibold">Back to Feed</span>
        </a>
        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1.5 rounded-pill small">
            <i class="bi bi-people-fill me-1 text-primary"></i> Connections
        </span>
    </div>

    <!-- Unified Single Friends Card -->
    <div class="card-modern border-0 shadow-sm overflow-hidden p-0" id="friendsMainCard">

        <!-- 1. Card Header -->
        <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="friends-header-icon-box">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-themed mb-0">Friends & Connections</h5>
                    <p class="text-muted small mb-0">Manage your connections and network in the SMCC community.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="friends-stat-pill-group">
                    <div class="text-center">
                        <span class="fw-bold text-themed fs-6" id="headerFollowersCount"><?= (int)$followersCount ?></span>
                        <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">Followers</small>
                    </div>
                    <div class="border-end" style="height: 20px;"></div>
                    <div class="text-center">
                        <span class="fw-bold text-themed fs-6" id="headerFollowingCount"><?= (int)$followingCount ?></span>
                        <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">Following</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Integrated Navigation Tabs -->
        <div class="px-4 py-3 border-bottom friends-tabs-strip">
            <ul class="nav nav-pills nav-fill friends-nav-pills gap-2" id="friendsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab === 'followers' ? 'active' : '' ?> d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 fw-semibold" 
                       id="followers-tab" 
                       href="<?= BASE_URL ?>/index.php?action=friends&tab=followers" 
                       role="tab" 
                       aria-selected="<?= $activeTab === 'followers' ? 'true' : 'false' ?>">
                        <i class="bi bi-person-check-fill"></i>
                        <span>Followers</span>
                        <span class="badge rounded-pill bg-light text-themed border ms-1" id="tabFollowersBadge"><?= (int)$followersCount ?></span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab === 'following' ? 'active' : '' ?> d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 fw-semibold" 
                       id="following-tab" 
                       href="<?= BASE_URL ?>/index.php?action=friends&tab=following" 
                       role="tab" 
                       aria-selected="<?= $activeTab === 'following' ? 'true' : 'false' ?>">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Following</span>
                        <span class="badge rounded-pill bg-light text-themed border ms-1" id="tabFollowingBadge"><?= (int)$followingCount ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 3. Tab Content inside the SAME card -->
        <div class="tab-content" id="friendsTabContent">

            <!-- ==============================================
                 TAB 1: FOLLOWERS
                 ============================================== -->
            <?php if ($activeTab === 'followers'): ?>
                <div class="tab-pane fade show active" id="followersPane" role="tabpanel">
                    <?php if (!empty($followers)): ?>
                        <div class="list-group list-group-flush" id="followersList">
                            <?php foreach ($followers as $user): ?>
                                <?php
                                $uId = (int)$user['id'];
                                $uFullName = $user['full_name'] ?? 'User';
                                $uUsername = $user['username'] ?? '';
                                $uBio = $user['bio'] ?? '';
                                $isFollowing = (bool)($user['is_following'] ?? false);

                                $uAvatarPath = null;
                                if (!empty($user['profile_image'])) {
                                    $cPath = __DIR__ . '/../../../public/uploads/avatars/' . basename($user['profile_image']);
                                    if (file_exists($cPath)) {
                                        $uAvatarPath = BASE_URL . '/public/uploads/avatars/' . htmlspecialchars(basename($user['profile_image']));
                                    }
                                }

                                $parts = explode(' ', trim($uFullName));
                                $uInitials = count($parts) >= 2
                                    ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts)-1], 0, 1))
                                    : strtoupper(mb_substr($uFullName, 0, 2));
                                ?>
                                <div class="list-group-item px-4 py-3 d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap gap-3 friend-list-item" id="friend-card-<?= $uId ?>">
                                    <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                        <!-- Avatar Link -->
                                        <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $uId ?>" class="text-decoration-none flex-shrink-0" title="View <?= htmlspecialchars($uFullName); ?>'s profile">
                                            <?php if (!empty($uAvatarPath)): ?>
                                                <img src="<?= $uAvatarPath ?>" alt="<?= htmlspecialchars($uFullName); ?>" class="friend-card-avatar" onerror="this.onerror=null; this.outerHTML='<div class=\'friend-card-avatar-initials\'><?= htmlspecialchars($uInitials); ?></div>';">
                                            <?php else: ?>
                                                <div class="friend-card-avatar-initials">
                                                    <?= htmlspecialchars($uInitials); ?>
                                                </div>
                                            <?php endif; ?>
                                        </a>

                                        <!-- User Info -->
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $uId ?>" class="text-decoration-none">
                                                    <span class="fw-bold text-themed friend-card-name"><?= htmlspecialchars($uFullName); ?></span>
                                                </a>
                                                <span class="text-muted small">@<?= htmlspecialchars($uUsername); ?></span>
                                            </div>
                                            <?php if (!empty($uBio)): ?>
                                                <p class="text-secondary small mb-0 mt-1 text-truncate" style="max-width: 480px;" title="<?= htmlspecialchars($uBio); ?>">
                                                    <?= htmlspecialchars($uBio); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <!-- Direct Message Button -->
                                        <a href="<?= BASE_URL ?>/index.php?action=conversation&user_id=<?= $uId ?>" 
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm"
                                           title="Send a message to <?= htmlspecialchars($uFullName); ?>">
                                            <i class="bi bi-chat-dots-fill"></i>
                                            <span class="d-none d-md-inline">Message</span>
                                        </a>

                                        <!-- Follow / Following Form -->
                                        <form action="<?= BASE_URL ?>/index.php?action=follow_user" method="POST" class="follow-action-form m-0" data-user-id="<?= $uId ?>">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="user_id" value="<?= $uId ?>">
                                            <input type="hidden" name="redirect" value="friends">
                                            <button type="submit" 
                                                    class="btn btn-sm <?= $isFollowing ? 'btn-outline-secondary' : 'btn-navy' ?> rounded-pill px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1-5 friend-toggle-btn shadow-sm"
                                                    data-following="<?= $isFollowing ? '1' : '0' ?>">
                                                <i class="bi <?= $isFollowing ? 'bi-check-lg' : 'bi-person-plus-fill' ?>"></i>
                                                <span class="btn-text"><?= $isFollowing ? 'Following' : 'Follow back' ?></span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty Followers State -->
                        <div class="p-5 text-center">
                            <div class="friends-empty-icon mx-auto mb-3">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5 class="fw-bold text-themed mb-2">No followers yet</h5>
                            <p class="text-muted small mx-auto mb-3" style="max-width: 420px;">
                                When fellow SMCC students follow your profile, they will appear here. Share posts and connect with peers to grow your network!
                            </p>
                            <a href="<?= BASE_URL ?>/index.php?action=insights" class="btn btn-navy btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-compass me-1"></i> Discover People
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- ==============================================
                 TAB 2: FOLLOWING
                 ============================================== -->
            <?php if ($activeTab === 'following'): ?>
                <div class="tab-pane fade show active" id="followingPane" role="tabpanel">
                    <?php if (!empty($following)): ?>
                        <div class="list-group list-group-flush" id="followingList">
                            <?php foreach ($following as $user): ?>
                                <?php
                                $uId = (int)$user['id'];
                                $uFullName = $user['full_name'] ?? 'User';
                                $uUsername = $user['username'] ?? '';
                                $uBio = $user['bio'] ?? '';

                                $uAvatarPath = null;
                                if (!empty($user['profile_image'])) {
                                    $cPath = __DIR__ . '/../../../public/uploads/avatars/' . basename($user['profile_image']);
                                    if (file_exists($cPath)) {
                                        $uAvatarPath = BASE_URL . '/public/uploads/avatars/' . htmlspecialchars(basename($user['profile_image']));
                                    }
                                }

                                $parts = explode(' ', trim($uFullName));
                                $uInitials = count($parts) >= 2
                                    ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts)-1], 0, 1))
                                    : strtoupper(mb_substr($uFullName, 0, 2));
                                ?>
                                <div class="list-group-item px-4 py-3 d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap gap-3 friend-list-item" id="friend-card-<?= $uId ?>">
                                    <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                        <!-- Avatar Link -->
                                        <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $uId ?>" class="text-decoration-none flex-shrink-0" title="View <?= htmlspecialchars($uFullName); ?>'s profile">
                                            <?php if (!empty($uAvatarPath)): ?>
                                                <img src="<?= $uAvatarPath ?>" alt="<?= htmlspecialchars($uFullName); ?>" class="friend-card-avatar" onerror="this.onerror=null; this.outerHTML='<div class=\'friend-card-avatar-initials\'><?= htmlspecialchars($uInitials); ?></div>';">
                                            <?php else: ?>
                                                <div class="friend-card-avatar-initials">
                                                    <?= htmlspecialchars($uInitials); ?>
                                                </div>
                                            <?php endif; ?>
                                        </a>

                                        <!-- User Info -->
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= $uId ?>" class="text-decoration-none">
                                                    <span class="fw-bold text-themed friend-card-name"><?= htmlspecialchars($uFullName); ?></span>
                                                </a>
                                                <span class="text-muted small">@<?= htmlspecialchars($uUsername); ?></span>
                                            </div>
                                            <?php if (!empty($uBio)): ?>
                                                <p class="text-secondary small mb-0 mt-1 text-truncate" style="max-width: 480px;" title="<?= htmlspecialchars($uBio); ?>">
                                                    <?= htmlspecialchars($uBio); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <!-- Direct Message Button -->
                                        <a href="<?= BASE_URL ?>/index.php?action=conversation&user_id=<?= $uId ?>" 
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm"
                                           title="Send a message to <?= htmlspecialchars($uFullName); ?>">
                                            <i class="bi bi-chat-dots-fill"></i>
                                            <span class="d-none d-md-inline">Message</span>
                                        </a>

                                        <!-- Unfollow Button -->
                                        <form action="<?= BASE_URL ?>/index.php?action=follow_user" method="POST" class="follow-action-form m-0" data-user-id="<?= $uId ?>" data-context="following-tab">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="user_id" value="<?= $uId ?>">
                                            <input type="hidden" name="redirect" value="friends">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1-5 friend-toggle-btn shadow-sm"
                                                    data-following="1">
                                                <i class="bi bi-check-lg"></i>
                                                <span class="btn-text">Following</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty Following State -->
                        <div class="p-5 text-center">
                            <div class="friends-empty-icon mx-auto mb-3">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <h5 class="fw-bold text-themed mb-2">You aren't following anyone yet</h5>
                            <p class="text-muted small mx-auto mb-3" style="max-width: 420px;">
                                Follow other students, faculty, and campus members to see their updates and activities in your News Feed.
                            </p>
                            <a href="<?= BASE_URL ?>/index.php?action=insights" class="btn btn-navy btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-search me-1"></i> Find Students to Follow
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
/* Inline styling for Unified Friends Card */
.friends-header-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: linear-gradient(135deg, #07193d 0%, #1e3a8a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.friends-stat-pill-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.35rem 1rem;
    border-radius: 50rem;
    border: 1px solid var(--border-color, rgba(0, 0, 0, 0.08));
    background-color: var(--bg-card, #ffffff);
}

.friends-nav-pills .nav-link {
    color: var(--text-muted, #64748b);
    border: 1px solid transparent;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.friends-nav-pills .nav-link:hover {
    color: var(--text-primary, #07193d);
    background-color: rgba(7, 25, 61, 0.05);
}

.friends-nav-pills .nav-link.active {
    background: linear-gradient(135deg, #07193d 0%, #1e3a8a 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(7, 25, 61, 0.15);
}

.friends-nav-pills .nav-link.active .badge {
    background-color: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
}

.friend-list-item {
    transition: background-color 0.15s ease;
    border-color: var(--border-color, rgba(0, 0, 0, 0.06)) !important;
}

.friend-list-item:hover {
    background-color: var(--bg-hover, rgba(0, 0, 0, 0.02));
}

.friend-list-item:last-child {
    border-bottom: 0 !important;
}

.friends-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background-color: rgba(7, 25, 61, 0.06);
    color: #07193d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

/* Dark Theme Overrides */
[data-bs-theme="dark"] .friends-stat-pill-group,
.dark-theme .friends-stat-pill-group {
    background-color: #1e293b;
    border-color: rgba(255, 255, 255, 0.1);
}

[data-bs-theme="dark"] .friends-nav-pills .nav-link,
.dark-theme .friends-nav-pills .nav-link {
    color: #94a3b8;
}

[data-bs-theme="dark"] .friends-nav-pills .nav-link:hover,
.dark-theme .friends-nav-pills .nav-link:hover {
    color: #f1f5f9;
    background-color: rgba(255, 255, 255, 0.05);
}

[data-bs-theme="dark"] .friends-nav-pills .nav-link.active,
.dark-theme .friends-nav-pills .nav-link.active {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

[data-bs-theme="dark"] .friend-list-item,
.dark-theme .friend-list-item {
    background-color: transparent;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .friend-list-item:hover,
.dark-theme .friend-list-item:hover {
    background-color: rgba(255, 255, 255, 0.03);
}

.friends-tabs-strip {
    background-color: #f8fafc;
}

[data-bs-theme="dark"] .friends-tabs-strip,
.dark-theme .friends-tabs-strip {
    background-color: #0a173b !important;
}

.friends-nav-pills .nav-link:not(.active) .badge {
    background-color: rgba(0, 0, 0, 0.06) !important;
    color: var(--text-muted, #64748b) !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
}

[data-bs-theme="dark"] .friends-nav-pills .nav-link:not(.active) .badge,
.dark-theme .friends-nav-pills .nav-link:not(.active) .badge {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: #93c5fd !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

[data-bs-theme="dark"] .friends-empty-icon,
.dark-theme .friends-empty-icon {
    background-color: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Handle Follow / Unfollow via AJAX in Friends Page
    document.querySelectorAll('.follow-action-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            const context = this.getAttribute('data-context');
            const btn = this.querySelector('.friend-toggle-btn');
            const btnIcon = btn.querySelector('i');
            const btnText = btn.querySelector('.btn-text');
            const isFollowingCurrently = btn.getAttribute('data-following') === '1';

            btn.disabled = true;

            fetch(window.BASE_URL + '/index.php?action=follow_user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    user_id: userId,
                    csrf_token: csrfToken,
                    ajax: '1'
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    const newStatus = data.is_following;
                    btn.setAttribute('data-following', newStatus ? '1' : '0');

                    if (newStatus) {
                        btn.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1-5 friend-toggle-btn shadow-sm';
                        btnIcon.className = 'bi bi-check-lg';
                        btnText.textContent = 'Following';
                    } else {
                        btn.className = 'btn btn-sm btn-navy rounded-pill px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1-5 friend-toggle-btn shadow-sm';
                        btnIcon.className = 'bi bi-person-plus-fill';
                        btnText.textContent = 'Follow back';

                        // If on following tab and unfollowed, smoothly animate card removal
                        if (context === 'following-tab') {
                            const card = document.getElementById('friend-card-' + userId);
                            if (card) {
                                card.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'scale(0.97)';
                                setTimeout(() => {
                                    card.remove();
                                    const remaining = document.querySelectorAll('#followingList .friend-list-item').length;
                                    if (remaining === 0) {
                                        window.location.reload();
                                    }
                                }, 250);
                            }
                        }
                    }

                    // Update header counters if present
                    if (data.my_following_count !== undefined) {
                        const hFollowing = document.getElementById('headerFollowingCount');
                        const tFollowing = document.getElementById('tabFollowingBadge');
                        if (hFollowing) hFollowing.textContent = data.my_following_count;
                        if (tFollowing) tFollowing.textContent = data.my_following_count;
                    }
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(err => {
                btn.disabled = false;
                console.error('Follow toggle error:', err);
                form.submit(); // fallback
            });
        });
    });
});
</script>
