<?php
// app/views/messages/inbox.php
// Inbox view: lists all conversations for the logged-in user inside a single unified card

$currentUserId = (int)($_SESSION['user_id'] ?? 0);
?>

<div class="mx-auto py-4" style="max-width: 760px;">

    <!-- Flash Error (if any) -->
    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Unified Single Messages Card -->
    <div class="card-modern border-0 shadow-sm p-0 overflow-hidden" id="messagesInboxCard">
        <!-- Card Header -->
        <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-3 text-white"
                     style="width: 40px; height: 40px; background: linear-gradient(135deg, #07193d, #1e3a8a); flex-shrink: 0;">
                    <i class="bi bi-chat-dots-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-themed mb-0">Messages</h5>
                    <p class="text-muted small mb-0">Your private conversations on CLIQ.</p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Feed
            </a>
        </div>

        <!-- Conversations List -->
        <?php if (!empty($conversations)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($conversations as $conv): ?>
                    <?php
                    $isUnread      = (int)($conv['unread_count'] ?? 0) > 0;
                    $lastMessage   = htmlspecialchars(mb_strimwidth($conv['last_message'] ?? '', 0, 80, '…'));
                    $lastAt        = !empty($conv['last_at']) ? date('M j, g:i a', strtotime($conv['last_at'])) : '';
                    $profileUrl    = BASE_URL . '/index.php?action=profile&user_id=' . (int)$conv['partner_id'];
                    $convUrl       = BASE_URL . '/index.php?action=conversation&user_id=' . (int)$conv['partner_id'];
                    $avatarUrl     = $conv['partner_avatar_url'] ?? null;
                    $initials      = $conv['partner_initials'] ?? 'U';
                    $partnerName   = htmlspecialchars($conv['partner_full_name'] ?? 'User');
                    $partnerHandle = htmlspecialchars('@' . ($conv['partner_username'] ?? ''));
                    ?>
                    <a href="<?= $convUrl ?>" class="list-group-item list-group-item-action px-4 py-3 inbox-conv-item <?= $isUnread ? 'inbox-unread' : '' ?>">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <?php if ($avatarUrl): ?>
                                    <img src="<?= $avatarUrl ?>" alt="<?= $partnerName ?>"
                                         class="rounded-circle"
                                         style="width:48px;height:48px;object-fit:cover;"
                                         onerror="this.onerror=null;this.outerHTML='<div class=\'rounded-circle d-flex align-items-center justify-content-center fw-bold text-white\' style=\'width:48px;height:48px;background-color:#07193d;font-size:1rem;\'><?= htmlspecialchars($initials) ?></div>';">
                                <?php else: ?>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                         style="width:48px;height:48px;background-color:#07193d;font-size:1rem;">
                                        <?= htmlspecialchars($initials) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Name, Handle & Last Message -->
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-baseline justify-content-between gap-2">
                                    <span class="fw-semibold text-themed text-truncate" style="max-width:55%;">
                                        <?= $partnerName ?>
                                    </span>
                                    <span class="text-muted" style="font-size:0.75rem;white-space:nowrap;">
                                        <?= $lastAt ?>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                    <span class="text-muted text-truncate small <?= $isUnread ? 'fw-semibold' : '' ?>">
                                        <?= $lastMessage ?>
                                    </span>
                                    <?php if ($isUnread): ?>
                                        <span class="badge rounded-pill bg-primary ms-auto" style="min-width:20px;font-size:0.7rem;">
                                            <?= (int)$conv['unread_count'] > 99 ? '99+' : (int)$conv['unread_count'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State inside card -->
            <div class="p-5 text-center">
                <i class="bi bi-chat-dots text-primary mb-3" style="font-size:3rem;opacity:0.5;"></i>
                <h5 class="fw-bold text-themed mb-2">No conversations yet</h5>
                <p class="text-muted mb-4">Start a conversation by visiting another member's profile and clicking <strong>Message</strong>.</p>
                <a href="<?= BASE_URL ?>/index.php?action=search" class="btn btn-navy rounded-pill px-4 fw-semibold">
                    <i class="bi bi-search me-1"></i> Find People
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>
