<?php
// admin/add_transport.php
include '../includes/header.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db_connection.php';

    $type = $conn->real_escape_string($_POST['type']);
    $number = $conn->real_escape_string($_POST['number']);
    $departure = $conn->real_escape_string($_POST['departure']);
    $arrival = $conn->real_escape_string($_POST['arrival']);

    // Determine the table based on transport type
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
        // Insert new transport into the database
        $stmt = $conn->prepare("INSERT INTO $table (number, departure, arrival) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $number, $departure, $arrival);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'New transport added successfully';
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Error adding transport: ' . $conn->error;
        }
    } else {
        $error = 'Invalid transport type selected';
    }
}
?>

<div class="dashboard">
    <h1>Add New Transport</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="add_transport.php" method="POST">
        <label for="type">Transport Type:</label>
        <select id="type" name="type" required>
            <option value="">Select Type</option>
            <option value="bus">Bus</option>
            <option value="cab">Cab</option>
            <option value="flight">Flight</option>
            <option value="train">Train</option>
        </select>
        
        <label for="number">Number:</label>
        <input type="text" id="number" name="number" required>
        
        <label for="departure">Departure:</label>
        <input type="text" id="departure" name="departure" required>
        
        <label for="arrival">Arrival:</label>
        <input type="text" id="arrival" name="arrival" required>
        
        <button type="submit">Add Transport</button>
    </form>
</div>

<?php
include '../includes/footer.php';
?>