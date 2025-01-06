<?php
// /includes/transport_options.php
global $conn;
$transports = $conn->query("SELECT * FROM transport LIMIT 10");
?>

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