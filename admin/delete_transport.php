<?php
// admin/delete_transport.php
include '../includes/db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

$type = $conn->real_escape_string($_GET['type']);
$id = $conn->real_escape_string($_GET['id']);

$table = '';
switch ($type) {
    case 'bus':
        $table = 'buses';
        break;
    case 'cab':
        $table = 'cabs';
        break;
    case 'flight':
        $table = 'flights';
        break;
    case 'train':
        $table = 'trains';
        break;
}

if ($table) {
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = ucfirst($type) . ' deleted successfully';
    } else {
        $_SESSION['message'] = 'Error deleting ' . $type . ': ' . $conn->error;
    }
} else {
    $_SESSION['message'] = 'Invalid transport type';
}

header('Location: dashboard.php');
exit();
?>