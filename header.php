<?php
// /includes/header.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connection.php';

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    $projectFolder = '/travelease'; // Adjust this to your project folder
    return $protocol . $domainName . $projectFolder;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelEase - Your Travel Companion</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/assets/css/style.css">
</head>
<body>
<!-- includes/header.php -->
    <nav class="navbar">
        <a href="index.php" class="logo">TravelEase</a>
        <div class="nav-links">
            <div class="dropdown">
                <button class="dropbtn">Hotels</button>
                <div class="dropdown-content">
                    <a href="hotel/hotel.php"><i class="fas fa-hotel"></i> All Hotels</a>
                    <a href="hotel/hotel.php?type=luxury"><i class="fas fa-star"></i> Luxury Hotels</a>
                    <a href="hotel/hotel.php?type=business"><i class="fas fa-building"></i> Business Hotels</a>
                    <a href="hotel/hotel.php?type=budget"><i class="fas fa-bed"></i> Budget Hotels</a>
                </div>
            </div>
            
            <div class="dropdown">
                <button class="dropbtn">Transport</button>
                <div class="dropdown-content">
                    <a href="bus/trial.php"><i class="fas fa-bus"></i> Bus</a>
                    <a href="bus/bus.php"><i class="fas fa-plane"></i> Flights</a>
                    <a href="train/train.php"><i class="fas fa-train"></i> Train</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Tour Guides</button>
                <div class="dropdown-content">
                    <a href="guides/guide_home_page.php"><i class="fas fa-map-marked-alt"></i> All Guides</a>
                    <a href="guides/guide_home_page.php"><i class="fas fa-street-view"></i> Local Experts</a>
                    <a href="guides/guide_home_page.php"><i class="fas fa-mountain"></i> Adventure Guides</a>
                    <a href="guides/guide_home_page.php"><i class="fas fa-landmark"></i> Cultural Tours</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Packages</button>
                <div class="dropdown-content">
                    <a href="packages.php"><i class="fas fa-suitcase"></i> All Packages</a>
                    <a href="packages.php?type=holiday"><i class="fas fa-umbrella-beach"></i> Holiday</a>
                    <a href="packages.php?type=adventure"><i class="fas fa-hiking"></i> Adventure</a>
                    <a href="packages.php?type=family"><i class="fas fa-users"></i> Family</a>
                </div>
            </div>
        </div>

        <div class="auth-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="dropdown">
                    <button class="dropbtn">
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </button>
                    <div class="dropdown-content">
                        <a href="user/profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="user/wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                        <a href="user/bookings.php"><i class="fas fa-history"></i> Bookings</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button class="btn login-btn" onclick="openModal('loginModal')">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button class="btn admin-btn" onclick="openModal('adminModal')">
                    <i class="fas fa-user-shield"></i> Admin
                </button>
            <?php endif; ?>
        </div>
    </nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current page URL
    const currentPage = window.location.pathname.split('/').pop();
    
    // Add active class to current page link
    document.querySelectorAll('.nav-links a').forEach(link => {
        if(link.getAttribute('href').includes(currentPage)) {
            link.classList.add('active');
        }
    });
});
</script>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('loginModal')">&times;</span>
            <h2>Login</h2>
            <form action="<?php echo getBaseUrl(); ?>/login_handler.php" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>

    <!-- Admin Modal -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('adminModal')">&times;</span>
            <h2>Admin Login</h2>
            <form action="<?php echo getBaseUrl(); ?>/admin_handler.php" method="POST">
                <label for="admin_email">Email:</label>
                <input type="email" id="admin_email" name="admin_email" required>
                
                <label for="admin_password">Password:</label>
                <input type="password" id="admin_password" name="admin_password" required>
                
                <button type="submit" name="admin_login">Login</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>