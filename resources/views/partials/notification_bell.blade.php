<li class="nav-item dropdown">
    <a href="#" class="navbar-nav-link dropdown-toggle caret-0" data-toggle="dropdown" id="notif-bell">
        <i class="icon-bell3"></i>
        <span class="d-md-none ml-2">Notifications</span>
        <span class="badge badge-pill bg-warning-400 ml-auto ml-md-0" id="notif-count" style="display: none;">0</span>
    </a>

    <div class="dropdown-menu dropdown-menu-right dropdown-content wmin-md-350">
        <div class="dropdown-content-header">
            <span class="font-weight-semibold">Notifications</span>
            <a href="javascript:void(0)" class="text-default" onclick="markAllAsRead()"><i class="icon-checkmark3"></i></a>
        </div>

        <div class="dropdown-content-body dropdown-scrollable" id="notif-list">
            <!-- Loaded via AJAX -->
        </div>

        <div class="dropdown-content-footer justify-content-center p-0">
            <a href="{{ route('notifications.index') }}" class="bg-light text-grey w-100 py-2" data-popup="tooltip" title="Voir toutes les notifications"><i class="icon-menu7 d-block top-0"></i></a>
        </div>
    </div>
</li>

<script>
    function loadNotifications() {
        $.get('{{ route("notifications.unread") }}', function(data) {
            let count = data.count;
            if(count > 0) {
                $('#notif-count').text(count).show();
            } else {
                $('#notif-count').hide();
            }

            let html = '';
            if(data.notifications.length > 0) {
                html += '<ul class="media-list">';
                data.notifications.forEach(function(notif) {
                    html += `
                        <li class="media">
                            <div class="mr-3">
                                <a href="${notif.link ? notif.link : '#'}" class="btn bg-transparent border-${notif.color || 'info'} text-${notif.color || 'info'} rounded-round border-2 btn-icon"><i class="${notif.icon || 'icon-info22'}"></i></a>
                            </div>
                            <div class="media-body">
                                <a href="${notif.link ? notif.link : '#'}" class="text-default font-weight-semibold">${notif.title}</a>
                                <div class="text-muted font-size-sm">${notif.message.substring(0, 50)}${notif.message.length > 50 ? '...' : ''}</div>
                            </div>
                        </li>
                    `;
                });
                html += '</ul>';
            } else {
                html = '<div class="text-center text-muted py-3">Aucune nouvelle notification</div>';
            }
            $('#notif-list').html(html);
        });
    }

    function markAllAsRead() {
        $.post('{{ route("notifications.read-all") }}', { _token: '{{ csrf_token() }}' }, function() {
            loadNotifications();
        });
    }

    $(document).ready(function() {
        loadNotifications();
        setInterval(loadNotifications, 60000); // Check every 60 seconds
    });
</script>
