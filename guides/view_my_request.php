<?php
require 'dbconnect.php';
session_start(); // Start the session

// Ensure guide_email is set in the session
if (!isset($_SESSION['guide_email'])) {
    header("Location: guide_login.php");  // Redirect if guide is not logged in
    exit();
}

$guide_email = $_SESSION['guide_email'];  // Get the email from the session

// Query to select all requests related to the logged-in guide's email, joining Guide_Needed and guide_registration
$query = "SELECT * FROM Guide_Needed gn
          JOIN guide_registration gr ON gn.email = gr.email
          WHERE gr.email = '$guide_email'";  // Filter by guide's email
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Request</title>
    <link rel="stylesheet" href="guide_homepage.css">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Country</th>
                <th>City</th>
                <th>Role</th>
                <th>Status</th>
                <th>Journey Date</th>
                <th>Return Date</th>
                <th>Payment</th>
                <th>Other Details</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['country']; ?></td>
                    <td><?php echo $row['city']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td>
                        <select class="status" data-id="<?php echo $row['id']; ?>">
                            <option value="pending" <?php echo ($row['status'] == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="accepted" <?php echo ($row['status'] == 'accepted' ? 'selected' : ''); ?>>Accepted</option>
                        </select>
                    </td>
                    <td><?php echo $row['journey_date']; ?></td>
                    <td><?php echo $row['return_date']; ?></td>
                    <td><?php echo $row['payment_amount']; ?></td>
                    <td><?php echo $row['other_details']; ?></td>
                    <td>
                        <button class="update-status">Update Status</button>
                        <button class="view-journey">View Journey</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        $(document).on('change', '.status', function () {
            const requestId = $(this).data('id');
            const newStatus = $(this).val();
            $.ajax({
                url: 'update_request_status.php',
                type: 'POST',
                data: { id: requestId, status: newStatus },
                success: function (response) {
                    alert("Status updated successfully!");
                }
            });
        });
    </script>
</body>
</html>

