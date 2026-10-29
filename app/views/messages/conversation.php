<?php
// app/views/messages/conversation.php
// Modern Split-Pane Messenger Layout:
// Left Column: Conversations / Users List
// Right Column: Active Chat Thread or Empty State

$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$conversations = $conversations ?? [];
$partner       = $partner ?? null;
$messages      = $messages ?? [];

// Group messages by date for display (if partner exists)
$grouped = [];
if ($partner && !empty($messages)) {
    foreach ($messages as $msg) {
        $dateKey = date('Y-m-d', strtotime($msg['created_at']));
        $grouped[$dateKey][] = $msg;
    }
}
?>

<div class="feed-layout-container mx-auto">

    <!-- Flash Error (if any) -->
    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Unified Split-Pane Messenger Card -->
    <div class="card-modern border-0 shadow-sm p-0 overflow-hidden d-flex flex-row messages-main-card" id="messagesMainCard">

        <!-- ========================================================
             LEFT COLUMN: USERS / CONVERSATIONS SIDEBAR
             ======================================================== -->
        <div class="messages-sidebar <?= $partner ? 'd-none d-md-flex' : 'd-flex' ?> flex-column border-end flex-shrink-0" id="messagesSidebar">

            <!-- Sidebar Header -->
            <div class="border-bottom messages-sidebar-header">
                <div class="d-flex align-items-center mb-2">
                    <h5 class="fw-bold mb-0 text-themed messages-title">Messages</h5>
                </div>

                <!-- Instant Search Filter -->
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px; font-size: 0.8rem;"></i>
                    <input type="text"
                           id="convSearchInput"
                           class="form-control form-control-sm rounded-pill conv-search-input"
                           placeholder="Search conversations..."
                           autocomplete="off">
                </div>
            </div>

            <!-- Scrollable Users / Conversations List -->
            <div class="messages-list flex-grow-1 overflow-y-auto p-2" id="conversationsList">
                <?php if (!empty($conversations)): ?>
                    <div class="d-flex flex-column gap-1">
                        <?php foreach ($conversations as $conv): ?>
                            <?php
                            $cPartnerId = (int)$conv['partner_id'];
                            $isActive   = ($partner && (int)$partner['id'] === $cPartnerId);
                            $isUnread   = (int)($conv['unread_count'] ?? 0) > 0;
                            $cName      = htmlspecialchars($conv['partner_full_name'] ?? 'User');
                            $cUsername  = htmlspecialchars($conv['partner_username'] ?? '');
                            $cAvatar    = $conv['partner_avatar_url'] ?? null;
                            $cInitials  = htmlspecialchars($conv['partner_initials'] ?? 'U');
                            $cLastMsg   = htmlspecialchars(mb_strimwidth($conv['last_message'] ?? '', 0, 38, '…'));
                            $cLastAt    = !empty($conv['last_at']) ? date('M j', strtotime($conv['last_at'])) : '';
                            $convUrl    = BASE_URL . '/index.php?action=conversation&user_id=' . $cPartnerId;
                            ?>
                            <a href="<?= $convUrl ?>"
                               class="conv-item d-flex align-items-center gap-3 text-decoration-none <?= $isActive ? 'conv-item-active' : '' ?>"
                               data-name="<?= strtolower($cName) ?>"
                               data-username="<?= strtolower($cUsername) ?>">

                                <!-- Avatar -->
                                <div class="position-relative flex-shrink-0">
                                    <?php if ($cAvatar): ?>
                                        <img src="<?= htmlspecialchars($cAvatar) ?>"
                                             alt="<?= $cName ?>"
                                             class="rounded-circle conv-avatar-img"
                                             onerror="this.onerror=null;this.outerHTML='<div class=\'rounded-circle d-flex align-items-center justify-content-center fw-bold text-white conv-avatar-fallback\'><?= $cInitials ?></div>';">
                                    <?php else: ?>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white conv-avatar-fallback">
                                            <?= $cInitials ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($isUnread): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-2 border-white rounded-circle" style="width: 10px; height: 10px;"></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Name, Snippet & Timestamp -->
                                <div class="flex-grow-1 min-w-0 d-flex flex-column justify-content-center ps-1">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <span class="conv-name text-truncate fw-semibold <?= $isActive ? 'text-primary' : 'text-themed' ?>" style="font-size: 0.9rem; line-height: 1.35; margin-right: 0.35rem; letter-spacing: 0.01em;">
                                            <?= $cName ?>
                                        </span>
                                        <?php if ($cLastAt): ?>
                                            <span class="conv-time text-muted small flex-shrink-0" style="font-size: 0.72rem; line-height: 1.35; white-space: nowrap;">
                                                <?= $cLastAt ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <span class="conv-snippet text-truncate small <?= $isUnread ? 'fw-bold text-themed' : 'text-muted' ?>" style="font-size: 0.78rem; line-height: 1.35;">
                                            <?= $cLastMsg ?: '<span class="fst-italic opacity-75">No messages yet</span>' ?>
                                        </span>
                                        <?php if ($isUnread): ?>
                                            <span class="badge rounded-pill bg-primary ms-1 flex-shrink-0" style="font-size: 0.65rem; padding: 0.25em 0.55em;">
                                                <?= (int)$conv['unread_count'] > 99 ? '99+' : (int)$conv['unread_count'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div id="noConvResults" class="p-4 text-center text-muted small d-none">
                        <i class="bi bi-search d-block mb-1 fs-5 opacity-50"></i>
                        No matching conversations.
                    </div>
                <?php else: ?>
                    <!-- Empty State for Sidebar -->
                    <div class="p-4 text-center text-muted h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 bg-primary-subtle text-primary" style="width: 48px; height: 48px;">
                            <i class="bi bi-chat-square-dots fs-5"></i>
                        </div>
                        <p class="small mb-2 fw-medium">No conversations yet</p>
                        <a href="<?= BASE_URL ?>/index.php?action=search" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-search me-1"></i> Find People
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========================================================
             RIGHT COLUMN: ACTIVE CHAT THREAD OR PLACEHOLDER
             ======================================================== -->
        <div class="messages-content <?= $partner ? 'd-flex' : 'd-none d-md-flex' ?> flex-column flex-grow-1 min-w-0" id="messagesContent">

            <?php if ($partner): ?>
                <!-- 1. Chat Header -->
                <div class="px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between flex-shrink-0 conversation-header">
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <!-- Mobile Back to Users List Button -->
                        <a href="<?= BASE_URL ?>/index.php?action=messages"
                           class="btn btn-sm btn-outline-secondary rounded-pill d-md-none p-1 px-2 me-1"
                           title="Back to conversations" aria-label="Back to conversations">
                            <i class="bi bi-chevron-left"></i>
                        </a>

                        <!-- Partner Avatar -->
                        <div class="flex-shrink-0">
                            <?php if (!empty($partnerAvatarUrl)): ?>
                                <img src="<?= htmlspecialchars($partnerAvatarUrl) ?>"
                                     alt="<?= htmlspecialchars($partner['full_name']) ?>"
                                     class="rounded-circle"
                                     style="width: 38px; height: 38px; object-fit: cover;"
                                     onerror="this.onerror=null;this.outerHTML='<div class=\'rounded-circle d-flex align-items-center justify-content-center fw-bold text-white\' style=\'width:38px;height:38px;background:#07193d;\'><?= htmlspecialchars($partnerInitials) ?></div>';">
                            <?php else: ?>
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                     style="width: 38px; height: 38px; background: #07193d; font-size: 0.85rem;">
                                    <?= htmlspecialchars($partnerInitials) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Partner Info -->
                        <div class="d-flex flex-column justify-content-center min-w-0 ms-1">
                            <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$partner['id'] ?>"
                               class="text-decoration-none text-themed d-block">
                                <span class="fw-bold d-block text-truncate" style="font-size: 0.95rem; line-height: 1.25;"><?= htmlspecialchars($partner['full_name']) ?></span>
                                <span class="text-muted small d-block text-truncate" style="font-size: 0.75rem; line-height: 1.2;">@<?= htmlspecialchars($partner['username']) ?></span>
                            </a>
                        </div>
                    </div>

                    <!-- Profile Link Button -->
                    <a href="<?= BASE_URL ?>/index.php?action=profile&user_id=<?= (int)$partner['id'] ?>"
                       class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-themed flex-shrink-0 d-none d-sm-inline-flex align-items-center gap-1 border">
                        <i class="bi bi-person"></i>
                        <span class="small fw-semibold" style="line-height: 1;">Profile</span>
                    </a>
                </div>

                <!-- 2. Scrollable Message Thread -->
                <div class="p-3 p-md-4 flex-grow-1 overflow-y-auto messages-thread-area" id="messageThread">
                    <?php if (empty($messages)): ?>
                        <div class="text-center py-5 d-flex flex-column align-items-center justify-content-center h-100">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 bg-primary-subtle text-primary" style="width: 60px; height: 60px;">
                                <i class="bi bi-chat-heart fs-3"></i>
                            </div>
                            <h6 class="fw-semibold text-themed mb-1">Start a conversation</h6>
                            <p class="text-muted small mb-0">Say hello to <?= htmlspecialchars($partner['full_name']) ?>! 👋</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($grouped as $dateKey => $dayMessages): ?>
                            <!-- Date Separator -->
                            <div class="text-center my-3 date-separator">
                                <span class="date-badge">
                                    <?= date('l, F j, Y', strtotime($dateKey)) === date('l, F j, Y') ? 'Today' : (date('l, F j, Y', strtotime($dateKey)) === date('l, F j, Y', strtotime('-1 day')) ? 'Yesterday' : date('M j, Y', strtotime($dateKey))) ?>
                                </span>
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
                                                 style="width: 28px; height: 28px; object-fit: cover;"
                                                 onerror="this.onerror=null;this.style.display='none';">
                                        <?php else: ?>
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                                 style="width: 28px; height: 28px; background: #07193d; font-size: 0.65rem;">
                                                <?= htmlspecialchars($initials) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Bubble + meta -->
                                    <div class="d-flex flex-column <?= $isOwn ? 'align-items-end' : 'align-items-start' ?>" style="max-width: 72%;">
                                        <div class="msg-bubble <?= $isOwn ? 'msg-own' : 'msg-other' ?>" style="text-align: left;">
                                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mt-1 px-1">
                                            <span class="text-muted" style="font-size: 0.68rem; line-height: 1;"><?= $timeStr ?></span>
                                            <?php if ($isOwn): ?>
                                                <!-- Read receipt -->
                                                <i class="bi <?= $msg['is_read'] ? 'bi-check2-all text-primary' : 'bi-check2 text-muted' ?>"
                                                   style="font-size: 0.72rem; line-height: 1;" title="<?= $msg['is_read'] ? 'Seen' : 'Sent' ?>"></i>

                                                <!-- Message Options Dropdown (3 dots) -->
                                                <div class="dropdown d-inline-block">
                                                    <button type="button"
                                                            class="btn btn-link text-muted p-0 border-0 msg-options-btn d-inline-flex align-items-center"
                                                            data-bs-toggle="dropdown"
                                                            aria-expanded="false"
                                                            title="Message options"
                                                            aria-label="Message options"
                                                            style="line-height: 1;">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end post-dropdown-menu shadow-sm" style="min-width: 125px;">
                                                        <li>
                                                            <button type="button"
                                                                    class="dropdown-item btn-delete-msg d-flex align-items-center gap-2"
                                                                    data-msg-id="<?= $msgId ?>"
                                                                    data-partner-id="<?= (int)$partner['id'] ?>">
                                                                <i class="bi bi-trash3"></i>
                                                                <span style="line-height: 1;">Delete</span>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
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

                <!-- 3. Compose Bar -->
                <div class="p-2.5 px-3 px-md-4 border-top flex-shrink-0 conversation-compose">
                    <form action="<?= BASE_URL ?>/index.php?action=send_message" method="POST" id="msgComposeForm" class="m-0 p-0">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="receiver_id" value="<?= (int)$partner['id'] ?>">

                        <div class="d-flex align-items-center gap-2 compose-pill-wrapper">
                            <textarea
                                name="message"
                                id="msgInput"
                                class="form-control border-0 bg-transparent conversation-input flex-grow-1"
                                placeholder="Type a message..."
                                rows="1"
                                maxlength="2000"
                                required
                                autofocus></textarea>

                            <button type="submit"
                                    class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 btn-send-msg"
                                    title="Send message"
                                    aria-label="Send message">
                                <i class="bi bi-send-fill" style="font-size: 0.85rem; margin-left: 2px;"></i>
                            </button>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <!-- No Conversation Selected (Placeholder State) -->
                <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4 text-center messages-placeholder">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 bg-primary-subtle text-primary"
                         style="width: 72px; height: 72px;">
                        <i class="bi bi-chat-dots-fill fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-themed mb-2" style="letter-spacing: -0.01em;">Your Messages</h5>
                    <p class="text-muted small mb-3" style="max-width: 360px; line-height: 1.55;">
                        Select a conversation from the left to start chatting, or discover other members on CLIQ.
                    </p>
                    <a href="<?= BASE_URL ?>/index.php?action=search" class="btn btn-primary rounded-pill px-4 py-2 small fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-search"></i>
                        <span>Find People</span>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
(function () {
    // 1. Auto-scroll to bottom of conversation thread
    var bottomEl = document.getElementById('msg-bottom');
    var threadEl = document.getElementById('messageThread');
    if (bottomEl && threadEl) {
        threadEl.scrollTop = threadEl.scrollHeight;
    }

    // 2. Auto-grow textarea & Enter-to-send
    var textarea = document.getElementById('msgInput');
    if (textarea) {
        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            var newHeight = Math.min(this.scrollHeight, 100);
            this.style.height = Math.max(newHeight, 32) + 'px';
        });

        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim().length > 0) {
                    document.getElementById('msgComposeForm').submit();
                }
            }
        });
    }

    // 3. Instant Search Filter in Sidebar
    var searchInput = document.getElementById('convSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('.conv-item');
            var visibleCount = 0;

            items.forEach(function (item) {
                var name     = item.getAttribute('data-name') || '';
                var username = item.getAttribute('data-username') || '';
                if (!q || name.indexOf(q) !== -1 || username.indexOf(q) !== -1) {
                    item.style.setProperty('display', 'flex', 'important');
                    visibleCount++;
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });

            var noResults = document.getElementById('noConvResults');
            if (noResults) {
                noResults.classList.toggle('d-none', visibleCount > 0);
            }
        });
    }

    // 4. Delete message (AJAX) via 3-dots dropdown
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
/* ===== Unified Messages Card & Split-Pane Layout ===== */
.messages-main-card {
    height: calc(100vh - 175px);
    min-height: 540px;
    max-height: 780px;
    border-radius: 16px;
    background-color: var(--card-bg, #ffffff);
    border: 1px solid var(--card-border, #e2e8f0) !important;
}

/* Sidebar (Left Column) */
.messages-sidebar {
    width: 320px;
    min-width: 280px;
    max-width: 350px;
    background-color: var(--card-bg, #ffffff);
}

.messages-sidebar-header {
    background-color: var(--card-bg, #ffffff);
    padding: 1rem 1.15rem !important;
}

.messages-title {
    font-size: 1.25rem !important;
    font-weight: 700 !important;
    letter-spacing: -0.015em;
    line-height: 1.25 !important;
    color: var(--text-primary, #0f172a);
    margin: 0 !important;
}

.conv-search-input {
    font-size: 0.82rem !important;
    height: 36px !important;
    padding-left: 2.25rem !important;
    background-color: var(--input-bg, #f1f5f9) !important;
    border: 1px solid transparent !important;
    transition: all 0.15s ease;
}

.conv-search-input:focus {
    background-color: var(--card-bg, #ffffff) !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
}

/* Conversation Item */
.conv-item {
    padding: 0.7rem 0.85rem !important;
    border-radius: 12px;
    margin: 2px 4px;
    transition: background-color 0.15s ease;
    color: inherit;
}

.conv-item:hover {
    background-color: var(--hover-bg, #f1f5f9);
}

.conv-item-active {
    background-color: rgba(30, 58, 138, 0.08) !important;
}

.conv-name {
    display: inline-block;
    letter-spacing: 0.01em;
}

.conv-avatar-img,
.conv-avatar-fallback {
    width: 42px;
    height: 42px;
    object-fit: cover;
}

.conv-avatar-fallback {
    background: #07193d;
    font-size: 0.85rem;
}

/* Chat Header & Thread Area */
.conversation-header {
    background-color: var(--card-bg, #ffffff);
    height: 60px;
}

.messages-thread-area {
    background-color: var(--chat-bg, #f8fafc);
}

/* Date separator badges */
.date-badge {
    font-size: 0.7rem;
    font-weight: 500;
    color: var(--text-muted, #64748b);
    background-color: var(--card-bg, #ffffff);
    padding: 0.2rem 0.7rem;
    border-radius: 12px;
    border: 1px solid var(--card-border, #e2e8f0);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    display: inline-block;
}

/* Message Bubbles */
.msg-bubble {
    padding: 0.6rem 0.95rem;
    font-size: 0.9rem;
    line-height: 1.45;
    word-break: break-word;
}

.msg-own {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    color: #ffffff;
    border-radius: 18px 18px 4px 18px;
    box-shadow: 0 2px 5px rgba(37, 99, 235, 0.18);
}

.msg-other {
    background-color: var(--card-bg, #ffffff);
    color: var(--text-primary, #0f172a);
    border-radius: 18px 18px 18px 4px;
    border: 1px solid var(--card-border, #e2e8f0);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

/* 3-dots Message Options Button */
.msg-options-btn {
    font-size: 0.75rem;
    opacity: 0.45;
    line-height: 1;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.15s ease, color 0.15s ease;
}

.msg-options-btn:hover,
.msg-options-btn:focus {
    opacity: 1 !important;
    color: var(--text-primary, #1e293b) !important;
}

/* Gray Delete Button in Dropdown */
.post-dropdown-menu .dropdown-item.btn-delete-msg,
.post-dropdown-menu .dropdown-item.btn-delete-msg i {
    color: var(--text-muted, #64748b) !important;
}

.post-dropdown-menu .dropdown-item.btn-delete-msg:hover,
.post-dropdown-menu .dropdown-item.btn-delete-msg:hover i,
.post-dropdown-menu .dropdown-item.btn-delete-msg:focus,
.post-dropdown-menu .dropdown-item.btn-delete-msg:focus i {
    color: var(--text-primary, #0f172a) !important;
    background-color: rgba(0, 0, 0, 0.05) !important;
}

[data-bs-theme="dark"] .post-dropdown-menu .dropdown-item.btn-delete-msg,
[data-bs-theme="dark"] .post-dropdown-menu .dropdown-item.btn-delete-msg i,
.dark-theme .post-dropdown-menu .dropdown-item.btn-delete-msg,
.dark-theme .post-dropdown-menu .dropdown-item.btn-delete-msg i {
    color: #94a3b8 !important;
}

[data-bs-theme="dark"] .post-dropdown-menu .dropdown-item.btn-delete-msg:hover,
[data-bs-theme="dark"] .post-dropdown-menu .dropdown-item.btn-delete-msg:hover i,
.dark-theme .post-dropdown-menu .dropdown-item.btn-delete-msg:hover,
.dark-theme .post-dropdown-menu .dropdown-item.btn-delete-msg:hover i {
    color: #f1f5f9 !important;
    background-color: rgba(255, 255, 255, 0.08) !important;
}

/* Compose Bar */
.conversation-compose {
    background-color: var(--card-bg, #ffffff);
}

.compose-pill-wrapper {
    background-color: var(--input-bg, #f1f5f9);
    border: 1px solid var(--card-border, #e2e8f0);
    border-radius: 24px;
    padding: 0.25rem 0.35rem 0.25rem 0.85rem;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.compose-pill-wrapper:focus-within {
    border-color: #3b82f6;
    background-color: var(--card-bg, #ffffff);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}

.conversation-input {
    font-size: 0.88rem !important;
    line-height: 1.4 !important;
    padding: 0.32rem 0.2rem !important;
    min-height: 32px !important;
    max-height: 100px !important;
    height: 32px;
    resize: none !important;
    box-shadow: none !important;
    outline: none !important;
}

.btn-send-msg {
    width: 32px;
    height: 32px;
    background-color: #2563eb;
    border-color: #2563eb;
    transition: all 0.15s ease;
}

.btn-send-msg:hover {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
    transform: scale(1.05);
}

/* Empty Placeholder */
.messages-placeholder {
    background-color: var(--chat-bg, #f8fafc);
}

/* ===== Dark Mode Theme Styles ===== */
[data-bs-theme="dark"] .messages-main-card,
.dark-theme .messages-main-card {
    background-color: #0b1739 !important;
    border-color: #1b356d !important;
}

[data-bs-theme="dark"] .messages-sidebar,
.dark-theme .messages-sidebar {
    background-color: #0b1739 !important;
    border-right-color: #1b356d !important;
}

[data-bs-theme="dark"] .messages-sidebar-header,
.dark-theme .messages-sidebar-header {
    background-color: #0b1739 !important;
    border-bottom-color: #1b356d !important;
}

[data-bs-theme="dark"] .messages-title,
.dark-theme .messages-title {
    color: #f0f6ff !important;
}

[data-bs-theme="dark"] .conv-search-input,
.dark-theme .conv-search-input {
    background-color: #071536 !important;
    border-color: #1e3a78 !important;
    color: #f0f6ff !important;
}

[data-bs-theme="dark"] .conv-search-input::placeholder,
.dark-theme .conv-search-input::placeholder {
    color: #7ea6e0 !important;
    opacity: 0.8;
}

[data-bs-theme="dark"] .conv-item:hover,
.dark-theme .conv-item:hover {
    background-color: #112555 !important;
}

[data-bs-theme="dark"] .conv-item-active,
.dark-theme .conv-item-active {
    background-color: rgba(37, 99, 235, 0.22) !important;
}

[data-bs-theme="dark"] .conversation-header,
.dark-theme .conversation-header {
    background-color: #0b1739 !important;
    border-bottom-color: #1b356d !important;
}

[data-bs-theme="dark"] .messages-thread-area,
.dark-theme .messages-thread-area {
    background-color: #07122b !important;
}

[data-bs-theme="dark"] .date-badge,
.dark-theme .date-badge {
    background-color: #0b1739 !important;
    border-color: #1e3a78 !important;
    color: #93c5fd !important;
}

[data-bs-theme="dark"] .msg-other,
.dark-theme .msg-other {
    background-color: #12244e !important;
    color: #f1f5f9 !important;
    border-color: #1e3a78 !important;
}

[data-bs-theme="dark"] .msg-own,
.dark-theme .msg-own {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%) !important;
    color: #ffffff !important;
}

[data-bs-theme="dark"] .msg-options-btn:hover,
.dark-theme .msg-options-btn:hover {
    color: #f1f5f9 !important;
}

[data-bs-theme="dark"] .conversation-compose,
.dark-theme .conversation-compose {
    background-color: #0b1739 !important;
    border-top-color: #1b356d !important;
}

[data-bs-theme="dark"] .compose-pill-wrapper,
.dark-theme .compose-pill-wrapper {
    background-color: #071536 !important;
    border-color: #1e3a78 !important;
}

[data-bs-theme="dark"] .compose-pill-wrapper:focus-within,
.dark-theme .compose-pill-wrapper:focus-within {
    border-color: #3b82f6 !important;
}

[data-bs-theme="dark"] .conversation-input,
.dark-theme .conversation-input {
    color: #f0f6ff !important;
}

[data-bs-theme="dark"] .conversation-input::placeholder,
.dark-theme .conversation-input::placeholder {
    color: #7ea6e0 !important;
    opacity: 0.85;
}

[data-bs-theme="dark"] .messages-placeholder,
.dark-theme .messages-placeholder {
    background-color: #07122b !important;
}

@media (max-width: 767.98px) {
    .messages-main-card {
        height: calc(100vh - 140px);
        min-height: 480px;
        border-radius: 12px;
    }
    .messages-sidebar {
        width: 100% !important;
        max-width: 100% !important;
    }
}
</style>
