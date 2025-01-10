<?php
require 'dbconnect.php';

session_start();
$guide_id = $_SESSION['guide_id']; 

// Fetch the guide packages from the database
$query = "SELECT tp.*, gr.name, gr.email, gr.language_proficiency, gr.age, gr.experience, gr.role
          FROM tour_packages tp
          JOIN guide_registration gr ON tp.guide_id = gr.id
          WHERE tp.guide_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Dashboard</title>
    <link rel="stylesheet" href="sliding_bar.css">
    <link rel="stylesheet" href="guide_homepage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .flex-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }

        .package-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 15px;
            text-align: center;
        }

        .package-card h3 {
            margin: 10px 0;
            color: #333;
        }

        .package-card p {
            margin: 5px 0;
            color: #666;
        }

        .package-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .package-btn:hover {
            background-color: #0056b3;
        }

        .sliding-bar {
            position: fixed;
            top: 0;
            height: 100%;
            width: 250px;
            background-color: #333;
            color: white;
            overflow-x: hidden;
            padding-top: 20px;
            transform: translateX(-250px);
            transition: transform 0.3s;
        }

        .sliding-bar.right {
            right: 0;
            left: auto;
            transform: translateX(250px);
        }

        .sliding-bar.open {
            transform: translateX(0);
        }

        .sliding-bar button {
            background: none;
            color: white;
            border: none;
            margin: 10px;
            font-size: 18px;
            cursor: pointer;
        }

        .toggle-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #333;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .toggle-button.right {
            left: auto;
            right: 20px;
        }

        .center-heading {
            text-align: center;
            font-size: 35px;
            margin: 20px 0;
        }

        /* Right sliding bar initial position off-screen */
        #rightBar {
            position: fixed;
            right: -300px; /* Initially hidden off-screen */
            top: 0;
            width: 300px; /* Adjust this width as needed */
            height: 100%;
            background-color: #333;
            color: white;
            z-index: 9999; /* Ensure it appears on top of other content */
            transition: right 0.3s ease-in-out; /* Smooth transition effect */
            padding-top: 20px; /* Add some space at the top */
        }

        /* When the sliding bar is toggled to show */
        #rightBar.open {
            right: 0; /* Slides into view */
        }

        /* Style for the close button */
        #rightBar button {
            background-color: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 10px;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        /* List styles inside the sliding bar */
        #rightBar ul {
            list-style-type: none;
            padding: 0;
        }

        #rightBar ul li {
            padding: 15px;
            text-align: center;
        }

        #rightBar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
        }

        #rightBar ul li a:hover {
            background-color: #575757; 
        }
    </style>
</head>

<body>
    <button class="toggle-button right" onclick="toggleSlidingBar('right')">☰</button>

    <div id="rightBar" class="sliding-bar right">
        <button onclick="toggleSlidingBar('right')">Close &times;</button>
        <ul>
            <li><a href="view_my_request.php" style="color: white;">View My Request</a></li>
            <li><a href="guide_register.php" style="color: white;">Register as a Guide</a></li>
            <li><a href="need_guide.php" style="color: white;">Need Guide</a></li>
        </ul>
    </div>

    <h2 class="center-heading">Your Guide Packages</h2>

    <div class="flex-container">
        <?php while ($package = $result->fetch_assoc()) { ?>
            <div class="package-card">
                <h3><?php echo htmlspecialchars($package['explore_country']) . ' - ' . htmlspecialchars($package['explore_city']); ?></h3>
                <p><strong>Guide:</strong> <?php echo htmlspecialchars($package['name']); ?></p>
                <p><strong>Hourly Rate:</strong> $<?php echo number_format($package['hourly_rate'], 2); ?>/hour</p>
                <p><strong>Journey:</strong> <?php echo htmlspecialchars($package['journey_date']) . ' to ' . htmlspecialchars($package['return_date']); ?></p>
                <button class="package-btn" onclick="window.location.href='view_guide_package.php?package_id=<?php echo $package['id']; ?>'">
                    View Details
                </button>
            </div>
        <?php } ?>
    </div>

    <script>
        function toggleSlidingBar(side) {
            const bar = document.getElementById(side + 'Bar');
            bar.classList.toggle('open');
        }
    </script>
</body>

</html>
 



