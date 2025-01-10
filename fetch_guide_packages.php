<?php
require 'dbconnect.php';

// Fetch tour packages from the database
$query = "SELECT * FROM tour_packages"; // Assuming the table for tour packages is named tour_packages
$result = $conn->query($query);

// Loop through and output the packages as HTML
while ($package = $result->fetch_assoc()) {
    echo '<div class="package-card">';
    echo '<h3>' . 'Guide Package-' . $package['id'] . '</h3>';
    echo '<p>' . $package['explore_country'] . ', ' . $package['explore_city'] . '</p>';
    echo '<button class="package-btn" onclick="window.location.href=\'pre_package_of_guide.php?id=' . $package['id'] . '\'">View Package</button>';
    echo '</div>';
}

// Close the database connection
$conn->close();

?>
