<?php
// search_handler.php
include 'includes/db_connection.php';

header('Content-Type: application/json');

$category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';
$location = isset($_POST['location']) ? $conn->real_escape_string($_POST['location']) : '';

$results = [];

switch ($category) {
    case 'hotels':
        $query = "SELECT name, location, description, image_url FROM hotels WHERE status = 'active'";
        if ($location) $query .= " AND location LIKE '%$location%'";
        break;

    case 'flights':
        $query = "SELECT flight_number AS name, departure AS location, description, image_url FROM flights WHERE status = 'available'";
        if ($location) $query .= " AND (departure LIKE '%$location%' OR arrival LIKE '%$location%')";
        break;

    case 'trains':
        $query = "SELECT train_number AS name, departure AS location, description, image_url FROM trains WHERE status = 'available'";
        if ($location) $query .= " AND (departure LIKE '%$location%' OR arrival LIKE '%$location%')";
        break;

    case 'buses':
        $query = "SELECT bus_number AS name, departure AS location, description, image_url FROM buses WHERE status = 'available'";
        if ($location) $query .= " AND (departure LIKE '%$location%' OR arrival LIKE '%$location%')";
        break;

    case 'guides':
        $query = "SELECT name, living_city AS location, description, image_url FROM guides WHERE status = 'active'";
        if ($location) $query .= " AND (living_city LIKE '%$location%' OR living_country LIKE '%$location%')";
        break;

    default:
        echo json_encode([]);
        exit;
}

$result = $conn->query($query);
if ($result) {
    $results = $result->fetch_all(MYSQLI_ASSOC);
} else {
    error_log("Query Error: " . $conn->error);
}

echo json_encode($results);
?>