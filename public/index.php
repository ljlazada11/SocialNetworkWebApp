<?php
// public/index.php
// Main entry point - Testing base layout

$pageTitle = 'Home';

require_once __DIR__ . '/../app/views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 text-center py-5">
        <h1 class="display-5 fw-bold text-primary mb-3">Welcome to Mini Social Network</h1>
        <p class="lead text-secondary mb-4">
            A clean, responsive PHP MVC social networking web application built with Bootstrap.
        </p>
        <div class="card p-4 text-start shadow-sm bg-white mb-4">
            <h5 class="card-title text-dark mb-2">Base Layout Status</h5>
            <p class="card-text text-muted mb-3">
                The reusable base layout is active and operational. Header, navigation bar, responsive grid container, and footer are rendered dynamically.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle">Bootstrap 5.3 CDN Loaded</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Responsive Navbar Active</span>
                <span class="badge bg-info-subtle text-info border border-info-subtle">Custom CSS Active</span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Sticky Footer Active</span>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../app/views/layouts/footer.php';
?>
