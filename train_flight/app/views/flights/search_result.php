<?php
// app/views/flights/search_results.php

if (!defined('APP_INIT')) {
    header("Location: ../../public/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results - Travelease</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="container" style="padding-top: 100px;">
        <h1 class="form-title">Available Flights</h1>

        <!-- Display session messages -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="messageDiv" style="color: <?= $_SESSION['message_type'] === 'error' ? 'red' : 'green'; ?>;">
                <?= $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($flights)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Flight Number</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Departure Date</th>
                        <th>Class</th>
                        <th>Trip Type</th>
                        <th>Price (USD)</th>
                        <th>Available Seats</th>
                        <th>Picture</th>
                        <th>Wishlist</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flights as $flight): ?>
                        <tr>
                            <td><?= htmlspecialchars($flight['id']) ?></td>
                            <td><?= htmlspecialchars($flight['flight_number']) ?></td>
                            <td><?= htmlspecialchars($flight['origin']) ?></td>
                            <td><?= htmlspecialchars($flight['destination']) ?></td>
                            <td><?= htmlspecialchars($flight['departure_time']) ?></td>
                            <td><?= htmlspecialchars($flight['arrival_time']) ?></td>
                            <td><?= htmlspecialchars($flight['departure_date']) ?></td>
                            <td><?= htmlspecialchars($flight['class']) ?></td>
                            <td><?= htmlspecialchars($flight['trip_type']) ?></td>
                            <td><?= htmlspecialchars($flight['price']) ?></td>
                            <td><?= htmlspecialchars($flight['available_seats']) ?></td>
                            <td>
                                <img src="../../public/images/flights/<?= htmlspecialchars($flight['flight_number']) ?>.jpg" alt="Flight Image" width="100">
                            </td>
                            <td>
                                <a href="index.php?controller=Flight&action=addToWishlist&id=<?= $flight['id'] ?>">Add to Wishlist</a>
                            </td>
                            <td>
                                <?php if ($flight['available_seats'] > 0): ?>
                                    <a href="index.php?controller=Flight&action=book&id=<?= $flight['id'] ?>">Book</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No flights match your search criteria.</p>
        <?php endif; ?>

        <br>
        <a href="index.php?controller=Flight&action=index" class="btn">Back to Search</a>
    </div>
</body>
</html>
