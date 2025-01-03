<?php
// Start session to store search results
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelease"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fromCity = $_POST['from'];
    $toCity = $_POST['to'];
    $departureDate = $_POST['date'];

    // Query to search for matching tickets
    $query = "SELECT * FROM bus WHERE FromCity = '$fromCity' AND ToCity = '$toCity' AND Departure = '$departureDate'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $_SESSION['bus_results'] = [];
        while ($row = $result->fetch_assoc()) {
            $_SESSION['bus_results'][] = $row;
        }
    } else {
        $_SESSION['bus_results'] = "No tickets found for your search.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus</title>
    <link rel="icon" href="bus1.png" type="image/x-icon">
    <style>
        /* Add a background image to the body */
        body {
            margin: 0;
            padding: 0;
            background-image: url('back5.jpg');
            background-size: 150%;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh; /* Ensures the image covers the full viewport height */
            font-family: Arial, sans-serif;
        }

        /* Style for positioning 'Travelease' at the top left corner */
        .logo {
            position: absolute; 
            top: 10px; left: 10px;
            font-size: 30px;
            font-weight: bold; 
            color: #1d1f1d; /* Light Green */ 
            font-style: italic; 
            text-shadow: 2px 2px 5px #5e4f4f, 0 0 10px #414441, 0 0 15px #303330, 0 0 20px #2c302c; 
            background: linear-gradient(to right, #ff5f6d, #ffc371); /* Gradient background */ 
            
            padding: 3px; 
            border-radius: 3px; 
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3); 
            transition: transform 0.3s ease; 
        }

        /* Style for the login button */
        .login-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-shadow: 1px 1px 1px black;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }

        /* Navigation bar styles */
        .navbar {
            display: flex;
            justify-content: center; /* Center the nav items horizontally */
            align-items: center;
            background-color: #fff;
            padding: 10px;
            box-shadow: 0 4px 2px -2px gray;
            border-radius: 25px; /* Round the corners of the nav bar */
            margin-top: 60px; /* Space from the top for 'Travelease' and login button */
            width: 80%; /* Adjust width to fit the screen */
            margin-left: auto;
            margin-right: auto;
        }

        .navbar a {
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            padding: 10px 15px;
            margin: 0 5px; /* Space between items */
            border-radius: 15px; /* Round the corners of nav items */
        }

        .navbar a:hover {
            background-color: #ddd;
        }

        .navbar .active {
            color: #007bff;
        }

        .navbar i {
            font-size: 20px;
            margin-right: 5px;
        }

        /* Bus Ticket Booking text */
        .booking-info {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: black;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        /* Ticket purchase section styles */
        .ticket-section {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        .ticket-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 80%;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* Slight opacity for background */
            border-radius: 10px;
            box-shadow: 0 4px 2px -2px gray;
        }

        .ticket-form-header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: black;
            margin-bottom: 20px;
        }

        /* Form input fields style */
        .ticket-form input,
        .ticket-form select {
            padding: 15px;
            margin: 10px;
            width: 100%;
            max-width: 400px; /* Make the fields wider */
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 18px;
        }

        .ticket-form button {
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            background-color: #2e2f31;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }

        .ticket-form button:hover {
            background-color: #111111;
        }

        /* Style for the row layout for form elements */
        .form-row {
            display: flex;
            justify-content: space-evenly; /* Spread the form elements evenly */
            width: 100%;
            margin-bottom: 20px;
        }

        .form-row div {
            flex: 1; /* Ensure each form element takes up equal space */
            padding: 0 10px;
        }

        .form-row label {
            display: block;
            margin-bottom: 5px;
            font-size: 18px;
            font-weight: bold;
        }

        .ticket-form-header {
            font-size: 15px;
        }

        /* Style the from/to/date fields to look like the photo */
        .form-row input {
            width: 80%;
            padding: 30px 30px;
            font-size: 16px;
            border-radius: 5px;
            
            border: 2px solid #ccc;
        }

        /* Offers Section */
        .offers-section {
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            width: 90%;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .offers-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .offer-category {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .offer-category a {
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }

        .offer-category a.active {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        .offers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .offer-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .offer-card img {
            width: 100%;
            border-radius: 10px;
        }

        .offer-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .offer-details {
            color: #555;
            font-size: 14px;
        }

        .offer-code {
            font-size: 14px;
            color: #007bff;
            font-weight: bold;
        }

        .offer-button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .offer-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- 'Travelease' logo and login button at the top -->
    <div class="logo">Travelease</div>
    <button class="login-btn">Login</button>

    <!-- Navigation Bar below the top section -->
    <div class="navbar">
        <a href="#" class="active"><i class="fas fa-plane"></i> Flights</a>
        <a href="#"><i class="fas fa-hotel"></i> Hotels</a>
        <a href="#"><i class="fas fa-suitcase"></i> Holiday Packages</a>
        <a href="#"><i class="fas fa-train"></i> Trains</a>
        <a href="#"><i class="fas fa-bus"></i> Buses</a>
        <a href="#"><i class="fas fa-taxi"></i> Cabs</a>
        <a href="#"><i class="fas fa-headset"></i> Tour Assistance</a>
    </div>

    <!-- Bus Ticket Booking Info with background bar -->
    <div class="ticket-section">
        <form action="trial.php" method="POST" class="ticket-form">
            <div class="ticket-form-header">
                Bus Ticket Booking. Travelling with a group? Hire a bus.
            </div>

            <div class="form-row">
                <div>
                    <label for="from">From:</label>
                    <input type="text" id="from" name="from" placeholder="Enter departure city" required>
                </div>
                <div>
                    <label for="to">To:</label>
                    <input type="text" id="to" name="to" placeholder="Enter destination city" required>
                </div>
                <div>
                    <label for="date">Travel Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
            </div>

            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Offers Section -->
    <div class="offers-section">
        <div class="offers-header">Offers</div>

        <!-- Offer Categories -->
        <div class="offer-category">
            <a href="#" class="active">Bus</a>
            <a href="#">All Offers</a>
            <a href="#">Cabs</a>
            <a href="#">Hotels</a>
            <a href="#">Flights</a>
            <a href="#">Holidays</a>
            <a href="#">Trains</a>
        </div>

        <!-- Offer Cards -->
        <div class="offers-grid">
            <div class="offer-card">
                <img src="road1.jpg" alt="Blockbuster Deal">
                <div class="offer-title">Blockbuster Deal: Up to 300৳ OFF*</div>
                <div class="offer-details">On Buses</div>
                <div class="offer-code">Code: DECEMBERDEAL</div>
                <button class="offer-button">Book Now</button>
            </div>

            <div class="offer-card">
                <img src="raod2.jpg" alt="Special Discount">
                <div class="offer-title">MMTBLACK SPECIAL: FLAT 150৳ OFF*</div>
                <div class="offer-details">On Bus Tickets</div>
                <div class="offer-code">Code: MMTBLACK</div>
                <button class="offer-button">Book Now</button>
            </div>

            <div class="offer-card">
                <img src="raod3.jpeg" alt="Regional Discount">
                <div class="offer-title">Grab FLAT 8% OFF* on Buses</div>
                <div class="offer-details">From Dhaka, Khulna & more</div>
                <div class="offer-code">Code: MMTNORTH</div>
                <button class="offer-button">Book Now</button>
            </div>

            <div class="offer-card">
                <img src="raod4.jpeg" alt="Regional Discount">
                <div class="offer-title">Grab FLAT 8% OFF* on Buses</div>
                <div class="offer-details">From Sylhet, Chattogram & more</div>
                <div class="offer-code">Code: MMTSOUTH</div>
                <button class="offer-button">Book Now</button>
            </div>
        </div>
    </div>

    <!-- Font Awesome for icons (You can link to a CDN or include locally) -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>


  
    <!-- Display Results Below -->
    <div class="result-container">

        <?php
        if (isset($_SESSION['bus_results'])) {
            $results = $_SESSION['bus_results'];

            if (is_array($results) && count($results) > 0) {
                foreach ($results as $ticket) {
                    echo "<div class='ticket'>";
                    echo "<h3>Ticket ID: " . $ticket['id'] . "</h3>";
                    echo "<p><strong>From:</strong> " . $ticket['FromCity'] . "</p>";
                    echo "<p><strong>To:</strong> " . $ticket['ToCity'] . "</p>";
                    echo "<p><strong>Departure:</strong> " . $ticket['Departure'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p class='no-result'>" . $results . "</p>";
            }

            unset($_SESSION['bus_results']);
        }
        ?>
    </div>

</body>
</html>
