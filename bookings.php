<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

// Fetch current settings
$settings_sql = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_sql);
$settings = mysqli_fetch_assoc($settings_result);

if (!$settings) {
    die("Settings could not be retrieved. Please check your database.");
}

// Handle form submissions and actions (if any)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle booking-related form submissions here
    // Example: Adding a new booking, editing a booking, deleting a booking
}

// Fetch bookings from the database
$bookings_sql = "SELECT * FROM bookings";
$bookings_result = mysqli_query($conn, $bookings_sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Bookings - <?php echo htmlspecialchars($settings['project_title']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <div class="sidebar" style="background-color: <?php echo htmlspecialchars($settings['menu_color']); ?>;">
        <h2><?php echo htmlspecialchars($settings['project_title']); ?></h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="vehicles.php">Vehicles</a></li>
            <li><a href="add_vehicle.php">Add Vehicle</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="drivers.php">Drivers</a></li>
            <li><a href="managers.php">Managers</a></li>
            <li><a href="customers.php">Customers</a></li>
            <li><a href="bookings.php">Bookings</a></li>
            <li><a href="income_expense.php">Income & Expense</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="admin_settings.php">Admin Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content" style="background-color: <?php echo htmlspecialchars($settings['dashboard_color']); ?>;">
        <h1>Bookings</h1>
        
        <div class="table-container">
            <h2>Manage Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer ID</th>
                        <th>Vehicle ID</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($bookings_result) > 0) {
                        while($booking = mysqli_fetch_assoc($bookings_result)) {
                            echo "<tr>";
                            echo "<td>{$booking['id']}</td>";
                            echo "<td>{$booking['customer_id']}</td>";
                            echo "<td>{$booking['vehicle_id']}</td>";
                            echo "<td>{$booking['start_date']}</td>";
                            echo "<td>{$booking['end_date']}</td>";
                            echo "<td>{$booking['status']}</td>";
                            echo "<td>
                                    <button onclick=\"editBooking({$booking['id']}, '{$booking['customer_id']}', '{$booking['vehicle_id']}', '{$booking['start_date']}', '{$booking['end_date']}', '{$booking['status']}')\">Edit</button>
                                    <form action='bookings.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='id' value='{$booking['id']}'>
                                        <input type='submit' name='delete_booking' value='Delete' onclick='return confirm(\"Are you sure you want to delete this booking?\")'>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No bookings found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Booking Form -->
        <div class="form-container" id="editBookingForm" style="display:none;">
            <h2>Edit Booking</h2>
            <form action="bookings.php" method="POST">
                <input type="hidden" id="edit_booking_id" name="id">
                <label for="edit_customer_id">Customer ID:</label>
                <input type="text" id="edit_customer_id" name="customer_id" required><br><br>
                <label for="edit_vehicle_id">Vehicle ID:</label>
                <input type="text" id="edit_vehicle_id" name="vehicle_id" required><br><br>
                <label for="edit_start_date">Start Date:</label>
                <input type="date" id="edit_start_date" name="start_date" required><br><br>
                <label for="edit_end_date">End Date:</label>
                <input type="date" id="edit_end_date" name="end_date" required><br><br>
                <label for="edit_status">Status:</label>
                <input type="text" id="edit_status" name="status" required><br><br>
                <input type="submit" name="edit_booking" value="Update Booking">
            </form>
        </div>
    </div>

    <script>
        function editBooking(id, customer_id, vehicle_id, start_date, end_date, status) {
            document.getElementById('edit_booking_id').value = id;
            document.getElementById('edit_customer_id').value = customer_id;
            document.getElementById('edit_vehicle_id').value = vehicle_id;
            document.getElementById('edit_start_date').value = start_date;
            document.getElementById('edit_end_date').value = end_date;
            document.getElementById('edit_status').value = status;
            document.getElementById('editBookingForm').style.display = 'block';
            window.scrollTo(0, document.getElementById('editBookingForm').offsetTop);
        }
    </script>
</body>
</html>