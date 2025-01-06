<?php
// admin/dashboard.php
include '../includes/header.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch all users and products for CRUD operations
$users = $conn->query("SELECT id, name, email FROM users");
$hotels = $conn->query("SELECT id, name, location FROM hotels");

// Fetch transport data
$buses = $conn->query("SELECT id, bus_number, departure, arrival FROM buses");
$cabs = $conn->query("SELECT id, cab_number, pickup_location, dropoff_location FROM cabs");
$flights = $conn->query("SELECT id, flight_number, departure, arrival FROM flights");
$trains = $conn->query("SELECT id, train_number, departure, arrival FROM trains");
?>

<div class="dashboard">
    <h1>Admin Dashboard</h1>
    <div class="dashboard-actions">
        <button onclick="location.href='add_admin.php'">Add Admin</button>
        <button onclick="location.href='add_hotel.php'">Add Hotel</button>
        <button onclick="location.href='add_transport.php'">Add Transport</button>
        <button onclick="location.href='../logout.php'" style="float: right;">Logout</button>
    </div>
    
    <h2>Manage Users</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users && $users->num_rows > 0): ?>
                <?php while ($user = $users->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">Nothing Found, Add some</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Manage Hotels</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($hotels && $hotels->num_rows > 0): ?>
                <?php while ($hotel = $hotels->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($hotel['name']); ?></td>
                        <td><?php echo htmlspecialchars($hotel['location']); ?></td>
                        <td>
                            <a href="edit_hotel.php?id=<?php echo $hotel['id']; ?>">Edit</a>
                            <a href="delete_hotel.php?id=<?php echo $hotel['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">Nothing Found, Add some</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Manage Buses</h2>
    <table>
        <thead>
            <tr>
                <th>Bus Number</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($buses && $buses->num_rows > 0): ?>
                <?php while ($bus = $buses->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bus['bus_number']); ?></td>
                        <td><?php echo htmlspecialchars($bus['departure']); ?></td>
                        <td><?php echo htmlspecialchars($bus['arrival']); ?></td>
                        <td>
                            <a href="edit_bus.php?id=<?php echo $bus['id']; ?>">Edit</a>
                            <a href="delete_bus.php?id=<?php echo $bus['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nothing Found, Add some</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Manage Cabs</h2>
    <table>
        <thead>
            <tr>
                <th>Cab Number</th>
                <th>Pickup Location</th>
                <th>Dropoff Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($cabs && $cabs->num_rows > 0): ?>
                <?php while ($cab = $cabs->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cab['cab_number']); ?></td>
                        <td><?php echo htmlspecialchars($cab['pickup_location']); ?></td>
                        <td><?php echo htmlspecialchars($cab['dropoff_location']); ?></td>
                        <td>
                            <a href="edit_cab.php?id=<?php echo $cab['id']; ?>">Edit</a>
                            <a href="delete_cab.php?id=<?php echo $cab['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nothing Found, Add some</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Manage Flights</h2>
    <table>
        <thead>
            <tr>
                <th>Flight Number</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($flights && $flights->num_rows > 0): ?>
                <?php while ($flight = $flights->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($flight['flight_number']); ?></td>
                        <td><?php echo htmlspecialchars($flight['departure']); ?></td>
                        <td><?php echo htmlspecialchars($flight['arrival']); ?></td>
                        <td>
                            <a href="edit_flight.php?id=<?php echo $flight['id']; ?>">Edit</a>
                            <a href="delete_flight.php?id=<?php echo $flight['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nothing Found, Add some</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Manage Trains</h2>
    <table>
        <thead>
            <tr>
                <th>Train Number</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($trains && $trains->num_rows > 0): ?>
                <?php while ($train = $trains->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($train['train_number']); ?></td>
                        <td><?php echo htmlspecialchars($train['departure']); ?></td>
                        <td><?php echo htmlspecialchars($train['arrival']); ?></td>
                        <td>
                            <a href="edit_train.php?id=<?php echo $train['id']; ?>">Edit</a>
                            <a href="delete_train.php?id=<?php echo $train['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nothing Found, Add some</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include '../includes/footer.php';
?>