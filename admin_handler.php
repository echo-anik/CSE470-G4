<?php
// admin_handler.php
include 'includes/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $email = $conn->real_escape_string($_POST['admin_email']);
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT admin_id, name FROM admin WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        header('Location: admin/dashboard.php');
        exit();
    }
    $_SESSION['message'] = 'Invalid admin credentials';
    header('Location: index.php');
    exit();
}
?>