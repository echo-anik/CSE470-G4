<?php
require 'dbconnect.php';

// Start session to identify the user
session_start();

// Assume user identification is stored in the session (e.g., user_id or email)
// Adjust based on how you store user details in your system
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

if (!$user_email) {
    // Redirect to login if user is not logged in
    header('Location: login.php');
    exit;
}

// Fetch the guide requests made by the logged-in user
$query = "SELECT * FROM Guide_Needed WHERE user_email = ? ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Guide Requests</title>
    <link rel="stylesheet" href="sliding_bar.css">
    <link rel="stylesheet" href="guide_homepage.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .action-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .action-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="completed-journeys">
    <h2>Your Guide Requests</h2>
    <table>
        <tr>
            <th>Country</th>
            <th>City</th>
            <th>Role</th>
            <th>Language Proficiency</th>
            <th>Journey Date</th>
            <th>Return Date</th>
            <th>Travelers</th>
            <th>Payment (USD)</th>
            <th>Other Details</th>
            <th>Actions</th>
        </tr>
        <?php while ($request = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($request['country']); ?></td>
            <td><?php echo htmlspecialchars($request['city']); ?></td>
            <td><?php echo htmlspecialchars($request['role']); ?></td>
            <td><?php echo htmlspecialchars($request['language_proficiency']); ?></td>
            <td><?php echo htmlspecialchars($request['journey_date']); ?></td>
            <td><?php echo htmlspecialchars($request['return_date']); ?></td>
            <td><?php echo htmlspecialchars($request['travelers_number']); ?></td>
            <td><?php echo htmlspecialchars($request['payment_amount']); ?></td>
            <td><?php echo htmlspecialchars($request['other_details']); ?></td>
            <td>
                <button class="action-button" 
                        onclick="window.location.href='view_completed_guide_journey.php?id=<?php echo $request['id']; ?>'">
                    View Details
                </button>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<!-- Back to homepage button -->
<div class="back-button">
    <button onclick="window.location.href='guide_home_page.php'">Back to Homepage</button>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>



