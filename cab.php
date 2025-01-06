<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelease";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from = $_POST['from'];
    $to = $_POST['to'];
    $departureDate = $_POST['departure'];
    $pickupTime = $_POST['pickup-time'];
    $dropTime = $_POST['drop-time'];

    
    $_SESSION['search_criteria'] = [
        'fromCity' => $from,
        'toCity' => $to,
        'departureDate' => $departureDate,
        'pickupTime' => $pickupTime,
        'dropTime' => $dropTime,
    ];

    
    header("Location: search_cab.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cab</title>
    <link rel="icon" href="cab.png" type="image/x-icon">
   
    <style>
        
        body {
            margin: 0;
            padding: 0;
            background-image: url('cab background.jpg');
            background-size: 150%;
            background-position: cover;
            background-repeat: no-repeat;
            height: 100vh; 
            font-family: Arial, sans-serif;
        }

        
        .logo {
            position: absolute; 
            top: 10px; left: 10px;
            font-size: 30px;
            font-weight: bold; 
            color: #1d1f1d; 
            font-style: italic; 
            text-shadow: 2px 2px 5px #5e4f4f, 0 0 10px #414441, 0 0 15px #303330, 0 0 20px #2c302c; 
            background: linear-gradient(to right, #ff5f6d, #ffc371); 
            
            padding: 3px; 
            border-radius: 3px; 
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3); 
            transition: transform 0.3s ease; 
        }

        
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

        
        .navbar {
            display: flex;
            justify-content: center; 
            align-items: center;
            background-color: #fff;
            padding: 10px;
            box-shadow: 0 4px 2px -2px gray;
            border-radius: 25px; 
            margin-top: 60px; 
            width: 80%; 
            margin-left: auto;
            margin-right: auto;
        }

        .navbar a {
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            padding: 10px 15px;
            margin: 0 5px;
            border-radius: 15px; 
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

       
        .booking-info {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: white;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        
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
            width: 90%;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9); 
            border-radius: 10px;
            box-shadow: 0 4px 2px -2px gray;
        }

        .ticket-form-header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #01050a;
            margin-bottom: 20px;
        }

       
        .ticket-form input,
        .ticket-form select {
            padding: 15px;
            margin: 10px;
            width: 100%;
            max-width: 400px;
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

        
        .form-row {
            display: flex;
            justify-content: space-between; 
            gap: 15px; 
            width: 100%;
            margin-bottom: 20px;
        }

        .form-row div {
            flex: 1;
        }

        .form-row label {
            display: block;
            margin-bottom: 5px;
            font-size: 18px;
            font-weight: bold;
        }

        .form-row input {
            width: 80%;
            padding: 10px;
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
   
    <div class="logo">Travelease</div>
    <button class="login-btn">Login</button>

    
    <div class="navbar">
        <a href="#"><i class="fas fa-plane"></i> Flights</a>
        <a href="#"><i class="fas fa-hotel"></i> Hotels</a>
        <a href="#"><i class="fas fa-suitcase"></i> Holiday Packages</a>
        <a href="#"><i class="fas fa-train"></i> Trains</a>
        <a href="#"><i class="fas fa-bus"></i> Buses</a>
        <a href="#" class="active"><i class="fas fa-taxi"></i> Cabs</a>
        <a href="#"><i class="fas fa-headset"></i> Tour Assistance</a>
    </div>
    <form method="POST" action="search_cab.php">
    
    <div class="ticket-section">
        <div class="ticket-form">
            
            <div class="ticket-form-header">
                Need Cabs?? Online Cab Booking is Here!!!!
            </div>
            
            <div class="form-row">
                <div>
                    <label for="from">From:</label>
                    <input type="text" id="from" name="from" placeholder="departure city" required>
                </div>
                <div>
                    <label for="to">To:</label>
                    <input type="text" id="to" name="to" placeholder="destination city" required>
                </div>
                <div>
                    <label for="departure">Departure Date:</label>
                    <input type="date" id="departure" name="departure" required>
                </div>
                <div>
                    <label for="pickup-time">Pickup Time:</label>
                    <input type="time" id="pickup-time" name="pickup-time" required>
                </div>
                <div>
                    <label for="drop-time">Drop Time:</label>
                    <input type="time" id="drop-time" name="drop-time" required>
                </div>
            </div>

            <button>Search</button>
            
        </div>
    </div>
        </form>
    
    <div class="offers-section">
        <div class="offers-header">Offers</div>

       
        <div class="offer-category">
            <a href="#">Bus</a>
            <a href="#">All Offers</a>
            <a href="#" class="active">Cabs</a>
            <a href="#">Hotels</a>
            <a href="#">Flights</a>
            <a href="#">Holidays</a>
            <a href="#">Trains</a>
        </div>

        
        <div class="offers-grid">
            <div class="offer-card">
                <img src="cab2.jpeg" alt="Blockbuster Deal">
                <div class="offer-title">Blockbuster Deal: Up to Tk300 OFF*</div>
                <div class="offer-details">On Cabs</div>
                <div class="offer-code">Code: JANUARYDEAL</div>
                <button class="offer-button">Book Now</button>
            </div>

            <div class="offer-card">
                <img src="cab3.jpg" alt="Special Discount">
                <div class="offer-title">MMTBLACK SPECIAL: FLAT TK150 OFF*</div>
                <div class="offer-details">On Cabs Tickets</div>
                <div class="offer-code">Code: MMTBLACK</div>
                <button class="offer-button">Book Now</button>
            </div>

            <div class="offer-card">
                <img src="cab4.jpg" alt="Regional Discount">
                <div class="offer-title">Grab FLAT 8% OFF* on Cabs</div>
                <div class="offer-details">From Dhaka, Narayanganj & more</div>
                <div class="offer-code">Code: MMTNORTH</div>
                <button class="offer-button">Book Now</button>
            </div>

            <div class="offer-card">
                <img src="cab5.jpg" alt="Regional Discount">
                <div class="offer-title">Grab FLAT 8% OFF* on Cabs</div>
                <div class="offer-details">From Sreemangal, Chattogram & more</div>
                <div class="offer-code">Code: MMTSOUTH</div>
                <button class="offer-button">Book Now</button>
            </div>
        </div>
    </div>
 
    
</body>
</html>
