<?php
// app/views/auth/welcome.php
// Welcome & Authentication Landing View for CLIQ - The SMCC Community Hub

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="landing-hero-container w-100 text-center py-5 px-3">
    <!-- Ambient Backdrop Glow -->
    <div class="landing-ambient-bg"></div>

    <!-- Global Flash Notification Messages (if any) -->
    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mx-auto mb-4 text-start" style="max-width: 620px;" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mx-auto mb-4 text-start" style="max-width: 620px;" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- HERO SECTION -->
    <div class="landing-hero-content mx-auto" style="max-width: 760px;">

        <!-- Prestigious Top Badge Pill -->
        <div class="mb-4">
            <div class="landing-badge-pill">
                <span class="badge-dot"></span>
                <span>SMCC Collegiate Network</span>
                <span class="opacity-50">•</span>
                <span>Campus Community Hub</span>
            </div>
        </div>

        <!-- CLIQ Brand Emblem -->
        <div class="mb-3">
            <div class="landing-logo-wrap" title="CLIQ">
                <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-light.png" alt="CLIQ" class="cliq-logo-full cliq-logo-landing cliq-logo-light">
                <img src="<?= BASE_URL ?>/public/assets/images/cliq-logo-dark.png" alt="CLIQ" class="cliq-logo-full cliq-logo-landing cliq-logo-dark">
            </div>
        </div>

        <!-- Main Heading -->
        <h1 class="landing-heading fw-bold">
            Connect. Share. <span class="landing-heading-gradient">Belong.</span>
        </h1>

        <!-- Subheading -->
        <div class="landing-subheading mb-3">CLIQ &bull; The SMCC Community Hub</div>

        <!-- Website Description -->
        <p class="landing-description mb-4">
            The dedicated social platform for Saint Michael College of Caraga. Connect with fellow students, share campus experiences, discover collegiate events, and be part of an inspiring community where every Michaelinian belongs.
        </p>

        <!-- Call to Action Buttons -->
        <div class="landing-actions d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3">
            <a href="<?= BASE_URL ?>/index.php?action=register" class="landing-btn-primary d-inline-flex align-items-center justify-content-center gap-2" aria-label="Join CLIQ">
                <i class="bi bi-person-plus-fill fs-5"></i>
                <span>Get Started — Join CLIQ</span>
                <i class="bi bi-arrow-right fs-6 btn-arrow"></i>
            </a>
            <a href="<?= BASE_URL ?>/index.php?action=login" class="landing-btn-secondary d-inline-flex align-items-center justify-content-center gap-2" aria-label="Log In to CLIQ">
                <i class="bi bi-box-arrow-in-right fs-5 text-primary"></i>
                <span>Log In to Account</span>
            </a>
        </div>

        <!-- Trust Highlights Bar -->
        <div class="landing-trust-bar d-flex justify-content-center align-items-center flex-wrap gap-3 gap-md-4 mt-4 pt-3">
            <div class="trust-item d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-success fs-5"></i>
                <span>Verified Student Access</span>
            </div>
            <div class="trust-dot d-none d-sm-block">•</div>
            <div class="trust-item d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-primary fs-5"></i>
                <span>Real-Time Campus Feed</span>
            </div>
            <div class="trust-dot d-none d-sm-block">•</div>
            <div class="trust-item d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event-fill text-warning fs-5"></i>
                <span>Events & Student Life</span>
            </div>
        </div>

    </div>

    <!-- COMMUNITY FEATURES GRID -->
    <div class="landing-features-section text-start">
        <div class="text-center mb-5">
            <span class="badge bg-secondary-subtle text-secondary px-3 py-1-5 rounded-pill fw-semibold text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.5px;">Platform Highlights</span>
            <h2 class="fw-bold mt-2 text-dark" style="letter-spacing: -0.5px;">Everything you need to thrive on campus</h2>
            <p class="text-muted mx-auto" style="max-width: 540px; font-size: 0.98rem;">Explore the interactive spaces and features designed specifically for the SMCC collegiate network.</p>
        </div>

        <div class="row g-4">
            <!-- Feature 1: Social Feed -->
            <div class="col-sm-6 col-lg-3">
                <div class="landing-feature-card">
                    <div class="landing-feature-icon icon-feed">
                        <i class="bi bi-chat-heart-fill"></i>
                    </div>
                    <h5 class="landing-feature-title">Live Campus Feed</h5>
                    <p class="landing-feature-desc">
                        Share thoughts, post study photos, celebrate campus victories, and react to updates with likes and vibrant comment threads.
                    </p>
                </div>
            </div>

            <!-- Feature 2: Student Connections -->
            <div class="col-sm-6 col-lg-3">
                <div class="landing-feature-card">
                    <div class="landing-feature-icon icon-connect">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <h5 class="landing-feature-title">Student Connections</h5>
                    <p class="landing-feature-desc">
                        Find classmates by name, follow friends across departments, view detailed student profiles, and grow your college network.
                    </p>
                </div>
            </div>

            <!-- Feature 3: Campus Events -->
            <div class="col-sm-6 col-lg-3">
                <div class="landing-feature-card">
                    <div class="landing-feature-icon icon-events">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <h5 class="landing-feature-title">Campus Events</h5>
                    <p class="landing-feature-desc">
                        Stay informed on upcoming college seminars, departmental competitions, sports fests, student org meetups, and schedules.
                    </p>
                </div>
            </div>

            <!-- Feature 4: Safe & Verified -->
            <div class="col-sm-6 col-lg-3">
                <div class="landing-feature-card">
                    <div class="landing-feature-icon icon-safe">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h5 class="landing-feature-title">Safe & Dedicated</h5>
                    <p class="landing-feature-desc">
                        A focused, respectful environment crafted exclusively for Saint Michael College of Caraga students, staff, and faculty.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
