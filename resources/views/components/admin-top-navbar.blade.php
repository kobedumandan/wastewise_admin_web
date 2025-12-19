{{-- Admin Top Navbar Component --}}
{{-- Displays page title at the top of admin pages --}}
{{-- Usage: @include('components.admin-top-navbar', ['title' => 'Your Page Title']) --}}

@php
    $title = $title ?? 'Admin Dashboard';
@endphp

<nav class="admin-top-navbar">
    <div class="navbar-content">
        <h1 class="navbar-title">{{ $title }}</h1>
        <div class="navbar-actions">
            {{-- Notifications Bell Icon --}}
            <div class="notification-container position-relative">
                <button type="button" class="btn btn-link text-dark p-2" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none; border: none; background: none;">
                    <i class="bi bi-bell" style="font-size: 1rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;">
                        <span id="notificationCount">0</span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" id="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header">
                        <h6 class="mb-0">Notifications</h6>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li id="notificationsList">
                        <div class="px-3 py-2 text-center text-muted">
                            <small>Loading notifications...</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
.admin-top-navbar {
    background-color:rgb(255, 255, 255);
    padding: 1rem 2rem;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 999;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.navbar-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-title {
    color: linear-gradient(to bottom, #152b28 0%, #123c14 50%, #172e11 100%);
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    font-family: 'Poppins', sans-serif;
}

.navbar-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.notification-container {
    position: relative;
}

.notification-dropdown {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.read {
    opacity: 0.6;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-message {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    color: #333;
}

.notification-time {
    font-size: 0.75rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .admin-top-navbar {
        padding: 1rem 1.5rem;
    }
    
    .navbar-title {
        font-size: 1.25rem;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationCount = document.getElementById('notificationCount');
    const notificationsList = document.getElementById('notificationsList');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    let notificationCheckInterval;
    
    // Function to fetch notifications
    function fetchNotifications() {
        fetch('{{ route("get.notifications") }}')
            .then(response => response.json())
            .then(data => {
                const count = data.new_count || 0;
                const notifications = data.notifications || [];
                
                // Update badge
                if (count > 0) {
                    notificationBadge.style.display = 'block';
                    notificationCount.textContent = count > 99 ? '99+' : count;
                } else {
                    notificationBadge.style.display = 'none';
                }
                
                // Update notifications list
                updateNotificationsList(notifications);
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    }
    
    // Function to update notifications list
    function updateNotificationsList(notifications) {
        if (!notificationsList) return;
        
        if (notifications.length === 0) {
            notificationsList.innerHTML = '<div class="px-3 py-2 text-center text-muted"><small>No notifications</small></div>';
            return;
        }
        
        notificationsList.innerHTML = '';
        
        notifications.forEach(function(notification) {
            const listItem = document.createElement('li');
            listItem.className = 'notification-item' + (notification.read ? ' read' : '');
            listItem.setAttribute('data-notification-key', notification.key);
            
            const message = notification.message || 'N/A';
            const createdAt = notification.created_at ? formatDateTime(notification.created_at) : 'N/A';
            
            listItem.innerHTML = `
                <div class="notification-message">${escapeHtml(message)}</div>
                <div class="notification-time">${createdAt}</div>
            `;
            
            // Mark as read when clicked
            if (!notification.read) {
                listItem.addEventListener('click', function() {
                    markAsRead(notification.key, listItem);
                });
            }
            
            notificationsList.appendChild(listItem);
        });
    }
    
    // Function to mark notification as read
    function markAsRead(notificationKey, listItem) {
        fetch('{{ route("mark.notification.read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                notification_key: notificationKey
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                listItem.classList.add('read');
                // Refresh notifications to update count
                fetchNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    // Function to format date time
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) {
            return 'Just now';
        } else if (diffMins < 60) {
            return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
        } else if (diffHours < 24) {
            return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        } else if (diffDays < 7) {
            return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        } else {
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    }
    
    // Function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Initial fetch
    fetchNotifications();
    
    // Poll for notifications every 30 seconds
    notificationCheckInterval = setInterval(fetchNotifications, 30000);
    
    // Refresh when dropdown is opened
    if (notificationBell) {
        notificationBell.addEventListener('click', function() {
            fetchNotifications();
        });
    }
    
    // Cleanup interval on page unload
    window.addEventListener('beforeunload', function() {
        if (notificationCheckInterval) {
            clearInterval(notificationCheckInterval);
        }
    });
});
</script>
@endpush

