<?php
require 'dbconnect.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $query = "UPDATE Guide_Needed SET status = '$status' WHERE id = '$id'";
    if ($conn->query($query)) {
        echo "Status updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
