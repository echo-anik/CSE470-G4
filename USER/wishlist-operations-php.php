<?php
session_start();
require_once 'db_connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$user_id = $_SESSION['user_id'];

// Get Wishlist
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $stmt = $conn->prepare("SELECT * FROM Wishlist WHERE user_id = ? ORDER BY date_added DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wishlist = [];
    while ($row = $result->fetch_assoc()) {
        $wishlist[] = $row;
    }
    
    echo json_encode($wishlist);
    exit();
}

// Add Wishlist Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['wishlist_id'])) {
    $destination = $_POST['destination'];
    $travel_method = $_POST['travel_method'];
    $priority = $_POST['priority'];
    $notes = $_POST['notes'] ?? '';

    $stmt = $conn->prepare("INSERT INTO Wishlist (user_id, destination, travel_method, priority, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $destination, $travel_method, $priority, $notes);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Wishlist item added successfully',
            'id' => $stmt->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add wishlist item'
        ]);
    }
    exit();
}

// Update Wishlist Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wishlist_id'])) {
    $wishlist_id = $_POST['wishlist_id'];
    $destination = $_POST['destination'];
    $travel_method = $_POST['travel_method'];
    $priority = $_POST['priority'];
    $notes = $_POST['notes'] ?? '';

    // Verify ownership
    $check_stmt = $conn->prepare("SELECT user_id FROM Wishlist WHERE wishlist_id = ?");
    $check_stmt->bind_param("i", $wishlist_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || $row['user_id'] !== $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    $update_stmt = $conn->prepare("UPDATE Wishlist SET destination = ?, travel_method = ?, priority = ?, notes = ? WHERE wishlist_id = ? AND user_id = ?");
    $update_stmt->bind_param("ssssii", $destination, $travel_method, $priority, $notes, $wishlist_id, $user_id);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Wishlist item updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update wishlist item']);
    }
    exit();
}

// Delete Wishlist Item
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || (isset($_POST['action']) && $_POST['action'] === 'delete')) {
    $input = json_decode(file_get_contents('php://input'), true);
    $wishlist_id = $input['wishlist_id'] ?? $_POST['wishlist_id'];

    // Verify ownership
    $check_stmt = $conn->prepare("SELECT user_id FROM Wishlist WHERE wishlist_id = ?");
    $check_stmt->bind_param("i", $wishlist_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || $row['user_id'] !== $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    $delete_stmt = $conn->prepare("DELETE FROM Wishlist WHERE wishlist_id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $wishlist_id, $user_id);

    if ($delete_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Wishlist item deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete wishlist item']);
    }
    exit();
}
?>
