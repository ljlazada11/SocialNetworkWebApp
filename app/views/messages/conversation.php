<?php
// app/views/messages/conversation.php
// Conversation (chat thread) view between two users inside a single unified card

$currentUserId = (int)($_SESSION['user_id'] ?? 0);

// Group messages by date for display
$grouped = [];
foreach ($messages as $msg) {
    $dateKey = date('Y-m-d', strtotime($msg['created_at']));
    $grouped[$dateKey][] = $msg;
}
?>

<div class="mx-auto py-3" style="max-width: 760px;">

    <!-- Flash Error (if any) -->
    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Unified Single Chat Card -->
    <div class="card-modern border-0 shadow-sm p-0 overflow-hidden d-flex flex-column" id="conversationCard" style="height: calc(100vh - 195px); min-height: 520px; max-height: 780px;">

        <!-- 1. Header (Border Bottom) -->
        <div class="p-3 px-4 border-bottom d-flex align-items-center gap-3 flex-shrink-0 conversation-header">
            <!-- Back to Inbox -->
            <a href="<?= BASE_URL ?>/index.php?action=messages"
               class="btn btn-sm btn-outline-secondary rounded-pill flex-shrink-0"
               title="Back to inbox" aria-label="Back to inbox">
                <i class="bi bi-arrow-left"></i>
            </a>

            <!-- Partner Avatar -->
            <div class="flex-shrink-0">
                <?php if (!empty($partnerAvatarUrl)): ?>
                    <img src="<?= htmlspecialchars($partnerAvatarUrl) ?>"
                         alt="<?= htmlspecialchars($partner['full_name']) ?>"
                         class="rounded-circle"
                         style="width: 40px; height: 40px; object-fit: cover;"
                         onerror="this.onerror=null;this.outerHTML='<div class=\'rounded-circle d-flex align-items-center justify-content-center fw-bold text-white\' style=\'width:40px;height:40px;background:#07193d;\'><?= htmlspecialchars($partnerInitials) ?></div>';">
                <?php else: ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                         style="width: 40px; height: 40px; background: #07193d;">
                        <?= htmlspecialchars($partnerInitials) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Partner Name + Link to Profile -->
            <div class="flex-grow-1 min-w-0">
                <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$partner['id'] ?>"
                   class="text-decoration-none text-themed">
                    <span class="fw-bold d-block text-truncate"><?= htmlspecialchars($partner['full_name']) ?></span>
                    <span class="text-muted small">@<?= htmlspecialchars($partner['username']) ?></span>
                </a>
            </div>

            <!-- View Profile button -->
            <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$partner['id'] ?>"
               class="btn btn-sm btn-outline-primary rounded-pill px-3 flex-shrink-0 d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-person-fill"></i>
                <span>Profile</span>
            </a>
        </div>

        <!-- 2. Scrollable Message Thread (Flex Grow) -->
        <div class="p-3 p-md-4 flex-grow-1 overflow-y-auto" id="messageThread">

            <?php if (empty($messages)): ?>
                <div class="text-center py-5 d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-chat-heart text-primary mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p class="text-muted mb-0">No messages yet. Say hello! 👋</p>
                </div>
            <?php else: ?>
                <?php foreach ($grouped as $dateKey => $dayMessages): ?>
                    <!-- Date Separator -->
                    <div class="d-flex align-items-center gap-2 my-3 date-separator">
                        <hr class="flex-grow-1 m-0" style="border-color: var(--card-border, #e5e7eb);">
                        <span class="date-badge">
                            <?= date('l, F j, Y', strtotime($dateKey)) === date('l, F j, Y') ? 'Today' : (date('l, F j, Y', strtotime($dateKey)) === date('l, F j, Y', strtotime('-1 day')) ? 'Yesterday' : date('M j, Y', strtotime($dateKey))) ?>
                        </span>
                        <hr class="flex-grow-1 m-0" style="border-color: var(--card-border, #e5e7eb);">
                    </div>

                    <?php foreach ($dayMessages as $msg): ?>
                        <?php
                        $isOwn     = ((int)$msg['sender_id'] === $currentUserId);
                        $timeStr   = date('g:i a', strtotime($msg['created_at']));
                        $msgId     = (int)$msg['id'];

                        if ($isOwn) {
                            $avatarUrl  = $currentUserAvatarUrl ?? null;
                            $initials   = $currentUserInitials;
                        } else {
                            $avatarUrl  = $partnerAvatarUrl ?? null;
                            $initials   = $partnerInitials;
                        }
                        ?>
                        <div class="d-flex <?= $isOwn ? 'flex-row-reverse' : 'flex-row' ?> align-items-end gap-2 mb-2 msg-row"
                             id="msg-<?= $msgId ?>">

                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <?php if ($avatarUrl): ?>
                                    <img src="<?= htmlspecialchars($avatarUrl) ?>"
                                         alt="avatar"
                                         class="rounded-circle"
                                         style="width: 30px; height: 30px; object-fit: cover;"
                                         onerror="this.onerror=null;this.style.display='none';">
                                <?php else: ?>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                         style="width: 30px; height: 30px; background: #07193d; font-size: 0.7rem;">
                                        <?= htmlspecialchars($initials) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Bubble + meta -->
                            <div class="d-flex flex-column <?= $isOwn ? 'align-items-end' : 'align-items-start' ?>" style="max-width: 72%;">
                                <div class="msg-bubble <?= $isOwn ? 'msg-own' : 'msg-other' ?>">
                                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="text-muted" style="font-size: 0.68rem;"><?= $timeStr ?></span>
                                    <?php if ($isOwn): ?>
                                        <!-- Read receipt -->
                                        <i class="bi <?= $msg['is_read'] ? 'bi-check2-all text-primary' : 'bi-check2 text-muted' ?>"
                                           style="font-size: 0.72rem;" title="<?= $msg['is_read'] ? 'Seen' : 'Sent' ?>"></i>

                                        <!-- Delete button -->
                                        <button type="button"
                                                class="btn-delete-msg border-0 bg-transparent p-0 text-muted"
                                                data-msg-id="<?= $msgId ?>"
                                                data-partner-id="<?= (int)$partner['id'] ?>"
                                                title="Delete message"
                                                style="font-size: 0.72rem; opacity: 0.5; cursor: pointer;"
                                                aria-label="Delete message">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Scroll anchor -->
            <div id="msg-bottom"></div>
        </div>

        <!-- 3. Sleek Compact Compose Bar (Border Top) -->
        <div class="p-2.5 px-3 border-top flex-shrink-0 conversation-compose">
            <form action="<?= BASE_URL ?>/index.php?action=send_message" method="POST" id="msgComposeForm" class="m-0 p-0">
                <?= csrf_field(); ?>
                <input type="hidden" name="receiver_id" value="<?= (int)$partner['id'] ?>">

                <div class="d-flex align-items-center gap-2">
                    <div class="flex-grow-1 position-relative">
                        <textarea name="message"
                                  id="msgTextarea"
                                  class="form-control conversation-input"
                                  rows="1"
                                  placeholder="Type a message to <?= htmlspecialchars($partner['full_name']) ?>…"
                                  maxlength="2000"
                                  required
                                  aria-label="Message input"></textarea>
                    </div>
                    <button type="submit"
                            class="btn btn-send-msg"
                            title="Send message"
                            aria-label="Send message">
                        <i class="bi bi-send-fill" style="font-size: 0.95rem; margin-left: 2px;"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<!-- ===== Conversation JavaScript ===== -->
