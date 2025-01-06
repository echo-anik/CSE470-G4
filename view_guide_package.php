<?php
require 'dbconnect.php';
session_start();

$error = "";
if (isset($_GET['package_id']) && is_numeric($_GET['package_id'])) {
    $package_id = intval($_GET['package_id']);

    // Fetch package details based on the provided package_id
    $query = "
    SELECT 
        tour_packages.id, 
        guide_registration.name, 
        guide_registration.email, 
        guide_registration.language_proficiency, 
        guide_registration.age, 
        guide_registration.experience, 
        guide_registration.role, 
        tour_packages.explore_country, 
        tour_packages.explore_city, 
        tour_packages.hourly_rate, 
        tour_packages.journey_date, 
        tour_packages.return_date, 
        tour_packages.other_details 
    FROM 
        tour_packages 
    INNER JOIN 
        guide_registration 
    ON 
        tour_packages.guide_id = guide_registration.id 
    WHERE 
        tour_packages.id = ?";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->$error);
    }

    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a package was found
    if ($result->num_rows > 0) {
        $package = $result->fetch_assoc();
    } else {
        echo "Package not found!";
        exit;
    }
} else {
    echo "Invalid package ID!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Details</title>
</head>
<body>
    <h2 style="text-align: center; margin-top: 20px; font-size: 24px;">Package Details</h2>
    <div>
        <h3><?php echo htmlspecialchars($package['explore_country']); ?> - <?php echo htmlspecialchars($package['explore_city']); ?></h3>
        <style>
            h3 {
               text-align : center;
               font-size: 24px;
               margin-top: 20px;
               }
        </style>
        <table border="1" cellpadding="10" cellspacing="0" style="margin: 20px auto; width: 70%; text-align: center; border-collapse: collapse;">
            <tr>
                <td><strong>Guide Name:</strong></td>
                <td><?php echo htmlspecialchars($package['name']); ?></td>
           
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><?php echo htmlspecialchars($package['email']); ?></td>
            </tr>
            <tr>
                <td><strong>Language Proficiency:</strong></td>
                <td><?php echo htmlspecialchars($package['language_proficiency']); ?></td>
            </tr>
            <tr>
                <td><strong>Age:</strong></td>
                <td><?php echo htmlspecialchars($package['age']); ?></td>
            </tr>
            <tr>
                <td><strong>Experience (Years):</strong></td>
                <td><?php echo htmlspecialchars($package['experience']); ?></td>
            </tr>
            <tr>
                <td><strong>Role:</strong></td>
                <td><?php echo htmlspecialchars($package['role']); ?></td>
            </tr>
            <tr>
                <td><strong>Explore Country:</strong></td>
                <td><?php echo htmlspecialchars($package['explore_country']); ?></td>
            </tr>
            <tr>
                <td><strong>Explore City:</strong></td>
                <td><?php echo htmlspecialchars($package['explore_city']); ?></td>
            </tr>
            <tr>
                <td><strong>Hourly Rate (USD):</strong></td>
                <td>
                    <?php echo isset($package['hourly_rate']) ? '$' . number_format($package['hourly_rate'], 2) : 'N/A'; ?>
                </td>
            </tr>
            <tr>
                <td><strong>Journey Date:</strong></td>
                <td><?php echo isset($package['journey_date']) ? date('Y-m-d', strtotime($package['journey_date'])) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td><strong>Return Date:</strong></td>
                <td><?php echo isset($package['return_date']) ? date('Y-m-d', strtotime($package['return_date'])) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td><strong>Other Details:</strong></td>
                <td><?php echo !empty($package['other_details']) ? nl2br(htmlspecialchars($package['other_details'])) : 'No details available'; ?></td>
            </tr>
            <tr>
                <td><strong>Timestamp:</strong></td>
                <td><?php echo date('Y-m-d H:i:s'); ?></td>
            </tr>
        </table>

        <form action="view_guide_package.php?id=<?php echo $package['id']; ?>" method="post" style="text-align: center; margin-top: 20px;">
            <input type="hidden" name="selected_package_id" value="<?php echo $package['id']; ?>">
            <button type="submit" name="select_package" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Select Package</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='guide_home_page.php'" style="padding: 10px 20px; background-color: #333; color: white; border: none; border-radius: 5px; cursor: pointer;">Return to Homepage</button>
        </div>
    </div>

    <?php
    // Handle package selection
    if (isset($_POST['select_package'])) {
        if (isset($_SESSION['guide_email'])) {
            // Retrieve guide_id based on guide_email
            $guide_email = $_SESSION['guide_email'];
            $query = "SELECT id FROM guides WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $guide_email);
            $stmt->execute();
            $stmt->bind_result($guide_id);
            $stmt->fetch();

            if ($guide_id) {
                $selected_package_id = $_POST['selected_package_id'];

                // Insert request into Guide_Needed table or similar
                $query = "INSERT INTO guide_requests (guide_id, package_id, status) VALUES (?, ?, 'pending')";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $guide_id, $selected_package_id);

                if ($stmt->execute()) {
                    echo "Package selected successfully!";
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "Guide not found!";
            }
        } else {
            echo "Please log in as a guide to select a package.";
        }
    }
    ?>
</body>
</html>

