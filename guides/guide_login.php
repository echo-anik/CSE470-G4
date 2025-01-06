<?php
require 'dbconnect.php';

$error = '';  // Variable to hold error messages
$successMessage = '';  // Variable to hold success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Check if both email and role are provided
    if (empty($email) || empty($role)) {
        $error = "Both email and role are required!";
    } else {
        // Prepare the SQL query to check if email and role match in the guide_registration table
        $stmt = $conn->prepare("SELECT * FROM guide_registration WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);

        // Execute the query
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            // Check if any matching record is found
            if ($result->num_rows > 0) {
                // Fetch the guide details
                $guide = $result->fetch_assoc();
                // Set session variables for guide ID
                session_start();
                $_SESSION['guide_id'] = $guide['id']; // Assuming 'id' is the unique identifier for the guide
                // Login successful
                $successMessage = "Login successful!";
                // Redirect to guide dashboard
                echo "<script>setTimeout(function(){ window.location.href = 'guide_dashboard.php'; }, 1000);</script>";
            } else {
                // Login failed, invalid email or role
                $error = "Invalid email or role!";
            }
        } else {
            $error = "Database query failed: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Guide Login</h1>
    <style> h1 { text-align: center; }</style>

    <!-- Show Error Message if Any -->
    <?php if ($error) { echo "<p style='color:red;'>$error</p>"; } ?>

    <!-- Show Success Message if Login is Successful -->
    <?php if ($successMessage) { echo "<p style='color:green;'>$successMessage</p>"; } ?>

    <form action="" method="POST">
        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <!-- Role -->
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="">Select Role</option>
            <option value="TravelMate">TravelMate (Tour Guide)</option>
            <option value="TalkMate">TalkMate (Translator)</option>
        </select>

        <button type="submit">Login</button>
        <p>Don't have an account? <a href="guide_register.php">Register Now</a></p>
        <style> p { text-align: center; }</style>
    </form>

</body>
</html>



