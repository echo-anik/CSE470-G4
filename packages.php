<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'travelease');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search parameters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : PHP_FLOAT_MAX;
$duration = isset($_GET['duration']) ? intval($_GET['duration']) : 0;

// Build the query
$query = "SELECT * FROM travel_packages WHERE status = 'active'";

// Add search conditions
if (!empty($search)) {
    $query .= " AND (package_name LIKE '%$search%' OR description LIKE '%$search%')";
}

if ($min_price > 0) {
    $query .= " AND total_price >= $min_price";
}

if ($max_price < PHP_FLOAT_MAX) {
    $query .= " AND total_price <= $max_price";
}

if ($duration > 0) {
    $query .= " AND duration_days = $duration";
}

// Add sorting
$query .= " ORDER BY created_at DESC";

// Execute query
$packages = $conn->query($query);

// Check for query execution error
if (!$packages) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Packages</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Search Section -->
        <section class="search-section">
            <div class="container">
                <form action="" method="GET" class="search-form">
                    <div class="search-grid">
                        <div class="search-input">
                            <input type="text" name="search" 
                                   placeholder="Search packages..." 
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                        <div class="search-input">
                            <input type="number" name="min_price" 
                                   placeholder="Min Price" 
                                   value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
                        </div>
                        <div class="search-input">
                            <input type="number" name="max_price" 
                                   placeholder="Max Price" 
                                   value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
                        </div>
                        <div class="search-input">
                            <select name="duration">
                                <option value="">Any Duration</option>
                                <?php for($i = 1; $i <= 30; $i++): ?>
                                    <option value="<?php echo $i; ?>" 
                                            <?php echo (isset($_GET['duration']) && $_GET['duration'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?> Days
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <?php if(!empty($_GET)): ?>
                            <a href="packages.php" class="reset-btn">Reset Filters</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </section>


        <section class="packages-section">
            <div class="container">
                <h1 class="section-title">Travel Packages</h1>
                
                <?php if ($packages->num_rows > 0): ?>
                    <div class="packages-grid">
                        <?php while($package = $packages->fetch_assoc()): ?>
                            <div class="product-card">
                                <div class="product-image" 
                                    style="background-image: url('assets/images/packages/<?php echo $package['package_image'] ?: 'default.jpg'; ?>')">
                                    <?php if ($package['available_spots'] < 5): ?>
                                        <div class="product-badge">
                                            Only <?php echo $package['available_spots']; ?> spots left!
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-details">
                                    <h3 class="product-title"><?php echo htmlspecialchars($package['package_name']); ?></h3>
                                    <p class="product-description">
                                        <?php echo htmlspecialchars(substr($package['description'], 0, 100)); ?>...
                                    </p>
                                    <div class="product-info">
                                        <span><i class="fas fa-clock"></i> <?php echo $package['duration_days']; ?> Days</span>
                                        <span><i class="fas fa-users"></i> <?php echo $package['available_spots']; ?> spots left</span>
                                    </div>
                                    <div class="product-dates">
                                        <span><i class="fas fa-calendar-alt"></i> 
                                            <?php echo date('M d', strtotime($package['start_date'])); ?> - 
                                            <?php echo date('M d, Y', strtotime($package['end_date'])); ?>
                                        </span>
                                    </div>
                                    <div class="product-price">
                                        <span class="amount">$<?php echo number_format($package['total_price'], 2); ?></span>
                                        <span class="duration">/person</span>
                                    </div>
                                    <div class="product-actions">
                                        <button class="book-btn" 
                                                onclick="openBookingModal(<?php echo $package['package_id']; ?>, 'package')"
                                                <?php echo ($package['available_spots'] <= 0) ? 'disabled' : ''; ?>>
                                            <i class="fas fa-bookmark"></i> Book Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <p>No packages found matching your criteria.</p>
                        <?php if(!empty($_GET)): ?>
                            <a href="packages.php" class="btn">Clear Filters</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

<style>
    main {
        padding-top: 80px; /* This creates space below the fixed header */
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .search-section {
        background: var(--color-secondary);
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .search-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }
    .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    width: 100%; /* Add this */
    }


    .packages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        padding: 2rem 0;
    }

    .product-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .product-details {
        padding: 1.5rem;
    }

    .product-title {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: var(--color-dark);
    }

    .product-description {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .product-info {
        display: flex;
        justify-content: space-between;
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
    }

    .product-price .duration {
        font-size: 0.9rem;
        color: #666;
    }

    .product-actions {
        display: flex;
        gap: 1rem;
    }

    .book-btn {
        flex: 1;
        background: var(--color-accent);
        color: white;
        border: none;
        padding: 0.8rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .book-btn:hover {
        background: #963351;
    }

    .search-input input {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .search-btn {
        background: var(--color-accent);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        width: 100%;
    }

    .search-btn:hover {
        background: #963351;
    }

    @media (max-width: 768px) {
        .packages-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }

        .search-grid {
            grid-template-columns: 1fr;
        }
    }
    .reset-btn {
            background: var(--color-dark);
            color: white;
            padding: 0.8rem;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .reset-btn:hover {
            background: #0a2440;
        }

        .search-input select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            background: white;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            margin: 2rem 0;
        }

        .no-results .btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 2rem;
            background: var(--color-accent);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .no-results .btn:hover {
            background: #963351;
        }
</style>