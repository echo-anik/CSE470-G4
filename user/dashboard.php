<?php
// user/dashboard.php
include '../includes/header.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

include '../includes/db_connection.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch wishlist and history
$wishlist = $conn->query("SELECT * FROM wishlist WHERE user_id = $user_id");
$history = $conn->query("SELECT * FROM orders WHERE user_id = $user_id");
?>

<div class="dashboard">
    <div class="user-details">
        <img src="assets/images/profile.jpg" alt="Profile Photo" class="profile-photo">
        <h1><?php echo htmlspecialchars($user['name']); ?></h1>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <button onclick="location.href='update_profile.php'">Update Profile</button>
        <button onclick="location.href='../logout.php'" style="float: right;">Logout</button>
    </div>

    <div class="tables-container">
        <div class="wishlist-table">
            <h2>Wishlist</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($wishlist && $wishlist->num_rows > 0): ?>
                        <?php while ($item = $wishlist->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td>
                                    <button onclick="removeFromWishlist(<?php echo $item['id']; ?>)">Remove</button>
                                    <button onclick="purchaseItem(<?php echo $item['id']; ?>)">Buy Now</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">Add some products to your wishlist</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="history-table">
            <h2>Order History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($history && $history->num_rows > 0): ?>
                        <?php while ($order = $history->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">No history to be shown</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function removeFromWishlist(itemId) {
    // Implement AJAX call to remove item from wishlist
    alert('Item removed from wishlist');
}

function purchaseItem(itemId) {
    // Implement AJAX call to simulate purchase
    alert('Purchase successful!');
}
</script>

<?php
include '../includes/footer.php';
?>