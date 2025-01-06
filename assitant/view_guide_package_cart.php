<?php
require 'dbconnect.php';

// Get the current date
$currentDate = date('Y-m-d');

// Fetch all packages dynamically
$query = "SELECT tp.*, gr.name as guide_name FROM tour_packages tp 
          JOIN guide_registration gr ON tp.guide_id = gr.id";
$result = $conn->query($query);



if ($result->num_rows > 0) {
    $packageNumber = 1;
    while ($row = $result->fetch_assoc()) {
        // Determine the package status based on the end date
        $endDate = $row['end_date']; // Ensure `end_date` exists in the `tour_packages` table
        $status = ($endDate && $currentDate > $endDate) ? "Completed" : "Pending";

        echo '<div class="package" data-id="' . htmlspecialchars($row['id']) . '">';
        echo '<h3 class="package-number">Package-' . $packageNumber++ . '</h3>';
        echo '<h4>' . htmlspecialchars($row['explore_country']) . ', ' . htmlspecialchars($row['explore_city']) . '</h4>';
        echo '<p>Guide: ' . htmlspecialchars($row['guide_name']) . '</p>';
        echo '<p>Status: <span class="status">' . $status . '</span></p>';
        echo '<button class="select-package" data-id="' . htmlspecialchars($row['id']) . '">Select</button>';
        echo '<button class="view-details" data-id="' . htmlspecialchars($row['id']) . '"';
        echo ' data-country="' . htmlspecialchars($row['explore_country']) . '"';
        echo ' data-city="' . htmlspecialchars($row['explore_city']) . '"';
        echo ' data-guide="' . htmlspecialchars($row['guide_name']) . '"';
        echo ' data-details="' . htmlspecialchars($row['other_details']) . '"';
        echo ' data-status="' . $status . '"';
        echo ' data-enddate="' . htmlspecialchars($row['end_date']) . '">View Details</button>';
        echo '</div>';
    }
} else {
    echo '<p>No packages available.</p>';
}

$conn->close();
?>

