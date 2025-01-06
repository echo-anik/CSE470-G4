<?php
// featured_packages.php

// Assuming session is already started and database connection ($conn) exists

// Function to check if item is in wishlist
function isInWishlist($itemId, $itemType) {
    global $conn;
    if (!isset($_SESSION['user_id'])) return false;
    
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND item_id = ? AND item_type = ?");
    $stmt->bind_param("iis", $_SESSION['user_id'], $itemId, $itemType);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Fetch featured packages with available spots
$query = "SELECT 
            p.*, 
            (p.max_participants - COALESCE(COUNT(pb.booking_id), 0)) as available_spots
          FROM travel_packages p
          LEFT JOIN package_bookings pb ON p.package_id = pb.package_id 
            AND pb.booking_status != 'cancelled'
          WHERE p.status = 'active' 
          GROUP BY p.package_id
          HAVING available_spots > 0
          ORDER BY p.created_at DESC
          LIMIT 6";

$featured_packages = $conn->query($query);

if (!$featured_packages) {
    error_log("Query failed: " . $conn->error);
    echo "<p>Error loading featured packages</p>";
    exit;
}
?>

<section class="category-section">
    <h2 class="section-title">Featured Travel Packages</h2>
    <div class="scroll-container">
        <?php while($package = $featured_packages->fetch_assoc()): ?>
            <div class="product-card" data-id="<?php echo $package['package_id']; ?>" data-type="package">
                <div class="product-image" style="background-image: url('images/packages/<?php echo $package['package_image'] ?: 'default.jpg'; ?>')">
                    <div class="product-status">
                        <span class="badge">Featured</span>
                        <?php if ($package['available_spots'] < 10): ?>
                            <span class="badge">Only <?php echo $package['available_spots']; ?> spots left</span>
                        <?php endif; ?>
                    </div>
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
                    <div class="product-price">
                        <span class="price-label">From</span>
                        <span class="amount">$<?php echo number_format($package['total_price'], 2); ?></span>
                        <span class="duration">/person</span>
                    </div>
                    <div class="product-actions">
                        <button class="wishlist-btn <?php echo isInWishlist($package['package_id'], 'package') ? 'active' : ''; ?>" 
                                onclick="toggleWishlist(<?php echo $package['package_id']; ?>, 'package')">
                            <i class="<?php echo isInWishlist($package['package_id'], 'package') ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                        <button class="btn book-btn" 
                                onclick="openBookingModal(<?php echo $package['package_id']; ?>, 'package')"
                                <?php echo ($package['available_spots'] <= 0) ? 'disabled' : ''; ?>>
                            <i class="fas fa-bookmark"></i> Book Now
                        </button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>