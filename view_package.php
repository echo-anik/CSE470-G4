<?php
session_start();

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    header('Location: guide_login.php');
    exit();
}

require 'dbconnect.php';

// Handle delete package request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_package'])) {
    $package_id = intval($_POST['package_id']);
    $stmt = $conn->prepare("DELETE FROM tour_packages WHERE id = ?");
    $stmt->bind_param("i", $package_id);

    if ($stmt->execute()) {
        $stmt->close();
        // Redirect to the guide_dashboard.php after deletion
        header('Location: guide_dashboard.php');
        exit();
    } else {
        echo "Error deleting package.";
        $stmt->close();
    }
}

// Get the package ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid package ID.";
    exit();
}

$package_id = intval($_GET['id']); 

// Fetch package details
$stmt = $conn->prepare("
    SELECT tp.*, tp.journey_date, tp.return_date, tp.other_details,  tp.hourly_rate, gr.name, gr.email, gr.language_proficiency, gr.age, gr.experience, gr.role
    FROM tour_packages tp
    JOIN guide_registration gr ON tp.guide_id = gr.id
    WHERE tp.id = ?
");
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Package not found.";
    exit();
}

$package = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Details</title>
    <link rel="stylesheet" href="guide_dashboard_styles.css">
</head>
<body>
    <h1>Package Details</h1>
    <table>
        <tr><td><strong>Guide Name:</strong></td><td><?php echo $package['name']; ?></td></tr>
        <tr><td><strong>Email:</strong></td><td><?php echo $package['email']; ?></td></tr>
        <tr><td><strong>Language Proficiency:</strong></td><td><?php echo $package['language_proficiency']; ?></td></tr>
        <tr><td><strong>Age:</strong></td><td><?php echo $package['age']; ?></td></tr>
        <tr><td><strong>Experience (Years):</strong></td><td><?php echo $package['experience']; ?></td></tr>
        <tr><td><strong>Role:</strong></td><td><?php echo $package['role']; ?></td></tr>
        <tr><td><strong>Explore Country:</strong></td><td><?php echo $package['explore_country']; ?></td></tr>
        <tr><td><strong>Explore City:</strong></td><td><?php echo $package['explore_city']; ?></td></tr>
        <tr><td><strong>Hourly Rate (USD):</strong></td><td>
            <?php 
            echo isset($package['hourly_rate']) ? '$' . number_format($package['hourly_rate'], 2) : 'N/A'; 
            ?>
        </td></tr>
        <!-- Display Journey and Return Date -->
        <tr><td><strong>Journey Date:</strong></td><td><?php echo isset($package['journey_date']) ? date('Y-m-d', strtotime($package['journey_date'])) : 'N/A'; ?></td></tr>
        <tr><td><strong>Return Date:</strong></td><td><?php echo isset($package['return_date']) ? date('Y-m-d', strtotime($package['return_date'])) : 'N/A'; ?></td></tr>

        <tr><td><strong>Other Details:</strong></td>
            <td><?php echo !empty($package['other_details']) ? nl2br($package['other_details']) : 'No details available'; ?></td>
        </tr>
        <tr><td><strong>Timestamp:</strong></td><td><?php echo date('Y-m-d H:i:s'); ?></td></tr>

    </table>




    <h2>Update Package Details</h2>
    <form action="update_package_details.php" method="POST">
        <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">

        <label for="other_details">Other Details:</label><br>
        <textarea name="other_details" placeholder="Enter package details"><?php echo htmlspecialchars($package['other_details']); ?></textarea><br>

        <label for="explore_country">Explore Country:</label><br>
        <input type="text" name="explore_country" value="<?php echo htmlspecialchars($package['explore_country']); ?>"><br>

        <label for="explore_city">Explore City:</label><br>
        <input type="text" name="explore_city" value="<?php echo htmlspecialchars($package['explore_city']); ?>"><br>

        <label for="hourly_rate">Hourly Rate (in USD):</label><br>
        <input type="number" name="hourly_rate" value="<?php echo htmlspecialchars($package['hourly_rate']); ?>" min="10" step="0.01" placeholder="Enter hourly rate"><br>

        <!-- Update Journey and Return Date -->
        <label for="journey_date">Journey Date:</label><br>
        <input type="date" name="journey_date" value="<?php echo $package['journey_date']; ?>" required><br>

        <label for="return_date">Return Date:</label><br>
        <input type="date" name="return_date" value="<?php echo $package['return_date']; ?>" required><br>


        <button type="submit">Update Package</button>
    </form>



    <h2>Delete Package</h2>
    <div class="center-form"></div>
    <form method="POST">
        <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">
        <button type="submit" name="delete_package" onclick="return confirm('Are you sure you want to delete this package?');">Delete Package</button>
    </form>

    <a href="guide_dashboard.php"><button type="button">Back to Dashboard</button></a>
</body>
</html>
