<?php
// app/views/trains/search_results.php

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
        <h1 class="form-title">Available Trains</h1>

        <!-- Display session messages -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="messageDiv" style="color: <?= $_SESSION['message_type'] === 'error' ? 'red' : 'green'; ?>;">
                <?= $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($trains)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Train Number</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Departure Date</th>
                        <th>Class</th>
                        <th>Price (USD)</th>
                        <th>Available Seats</th>
                        <th>Picture</th>
                        <th>Wishlist</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trains as $train): ?>
                        <tr>
                            <td><?= htmlspecialchars($train['id']) ?></td>
                            <td><?= htmlspecialchars($train['train_number']) ?></td>
                            <td><?= htmlspecialchars($train['origin']) ?></td>
                            <td><?= htmlspecialchars($train['destination']) ?></td>
                            <td><?= htmlspecialchars($train['departure_time']) ?></td>
                            <td><?= htmlspecialchars($train['arrival_time']) ?></td>
                            <td><?= htmlspecialchars($train['departure_date']) ?></td>
                            <td><?= htmlspecialchars($train['class']) ?></td>
                            <td><?= htmlspecialchars($train['price']) ?></td>
                            <td><?= htmlspecialchars($train['available_seats']) ?></td>
                            <td>
                                <img src="../../public/images/trains/<?= htmlspecialchars($train['train_number']) ?>.jpg" alt="Train Image" width="100">
                            </td>
                            <td>
                                <a href="index.php?controller=Train&action=addToWishlist&id=<?= $train['id'] ?>">Add to Wishlist</a>
                            </td>
                            <td>
                                <?php if ($train['available_seats'] > 0): ?>
                                    <a href="index.php?controller=Train&action=book&id=<?= $train['id'] ?>">Book</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No trains match your search criteria.</p>
        <?php endif; ?>

        <br>
        <a href="index.php?controller=Train&action=index" class="btn">Back to Search</a>
    </div>
</body>
</html>