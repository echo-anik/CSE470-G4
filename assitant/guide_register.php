<?php
require 'dbconnect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


$error = '';
$success = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $language_proficiency = $_POST['language_proficiency'];  // Correct key
    $living_country = $_POST['living_country'];
    $living_city = $_POST['living_city'];
    $age = $_POST['age'];
    $experience = $_POST['experience'];
    $role = $_POST['role'];

    // Check if all fields are filled
    if (empty($name) || empty($email) || empty($language_proficiency) || empty($living_country)
        || empty($living_city) || empty($age) || empty($experience) || empty($role)) {
        $error = "All fields are required!";
    } elseif ($age <= 10) {
        $error = "Age should be more than 10!";
    } else {
        // Prepare the SQL query
        $stmt = $conn->prepare("INSERT INTO guide_registration
            (name, email, language_proficiency, living_country, living_city, age, experience, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param("sssssiis", $name, $email, $language_proficiency, $living_country, $living_city,
                         $age, $experience, $role);

        // Execute the query
        if ($stmt->execute()) {
            $success = "Registration successful!";  // Set success message
            // Redirect after 1 second
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'guide_login.php';
                    }, 500);
                </script>";
        } else {
            $error = "Error: " . $stmt->error;  // Set error message
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
    <title>Tour Guide and Translator Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Register as Guide or Translator</h1>
    <style> h1 { text-align: center; }</style>

    <!-- Show Error Message if Any -->
    <!-- <?php if ($error) { echo "<p style='color:red;'>$error</p>"; } ?> -->

    <!-- Show Success Message if Registration is Successful -->
    <?php if ($success) { echo "<p style='color:green;'>$success</p>"; } ?>

    <form action="" method="POST">
        <!-- Name -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <!-- Language Proficiency -->
        <label for="language_proficiency">Language Proficiency:</label>
        <select id="language_proficiency" name="language_proficiency" required>
            <option value="">Select Language</option>
            <option value="English">English</option>
            <option value="Bengali">Bengali</option>
            <option value="Arabic">Arabic</option>
            <option value="Spanish">Spanish</option>
            <option value="French">French</option>
            <option value="German">German</option>
            <option value="Chinese">Chinese</option>
            <option value="Japanese">Japanese</option>
            <option value="Russian">Russian</option>
            <option value="Hindi">Hindi</option>
            <option value="Urdu">Urdu</option>
        </select>

        <!-- Living Country -->
        <label for="living_country">Living Country:</label>
        <input type="text" id="living_country" name="living_country" required>

        <!-- Living City -->
        <label for="living_city">Living City:</label>
        <input type="text" id="living_city" name="living_city" required>

        <!-- Age -->
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" min="11" required>

        <!-- Experience -->
        <label for="experience">Experience (Years):</label>
        <input type="number" id="experience" name="experience" min="0" required>

        <!-- Role -->
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="">Select Role</option>
            <option value="TravelMate">TravelMate (Tour Guide)</option>
            <option value="TalkMate">TalkMate (Translator)</option>
        </select>

        <button type="submit">Register Now</button>
        <p>Already have a profile? <a href="guide_login.php">Sign in</a></p>
        <style> p { text-align: center; }</style>
    </form>

</body>
</html>
