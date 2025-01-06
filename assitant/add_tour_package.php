<?php
session_start();  // Start the session to access guide information

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    http_response_code(403);
    echo "Unauthorized access!";
    exit();
}

require 'db_connection.php';  // Include database connection

$guide_id = $_SESSION['guide_id'];

// Validate and sanitize input
$explore_country = isset($_POST['explore_country']) ? htmlspecialchars(trim($_POST['explore_country'])) : '';
$explore_city = isset($_POST['explore_city']) ? htmlspecialchars(trim($_POST['explore_city'])) : '';

if (empty($explore_country) || empty($explore_city)) {
    http_response_code(400);
    echo "Country and City are required fields!";
    exit();
}

// Insert the new tour package into the database
$stmt = $conn->prepare("INSERT INTO tour_packages (guide_id, explore_country, explore_city) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $guide_id, $explore_country, $explore_city);

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
