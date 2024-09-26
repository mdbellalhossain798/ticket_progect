<!-- resources/views/emails/ticket_closed.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Closed</title>
</head>
<body>
    <h1>Ticket Closed</h1>
    <p>Hello Sir,</p>
    <p>Your ticket with ID {{ $ticket->id }} has been successfully closed by our team.</p>
    <p>Thank you for using our services!</p>
</body>
</html>