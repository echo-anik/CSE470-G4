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

// Get Login History
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && $_GET['type'] === 'login_history') {
    $stmt = $conn->prepare("
        SELECT login_time, ip_address, device_info, status 
        FROM LoginHistory 
        WHERE user_id = ? 
        ORDER BY login_time DESC 
        LIMIT 50
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    echo json_encode($history);
    exit();
}

// Get Booking History
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && $_GET['type'] === 'bookings') {
    $stmt = $conn->prepare("
        SELECT 
            bh.history_id,
            bh.booking_id,
            bh.action,
            bh.action_timestamp,
            b.created_by,
            b.last_modified_by,
            b.last_modified_at
        FROM BookingHistory bh
        JOIN Bookings b ON bh.booking_id = b.booking_id
        WHERE bh.user_id = ?
        ORDER BY bh.action_timestamp DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            'history_id' => $row['history_id'],
            'booking_id' => $row['booking_id'],
            'action' => $row['action'],
            'action_timestamp' => $row['action_timestamp'],
            'created_by' => $row['created_by'],
            'last_modified_by' => $row['last_modified_by'],
            'last_modified_at' => $row['last_modified_at']
        ];
    }
    
    echo json_encode($bookings);
    exit();
}

// Record New Login
function recordLogin($user_id, $status = 'SUCCESS') {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $device_info = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt = $conn->prepare("
        INSERT INTO LoginHistory (user_id, ip_address, device_info, status) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $user_id, $ip_address, $device_info, $status);
    $stmt->execute();
}
?>
