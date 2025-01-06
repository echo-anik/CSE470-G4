<?php
// /includes/popular_hotels.php
include 'db_connection.php';
$hotels = $conn->query("SELECT id, name, location, description, rating, hotel_image, amenities, status FROM hotels WHERE status = 'active' LIMIT 10");
?>

<!-- Hotels Section -->
<section class="category-section">
    <h2 class="section-title">Popular Hotels</h2>
    <div class="scroll-container">
        <?php if ($hotels && $hotels->num_rows > 0): ?>
            <?php while ($hotel = $hotels->fetch_assoc()): ?>
                <div class="product-card" data-id="<?php echo $hotel['id']; ?>" data-type="hotel">
                    <div class="product-image" style="background-image: url('images/hotels/<?php echo htmlspecialchars($hotel['hotel_image']); ?>');">
                        <div class="product-status">
                            <span class="badge"><?php echo ucfirst($hotel['status']); ?></span>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                        <div class="product-info">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?></span>
                            <span><i class="fas fa-star"></i> <?php echo htmlspecialchars($hotel['rating']); ?></span>
                        </div>
                        <div class="product-price">
                            <span class="price-label">Amenities:</span>
                            <span class="amount"><?php echo htmlspecialchars($hotel['amenities']); ?></span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $hotel['id']; ?>, 'hotel')">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="btn book-btn" onclick="openBookingModal(<?php echo $hotel['id']; ?>, 'hotel')">
                                <i class="fas fa-bookmark"></i> Book Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hotels found.</p>
        <?php endif; ?>
    </div>
</section>