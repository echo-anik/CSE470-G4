<?php
// public/payment.php

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Page - Travelease</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include '../app/views/navbar.php'; ?>

    <div class="container" style="padding-top: 100px;">
        <h1 class="form-title">Payment Page</h1>
        <p>This is a placeholder for the payment processing page.</p>
        <p>After completing payment, your booking will be confirmed.</p>
        <a href="index.php?controller=Home&action=index" class="btn">Back to Home</a>
    </div>
</body>
</html>