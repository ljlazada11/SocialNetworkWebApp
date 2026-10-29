<?php
// app/views/auth/forgot_password.php
// Forgot / Reset Password View for CLIQ - The SMCC Community Hub

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="auth-card-wrapper w-100" style="max-width: 480px;">
    <div class="card-modern shadow-lg border-0 p-4 p-md-5 my-auto">
        <div class="text-center mb-4">
            <div class="mb-3">
                <a href="<?= BASE_URL ?>/" title="CLIQ Home" class="d-inline-block">
                    <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-light.png" alt="CLIQ" class="cliq-logo-full cliq-logo-auth cliq-logo-light">
                    <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-dark.png" alt="CLIQ" class="cliq-logo-full cliq-logo-auth cliq-logo-dark">
                </a>
            </div>
            <h3 class="fw-bold text-navy mb-1">Reset Password</h3>
            <p class="text-muted small">Verify your account details to set a new password.</p>
        </div>

        <!-- Flash Success Message -->
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>
                <?= htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Flash Error Message -->
        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?= htmlspecialchars($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Form Validation Errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/index.php?action=forgot_password" method="POST" id="forgotPasswordForm" novalidate>
            <?= csrf_field(); ?>

            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small text-navy">Username <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-at"></i></span>
                    <input
                        type="text"
                        class="form-control border-start-0 ps-0"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username ?? ''); ?>"
                        placeholder="Enter your username"
                        required
                        autofocus
                    >
                </div>
            </div>

            <!-- Full Name Verification -->
            <div class="mb-3">
                <label for="full_name" class="form-label fw-semibold small text-navy">Full Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input
                        type="text"
                        class="form-control border-start-0 ps-0"
                        id="full_name"
                        name="full_name"
                        value="<?= htmlspecialchars($fullName ?? ''); ?>"
                        placeholder="Enter the full name registered on your account"
                        required
                    >
                </div>
                <div class="form-text small text-muted">Used to verify account ownership.</div>
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="new_password" class="form-label fw-semibold small text-navy">New Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        class="form-control border-start-0 border-end-0 ps-0"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter your new password"
                        required
                    >
                    <button type="button" class="btn toggle-password-btn border border-start-0" data-target="new_password" tabindex="-1" title="Toggle password visibility" aria-label="Toggle password visibility">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="form-text small text-muted">At least 8 characters with upper, lower, number, and special character.</div>
            </div>

            <!-- Confirm New Password -->
            <div class="mb-4">
                <label for="confirm_password" class="form-label fw-semibold small text-navy">Confirm New Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input
                        type="password"
                        class="form-control border-start-0 border-end-0 ps-0"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Re-enter your new password"
                        required
                    >
                    <button type="button" class="btn toggle-password-btn border border-start-0" data-target="confirm_password" tabindex="-1" title="Toggle password visibility" aria-label="Toggle password visibility">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-navy py-2-5 fw-semibold shadow-sm" id="resetSubmitBtn">
                    <i class="bi bi-shield-check me-1"></i> Reset Password
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="text-center pt-3 border-top">
                <div class="mb-2">
                    <span class="text-muted small">Remember your password?</span>
                    <a href="<?= BASE_URL ?>/index.php?action=login" class="text-navy small fw-bold text-decoration-none ms-1">
                        Log In
                    </a>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/index.php?action=welcome" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Back to Welcome
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle Password Visibility
    document.querySelectorAll('.toggle-password-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
                this.setAttribute('title', 'Hide password');
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
                this.setAttribute('title', 'Show password');
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
