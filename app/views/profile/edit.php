<?php
// app/views/profile/edit.php
// Edit Profile View - Redesigned with Modern Dashboard Form Styling

require_once __DIR__ . '/../layouts/header.php';

// Prepare profile image URL or fallback
$avatarUrl = null;
if (!empty($user['profile_image'])) {
    $uploadedPath = __DIR__ . '/../../../public/uploads/avatars/' . basename($user['profile_image']);
    if (file_exists($uploadedPath)) {
        $avatarUrl = BASE_URL . '/public/uploads/avatars/' . htmlspecialchars(basename($user['profile_image']));
    } else {
        $avatarUrl = BASE_URL . '/public/uploads/avatars/' . htmlspecialchars($user['profile_image']);
    }
}

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
    <div class="col-lg-8 col-xl-7">
        <div class="card-modern p-4 p-md-5 my-2">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Settings & Profile</h3>
                    <p class="text-muted small mb-0">Update your account information and preferences</p>
                </div>
                <span class="badge" style="background-color: #f1f5f9; color: #475569; font-weight: 600; padding: 0.5rem 0.85rem; border-radius: 20px;">
                    @<?= htmlspecialchars($user['username']); ?>
                </span>
            </div>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Please review the errors below:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/index.php?action=edit_profile" method="POST" enctype="multipart/form-data" novalidate>
                <!-- Avatar Preview & Upload -->
                <div class="mb-4 d-flex flex-column flex-sm-row align-items-center gap-4 p-3 bg-light rounded-3 border border-light-subtle">
                    <div class="flex-shrink-0 text-center">
                        <?php if ($avatarUrl): ?>
                            <img 
                                src="<?= $avatarUrl ?>" 
                                alt="Current Avatar" 
                                id="avatarPreview"
                                class="rounded-circle border border-3 border-white shadow-sm"
                                style="width: 80px; height: 80px; object-fit: cover;"
                                onerror="this.onerror=null; this.outerHTML='<div class=\'rounded-circle border border-3 border-white shadow-sm text-white d-inline-flex align-items-center justify-content-center fs-3 fw-bold\' style=\'width: 80px; height: 80px; background-color: #07193d;\'><?= htmlspecialchars($userInitials) ?></div>';"
                            >
                        <?php else: ?>
                            <div 
                                id="avatarPreviewPlaceholder"
                                class="rounded-circle border border-3 border-white shadow-sm text-white d-inline-flex align-items-center justify-content-center fs-3 fw-bold"
                                style="width: 80px; height: 80px; background-color: #07193d;"
                            >
                                <?= htmlspecialchars($userInitials); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1 w-100">
                        <label for="profile_image" class="form-label fw-semibold mb-1 small text-secondary">Profile Picture</label>
                        <input 
                            type="file" 
                            class="form-control form-control-sm" 
                            id="profile_image" 
                            name="profile_image" 
                            accept="image/jpeg,image/png,image/gif,image/webp"
                        >
                        <div class="form-text small text-muted">
                            Supported: JPG, PNG, GIF, WEBP. Max size: 2MB.
                        </div>

                        <?php if (!empty($user['profile_image'])): ?>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_picture" id="remove_picture" value="1">
                                <label class="form-check-label text-danger small" for="remove_picture">
                                    Remove current picture
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Full Name -->
                <div class="mb-3">
                    <label for="full_name" class="form-label fw-semibold small text-secondary">
                        Full Name <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-person"></i></span>
                        <input 
                            type="text" 
                            class="form-control border-start-0 ps-0" 
                            id="full_name" 
                            name="full_name" 
                            value="<?= htmlspecialchars($formData['full_name'] ?? ''); ?>" 
                            placeholder="Enter your full name" 
                            required
                            maxlength="100"
                        >
                    </div>
                </div>

                <!-- Username (Readonly) -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-at"></i></span>
                        <input 
                            type="text" 
                            class="form-control bg-light border-start-0 ps-0" 
                            id="username" 
                            value="<?= htmlspecialchars($user['username']); ?>" 
                            readonly 
                            disabled
                        >
                    </div>
                    <div class="form-text small text-muted">Unique campus handle (cannot be altered).</div>
                </div>

                <!-- Bio -->
                <div class="mb-4">
                    <label for="bio" class="form-label fw-semibold small text-secondary">Bio</label>
                    <textarea 
                        class="form-control" 
                        id="bio" 
                        name="bio" 
                        rows="3" 
                        maxlength="500" 
                        placeholder="Write a few lines about yourself, your department, hobbies..."
                    ><?= htmlspecialchars($formData['bio'] ?? ''); ?></textarea>
                    <div class="form-text small text-muted text-end">Maximum 500 characters</div>
                </div>

                <!-- Form Action Buttons -->
                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <a href="<?= BASE_URL ?>/index.php?action=profile" class="btn btn-outline-secondary px-4">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-accent px-4 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