<script>
(function () {
    'use strict';

    // ---- Scroll to bottom of thread on load ----
    var thread = document.getElementById('messageThread');
    if (thread) {
        thread.scrollTop = thread.scrollHeight;
    }

    // ---- Auto-grow textarea & Enter-to-send ----
    var textarea = document.getElementById('msgTextarea');
    if (textarea) {
        textarea.addEventListener('input', function () {
            this.style.height = '38px';
            var scrollH = this.scrollHeight;
            if (scrollH > 38) {
                this.style.height = Math.min(scrollH, 100) + 'px';
            }
        });

        // Enter = submit; Shift+Enter = new line
        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim().length > 0) {
                    document.getElementById('msgComposeForm').submit();
                }
            }
        });
    }

    // ---- Delete message (AJAX) ----
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete-msg');
        if (!btn) return;

        if (!confirm('Delete this message?')) return;

        var msgId     = btn.getAttribute('data-msg-id');
        var partnerId = btn.getAttribute('data-partner-id');
        var csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

        var formData = new FormData();
        formData.append('message_id', msgId);
        formData.append('partner_id', partnerId);
        formData.append('csrf_token', csrfToken);

        fetch(window.BASE_URL + '/index.php?action=delete_message', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                var row = document.getElementById('msg-' + msgId);
                if (row) {
                    row.style.transition = 'opacity 0.25s';
                    row.style.opacity = '0';
                    setTimeout(function () { row.remove(); }, 260);
                }
            } else {
                alert(data.error || 'Could not delete message.');
            }
        })
        .catch(function () {
            alert('Network error. Please try again.');
        });
    });

})();
</script>

