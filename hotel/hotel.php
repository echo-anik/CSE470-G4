<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include '../includes/db_connection.php';

// Initialize search parameters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : PHP_FLOAT_MAX;
$rating = isset($_GET['rating']) ? floatval($_GET['rating']) : 0;

// Build the query
$query = "SELECT * FROM hotels WHERE status = 'active'";

// Add search conditions
if (!empty($search)) {
    $query .= " AND (name LIKE '%$search%' OR location LIKE '%$search%' OR description LIKE '%$search%')";
}

if ($min_price > 0 || $max_price < PHP_FLOAT_MAX) {
    $query .= " AND id IN (SELECT hotel_id FROM hotel_rooms WHERE price_per_night BETWEEN $min_price AND $max_price)";
}

if ($rating > 0) {
    $query .= " AND rating >= $rating";
}

// Add sorting
$query .= " ORDER BY name ASC";

// Execute query
$hotels = $conn->query($query);

// Check for query execution error
if (!$hotels) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .hotel-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
        }

        .hotel-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .hotel-details {
            padding: 15px;
        }

        .hotel-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .hotel-location,
        .hotel-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .hotel-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #666;
        }

        .details-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .details-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main>
        <section class="hotels-section">
            <div class="container">
                <h1 class="section-title">Hotels</h1>
                
                <?php if ($hotels->num_rows > 0): ?>
                    <div class="hotels-grid">
                        <?php while($hotel = $hotels->fetch_assoc()): ?>
                            <div class="hotel-card">
                                <div class="hotel-image" style="background-image: url('/assets/images/hotels/<?php echo $hotel['hotel_image'] ?: 'default.jpg'; ?>')"></div>
                                <div class="hotel-details">
                                    <h3 class="hotel-title"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                                    <p class="hotel-location"><?php echo htmlspecialchars($hotel['location']); ?></p>
                                    <p class="hotel-description"><?php echo htmlspecialchars(substr($hotel['description'], 0, 100)); ?>...</p>
                                    <div class="hotel-info">
                                        <span>Rating: <?php echo $hotel['rating']; ?> Stars</span>
                                    </div>
                                    <a href="hotel_details.php?id=<?php echo $hotel['id']; ?>" class="details-btn">View Details</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <p>No hotels found matching your criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>