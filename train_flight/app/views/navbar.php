<?php
// app/views/navbar.php
?>
<nav class="navbar">
    <div class="logo">Travelease</div>
    <div class="nav-links">
        <a href="index.php?controller=Home&action=index">Home</a>
        <a href="index.php?controller=Train&action=index">Trains</a>
        <a href="index.php?controller=Flight&action=index">Flights</a>
        <a href="index.php?controller=History&action=index"><i class="fas fa-history"></i> History</a> <!-- Added History Icon -->
        <!-- Add more links as needed -->
    </div>
    <div class="auth-buttons">
        <?php if(isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn">Login</a>
        <?php endif; ?>
    </div>
</nav>