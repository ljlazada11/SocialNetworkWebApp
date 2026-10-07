<?php
// app/views/auth/login.php
// User Login View - Modernized Design matching Dashboard theme

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5">
        <div class="card-modern p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-icon-box mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.5rem;">
                    <i class="bi bi-box-arrow-in-right"></i>
                </div>
                <h3 class="fw-bold text-dark">Welcome Back</h3>
                <p class="text-muted small">Enter your credentials to access your SMCC Connect account.</p>
            </div>

            <!-- Flash Success Message -->
            <?php if (!empty($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    <?= htmlspecialchars($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/index.php?action=login" method="POST" novalidate>
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-at"></i></span>
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

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                        <input
                            type="password"
                            class="form-control border-start-0 ps-0"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-accent py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </button>
                </div>

                <div class="text-center pt-2 border-top">
                    <span class="text-muted small">Don't have an account?</span>
                    <a href="<?= BASE_URL ?>/index.php?action=register" class="text-primary small fw-semibold text-decoration-none ms-1">
                        Create an account
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
