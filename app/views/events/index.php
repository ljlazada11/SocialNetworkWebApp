<?php
// app/views/events/index.php
// Dedicated full-page view for CLIQ Campus Events Calendar
?>

<div class="container py-4">
    <!-- Breadcrumb & Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?action=home" class="text-decoration-none">Feed</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Events</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <div class="ve-header-badge" style="width: 48px; height: 48px; font-size: 1.4rem;">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-themed mb-0">Campus Events Calendar</h3>
                    <p class="text-muted small mb-0">Official schedule of community events, assemblies, and activities on CLIQ</p>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?= BASE_URL ?>/index.php?action=home" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Feed
            </a>
            <span class="badge bg-primary-subtle text-primary border-0 rounded-pill px-3 py-1.5 fw-semibold">
                <?= count($upcomingEvents); ?> Upcoming
            </span>
        </div>
    </div>

    <!-- Main Events Container -->
    <div class="row g-4">
        <!-- Upcoming Events Column -->
        <div class="col-lg-8">
            <div class="card-modern p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <h5 class="fw-bold text-themed mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-check text-primary"></i>
                        <span>Upcoming Schedules</span>
                    </h5>
                    <span class="micro-text text-muted">Sorted chronologically</span>
                </div>

                <?php if (!empty($upcomingEvents)): ?>
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
                                $statusLabel = $isToday ? 'Happening Today' : ($isTomorrow ? 'Tomorrow' : 'Upcoming');
                            ?>
                            <div class="ve-card" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#modalPageEventDetails<?= (int)$evt['id']; ?>">
                                <div class="ve-date-badge">
                                    <span class="ve-date-month"><?= htmlspecialchars($evtMon); ?></span>
                                    <span class="ve-date-day"><?= htmlspecialchars($evtDay); ?></span>
                                    <span class="ve-date-dow"><?= htmlspecialchars($evtDow); ?></span>
                                </div>
                                <div class="ve-body">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <span class="ve-status-pill <?= $statusClass; ?>">
                                            <?php if ($isToday): ?><i class="bi bi-broadcast me-1"></i><?php endif; ?>
                                            <?= $statusLabel; ?>
                                        </span>
                                        <span class="micro-text text-primary fw-semibold">
                                            View Details <i class="bi bi-arrow-right ms-1"></i>
                                        </span>
                                    </div>
                                    <h5 class="ve-title mt-1 mb-1"><?= htmlspecialchars($evt['title']); ?></h5>
                                    <div class="ve-meta-row">
                                        <span class="ve-meta-item">
                                            <i class="bi bi-clock text-warning"></i>
                                            <?= !empty($evt['event_time']) ? htmlspecialchars($evt['event_time']) : 'TBA'; ?>
                                        </span>
                                        <span class="ve-meta-item">
                                            <i class="bi bi-geo-alt-fill text-danger"></i>
                                            <?= htmlspecialchars($evt['location']); ?>
                                        </span>
                                        <?php if (!empty($evt['organizer_name'])): ?>
                                            <span class="ve-meta-item text-muted">
                                                <i class="bi bi-person text-secondary"></i>
                                                <?= htmlspecialchars($evt['organizer_name']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($evt['description'])): ?>
                                        <div class="ve-description mt-2">
                                            <?= htmlspecialchars($evt['description']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 text-primary mb-2 d-block"></i>
                        <h6 class="fw-bold text-themed">No upcoming events scheduled</h6>
                        <p class="small mb-0">Check back soon for new campus activities and announcements.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar / Guidelines Column -->
        <div class="col-lg-4">
            <div class="card-modern p-4 mb-4">
                <h6 class="fw-bold text-themed mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span>Campus Event Guidelines</span>
                </h6>
                <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-check2-circle text-success mt-0.5"></i>
                        <span>All events follow Saint Michael College of Caraga policies and dress code guidelines.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-check2-circle text-success mt-0.5"></i>
                        <span>Students must present their validated SMCC Student ID for campus admission.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-check2-circle text-success mt-0.5"></i>
                        <span>All event schedules are shown in Philippine Standard Time (PST).</span>
                    </li>
                </ul>
            </div>

            <div class="card-modern p-4 text-center">
                <div class="brand-icon-box mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.4rem; border-radius: 12px;">
                    <i class="bi bi-megaphone-fill"></i>
                </div>
                <h6 class="fw-bold text-themed mb-1">Host an Event?</h6>
                <p class="micro-text text-muted mb-3">Recognized student organizations and academic departments can propose activities through the Student Affairs Office.</p>
                <a href="<?= BASE_URL ?>/index.php?action=home" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                    Return to Community Feed
                </a>
            </div>
        </div>
    </div>
</div>

<!-- EVENT DETAIL MODALS FOR FULL PAGE -->
<?php if (!empty($upcomingEvents)): ?>
    <?php foreach ($upcomingEvents as $evt): ?>
        <?php
            $evtTimestamp = !empty($evt['event_date']) ? strtotime($evt['event_date']) : time();
            $todayDate = date('Y-m-d');
            $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
            $isToday = ($evt['event_date'] === $todayDate);
            $isTomorrow = ($evt['event_date'] === $tomorrowDate);
        ?>
        <div class="modal fade ve-detail-modal" id="modalPageEventDetails<?= (int)$evt['id']; ?>" tabindex="-1" aria-labelledby="pageEventModalLabel<?= (int)$evt['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-bottom py-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ve-header-badge">
                                <i class="bi bi-calendar-event-fill"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0 text-themed" id="pageEventModalLabel<?= (int)$evt['id']; ?>">
                                    <?= htmlspecialchars($evt['title']); ?>
                                </h5>
                                <span class="micro-text text-muted">Official Campus Event</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
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
                    <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="micro-text text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i>Official CLIQ Announcement
                        </span>
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
