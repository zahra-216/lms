<!-- Bell Icon in navbar -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span id="unreadCount" class="badge bg-danger">0</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notificationList">
        <li class="text-center">No notifications</li>
    </ul>
</li>

<!-- Scripts -->
<script src="https://js.pusher.com/8.0/pusher.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    const userId = @json(auth()->id());

    // Enable pusher logging - optional for debugging
    Pusher.logToConsole = false;

    const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true
    });

    const channel = pusher.subscribe('private-user.' + userId);

    channel.bind('notification.sent', function(data) {
        console.log('New notification:', data);

        // Update unread count
        let countElem = document.getElementById('unreadCount');
        let count = parseInt(countElem.innerText) || 0;
        countElem.innerText = count + 1;

        // Prepend notification to list
        const list = document.getElementById('notificationList');
        const li = document.createElement('li');
        li.innerHTML = `<a class="dropdown-item" href="${data.link}"><strong>${data.title}</strong><br>${data.message}</a>`;
        list.prepend(li);
    });

    // Optional: Load unread notifications on page load
    fetch('/notifications/unread')
        .then(res => res.json())
        .then(data => {
            document.getElementById('unreadCount').innerText = data.unread_count;
            const list = document.getElementById('notificationList');
            list.innerHTML = '';
            if (data.notifications.length === 0) {
                list.innerHTML = '<li class="text-center">No notifications</li>';
            } else {
                data.notifications.forEach(n => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item" href="${n.link}"><strong>${n.title}</strong><br>${n.message}</a>`;
                    list.appendChild(li);
                });
            }
        });
</script>