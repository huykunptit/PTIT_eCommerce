<div id="notifications">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bell fa-fw"></i>
        <!-- Counter - Alerts -->
        <span class="badge badge-danger badge-counter" id="notificationBadge" style="display: none;">
            <span class="count" id="notificationCount">0</span>
        </span>
    </a>
    <!-- Dropdown - Alerts -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
        <h6 class="dropdown-header">
            Notifications Center
            <a href="{{ route('admin.notification.index') }}" class="float-right small">Xem tất cả</a>
        </h6>
        <div id="notificationsList">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
        <a class="dropdown-item text-center small text-gray-500" href="{{ route('admin.notification.index') ?? '#' }}">
            Show All Notifications
        </a>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let notificationCheckInterval;
    
    function loadNotifications() {
        $.ajax({
            url: '{{ route("admin.notifications.api") }}',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    updateNotificationBadge(response.unread_count);
                    renderNotifications(response.notifications);
                }
            },
            error: function() {
                console.error('Failed to load notifications');
            }
        });
    }
    
    function updateNotificationBadge(count) {
        const $badge = $('#notificationBadge');
        const $count = $('#notificationCount');
        
        if (count > 0) {
            $count.text(count);
            $badge.show();
        } else {
            $badge.hide();
        }
    }
    
    function renderNotifications(notifications) {
        const $list = $('#notificationsList');
        
        if (notifications.length === 0) {
            $list.html('<div class="dropdown-item text-center text-muted p-3">Không có thông báo mới</div>');
            return;
        }
        
        let html = '';
        notifications.forEach(function(notif) {
            const iconClass = notif.type === 'order' ? 'fa-shopping-cart' : 
                            notif.type === 'payment' ? 'fa-money-bill' : 
                            notif.type === 'user' ? 'fa-user' : 'fa-bell';
            const bgClass = notif.type === 'order' ? 'bg-primary' : 
                          notif.type === 'payment' ? 'bg-success' : 
                          notif.type === 'user' ? 'bg-info' : 'bg-warning';
            
            html += `
                <a class="dropdown-item d-flex align-items-center ${notif.unread ? 'bg-light' : ''}" 
                   href="${notif.url || '#'}" onclick="markAsRead('${notif.id}')">
                    <div class="mr-3">
                        <div class="icon-circle ${bgClass}">
                            <i class="fa ${iconClass} text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-gray-500">${notif.time}</div>
                        <span class="font-weight-bold">${notif.title}</span>
                        ${notif.message ? '<div class="small text-muted">' + notif.message + '</div>' : ''}
                    </div>
                    ${notif.unread ? '<span class="badge badge-danger badge-sm ml-2">Mới</span>' : ''}
                </a>
            `;
        });
        
        $list.html(html);
    }
    
    function markAsRead(notificationId) {
        $.ajax({
            url: `/admin/notifications/${notificationId}/read`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
    
    // Load notifications on page load
    loadNotifications();
    
    // Check for new notifications every 30 seconds
    notificationCheckInterval = setInterval(loadNotifications, 30000);
    
    // Also check when dropdown is opened
    $('#alertsDropdown').on('click', function() {
        loadNotifications();
    });
    
    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (notificationCheckInterval) {
            clearInterval(notificationCheckInterval);
        }
    });
});
</script>
@endpush
