<?php
require 'dbconnect.php';

// Start session to get user information (if needed)
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $country = $_POST['country'];
    $city = $_POST['city'];
    $role = $_POST['role'];
    $language_proficiency = $_POST['language_proficiency'];
    $journey_date = $_POST['journey_date'];
    $return_date = $_POST['return_date'];
    $travelers_number = $_POST['travelers_number'];
    $payment_amount = $_POST['payment_amount'];
    $other_details = $_POST['other_details'];

    // PHP Validation: Check if any field is empty or invalid
    if (empty($country) || empty($city) || empty($role) || empty($language_proficiency) || 
        empty($journey_date) || empty($return_date) || empty($travelers_number) || 
        empty($payment_amount) || empty($other_details)) {
        $error_message = "All fields are required!";
    } elseif ($travelers_number <= 0) {
        $error_message = "Number of Travelers must be greater than 0.";
    } elseif ($payment_amount <= 10) {
        $error_message = "Payment Amount must be greater than 10.";
    } else {
       
        $stmt = $conn->prepare("INSERT INTO Guide_Needed (country, city, role, language_proficiency, journey_date, 
                                return_date, travelers_number, payment_amount, other_details) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param("ssssssiis", $country, $city, $role, $language_proficiency, $journey_date, 
                         $return_date, $travelers_number, $payment_amount, $other_details);

       
        if ($stmt->execute()) {
            // Success message
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'view_my_request.php'; 
                    }, 1000);
                </script>";
        } else {
            $error_message = "Error: " . $stmt->error; 
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
    <title>Need Guide</title>
    <link rel="stylesheet" href="sliding_bar.css">
    <link rel="stylesheet" href="guide_homepage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
    
        #guide-request-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        /* Styling for individual form fields to appear side by side */
        #guide-request-form div {
            flex: 1 1 45%;
            min-width: 250px;
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

        /* Styling for the bottom-left "Back to Homepage" button */
        .back-button {
            position: fixed;
            bottom: 10px;
            left: 10px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Form for submitting a guide request -->
    <form method="POST" id="guide-request-form" onsubmit="return validateForm()">
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
            <input type="number" name="travelers_number" id="travelers_number" min="1" required>
        </div>
        <div>
            <label for="payment_amount">Payment Amount (USD):</label>
            <input type="number" name="payment_amount" id="payment_amount" min="10" required>
        </div>
        <div>
            <label for="other_details">Other Details:</label>
            <textarea name="other_details" id="other_details" required></textarea>
        </div>

        <?php if (isset($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>

        <button type="submit">Submit Request</button>
    </form>

    <!-- Back to homepage button -->
    <div class="back-button">
        <button onclick="window.location.href='guide_home_page.php'">Back to Homepage</button>
    </div>

    <script>
        function validateForm() {
            // Get the values of the inputs
            var travelersNumber = document.getElementById("travelers_number").value;
            var paymentAmount = document.getElementById("payment_amount").value;

            // Validate Number of Travelers
            if (travelersNumber <= 0) {
                alert("Number of Travelers must be greater than 0.");
                return false; // Prevent form submission
            }

            // Validate Payment Amount
            if (paymentAmount <= 10) {
                alert("Payment Amount must be greater than 10.");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>

</body>
</html>