<style>
/* ===== Conversation Combined Card Styles ===== */
.conversation-header {
    background-color: var(--card-bg, #ffffff);
}
.conversation-compose {
    background-color: var(--card-bg, #ffffff);
    padding: 0.65rem 1rem !important;
}

.conversation-input {
    border-radius: 20px !important;
    padding: 0.45rem 1rem !important;
    font-size: 0.88rem !important;
    line-height: 1.4 !important;
    min-height: 38px !important;
    max-height: 100px !important;
    height: 38px;
    resize: none !important;
    background-color: var(--input-bg, #f8fafc) !important;
    border-color: var(--card-border, #cbd5e1) !important;
    color: var(--text-primary, #0f172a) !important;
}

.btn-send-msg {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background-color: #1e3a8a !important;
    color: #ffffff !important;
    border: none !important;
    transition: background-color 0.15s ease, transform 0.1s ease;
    flex-shrink: 0;
}

.btn-send-msg:hover {
    background-color: #2563eb !important;
    color: #ffffff !important;
    transform: scale(1.04);
}

.date-badge {
    background-color: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
    font-size: 0.72rem;
    padding: 0.15rem 0.65rem;
    border-radius: 50rem;
    white-space: nowrap;
}

/* ===== Chat Bubbles ===== */
.msg-bubble {
    padding: 0.55rem 0.85rem;
    border-radius: 18px;
    font-size: 0.88rem;
    line-height: 1.45;
    word-break: break-word;
    max-width: 100%;
}
.msg-own {
    background-color: #1e3a8a;
    color: #ffffff;
    border-bottom-right-radius: 4px;
}
.msg-other {
    background-color: var(--card-bg, #f3f4f6);
    color: var(--text-primary, #111827);
    border-bottom-left-radius: 4px;
    border: 1px solid var(--card-border, #e5e7eb);
}

/* ===== Dark Mode Theme Styles ===== */
[data-bs-theme="dark"] .conversation-header,
.dark-theme .conversation-header {
    background-color: #0d1e44 !important;
    border-bottom-color: #1b356d !important;
}

[data-bs-theme="dark"] .conversation-compose,
.dark-theme .conversation-compose {
    background-color: #0d1e44 !important;
    border-top-color: #1b356d !important;
}

[data-bs-theme="dark"] .conversation-input,
.dark-theme .conversation-input {
    background-color: #071536 !important;
    border-color: #1b356d !important;
    color: #f0f6ff !important;
}

[data-bs-theme="dark"] .conversation-input::placeholder,
.dark-theme .conversation-input::placeholder {
    color: #7ea6e0 !important;
    opacity: 0.85;
}

[data-bs-theme="dark"] .btn-send-msg,
.dark-theme .btn-send-msg {
    background-color: #2563eb !important;
    color: #ffffff !important;
}

[data-bs-theme="dark"] .btn-send-msg:hover,
.dark-theme .btn-send-msg:hover {
    background-color: #3b82f6 !important;
}

[data-bs-theme="dark"] .date-badge,
.dark-theme .date-badge {
    background-color: #081534 !important;
    border-color: #1b356d !important;
    color: #93c5fd !important;
}

[data-bs-theme="dark"] .msg-other {
    background-color: #152d60;
    color: #e2e8f0;
    border-color: #1b356d;
}
[data-bs-theme="dark"] .msg-own {
    background-color: #2563eb;
    color: #ffffff;
}
</style>
