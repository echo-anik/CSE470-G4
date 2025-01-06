<?php
session_start();

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    header('Location: guide_login.php');
    exit();
}

require 'dbconnect.php';

// Get the package ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid package ID.";
    exit();
}

$package_id = intval($_GET['id']); // Sanitize package ID

// Fetch package details
$stmt = $conn->prepare("
    SELECT tp.*, gr.name, gr.email, gr.language_proficiency, gr.age, gr.experience, gr.role
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
        <tr><td><strong>Other Details:</strong></td>
            <td><?php echo !empty($package['package_details']) ? nl2br($package['package_details']) : 'No details available'; ?></td>
        </tr>
        <tr><td><strong>Timestamp:</strong></td><td><?php echo date('Y-m-d H:i:s'); ?></td></tr>
    </table>

    <h2>Update Package Details</h2>
    <form action="update_package_details.php" method="POST">
        <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">

        <label for="package_details">Other Details:</label><br>
        <textarea name="package_details" placeholder="Enter package details"><?php echo htmlspecialchars($package['package_details']); ?></textarea><br>

        <label for="explore_country">Explore Country:</label><br>
        <input type="text" name="explore_country" value="<?php echo htmlspecialchars($package['explore_country']); ?>"><br>

        <label for="explore_city">Explore City:</label><br>
        <input type="text" name="explore_city" value="<?php echo htmlspecialchars($package['explore_city']); ?>"><br>

        <button type="submit">Update Package</button>
    </form>

    <a href="guide_dashboard.php"><button type="button">Back to Dashboard</button></a>
</body>
</html>
