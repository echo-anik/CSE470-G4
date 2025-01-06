<?php
// admin/delete_hotel.php
include '../includes/db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

$id = $conn->real_escape_string($_GET['id']);

$stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = 'Hotel deleted successfully';
} else {
    $_SESSION['message'] = 'Error deleting hotel: ' . $conn->error;
}

header('Location: dashboard.php');
exit();
?>