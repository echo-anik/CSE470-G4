<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_page_styles.css">
</head>
<body>
    <div class="admin-dashboard">
        <h1>Welcome Admin</h1>
        <button id="search-guide-btn" class="search-guide-btn">Search Guide</button>
    </div>

    <script>
        document.getElementById("search-guide-btn").addEventListener("click", function() {
            window.location.href = "guide_home_page.php";
        });
    </script>
</body>
</html>

