<?php
session_start();  

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    header('Location: guide_login.php');
    exit();
}

require 'dbconnect.php';

$guide_id = $_SESSION['guide_id'];

// Fetch guide profile details
$stmt = $conn->prepare("SELECT * FROM guide_registration WHERE id = ?");
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$result = $stmt->get_result();
$guide = $result->fetch_assoc();
$stmt->close();


// Fetch completed journeys
$stmt = $conn->prepare("SELECT * FROM completed_journey WHERE guide_id = ?");
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$journeys = $stmt->get_result();
$stmt->close();


// Fetch tour packages
$stmt = $conn->prepare("SELECT * FROM tour_packages WHERE guide_id = ?");
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$tour_packages = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Dashboard</title>
    <link rel="stylesheet" href="guide_dashboard_styles.css">
</head>
<body>
    <h1>Welcome to Your Dashboard, <?php echo $guide['name']; ?>!</h1>

    <div class="profile-info">
        <h2>Your Profile</h2>
        <table>
            <tr><td>Name:</td><td><?php echo $guide['name']; ?></td></tr>
            <tr><td>Email:</td><td><?php echo $guide['email']; ?></td></tr>
            <tr><td>Language Proficiency:</td><td><?php echo $guide['language_proficiency']; ?></td></tr>
            <tr><td>Living Country:</td><td><?php echo $guide['living_country']; ?></td></tr>
            <tr><td>Living City:</td><td><?php echo $guide['living_city']; ?></td></tr>
            <tr><td>Age:</td><td><?php echo $guide['age']; ?></td></tr>
            <tr><td>Experience (Years):</td><td><?php echo $guide['experience']; ?></td></tr>
            <tr><td>Role:</td><td><?php echo $guide['role']; ?></td></tr>
        </table>
    </div>

    <div class="completed-journeys">
        <h2>Your Completed Journeys</h2>
        <table>
            <tr><th>Journey Name</th><th>Journey Details</th></tr>
            <?php while ($journey = $journeys->fetch_assoc()) { ?>
                <tr><td><?php echo $journey['journey_name']; ?></td><td><?php echo $journey['journey_details']; ?></td></tr>
            <?php } ?>
        </table>
    </div>

    <div class="tour-packages">
        <h2>Your Tour Packages</h2>
        <div class="flex-container">
            <?php while ($package = $tour_packages->fetch_assoc()) { ?>
                <div class="package-card">
                    <h3><?php echo $package['explore_country']; ?></h3>
                    <p><?php echo $package['explore_city']; ?></p>
                    <!-- <table>
                         <tr><td><strong>Other Details:</strong></td><td><?php echo nl2br($package['package_details']); ?></td></tr>
                    </table> -->
                    <button class="package-btn" onclick="window.location.href='view_package.php?id=<?php echo $package['id']; ?>'">View Package</button>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="where-i-will-go">
    <h2>Where I Wish To Go!</h2>
    <form id="where-i-will-go-form">
        <!-- Name field - already populated, no need to be required -->
        <input type="text" name="name" value="<?php echo $guide['name']; ?>" readonly>

        <!-- Email field - already populated, no need to be required -->
        <input type="email" name="email" value="<?php echo $guide['email']; ?>" readonly>

        <!-- Language proficiency - already populated, no need to be required -->
        <input type="text" name="language_proficiency" value="<?php echo $guide['language_proficiency']; ?>" readonly>

        <!-- Explore Country - this field is required -->
        <input type="text" name="explore_country" placeholder="Enter Country" required>

        <!-- Explore City - this field is required -->
        <input type="text" name="explore_city" placeholder="Enter City" required>

        <!-- Age - already populated, no need to be required -->
        <input type="number" name="age" value="<?php echo $guide['age']; ?>" readonly>

        <!-- Experience - already populated, no need to be required -->
        <input type="number" name="experience" value="<?php echo $guide['experience']; ?>" readonly>

        <!-- Role - already populated, no need to be required -->
        <input type="text" name="role" value="<?php echo $guide['role']; ?>" readonly>

        <!-- Price (in USD) -->
        <input type="number" name="price" step="0.01" placeholder="Enter Price in USD" required>


        <!-- Other Details Table -->
        <table>
            <tr>
                <td><strong>Other Details:</strong></td>
                <td>
                    <textarea name="package_details" placeholder="Add your details here" required></textarea>
                </td>
            </tr>
        </table>

        <!-- Submit button -->
        <button type="submit">Submit</button>
    </form>
    </div>

    <script>
    // Handle form submission via Ajax
      document.getElementById('where-i-will-go-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_tour_package.php', true);
        xhr.onload = function() {
            if (xhr.status == 200) {
                // Reload the packages dynamically
                location.reload();
            } else {
                alert('Error submitting the form');
            }
        };
        xhr.send(formData);
      });
    </script>



    <script>
        // Handle form submission via Ajax
        document.getElementById('where-i-will-go-form').addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_tour_package.php', true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Reload the packages dynamically
                    location.reload();
                }
            };
            xhr.send(formData);
        });

        function viewPackageDetails(packageId) {
            // Implement the logic to show pre-packaged tour details
            alert('Show details for package ID: ' + packageId);
        }
    </script>

    <!-- Back to Login Button -->
    <div class="back-to-login">
        <a href="guide_login.php">
            <button type="button">Back to Login</button>
        </a>
    </div>
    <div class="back-to-home-page">
        <a href="guide_home_page.php">
            <button type="button">Back to Home Page</button>
        </a>
    </div>
</body>
</html>