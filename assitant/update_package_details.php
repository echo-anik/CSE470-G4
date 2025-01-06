<?php
session_start();

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    header('Location: guide_login.php');
    exit();
}

require 'dbconnect.php';

// Check if package_id, package_details, explore_country, and explore_city are provided
if (isset($_POST['package_id']) && isset($_POST['package_details']) && isset($_POST['explore_country']) && isset($_POST['explore_city'])) {
    $package_id = intval($_POST['package_id']);
    $package_details = $_POST['package_details'];
    $explore_country = $_POST['explore_country'];
    $explore_city = $_POST['explore_city'];

    // Update package details, explore country, and explore city in the database
    $stmt = $conn->prepare("
        UPDATE tour_packages 
        SET package_details = ?, explore_country = ?, explore_city = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("sssi", $package_details, $explore_country, $explore_city, $package_id);

    if ($stmt->execute()) {
        // Redirect back to the package view page after successful update
        header("Location: view_package.php?id=" . $package_id);
        exit();
    } else {
        echo "Error updating package details.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>


