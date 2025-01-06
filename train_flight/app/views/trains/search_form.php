<?php
// app/views/trains/search_form.php

// Protect against direct access
if (!defined('APP_INIT')) {
    header("Location: ../../public/index.php");
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
    <title>Search Trains - Travelease</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="container" style="padding-top: 100px;">
        <h1 class="form-title">Search Trains</h1>

        <!-- Display session messages -->
        <?php if(isset($_SESSION['message'])): ?>
            <div class="messageDiv" style="color: <?= $_SESSION['message_type'] === 'error' ? 'red' : 'green'; ?>;">
                <?= $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="Train">
            <input type="hidden" name="action" value="search">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="input-group">
               <i class="fas fa-map-marker-alt"></i>
               <input type="text" name="origin" placeholder="From" required>
               <label for="origin">From</label>
            </div>

            <div class="input-group">
               <i class="fas fa-map-marker-alt"></i>
               <input type="text" name="destination" placeholder="To" required>
               <label for="destination">To</label>
            </div>

            <div class="input-group">
               <i class="fas fa-calendar-alt"></i>
               <input type="date" name="departure_date" placeholder="Departure Date" required>
               <label for="departure_date">Departure Date</label>
            </div>

            <div class="input-group">
               <i class="fas fa-clock"></i>
               <input type="time" name="departure_time" placeholder="Departure Time" required>
               <label for="departure_time">Departure Time</label>
            </div>

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

            <button class="btn">Search</button>
        </form>

        <br>
        <a href="index.php?controller=Train&action=bookingHistory" class="btn">View Booking History</a>
    </div>
</body>
</html>