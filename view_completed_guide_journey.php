<?php
require 'dbconnect.php';

if (isset($_GET['id'])) {
    $journey_id = intval($_GET['id']);

    // Fetch journey details based on the journey ID
    $stmt = $conn->prepare("SELECT * FROM completed_journey WHERE id = ?");
    $stmt->bind_param("i", $journey_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $journey = $result->fetch_assoc();
    $stmt->close();

    if (!$journey) {
        echo "Journey not found.";
        exit();
    }
} else {
    echo "No journey selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journey Details</title>
</head>
<body>
    <h1>Journey Details</h1>
    <table>
        <tr><td><strong>Journey Name:</strong></td><td><?php echo $journey['journey_name']; ?></td></tr>
        <tr><td><strong>Journey Details:</strong></td><td><?php echo $journey['journey_details']; ?></td></tr>
        <tr><td><strong>Guide:</strong></td><td><?php echo $journey['guide_name']; ?></td></tr>
        <tr><td><strong>Package:</strong></td><td><?php echo $journey['package_name']; ?></td></tr>
        <tr><td><strong>Journey Date:</strong></td><td><?php echo date('Y-m-d', strtotime($journey['journey_date'])); ?></td></tr>
    </table>
</body>
</html>
