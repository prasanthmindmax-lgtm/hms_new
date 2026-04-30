
<style>
.notif-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.notif-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    color: #475569;
    position: relative;
    transition: background .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}
.notif-btn:hover { background: #f1f5f9; }
.notif-badge {
    position: absolute;
    top: 2px; right: 2px;
    background: #dc2626;
    color: #fff;
    border-radius: 10px;
    font-size: 0.62rem;
    font-weight: 700;
    padding: 1px 5px;
    min-width: 16px;
    text-align: center;
    line-height: 1.4;
    display: none;
}
.notif-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 360px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,.12);
    z-index: 9999;
    display: none;
    overflow: hidden;
}
.notif-dropdown.open { display: block; }
.notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    font-size: .875rem;
    font-weight: 700;
    color: #1e293b;
}
.notif-mark-all {
    font-size: .75rem;
    font-weight: 600;
    color: #4f46e5;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
}
.notif-mark-all:hover { text-decoration: underline; }
.notif-list {
    max-height: 380px;
    overflow-y: auto;
}
.notif-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 12px 18px;
    border-bottom: 1px solid #f8fafc;
    cursor: pointer;
    transition: background .12s;
    text-decoration: none;
}
.notif-item:hover { background: #f8fafc; }
.notif-item:last-child { border-bottom: none; }
.notif-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}
.dot-overdue  { background: #dc2626; }
.dot-due_today { background: #d97706; }
.notif-content { flex: 1; min-width: 0; }
.notif-title {
    font-size: .82rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.notif-msg {
    font-size: .75rem;
    color: #64748b;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.notif-time {
    font-size: .7rem;
    color: #94a3b8;
    white-space: nowrap;
    margin-top: 3px;
}
.notif-empty {
    padding: 36px 18px;
    text-align: center;
    color: #94a3b8;
    font-size: .85rem;
}
.notif-empty i { font-size: 1.8rem; display: block; margin-bottom: 8px; opacity: .4; }
</style>

<div class="notif-wrap" id="notifWrap">
    <button class="notif-btn" id="notifBtn" title="Notifications">
        <i class="bi bi-bell-fill"></i>
        <span class="notif-badge" id="notifBadge"></span>
    </button>

    <div class="notif-dropdown" id="notifDropdown">
        <div class="notif-header">
            <span>Notifications <span id="notifHeaderCount" style="color:#94a3b8;font-weight:500;"></span></span>
            <button class="notif-mark-all" id="notifMarkAll">Mark all read</button>
        </div>
        <div class="notif-list" id="notifList">
            <div class="notif-empty"><i class="bi bi-bell-slash"></i>No new notifications</div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Explicitly set the base path with /public
    var basePath = '/hms_new/public';
    
    // Configuration
    // var POLL_INTERVAL = 3600000; 
    var UNREAD_URL    = basePath + '/notifications/unread';
    var READ_URL      = basePath + '/notifications/{id}/read';
    var READ_ALL_URL  = basePath + '/notifications/read-all';
    var CSRF          = $('meta[name="csrf-token"]').attr('content') || '';
    
    console.log('Unread URL:', UNREAD_URL); // Will show: /hms_new/public/notifications/unread
    
    var $btn     = $('#notifBtn');
    var $drop    = $('#notifDropdown');
    var $badge   = $('#notifBadge');
    var $list    = $('#notifList');
    var $hdrCnt  = $('#notifHeaderCount');
    var $markAll = $('#notifMarkAll');
    
    // Ensure we have elements before proceeding
    if (!$btn.length) {
        console.warn('Notification button not found');
        return;
    }
    
    // ── Open/close ──────────────────────────────────────────────────────────
    $btn.on('click', function (e) {
        e.stopPropagation();
        $drop.toggleClass('open');
    });
    
    $(document).on('click', function (e) {
        var $wrap = $('#notifWrap');
        if ($wrap.length && !$wrap[0].contains(e.target)) {
            $drop.removeClass('open');
        }
    });
    
    // ── Mark all read ────────────────────────────────────────────────────────
    $markAll.on('click', function () {
        $.ajax({
            url: READ_ALL_URL,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            contentType: 'application/json',
            success: function() {
                fetchNotifications();
            },
            error: function(xhr, status, error) {
                console.error('Error marking all as read:', error);
            }
        });
    });
    
    // ── Mark single read on click ────────────────────────────────────────────
    $list.on('click', '.notif-item', function (e) {
        var $item = $(this);
        var id  = $item.data('id');
        var url = $item.data('url');
        var readUrl = READ_URL.replace('{id}', id);
        
        $.ajax({
            url: readUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            success: function() {
                fetchNotifications();
                if (url && url !== '#') {
                    window.location.href = url;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
                // Still redirect even if marking failed
                if (url && url !== '#') {
                    window.location.href = url;
                }
            }
        });
    });
    
    // ── Fetch & render ────────────────────────────────────────────────────────
    function fetchNotifications() {
        $.ajax({
            url: UNREAD_URL,
            method: 'GET',
            dataType: 'json',
            headers: { 'Accept': 'application/json' },
            success: function(data) {
                render(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
                $list.html('<div class="notif-empty"><i class="bi bi-exclamation-triangle"></i>Error loading notifications</div>');
            }
        });
    }
    
    function render(data) {
        var count = data.count || 0;
        var items = data.notifications || [];
        
        // Badge
        if (count > 0) {
            $badge.text(count > 99 ? '99+' : count).show();
        } else {
            $badge.hide();
        }
        
        // Header count
        $hdrCnt.text(count > 0 ? '(' + count + ')' : '');
        
        // List
        if (!items.length) {
            $list.html('<div class="notif-empty"><i class="bi bi-bell-slash"></i>No new notifications</div>');
            return;
        }
        
        var html = '';
        $.each(items, function(index, n) {
            var dotClass = n.due_status === 'overdue' ? 'dot-overdue' : 'dot-due_today';
            var timeAgo  = formatTime(n.created_at);
            var typeIcon = n.type === 'bill' ? '📄' : n.type === 'po' ? '📦' : '📝';
            html += '<div class="notif-item" data-id="' + escapeHtml(String(n.id)) + '" data-url="' + escapeHtml(basePath+n.url || '#') + '">'
                  + '<span class="notif-dot ' + dotClass + '"></span>'
                  + '<div class="notif-content">'
                  + '<div class="notif-title">' + typeIcon + ' ' + escapeHtml(n.title) + '</div>'
                  + '<div class="notif-msg">' + escapeHtml(n.message) + '</div>'
                  + '<div class="notif-time">' + timeAgo + '</div>'
                  + '</div></div>';
        });
        $list.html(html);
    }
    
    function formatTime(ts) {
        if (!ts) return '';
        var d    = new Date(ts.replace(' ', 'T'));
        var now  = new Date();
        var diff = Math.floor((now - d) / 1000);
        if (diff < 60)   return 'just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return d.toLocaleDateString('en-IN');
    }
    
    function escapeHtml(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
    
    // ── Polling (pause when tab hidden so session idle timeout can run) ─────
    var pollTimer = null;
    function startNotifPoll() {
        stopNotifPoll();
        if (document.hidden) return;
        fetchNotifications();
        pollTimer = setInterval(function () {
            if (!document.hidden) fetchNotifications();
        }, POLL_INTERVAL);
    }
    function stopNotifPoll() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) stopNotifPoll();
        else startNotifPoll();
    });
    startNotifPoll();
});
</script>