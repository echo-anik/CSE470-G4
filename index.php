<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'travelease');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Error handling middleware
function handleError($message, $type = 'error') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Login handler
if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            handleError('Login successful!', 'success');
            header('Location: index.php');
            exit();
        }
    }
    handleError('Invalid email or password');
}

// Admin login handler
if (isset($_POST['admin_login'])) {
    $email = $conn->real_escape_string($_POST['admin_email']);
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT admin_id, name FROM admin WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        handleError('Admin login successful!', 'success');
        header('Location: admin/dashboard.php');
        exit();
    }
    handleError('Invalid admin credentials');
}

// Fetch data for display
$featured_packages = $conn->query("SELECT * FROM travel_packages WHERE status = 'active' LIMIT 6");
$hotels = $conn->query("SELECT * FROM hotel_booking WHERE status = 'available' LIMIT 10");
$guides = $conn->query("SELECT * FROM guide_registration LIMIT 10");
$transports = $conn->query("SELECT * FROM transport LIMIT 10");

// Search functionality
if (isset($_GET['search'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $location = $conn->real_escape_string($_GET['location']);
    $date = $conn->real_escape_string($_GET['date']);
    $price_range = isset($_GET['price_range']) ? explode('-', $_GET['price_range']) : null;
    
    // Build search query based on category
    $search_results = [];
    switch($category) {
        case 'hotels':
            $query = "SELECT * FROM hotel_booking WHERE status = 'available'";
            if ($location) $query .= " AND hotelID LIKE '%$location%'";
            if ($date) $query .= " AND checkInDate <= '$date' AND checkOutDate >= '$date'";
            if ($price_range) $query .= " AND price BETWEEN {$price_range[0]} AND {$price_range[1]}";
            $search_results = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
            break;
        // Add cases for other categories
    }
}

// Wishlist functionality
if (isset($_POST['toggle_wishlist'])) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $item_id = (int)$_POST['item_id'];
    $item_type = $conn->real_escape_string($_POST['item_type']);

    // Check if item exists in wishlist
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND item_id = ? AND item_type = ?");
    $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        // Remove from wishlist
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND item_id = ? AND item_type = ?");
        $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    } else {
        // Add to wishlist
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, item_id, item_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $item_id, $item_type);
    }
    
    $result = $stmt->execute();
    echo json_encode(['success' => $result]);
    exit();
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
    <style>
        :root {
            --color-primary: #EDC7B7;
            --color-secondary: #EEE2DC;
            --color-tertiary: #BAB2B5;
            --color-dark: #123C69;
            --color-accent: #AC3B61;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--color-secondary);
            line-height: 1.6;
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(18, 60, 105, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(237, 199, 183, 0.1);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            color: var(--color-primary);
            font-size: 2rem;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            color: var(--color-primary);
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: color 0.3s;
        }

        .dropbtn:hover {
            color: var(--color-accent);
        }

        .dropdown-content {
            position: absolute;
            background: rgba(18, 60, 105, 0.95);
            backdrop-filter: blur(10px);
            min-width: 200px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.5rem 0;
            transform: translateY(10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-content {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .dropdown-content a {
            color: var(--color-primary);
            padding: 0.8rem 1.5rem;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: rgba(237, 199, 183, 0.1);
        }

        /* Auth Buttons */
        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .login-btn {
            background: transparent;
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
        }

        .login-btn:hover {
            background: var(--color-primary);
            color: var(--color-dark);
        }

        .admin-btn {
            background: var(--color-accent);
            color: white;
        }

        .admin-btn:hover {
            background: #963351;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(18, 60, 105, 0.7), rgba(18, 60, 105, 0.7)),
                        url('images/hero-bg.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding-top: 80px;
        }

        .hero-content {
            max-width: 800px;
            padding: 2rem;
        }

        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInUp 0.6s ease-out;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out 0.2s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        /* Search Section */
        .search-section {
            margin-top: -50px;
            padding: 0 5%;
            position: relative;
            z-index: 2;
        }

        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
        }

        .search-input input,
        .search-input select {
            width: 100%;
            padding: 1rem;
            border: 2px solid transparent;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .search-input input:focus,
        .search-input select:focus {
            border-color: var(--color-accent);
            outline: none;
            box-shadow: 0 0 0 4px rgba(172, 59, 97, 0.1);
        }

        .search-btn {
            background: var(--color-accent);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: #963351;
            transform: translateY(-2px);
        }

        /* Category Sections */
        .category-section {
            padding: 4rem 5%;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--color-dark);
            margin-bottom: 2rem;
            position: relative;
            animation: fadeInUp 0.6s ease-out;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--color-accent);
        }

                /* Product Cards */
                .scroll-container {
            display: flex;
            gap: 2rem;
            overflow-x: auto;
            padding: 1rem 0;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: var(--color-accent) var(--color-secondary);
            -ms-overflow-style: none;
        }

        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }

        .scroll-container::-webkit-scrollbar-track {
            background: var(--color-secondary);
            border-radius: 4px;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background: var(--color-accent);
            border-radius: 4px;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            width: 300px;
            flex-shrink: 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            animation: fadeInUp 0.6s ease-out;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 50%, rgba(0, 0, 0, 0.7));
        }

        .product-details {
            padding: 1.5rem;
        }

        .product-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-title {
            font-size: 1.2rem;
            color: var(--color-dark);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .product-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-price {
            font-size: 1.3rem;
            color: var(--color-accent);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .price-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: normal;
        }

        .duration {
            font-size: 0.9rem;
            color: #666;
            font-weight: normal;
        }

        .product-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .wishlist-btn {
            background: transparent;
            border: 2px solid var(--color-accent);
            color: var(--color-accent);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .wishlist-btn:hover,
        .wishlist-btn.active {
            background: var(--color-accent);
            color: white;
        }

        .book-btn {
            flex: 1;
            background: var(--color-accent);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .book-btn:hover {
            background: #963351;
            transform: translateY(-2px);
        }

        /* Badge Styles */
        .badge {
            background: var(--color-dark);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            display: inline-block;
            margin-right: 0.5rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1100;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: var(--color-secondary);
            width: 90%;
            max-width: 500px;
            margin: 10vh auto;
            border-radius: 15px;
            padding: 2rem;
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        .modal-header {
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            color: var(--color-dark);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: var(--color-dark);
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: var(--color-accent);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--color-dark);
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid transparent;
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--color-accent);
            outline: none;
            box-shadow: 0 0 0 4px rgba(172, 59, 97, 0.1);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { 
                transform: translateY(-20px);
                opacity: 0;
            }
            to { 
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading Animation */
        .loading {
            width: 24px;
            height: 24px;
            border: 3px solid var(--color-secondary);
            border-top: 3px solid var(--color-accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }

            .nav-links {
                display: none;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .search-container {
                flex-direction: column;
            }

            .search-input {
                width: 100%;
            }

            .product-card {
                width: 280px;
            }
        }

        /* Footer Styles */
        .footer {
            background: var(--color-dark);
            color: var(--color-secondary);
            padding: 3rem 5%;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            gap: 2rem;
        }

        .footer-info h3 {
            font-family: 'Playfair Display', serif;
            color: var(--color-primary);
            margin-bottom: 1.5rem;
        }

        .contact-info {
            list-style: none;
        }

        .contact-info li {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            color: var(--color-primary);
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: var(--color-accent);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">TravelEase</div>
        <div class="nav-links">
            <div class="dropdown">
                <button class="dropbtn">Hotels</button>
                <div class="dropdown-content">
                    <a href="#"><i class="fas fa-hotel"></i> Luxury Hotels</a>
                    <a href="#"><i class="fas fa-building"></i> Business Hotels</a>
                    <a href="#"><i class="fas fa-spa"></i> Resort & Spa</a>
                    <a href="#"><i class="fas fa-bed"></i> Budget Hotels</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Transport</button>
                <div class="dropdown-content">
                    <a href="#"><i class="fas fa-plane"></i> Flights</a>
                    <a href="#"><i class="fas fa-train"></i> Trains</a>
                    <a href="#"><i class="fas fa-bus"></i> Buses</a>
                    <a href="#"><i class="fas fa-car"></i> Car Rentals</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Tour Guides</button>
                <div class="dropdown-content">
                    <a href="#"><i class="fas fa-map-marked-alt"></i> Local Experts</a>
                    <a href="#"><i class="fas fa-mountain"></i> Adventure Guides</a>
                    <a href="#"><i class="fas fa-landmark"></i> Cultural Tours</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Packages</button>
                <div class="dropdown-content">
                    <a href="#"><i class="fas fa-umbrella-beach"></i> Holiday Packages</a>
                    <a href="#"><i class="fas fa-hiking"></i> Adventure Packages</a>
                    <a href="#"><i class="fas fa-users"></i> Family Packages</a>
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
                        <a href="#"><i class="fas fa-user"></i> Profile</a>
                        <a href="#"><i class="fas fa-heart"></i> Wishlist</a>
                        <a href="#"><i class="fas fa-history"></i> Bookings</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button class="btn login-btn" onclick="openModal('login')">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button class="btn admin-btn" onclick="openModal('admin')">
                    <i class="fas fa-user-shield"></i> Admin
                </button>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover Your Next Adventure</h1>
            <p>Find and book the best travel experiences worldwide</p>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="search-container">
            <div class="search-input">
                <select id="category-select">
                    <option value="">Select Category</option>
                    <option value="hotels">Hotels</option>
                    <option value="flights">Flights</option>
                    <option value="trains">Trains</option>
                    <option value="buses">Buses</option>
                    <option value="guides">Tour Guides</option>
                </select>
            </div>
            <div class="search-input">
                <input type="text" id="location-input" placeholder="Where to?">
            </div>
            <div class="search-input">
                <input type="date" id="date-input">
            </div>
            <div class="search-input">
                <select id="price-range">
                    <option value="">Price Range</option>
                    <option value="0-100">Under $100</option>
                    <option value="100-300">$100 - $300</option>
                    <option value="300-500">$300 - $500</option>
                    <option value="500+">$500+</option>
                </select>
            </div>
            <button class="btn search-btn">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </section>

    <!-- Featured Packages Section -->
    <section class="category-section">
        <h2 class="section-title">Featured Travel Packages</h2>
        <div class="scroll-container">
            <?php while($package = $featured_packages->fetch_assoc()): ?>
                <div class="product-card" data-id="<?php echo $package['package_id']; ?>" data-type="package">
                    <div class="product-image" style="background-image: url('images/packages/<?php echo $package['package_id']; ?>.jpg')">
                        <div class="product-status">
                            <span class="badge">Featured</span>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($package['package_name']); ?></h3>
                        <p class="product-description">
                            <?php echo htmlspecialchars(substr($package['description'], 0, 100)); ?>...
                        </p>
                        <div class="product-info">
                            <span><i class="fas fa-clock"></i> <?php echo $package['duration_days']; ?> Days</span>
                            <span><i class="fas fa-users"></i> Max <?php echo $package['max_people']; ?> People</span>
                        </div>
                        <div class="product-price">
                            <span class="price-label">From</span>
                            <span class="amount">$<?php echo number_format($package['total_price'], 2); ?></span>
                            <span class="duration">/person</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $package['package_id']; ?>, 'package')">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="btn book-btn" onclick="openBookingModal(<?php echo $package['package_id']; ?>, 'package')">
                                <i class="fas fa-bookmark"></i> Book Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Hotels Section -->
    <section class="category-section">
        <h2 class="section-title">Popular Hotels</h2>
        <div class="scroll-container">
            <?php while($hotel = $hotels->fetch_assoc()): ?>
                <div class="product-card" data-id="<?php echo $hotel['booking_id']; ?>" data-type="hotel">
                    <div class="product-image" style="background-image: url('images/hotels/<?php echo $hotel['hotelID']; ?>.jpg')">
                        <div class="product-status">
                            <span class="badge">Available</span>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($hotel['hotelID']); ?></h3>
                        <div class="product-info">
                            <span><i class="fas fa-map-marker-alt"></i> Location</span>
                            <span><i class="fas fa-star"></i> 4.5</span>
                        </div>
                        <div class="product-price">
                            <span class="price-label">From</span>
                            <span class="amount">$<?php echo number_format($hotel['price'], 2); ?></span>
                            <span class="duration">/night</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $hotel['booking_id']; ?>, 'hotel')">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="btn book-btn" onclick="openBookingModal(<?php echo $hotel['booking_id']; ?>, 'hotel')">
                                <i class="fas fa-bookmark"></i> Book Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <!-- Transport Section -->
    <section class="category-section">
        <h2 class="section-title">Transport Options</h2>
        <div class="scroll-container">
            <?php while($transport = $transports->fetch_assoc()): ?>
                <div class="product-card" data-id="<?php echo $transport['transport_id']; ?>" data-type="transport">
                    <div class="product-image" style="background-image: url('images/transport/<?php echo strtolower($transport['transport_type']); ?>.jpg')">
                        <div class="product-status">
                            <?php 
                            $icon = match($transport['transport_type']) {
                                'Flight' => 'plane',
                                'Train' => 'train',
                                'Bus' => 'bus',
                                'Cab' => 'taxi',
                                default => 'car'
                            };
                            ?>
                            <span class="badge">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                                <?php echo htmlspecialchars($transport['transport_type']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title">
                            <?php echo htmlspecialchars($transport['locations']); ?>
                        </h3>
                        <div class="product-info">
                            <span>
                                <i class="far fa-clock"></i>
                                <?php echo htmlspecialchars($transport['schedule']); ?>
                            </span>
                            <span>
                                <i class="fas fa-couch"></i>
                                <?php echo htmlspecialchars($transport['class']); ?>
                            </span>
                        </div>
                        <div class="transport-details">
                            <div class="route-info">
                                <div class="departure">
                                    <i class="fas fa-circle"></i>
                                    <span><?php echo explode(' - ', $transport['locations'])[0]; ?></span>
                                </div>
                                <div class="route-line"></div>
                                <div class="arrival">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo explode(' - ', $transport['locations'])[1]; ?></span>
                                </div>
                            </div>
                            <div class="schedule-info">
                                <span>
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('M d, Y', strtotime($transport['date'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="product-price">
                            <span class="price-label">From</span>
                            <span class="amount">$<?php echo number_format($transport['fare'], 2); ?></span>
                            <span class="duration">/person</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $transport['transport_id']; ?>, 'transport')">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="btn book-btn" onclick="openBookingModal(<?php echo $transport['transport_id']; ?>, 'transport')">
                                <i class="fas fa-ticket-alt"></i> Book Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Tour Guides Section -->
    <section class="category-section">
        <h2 class="section-title">Expert Tour Guides</h2>
        <div class="scroll-container">
            <?php while($guide = $guides->fetch_assoc()): ?>
                <div class="product-card" data-id="<?php echo $guide['id']; ?>" data-type="guide">
                    <div class="product-image" style="background-image: url('images/guides/<?php echo $guide['id']; ?>.jpg')">
                        <div class="product-status">
                            <span class="badge">
                                <i class="fas fa-certificate"></i>
                                <?php echo htmlspecialchars($guide['experience']); ?>+ Years
                            </span>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($guide['name']); ?></h3>
                        <div class="guide-info">
                            <span>
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($guide['living_city'] . ', ' . $guide['living_country']); ?>
                            </span>
                            <span>
                                <i class="fas fa-language"></i>
                                <?php 
                                $languages = explode(',', $guide['language_proficiency']);
                                echo count($languages) . ' Languages';
                                ?>
                            </span>
                        </div>
                        <div class="guide-specialization">
                            <?php 
                            $specializations = explode(',', $guide['role']);
                            foreach($specializations as $specialization): ?>
                                <span class="badge"><?php echo trim($specialization); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="guide-languages">
                            <p class="languages-title">Languages:</p>
                            <div class="language-list">
                                <?php foreach($languages as $language): ?>
                                    <span class="language-badge"><?php echo trim($language); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="guide-rating">
                            <?php
                            // Fetch guide's average rating
                            $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM guide_reviews WHERE guide_id = ?");
                            $stmt->bind_param("i", $guide['id']);
                            $stmt->execute();
                            $rating_result = $stmt->get_result()->fetch_assoc();
                            $avg_rating = round($rating_result['avg_rating'], 1);
                            ?>
                            <div class="stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if($i <= $avg_rating): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif($i - 0.5 <= $avg_rating): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span class="rating-number"><?php echo $avg_rating; ?></span>
                            </div>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $guide['id']; ?>, 'guide')">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="btn book-btn" onclick="openContactModal(<?php echo $guide['id']; ?>)">
                                <i class="fas fa-comments"></i> Contact Guide
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-info">
                <h3>TravelEase</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        123 Travel Street, City, Country
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        contact@travelease.com
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        +1 234 567 890
                    </li>
                </ul>
            </div>
            <div class="footer-info">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>
        <!-- Login Modal -->
        <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('loginModal')">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title">Welcome Back</h2>
                <p class="modal-subtitle">Login to your account</p>
            </div>
            <form id="loginForm" method="POST" onsubmit="return handleLogin(event)">
                <div class="form-group">
                    <label for="login-email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input 
                        type="email" 
                        id="login-email" 
                        name="email" 
                        required 
                        autocomplete="email"
                        placeholder="Enter your email"
                    >
                </div>
                <div class="form-group">
                    <label for="login-password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="login-password" 
                            name="password" 
                            required
                            placeholder="Enter your password"
                        >
                        <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('login-password')"></i>
                    </div>
                </div>
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <div class="form-message"></div>
            </form>
            <div class="modal-footer">
                <p>Don't have an account? <a href="#" onclick="switchModal('loginModal', 'registerModal')">Register</a></p>
            </div>
        </div>
    </div>

    <!-- Admin Login Modal -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('adminModal')">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title">Admin Login</h2>
                <p class="modal-subtitle">Access admin dashboard</p>
            </div>
            <form id="adminLoginForm" method="POST" onsubmit="return handleAdminLogin(event)">
                <div class="form-group">
                    <label for="admin-email">
                        <i class="fas fa-envelope"></i> Admin Email
                    </label>
                    <input 
                        type="email" 
                        id="admin-email" 
                        name="admin_email" 
                        required 
                        autocomplete="email"
                        placeholder="Enter admin email"
                    >
                </div>
                <div class="form-group">
                    <label for="admin-password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="admin-password" 
                            name="admin_password" 
                            required
                            placeholder="Enter admin password"
                        >
                        <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('admin-password')"></i>
                    </div>
                </div>
                <button type="submit" name="admin_login" class="btn btn-primary btn-block">
                    <i class="fas fa-user-shield"></i> Login as Admin
                </button>
                <div class="form-message"></div>
            </form>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('bookingModal')">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title">Book Now</h2>
                <p class="modal-subtitle">Complete your booking</p>
            </div>
            <form id="bookingForm" method="POST" onsubmit="return handleBooking(event)">
                <input type="hidden" id="booking-item-id" name="item_id">
                <input type="hidden" id="booking-item-type" name="item_type">
                
                <div class="booking-summary">
                    <h3>Booking Details</h3>
                    <div id="booking-details"></div>
                </div>

                <div class="form-group">
                    <label for="booking-date">
                        <i class="fas fa-calendar"></i> Date
                    </label>
                    <input 
                        type="date" 
                        id="booking-date" 
                        name="booking_date" 
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="booking-guests">
                        <i class="fas fa-users"></i> Number of Guests
                    </label>
                    <input 
                        type="number" 
                        id="booking-guests" 
                        name="guests" 
                        required
                        min="1"
                        max="10"
                        value="1"
                        onchange="updateBookingPrice()"
                    >
                </div>

                <div class="form-group">
                    <label for="booking-requests">
                        <i class="fas fa-comment"></i> Special Requests
                    </label>
                    <textarea 
                        id="booking-requests" 
                        name="special_requests"
                        rows="3"
                        placeholder="Any special requirements?"
                    ></textarea>
                </div>

                <div class="price-summary">
                    <div class="price-row">
                        <span>Base Price:</span>
                        <span id="base-price">$0</span>
                    </div>
                    <div class="price-row">
                        <span>Taxes & Fees:</span>
                        <span id="taxes">$0</span>
                    </div>
                    <div class="price-row total">
                        <span>Total:</span>
                        <span id="total-price">$0</span>
                    </div>
                </div>

                <button type="submit" name="make_booking" class="btn btn-primary btn-block">
                    <i class="fas fa-check-circle"></i> Confirm Booking
                </button>
                <div class="form-message"></div>
            </form>
        </div>
    </div>

    <!-- Contact Guide Modal -->
    <div id="contactGuideModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('contactGuideModal')">&times;</span>
            <div class="modal-header">
                <h2 class="modal-title">Contact Guide</h2>
                <p class="modal-subtitle">Send a message to the guide</p>
            </div>
            <form id="contactGuideForm" method="POST" onsubmit="return handleGuideContact(event)">
                <input type="hidden" id="guide-id" name="guide_id">
                
                <div class="guide-preview">
                    <div id="guide-details"></div>
                </div>

                <div class="form-group">
                    <label for="contact-subject">
                        <i class="fas fa-heading"></i> Subject
                    </label>
                    <input 
                        type="text" 
                        id="contact-subject" 
                        name="subject" 
                        required
                        placeholder="What would you like to discuss?"
                    >
                </div>

                <div class="form-group">
                    <label for="contact-message">
                        <i class="fas fa-envelope"></i> Message
                    </label>
                    <textarea 
                        id="contact-message" 
                        name="message" 
                        required
                        rows="4"
                        placeholder="Write your message here..."
                    ></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
                <div class="form-message"></div>
            </form>
        </div>
    </div>
    <script>
// Global variables
let currentModal = null;

// Modal handling functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'block';
    currentModal = modal;
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    clearFormMessages();
}

function switchModal(closeId, openId) {
    closeModal(closeId);
    openModal(openId);
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
        clearFormMessages();
    }
}

// Password visibility toggle
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Form handling functions
async function handleLogin(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const messageDiv = form.querySelector('.form-message');
    
    try {
        const response = await fetch('login_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(messageDiv, data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showMessage(messageDiv, data.message, 'error');
        }
    } catch (error) {
        showMessage(messageDiv, 'An error occurred. Please try again.', 'error');
    }
}

async function handleAdminLogin(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const messageDiv = form.querySelector('.form-message');
    
    try {
        const response = await fetch('admin_login_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(messageDiv, data.message, 'success');
            setTimeout(() => window.location.href = 'admin/dashboard.php', 1500);
        } else {
            showMessage(messageDiv, data.message, 'error');
        }
    } catch (error) {
        showMessage(messageDiv, 'An error occurred. Please try again.', 'error');
    }
}

// Booking functionality
function openBookingModal(itemId, itemType) {
    if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
        openModal('loginModal');
        return;
    }

    const modal = document.getElementById('bookingModal');
    document.getElementById('booking-item-id').value = itemId;
    document.getElementById('booking-item-type').value = itemType;

    // Fetch item details
    fetchItemDetails(itemId, itemType).then(details => {
        document.getElementById('booking-details').innerHTML = generateBookingDetails(details);
        updateBookingPrice();
        openModal('bookingModal');
    });
}

async function fetchItemDetails(itemId, itemType) {
    try {
        const response = await fetch(`get_item_details.php?id=${itemId}&type=${itemType}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching item details:', error);
        return null;
    }
}

function generateBookingDetails(details) {
    if (!details) return '<p>Error loading details</p>';

    return `
        <div class="booking-item-details">
            <h4>${details.name}</h4>
            <p>${details.description}</p>
            <div class="details-grid">
                ${Object.entries(details.attributes).map(([key, value]) => `
                    <div class="detail-item">
                        <span class="detail-label">${key}:</span>
                        <span class="detail-value">${value}</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function updateBookingPrice() {
    const guests = parseInt(document.getElementById('booking-guests').value) || 1;
    const basePrice = parseFloat(document.getElementById('base-price').dataset.price) || 0;
    
    const subtotal = basePrice * guests;
    const taxes = subtotal * 0.1; // 10% tax
    const total = subtotal + taxes;

    document.getElementById('base-price').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('taxes').textContent = `$${taxes.toFixed(2)}`;
    document.getElementById('total-price').textContent = `$${total.toFixed(2)}`;
}

async function handleBooking(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const messageDiv = form.querySelector('.form-message');
    
    try {
        const response = await fetch('booking_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(messageDiv, data.message, 'success');
            setTimeout(() => {
                closeModal('bookingModal');
                window.location.href = 'bookings.php';
            }, 1500);
        } else {
            showMessage(messageDiv, data.message, 'error');
        }
    } catch (error) {
        showMessage(messageDiv, 'An error occurred. Please try again.', 'error');
    }
}

// Wishlist functionality
async function toggleWishlist(itemId, itemType) {
    if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
        openModal('loginModal');
        return;
    }

    try {
        const response = await fetch('wishlist_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ item_id: itemId, item_type: itemType })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const wishlistBtn = event.target.closest('.wishlist-btn');
            const icon = wishlistBtn.querySelector('i');
            
            if (data.action === 'added') {
                icon.classList.replace('far', 'fas');
                wishlistBtn.classList.add('active');
            } else {
                icon.classList.replace('fas', 'far');
                wishlistBtn.classList.remove('active');
            }
            
            showToast(data.message);
        }
    } catch (error) {
        showToast('Error updating wishlist');
    }
}

// Guide contact functionality
function openContactModal(guideId) {
    if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
        openModal('loginModal');
        return;
    }

    document.getElementById('guide-id').value = guideId;
    fetchGuideDetails(guideId).then(details => {
        document.getElementById('guide-details').innerHTML = generateGuidePreview(details);
        openModal('contactGuideModal');
    });
}

async function fetchGuideDetails(guideId) {
    try {
        const response = await fetch(`get_guide_details.php?id=${guideId}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching guide details:', error);
        return null;
    }
}

function generateGuidePreview(details) {
    if (!details) return '<p>Error loading guide details</p>';

    return `
        <div class="guide-preview-content">
            <img src="images/guides/${details.id}.jpg" alt="${details.name}" class="guide-preview-image">
            <div class="guide-preview-info">
                <h4>${details.name}</h4>
                <p>${details.experience} years experience</p>
                <p>${details.languages.join(', ')}</p>
            </div>
        </div>
    `;
}

async function handleGuideContact(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const messageDiv = form.querySelector('.form-message');
    
    try {
        const response = await fetch('guide_contact_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(messageDiv, data.message, 'success');
            setTimeout(() => closeModal('contactGuideModal'), 1500);
        } else {
            showMessage(messageDiv, data.message, 'error');
        }
    } catch (error) {
        showMessage(messageDiv, 'An error occurred. Please try again.', 'error');
    }
}

// Utility functions
function showMessage(element, message, type) {
    element.textContent = message;
    element.className = `form-message ${type}`;
}

function clearFormMessages() {
    document.querySelectorAll('.form-message').forEach(element => {
        element.textContent = '';
        element.className = 'form-message';
    });
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }, 100);
}

// Initialize horizontal scroll functionality
document.querySelectorAll('.scroll-container').forEach(container => {
    let isDown = false;
    let startX;
    let scrollLeft;

    container.addEventListener('mousedown', (e) => {
        isDown = true;
        container.style.cursor = 'grabbing';
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
    });

    container.addEventListener('mouseleave', () => {
        isDown = false;
        container.style.cursor = 'grab';
    });

    container.addEventListener('mouseup', () => {
        isDown = false;
        container.style.cursor = 'grab';
    });

    container.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const walk = (x - startX) * 2;
        container.scrollLeft = scrollLeft - walk;
    });
});

// Initialize date inputs with min date
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.min = new Date().toISOString().split('T')[0];
});
</script>
</body>
</html>
