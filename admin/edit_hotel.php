<?php
// admin/edit_hotel.php
include '../includes/header.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

include '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $image_url = $conn->real_escape_string($_POST['image_url']);

    $stmt = $conn->prepare("UPDATE hotels SET name = ?, location = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $location, $description, $image_url, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Hotel updated successfully';
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Error updating hotel: ' . $conn->error;
    }
} else {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM hotels WHERE id = $id");
    $hotel = $result->fetch_assoc();
}
?>

<div class="dashboard">
    <h1>Edit Hotel</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="edit_hotel.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($hotel['id']); ?>">
        
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($hotel['name']); ?>" required>
        
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($hotel['location']); ?>" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($hotel['description']); ?></textarea>
        
        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($hotel['image_url']); ?>" required>
        
        <button type="submit">Update Hotel</button>
    </form>
</div>

<?php
include '../includes/footer.php';
?>