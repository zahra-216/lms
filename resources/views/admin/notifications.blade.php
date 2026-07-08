<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   
<h1>Notifications</h1>
<ul>
@foreach($notifications as $notif)
    <li>{{ $notif->data['message'] ?? 'New notification' }} - {{ $notif->created_at->diffForHumans() }}</li>
@endforeach
</ul>

</body>
</html>