<?php
// app/views/history/history.php

if (!defined('APP_INIT')) {
    header("Location: ../../public/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking History - Travelease</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="container" style="padding-top: 100px;">
        <h1 class="form-title">Your Booking History</h1>

        <!-- Display session messages -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="messageDiv" style="color: <?= $_SESSION['message_type'] === 'error' ? 'red' : 'green'; ?>;">
                <?= $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <h2>Train Bookings</h2>
        <?php if (!empty($train_bookings)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Train Number</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Date</th>
                        <th>Class</th>
                        <th>Price (USD)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($train_bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['id']) ?></td>
                            <td><?= htmlspecialchars($booking['train_number']) ?></td>
                            <td><?= htmlspecialchars($booking['origin']) ?></td>
                            <td><?= htmlspecialchars($booking['destination']) ?></td>
                            <td><?= htmlspecialchars($booking['departure_date']) ?></td>
                            <td><?= htmlspecialchars($booking['class']) ?></td>
                            <td><?= htmlspecialchars($booking['price']) ?></td>
                            <td><?= htmlspecialchars($booking['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no train bookings.</p>
        <?php endif; ?>

        <br>

        <h2>Flight Bookings</h2>
        <?php if (!empty($flight_bookings)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Flight Number</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Date</th>
                        <th>Class</th>
                        <th>Trip Type</th>
                        <th>Price (USD)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flight_bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['id']) ?></td>
                            <td><?= htmlspecialchars($booking['flight_number']) ?></td>
                            <td><?= htmlspecialchars($booking['origin']) ?></td>
                            <td><?= htmlspecialchars($booking['destination']) ?></td>
                            <td><?= htmlspecialchars($booking['departure_date']) ?></td>
                            <td><?= htmlspecialchars($booking['class']) ?></td>
                            <td><?= htmlspecialchars($booking['trip_type']) ?></td>
                            <td><?= htmlspecialchars($booking['price']) ?></td>
                            <td><?= htmlspecialchars($booking['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no flight bookings.</p>
        <?php endif; ?>

        <br>
        <a href="index.php?controller=Home&action=index" class="btn">Back to Home</a>
    </div>
</body>
</html>