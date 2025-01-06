<?php
session_start();
include '../includes/header.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db_connection.php';

    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']); // No hashing
    $role = $conn->real_escape_string($_POST['role']);

    $stmt = $conn->prepare("INSERT INTO admin (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'New admin added successfully';
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Error adding admin: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<div class="dashboard">
    <h1>Add New Admin</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="add_admin.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    
        <label for="role">Role:</label>
        <input type="text" id="role" name="role" required>

        <button type="submit">Add Admin</button>
    </form>
</div>

<?php
include '../includes/footer.php';
?>