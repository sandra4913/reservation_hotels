<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <link href='style.css' rel='stylesheet'>

</head>

<body>
    <?php
    require('class/Booking.php');
    $booking = new Booking;
    $booking->setBooking();
    ?>

</body>

</html>