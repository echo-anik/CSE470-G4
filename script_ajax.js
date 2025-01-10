function fetchCities(type, countryId) {
    var citySelect = document.getElementById(type === 'living_country' ? 'living_city' : 'explore_city');

    // Make an AJAX request to fetch cities based on the selected country
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_cities.php?country_id=" + countryId, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var cities = JSON.parse(xhr.responseText);
            citySelect.innerHTML = "<option value=''>Select City</option>";
            cities.forEach(function(city) {
                citySelect.innerHTML += "<option value='" + city.id + "'>" + city.name + "</option>";
            });
        }
    };
    xhr.send();
}
