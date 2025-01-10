<?php
session_start();

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    header('Location: guide_login.php');
    exit();
}

require 'dbconnect.php';

// Check if package_id, package_details, explore_country, and explore_city are provided
if (isset($_POST['package_id']) && isset($_POST['other_details']) && isset($_POST['explore_country']) && isset($_POST['explore_city'])) {
    $package_id = intval($_POST['package_id']);
    $other_details = $_POST['other_details'];
    $explore_country = $_POST['explore_country'];
    $explore_city = $_POST['explore_city'];
    $hourly_rate = intval($_POST['hourly_rate']);
    $journey_date = $_POST['journey_date'];
    $return_date = $_POST['return_date'];


    if ($hourly_rate < 10) {
        echo "Hourly rate must be at least 10.";
        exit();
    }

    // Update package details, explore country, and explore city in the database
    $stmt = $conn->prepare("
        UPDATE tour_packages 
        SET other_details = ?, explore_country = ?, explore_city = ?, hourly_rate = ?, journey_date = ?, return_date = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssissi", $other_details, $explore_country, $explore_city, $hourly_rate, $journey_date, $return_date, $package_id);

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


