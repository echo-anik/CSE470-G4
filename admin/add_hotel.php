<?php
// admin/add_hotel.php
include '../includes/header.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db_connection.php';

    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $image_url = $conn->real_escape_string($_POST['image_url']);

    // Insert new hotel into the database
    $stmt = $conn->prepare("INSERT INTO hotels (name, location, description, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $location, $description, $image_url);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'New hotel added successfully';
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Error adding hotel: ' . $conn->error;
    }
}
?>

<div class="dashboard">
    <h1>Add New Hotel</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="add_hotel.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" required>
        
        <button type="submit">Add Hotel</button>
    </form>
</div>

<?php
include '../includes/footer.php';
?>