<?php
require 'dbconnect.php';

$query = "SELECT tp.*, gr.name as guide_name FROM tour_packages tp 
          JOIN guide_registration gr ON tp.guide_id = gr.id";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()): ?>
    <div class="package">
        <h3><?php echo htmlspecialchars($row['explore_country']) . ", " . htmlspecialchars($row['explore_city']); ?></h3>
        <p>Guide: <?php echo htmlspecialchars($row['guide_name']); ?></p>
        <p>Details: <?php echo htmlspecialchars($row['other_details']); ?></p>
        <button class="select-package" data-id="<?php echo $row['id']; ?>">Select</button>
    </div>
<?php endwhile;
?>
