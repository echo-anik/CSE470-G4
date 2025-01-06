<?php
require 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['package_id'])) {
        $packageId = intval($_POST['package_id']);
        
        // Check if the package ID exists
        $query = "SELECT * FROM tour_packages WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $packageId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Insert the package into the cart
            $query = "INSERT INTO guide_package_cart (package_id) VALUES (?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $packageId);

            if ($stmt->execute()) {
                echo "Package added to cart successfully!";
            } else {
                echo "Failed to add package to cart.";
            }
        } else {
            echo "Package not found.";
        }
        $stmt->close();
    } else {
        echo "Invalid package ID.";
    }
} else {
    echo "Invalid request method.";
}
$conn->close();
?>
