<?php
// app/views/flights/booking.php

if (!defined('APP_INIT')) {
    header("Location: ../train_flight/public/index.php");
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Flight - Travelease</title>
    <link rel="stylesheet" href="../train_flight/public/css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include 'train_flight/navbar.php'; ?>

    <div class="container" style="padding-top: 100px;">
        <h1 class="form-title">Book Flight</h1>

        <!-- Display session messages -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="messageDiv" style="color: <?= $_SESSION['message_type'] === 'error' ? 'red' : 'green'; ?>;">
                <?= $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=Flight&action=confirmBooking">
            <input type="hidden" name="flight_id" value="<?= htmlspecialchars($flight['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <h2>Flight Details</h2>
            <p><strong>Flight Number:</strong> <?= htmlspecialchars($flight['flight_number']) ?></p>
            <p><strong>Origin:</strong> <?= htmlspecialchars($flight['origin']) ?></p>
            <p><strong>Destination:</strong> <?= htmlspecialchars($flight['destination']) ?></p>
            <p><strong>Departure Time:</strong> <?= htmlspecialchars($flight['departure_time']) ?></p>
            <p><strong>Arrival Time:</strong> <?= htmlspecialchars($flight['arrival_time']) ?></p>
            <p><strong>Departure Date:</strong> <?= htmlspecialchars($flight['departure_date']) ?></p>
            <p><strong>Class:</strong> <?= htmlspecialchars($flight['class']) ?></p>
            <p><strong>Trip Type:</strong> <?= htmlspecialchars($flight['trip_type']) ?></p>
            <p><strong>Price:</strong> <?= htmlspecialchars($flight['price']) ?> USD</p>
            <p><strong>Available Seats:</strong> <?= htmlspecialchars($flight['available_seats']) ?></p>

            <h2>Select Class</h2>
            <div class="input-group">
                <i class="fas fa-clipboard-list"></i>
                <select name="class" required>
                    <option value="">Select Class</option>
                    <option value="Economy">Economy</option>
                    <option value="Business">Business</option>
                    <option value="First">First</option>
                </select>
                <label for="class">Class</label>
            </div>

            <h2>Payment Method</h2>
            <div class="input-group">
                <i class="fas fa-credit-card"></i>
                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
                <label for="payment_method">Payment Method</label>
            </div>

            <h2>Trip Type</h2>
            <div class="input-group">
                <i class="fas fa-exchange-alt"></i>
                <select name="trip_type" required>
                    <option value="">Select Trip Type</option>
                    <option value="One Way">One Way</option>
                    <option value="Round Trip">Round Trip</option>
                </select>
                <label for="trip_type">Trip Type</label>
            </div>

            <h2>Age Category</h2>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <select name="age" required>
                    <option value="">Select Age Category</option>
                    <option value="Minor">Minor</option>
                    <option value="Adult">Adult</option>
                    <option value="Senior">Senior Citizen</option>
                </select>
                <label for="age">Age Category</label>
            </div>

            <br>
            <!-- Confirm Booking Button -->
            <button type="submit" class="btn">Confirm Booking</button>
        </form>

        <br>
        <a href="index.php?controller=Flight&action=search" class="btn">Back to Search Results</a>
    </div>
</body>
</html>