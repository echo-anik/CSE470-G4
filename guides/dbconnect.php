<?php
// Database configuration
$servername = 'localhost';  // Database host (usually localhost)
$username = 'root';         // Database username
$password = '';             // Database password
$dbname = 'TravelEase';     // Name of the database

// Create a connection using $servername
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
