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
    } else {
        $avatarUrl = BASE_URL . '/public/uploads/avatars/' . htmlspecialchars($user['profile_image']);
    }
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

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">

        <!-- Flash Success Message -->
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="card-modern overflow-hidden mb-4">
            <!-- Header Banner Background -->
            <div style="background: linear-gradient(135deg, #07193d 0%, #1e3a8a 100%); height: 160px;" class="position-relative">
                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge" style="background-color: var(--accent-yellow); color: #07193d; font-weight: 600;">
                        <i class="bi bi-patch-check-fill me-1"></i> Verified Profile
                    </span>
                </div>
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
                <h2 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($user['full_name']); ?></h2>
                <p class="text-muted fw-medium mb-3">@<?= htmlspecialchars($user['username']); ?></p>

                <!-- Bio -->
                <div class="col-md-9 mx-auto mb-4">
                    <?php if (!empty($user['bio'])): ?>
                        <div class="p-3 bg-light rounded-3 text-start border border-light-subtle">
                            <i class="bi bi-quote text-warning me-1 fs-5 align-middle"></i>
                            <span class="text-secondary"><?= nl2br(htmlspecialchars($user['bio'])); ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-muted fst-italic mb-0 small">
                            No bio provided yet. Tell others about yourself by updating your profile!
                        </p>
                    <?php endif; ?>
                </div>

                <hr class="my-4" style="opacity: 0.1;">

                <!-- Profile Meta Information -->
                <div class="row g-3 justify-content-center text-start mb-4">
                    <div class="col-sm-6 col-md-5">
                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2 fs-4 d-flex align-items-center justify-content-center" style="background-color: var(--accent-yellow-light); color: var(--accent-yellow-hover); width: 42px; height: 42px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Username</small>
                                <span class="fw-bold text-dark">@<?= htmlspecialchars($user['username']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-5">
                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2 fs-4 d-flex align-items-center justify-content-center" style="background-color: #e0f2fe; color: #0284c7; width: 42px; height: 42px;">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Member Since</small>
                                <span class="fw-bold text-dark"><?= $formattedDate; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= BASE_URL ?>/index.php?action=edit_profile" class="btn btn-accent px-4">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </a>
                    <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary px-4 rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Back to Feed
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
