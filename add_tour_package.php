<?php
session_start();

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    http_response_code(403);
    echo "Unauthorized access!";
    exit();
}

require 'dbconnect.php';

$guide_id = $_SESSION['guide_id'];

// Validate and sanitize input
$explore_country = isset($_POST['explore_country']) ? htmlspecialchars(trim($_POST['explore_country'])) : '';
$explore_city = isset($_POST['explore_city']) ? htmlspecialchars(trim($_POST['explore_city'])) : '';
$hourly_rate = isset($_POST['hourly_rate']) ? intval($_POST['hourly_rate']) : 0; // Convert to integer
$other_details = isset($_POST['other_details']) ? htmlspecialchars(trim($_POST['other_details'])) : '';

// Journey Date and Return Date
$journey_date = isset($_POST['journey_date']) ? htmlspecialchars(trim($_POST['journey_date'])) : '';
$return_date = isset($_POST['return_date']) ? htmlspecialchars(trim($_POST['return_date'])) : '';

if (empty($explore_country) || empty($explore_city) || $hourly_rate < 10 || empty($journey_date) || empty($return_date)) {
    http_response_code(400);
    echo "Country, City, a valid Hourly Rate (at least 10), Journey Date, and Return Date are required fields!";
    exit();
}


// Insert the new tour package into the database
$stmt = $conn->prepare("
    INSERT INTO tour_packages (guide_id, explore_country, explore_city, hourly_rate, journey_date, return_date, other_details) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ississs", $guide_id, $explore_country, $explore_city, $hourly_rate, $journey_date, $return_date, $other_details);

if ($stmt->execute()) {
    http_response_code(200);
    echo "Package added successfully!";
} else {
    http_response_code(500);
    echo "Error adding package!";
}

$stmt->close();
$conn->close();
?>
