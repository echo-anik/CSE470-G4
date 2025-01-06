<?php
// /includes/expert_guides.php
global $conn;
$guides = $conn->query("SELECT * FROM guide_registration LIMIT 10");
?>

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