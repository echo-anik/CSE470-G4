<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelease";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_SESSION['search_criteria'])) {
    $fromCity = $_SESSION['search_criteria']['fromCity'];
    $toCity = $_SESSION['search_criteria']['toCity'];
    $departureDate = $_SESSION['search_criteria']['departureDate'];

    
    $query = "SELECT * FROM Bus WHERE FromCity = '$fromCity' AND ToCity = '$toCity' AND Departure = '$departureDate'";
    $result = $conn->query($query);

    
    $buses = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $buses[] = $row;
        }
    }
} else {
    echo "No search criteria provided!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="bus1.png" type="image/x-icon">
    <title>Bus Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('back5.jpg');
            background-size: cover;
            background-position: cover;
            background-repeat: no-repeat;
            margin: 0;
            padding: 10px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .bus-card {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #fff;
        }
        .bus-image {
            flex: 0 0 120px;
            margin-right: 15px;
        }
        .bus-image img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .bus-details {
            flex: 1;
        }
        .bus-details h3 {
            margin: 0;
            font-size: 20px;
        }
        .bus-details p {
            margin: 5px 0;
            color: #555;
            font-size: 14px;
        }
        .book-button {
            flex: 0 0 150px;
            text-align: center;
            margin-left: auto;
        }
        .book-button button{
            display: inline-block;
            padding: 10px 20px;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .book-button button:hover {
            background-color: #0056b3;
        }
        .no-results {
            text-align: center;
            font-size: 18px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Results</h2>
        <?php if (count($buses) > 0): ?>
            <?php foreach ($buses as $bus): ?>
                <div class="bus-card">
                    <div class="bus-image">
                        <img src="road1.jpg" alt="Bus Image"> 
                    </div>
                    <div class="bus-details">
                        <h3>Bus Name: <?php echo $bus['BusName']; ?></h3>
                        <p><strong>From:</strong> <?php echo $bus['FromCity']; ?></p>
                        <p><strong>To:</strong> <?php echo $bus['ToCity']; ?></p>
                        <p><strong>Departure Date:</strong> <?php echo $bus['Departure']; ?></p>
                        <p><strong>Departure Time:</strong> <?php echo $bus['DepartureTime']; ?></p>
                        <p><strong>Seats Available:</strong> <?php echo $bus['Seat']; ?></p>
                        <p><strong>Availability:</strong> <?php echo $bus['Availability']; ?></p>
                    </div>
                    <div class="book-button">
                        <button onclick="bookTicket()">Book Now</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">No buses found matching your criteria.</p>
        <?php endif; ?>
    </div>
    <script>
        function bookTicket() {
            alert("Your ticket has been booked.");
            window.location.href = "trial.php";
        }
        
    </script>
</body>
</html>
