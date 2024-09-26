<!-- resources/views/emails/ticket_closed.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Closed</title>
</head>
<body>
    <h1>Ticket Open</h1>
    <p>Hello Team,</p>
    <p>A new ID {{ $ticket->id }} has been Open by {{$ticket->user_id}}.</p>
  
</body>
</html>