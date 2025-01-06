<?php
require 'dbconnect.php';

// Fetch the guide's details (if needed, based on logged-in user)
session_start();
$guide_id = $_SESSION['guide_id']; // Assuming guide_id is stored in session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert guide request
    $country = $_POST['country'];
    $city = $_POST['city'];
    $role = $_POST['role'];
    $language_proficiency = $_POST['language_proficiency'];
    $journey_date = $_POST['journey_date'];
    $return_date = $_POST['return_date'];
    $travelers_number = $_POST['travelers_number'];
    $payment_amount = $_POST['payment_amount'];
    $other_details = $_POST['other_details'];

    $query = "INSERT INTO Guide_Needed (country, city, role, language_proficiency, journey_date, return_date, travelers_number, payment_amount, other_details) 
              VALUES ('$country', '$city', '$role', '$language_proficiency', '$journey_date', '$return_date', '$travelers_number', '$payment_amount', '$other_details')";
    if ($conn->query($query)) {
        echo "Request submitted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Homepage</title>
    <link rel="stylesheet" href="sliding_bar.css">
    <link rel="stylesheet" href="guide_homepage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .top-buttons {
            position: fixed;
            top: 10px;
            right: 10px;
        }
        .top-buttons button {
            margin-left: 10px;
        }

        /* Flexbox styling for the form */
        #guide-request-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between; /* Distribute space between form elements */
            display: none; /* Initially hide the form */
        }

        /* Styling for individual form fields to appear side by side */
        #guide-request-form div {
            flex: 1 1 45%; /* Allow each field to take up 45% of the available width */
            min-width: 250px; /* Set a minimum width to avoid squishing */
        }

        /* Styling for form inputs */
        input[type="text"], input[type="date"], input[type="number"], select, textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }

        textarea {
            height: 100px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="top-buttons">
        <button onclick="window.location.href='view_my_request.php'">View My Request</button>
        <button onclick="window.location.href='guide_register.php'">Register as a Guide</button>
        <button onclick="window.location.href='need_guide.php'">Need Guide</button>
        <!-- <button onclick="toggleGuideRequestForm()">Need Guide</button> New button to toggle form visibility -->
    </div>

    <!-- Form for submitting a guide request
    <form method="POST" id="guide-request-form">
        <div>
            <label for="country">Countries (comma-separated):</label>
            <input type="text" name="country" id="country" required>
        </div>
        <div>
            <label for="city">Cities (comma-separated):</label>
            <input type="text" name="city" id="city" required>
        </div>
        <div>
            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="TalkMate">TalkMate (Translator)</option>
                <option value="TravelMate">TravelMate (Guide)</option>
            </select>
        </div>
        <div>
            <label for="language_proficiency">Language Proficiency:</label>
            <input type="text" name="language_proficiency" id="language_proficiency" required>
        </div>
        <div>
            <label for="journey_date">Journey Date:</label>
            <input type="date" name="journey_date" id="journey_date" required>
        </div>
        <div>
            <label for="return_date">Return Date:</label>
            <input type="date" name="return_date" id="return_date" required>
        </div>
        <div>
            <label for="travelers_number">Number of Travelers:</label>
            <input type="number" name="travelers_number" id="travelers_number" required>
        </div>
        <div>
            <label for="payment_amount">Payment Amount (USD):</label>
            <input type="number" name="payment_amount" id="payment_amount" required>
        </div>
        <div>
            <label for="other_details">Other Details:</label>
            <textarea name="other_details" id="other_details" required></textarea>
        </div>
        <button type="submit">Submit Request</button> -->
    <!-- </form>

    <script>
        // JavaScript function to toggle visibility of the guide request form
        function toggleGuideRequestForm() {
            const form = document.getElementById('guide-request-form');
            form.style.display = form.style.display === 'none' ? 'flex' : 'none';
        }
    </script> -->
</body>
</html>
