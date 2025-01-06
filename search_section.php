<?php
// /includes/search_section.php
?>

<!-- Search Section -->
<section class="search-section">
    <div class="search-container">
        <div class="search-input">
            <select id="category-select">
                <option value="">Select Category</option>
                <option value="hotels">Hotels</option>
                <option value="flights">Flights</option>
                <option value="trains">Trains</option>
                <option value="buses">Buses</option>
                <option value="guides">Tour Guides</option>
            </select>
        </div>
        <div class="search-input">
            <input type="text" id="location-input" placeholder="Where to?">
        </div>
        <button class="btn search-btn" onclick="performSearch()">
            <i class="fas fa-search"></i> Search
        </button>
    </div>
    <div id="search-results"></div>
</section>