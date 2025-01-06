<?php
// Start the session and include the database connection
session_start();
include 'includes/db_connection.php';

// Include the header
include 'includes/header.php';

// Include the hero section
include 'includes/hero_section.php';

// Include the search section
include 'includes/search_section.php';

// Include the featured packages section
include 'includes/featured_packages.php';

// Include the popular hotels section
include 'includes/popular_hotels.php';

// Include the transport options section
include 'includes/transport_options.php';

// Include the expert guides section
include 'includes/expert_guides.php';

// Include the footer
include 'includes/footer.php';

// Close the database connection
$conn->close();
?>