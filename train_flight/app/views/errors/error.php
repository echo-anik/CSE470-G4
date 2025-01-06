<?php
// app/views/errors/error.php

if (!defined('APP_INIT')) {
    header("Location: ../train_flight/public/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error - Travelease</title>
    <link rel="stylesheet" href="../train_flight/public/css/styles.css">
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="container" style="padding-top: 100px;">
        <h1 class="form-title">Error</h1>
        <p class="messageDiv" style="color: red;">
            <?= isset($_SESSION['message']) ? htmlspecialchars($_SESSION['message']) : 'An unexpected error occurred.' ?>
        </p>
        <br>
        <a href="index.php?controller=Home&action=index" class="btn">Back to Home</a>
    </div>
</body>
</html>
