



// Variable to store selected option
let selectedOption = '';

// Function to handle selection
function selectOption(option) {
    selectedOption = option; // Store selected option
    document.getElementById('searchInput').value = option; // Update search input with selected option
    document.getElementById('message').innerText = ''; // Clear error message
}

// Function to handle search
function searchTourGuide() {
    const inputValue = document.getElementById('searchInput').value.trim(); // Get trimmed input value
    const messageElement = document.getElementById('message'); // Get message element

    // Validate if the input matches the selected option
    if (selectedOption && inputValue === selectedOption) {
        messageElement.innerText = 'Successfully searched!';
        messageElement.style.color = 'green'; // Success message color
    } else {
        messageElement.innerText = 'Incorrect Search. Please select a valid option.';
        messageElement.style.color = 'red'; // Error message color
    }

    // Clear search bar and reset message after 2 seconds
    setTimeout(() => {
        document.getElementById('searchInput').value = ''; // Clear search bar
        messageElement.innerText = ''; // Clear message
    }, 1000);
}

// Function to show dropdown when search input is focused
function showDropdown() {
    document.getElementById('dropdownContent').style.display = 'block'; // Show dropdown
    // Do not clear the input here to allow selection from dropdown
}

// Function to hide dropdown when search input loses focus
function hideDropdown() {
    setTimeout(() => {
        document.getElementById('dropdownContent').style.display = 'none'; // Hide dropdown after slight delay
    }, 200); // Add a slight delay to allow clicking on dropdown links
}

// Prevent user from typing anything other than 'TravelMate' or 'TalkMate'
document.getElementById('searchInput').addEventListener('input', function() {
    const inputValue = this.value.trim().toLowerCase();
    if (inputValue !== '' && inputValue !== 'travelmate' && inputValue !== 'talkmate') {
        this.value = ''; // Clear the input if it doesn't match either option
    }
});



