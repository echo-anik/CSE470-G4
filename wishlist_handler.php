<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include '../includes/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to add to wishlist.']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get product details from request
$product_type = $_POST['product_type'] ?? '';
$product_id = $_POST['product_id'] ?? 0;

// Validate inputs
if (empty($product_type) || empty($product_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product details.']);
    exit;
}

// Check if the item is already in the wishlist
$query = "SELECT * FROM wishlist WHERE user_id = ? AND product_type = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('isi', $user_id, $product_type, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Item already in wishlist.']);
    exit;
}

// Insert into wishlist
$query = "INSERT INTO wishlist (user_id, product_type, product_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('isi', $user_id, $product_type, $product_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Item added to wishlist.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add item to wishlist.']);
}

$stmt->close();
$conn->close();
?>