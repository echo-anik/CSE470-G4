<?php
require 'dbconnect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in.";
    exit();
}

$user_id = $_SESSION['user_id'];
$package_id = $_POST['package_id'];

// Check if the package is already in the cart
$query = "SELECT * FROM user_cart WHERE user_id = ? AND package_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Package already in the cart.";
    exit();
}

// Add to cart
$query = "INSERT INTO user_cart (user_id, package_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $package_id);
if ($stmt->execute()) {
    echo "Package added to cart.";
} else {
    echo "Error adding package.";
}
?>

