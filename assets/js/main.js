// Global variables
let currentModal = null;

// Modal handling functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        currentModal = modal;
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        clearFormMessages();
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
        clearFormMessages();
    }
}

// Password visibility toggle
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;
    
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
}

// Form handling functions
async function handleLogin(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const messageDiv = form.querySelector('.form-message');
    
    try {
        const response = await fetch('login_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(messageDiv, data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showMessage(messageDiv, data.message, 'error');
        }
    } catch (error) {
        showMessage(messageDiv, 'An error occurred. Please try again.', 'error');
    }
}

// Wishlist functionality
async function toggleWishlist(itemId, itemType) {
    if (!isUserLoggedIn()) {
        openModal('loginModal');
        return;
    }

    const wishlistBtn = document.querySelector(`.product-card[data-id="${itemId}"] .wishlist-btn`);
    if (!wishlistBtn) {
        console.error('Wishlist button not found');
        showToast('Error updating wishlist');
        return;
    }

    wishlistBtn.disabled = true;
    wishlistBtn.style.opacity = '0.7';

    try {
        const response = await fetch('wishlist_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'toggle',
                product_id: itemId,
                product_type: itemType
            })
        });

        const data = await response.json();

        if (data.success) {
            const icon = wishlistBtn.querySelector('i');
            if (data.action === 'added') {
                wishlistBtn.classList.add('active');
                icon.classList.replace('far', 'fas');
                showToast('Added to wishlist');
            } else {
                wishlistBtn.classList.remove('active');
                icon.classList.replace('fas', 'far');
                showToast('Removed from wishlist');
            }

            const wishlistCounter = document.querySelector('.wishlist-counter');
            if (wishlistCounter) {
                wishlistCounter.textContent = data.wishlist_count || '0';
            }
        } else {
            throw new Error(data.message || 'Error updating wishlist');
        }
    } catch (error) {
        console.error('Wishlist error:', error);
        showToast(error.message || 'Error updating wishlist');
    } finally {
        wishlistBtn.disabled = false;
        wishlistBtn.style.opacity = '1';
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                          type === 'error' ? 'fa-exclamation-circle' : 
                          'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
        <div class="toast-progress"></div>
    `;

    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
        toast.querySelector('.toast-progress').style.width = '0%';
    });

    const TOAST_DURATION = 3000;
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, TOAST_DURATION);
}

// Utility functions
function showMessage(element, message, type) {
    element.textContent = message;
    element.className = `form-message ${type}`;
}

function clearFormMessages() {
    document.querySelectorAll('.form-message').forEach(element => {
        element.textContent = '';
        element.className = 'form-message';
    });
}

function isUserLoggedIn() {
    return <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
}