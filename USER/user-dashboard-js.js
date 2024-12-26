document.addEventListener('DOMContentLoaded', () => {
    // Navigation
    const sidebarItems = document.querySelectorAll('.sidebar nav ul li');
    const sections = document.querySelectorAll('.section');

    sidebarItems.forEach(item => {
        item.addEventListener('click', () => {
            // Remove active class from all items and sections
            sidebarItems.forEach(i => i.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));

            // Add active class to clicked item and corresponding section
            item.classList.add('active');
            const sectionId = item.dataset.section;
            document.getElementById(sectionId).classList.add('active');
        });
    });

    // Profile Form Submission
    const profileForm = document.getElementById('profile-form');
    profileForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(profileForm);
        
        try {
            const response = await fetch('update_profile.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                alert('Profile updated successfully');
                // Optionally update displayed name/email
                document.getElementById('user-fullname').textContent = formData.get('full_name');
                document.getElementById('user-email').textContent = formData.get('email');
            } else {
                alert('Error updating profile: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating profile');
        }
    });

    // Wishlist Management
    const addWishlistBtn = document.getElementById('add-wishlist-btn');
    const addWishlistModal = document.getElementById('add-wishlist-modal');
    const closeModalBtn = document.querySelector('.close-modal');
    const addWishlistForm = document.getElementById('add-wishlist-form');

    addWishlistBtn.addEventListener('click', () => {
        addWishlistModal.style.display = 'block';
    });

    closeModalBtn.addEventListener('click', () => {
        addWishlistModal.style.display = 'none';
    });

    // Close modal if clicked outside
    window.addEventListener('click', (e) => {
        if (e.target === addWishlistModal) {
            addWishlistModal.style.display = 'none';
        }
    });

    addWishlistForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addWishlistForm);
        
        try {
            const response = await fetch('add_wishlist.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                // Add new row to wishlist table
                const wishlistBody = document.getElementById('wishlist-body');
                const newRow = wishlistBody.insertRow();
                newRow.innerHTML = `
                    <td>${formData.get('destination')}</td>
                    <td>${formData.get('travel_method')}</td>
                    <td>${formData.get('priority')}</td>
                    <td>${new Date().toLocaleDateString()}</td>
                    <td>
                        <button class="btn-edit">Edit</button>
                        <button class="btn-delete">Delete</button>
                    </td>
                `;
                
                // Close modal
                addWishlistModal.style.display = 'none';
                addWishlistForm.reset();
            } else {
                alert('Error adding wishlist item: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding wishlist item');
        }
    });

    // Fetch and populate initial data
    async function fetchUserData() {
        try {
            const [profileResponse, wishlistResponse, bookingsResponse, loginHistoryResponse] = await Promise.all([
                fetch('get_profile.php'),
                fetch('get_wishlist.php'),
                fetch('get_bookings.php'),
                fetch('get_login_history.php')
            ]);

            const profileData = await profileResponse.json();
            const wishlistData = await wishlistResponse.json();
            const bookingsData = await bookingsResponse.json();
            const loginHistoryData = await loginHistoryResponse.json();

            // Populate profile
            document.getElementById('user-fullname').textContent = profileData.full_name;
            document.getElementById('user-email').textContent = profileData.email;
            document.getElementById('full_name').value = profileData.full_name;
            document.getElementById('email').value = profileData.email;

            // Populate wishlist
            const wishlistBody = document.getElementById('wishlist-body');
            wishlistBody.innerHTML = ''; // Clear existing rows
            wishlistData.forEach(item => {
                const row = wishlistBody.insertRow();
                row.innerHTML = `
                    <td>${item.destination}</td>
                    <td>${item.travel_method}</td>
                    <td>${item.priority}</td>
                    <td>${new Date(item.date_added).toLocaleDateString()}</td>
                    <td>
                        <button class="btn-edit" data-id="${item.wishlist_id}">Edit</button>
                        <button class="btn-delete" data-id="${item.wishlist_id}">Delete</button>
                    </td>
                `;
            });

            // Add event listeners for edit and delete wishlist items
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const wishlistId = e.target.dataset.id;
                    editWishlistItem(wishlistId);
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const wishlistId = e.target.dataset.id;
                    deleteWishlistItem(wishlistId);
                });
            });

            // Populate bookings
            const bookingsBody = document.getElementById('bookings-body');
            bookingsBody.innerHTML = ''; // Clear existing rows
            bookingsData.forEach(booking => {
                const row = bookingsBody.insertRow();
                row.innerHTML = `
                    <td>${booking.booking_id}</td>
                    <td>${booking.destination}</td>
                    <td>${new Date(booking.date).toLocaleDateString()}</td>
                    <td>${booking.status}</td>
                    <td>
                        <button class="btn-view" data-id="${booking.booking_id}">View Details</button>
                    </td>
                `;
            });

            // Populate login history
            const loginHistoryBody = document.getElementById('login-history-body');
            loginHistoryBody.innerHTML = ''; // Clear existing rows
            loginHistoryData.forEach(login => {
                const row = loginHistoryBody.insertRow();
                row.innerHTML = `
                    <td>${new Date(login.login_time).toLocaleString()}</td>
                    <td>${login.ip_address}</td>
                    <td>${login.device_info}</td>
                    <td>${login.status}</td>
                `;
            });

        } catch (error) {
            console.error('Error fetching user data:', error);
            alert('Failed to load user data');
        }
    }

    // Function to edit wishlist item
    async function editWishlistItem(wishlistId) {
        try {
            const response = await fetch(`get_wishlist_item.php?id=${wishlistId}`);
            const item = await response.json();

            // Populate modal with existing item details
            document.getElementById('destination').value = item.destination;
            document.getElementById('travel-method').value = item.travel_method;
            document.getElementById('priority').value = item.priority;
            document.getElementById('notes').value = item.notes;

            // Change form submission to update instead of add
            addWishlistForm.onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(addWishlistForm);
                formData.append('wishlist_id', wishlistId);

                try {
                    const updateResponse = await fetch('update_wishlist.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await updateResponse.json();

                    if (result.success) {
                        // Refresh user data to reflect changes
                        fetchUserData();
                        addWishlistModal.style.display = 'none';
                        addWishlistForm.reset();
                    } else {
                        alert('Error updating wishlist item: ' + result.message);
                    }
                } catch (updateError) {
                    console.error('Error:', updateError);
                    alert('An error occurred while updating wishlist item');
                }
            };

            // Show modal
            addWishlistModal.style.display = 'block';

        } catch (error) {
            console.error('Error fetching wishlist item:', error);
            alert('Failed to load wishlist item details');
        }
    }

    // Function to delete wishlist item
    async function deleteWishlistItem(wishlistId) {
        if (!confirm('Are you sure you want to delete this wishlist item?')) return;

        try {
            const response = await fetch('delete_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ wishlist_id: wishlistId })
            });
            const result = await response.json();

            if (result.success) {
                // Remove row from table
                const row = document.querySelector(`button[data-id="${wishlistId}"]`).closest('tr');
                row.remove();
            } else {
                alert('Error deleting wishlist item: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while deleting wishlist item');
        }
    }

    // Initial data fetch
    fetchUserData();
});
