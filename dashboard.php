<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

// Handle the form submission for updating a booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_booking'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $vehicle_id = mysqli_real_escape_string($conn, $_POST['vehicle_id']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

    $sql = "UPDATE bookings SET vehicle_id='$vehicle_id', customer_name='$customer_name', start_date='$start_date', end_date='$end_date' WHERE id='$booking_id'";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Booking updated successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Fetch upcoming and ongoing bookings
$bookings_sql = "SELECT b.id, b.vehicle_id, v.make, v.model, b.customer_name, b.start_date, b.end_date 
                 FROM bookings b
                 JOIN vehicles v ON b.vehicle_id = v.id
                 WHERE b.start_date >= CURDATE() OR (b.start_date <= CURDATE() AND b.end_date >= CURDATE())
                 ORDER BY b.start_date ASC";
$bookings_result = mysqli_query($conn, $bookings_sql);

// Fetch vehicles for the dropdown
$vehicles_sql = "SELECT id, make, model FROM vehicles";
$vehicles_result = mysqli_query($conn, $vehicles_sql);

// Fetch current settings
$settings_sql = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_sql);
$settings = mysqli_fetch_assoc($settings_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - <?php echo htmlspecialchars($settings['project_title']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <style>
        .table-container, .calendar-container, .form-container {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
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
    </div>

    <div class="content" style="background-color: <?php echo htmlspecialchars($settings['dashboard_color']); ?>;">
        <h1>Dashboard</h1>
        
        <!-- Upcoming and Ongoing Bookings -->
        <div class="table-container">
            <h2>Upcoming and Ongoing Bookings</h2>
            <?php if(isset($success_message)) { echo "<div class='success'>$success_message</div>"; } ?>
            <?php if(isset($error_message)) { echo "<div class='error'>$error_message</div>"; } ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Customer Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($bookings_result) > 0) {
                        while($booking = mysqli_fetch_assoc($bookings_result)) {
                            echo "<tr>";
                            echo "<td>{$booking['id']}</td>";
                            echo "<td>{$booking['make']} {$booking['model']}</td>";
                            echo "<td>{$booking['customer_name']}</td>";
                            echo "<td>{$booking['start_date']}</td>";
                            echo "<td>{$booking['end_date']}</td>";
                            echo "<td><button onclick=\"editBooking({$booking['id']}, '{$booking['vehicle_id']}', '{$booking['customer_name']}', '{$booking['start_date']}', '{$booking['end_date']}')\">Edit</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No upcoming or ongoing bookings found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Booking Form -->
        <div class="form-container" id="editBookingForm" style="display:none;">
            <h2>Edit Booking</h2>
            <form action="dashboard.php" method="POST">
                <input type="hidden" id="booking_id" name="booking_id">
                <label for="vehicle_id">Vehicle:</label>
                <select id="vehicle_id" name="vehicle_id" required>
                    <?php
                    if (mysqli_num_rows($vehicles_result) > 0) {
                        while($vehicle = mysqli_fetch_assoc($vehicles_result)) {
                            echo "<option value='{$vehicle['id']}'>{$vehicle['make']} {$vehicle['model']}</option>";
                        }
                    } else {
                        echo "<option value=''>No vehicles available</option>";
                    }
                    ?>
                </select><br><br>
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" required><br><br>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required><br><br>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required><br><br>
                <input type="submit" name="update_booking" value="Update Booking">
            </form>
        </div>

        <!-- Booking Calendar -->
        <div class="calendar-container">
            <h2>Booking Calendar</h2>
            <div id="calendar"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                events: 'load_events.php'
            });
        });

        function editBooking(id, vehicleId, customerName, startDate, endDate) {
            document.getElementById('booking_id').value = id;
            document.getElementById('vehicle_id').value = vehicleId;
            document.getElementById('customer_name').value = customerName;
            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
            document.getElementById('editBookingForm').style.display = 'block';
            window.scrollTo(0, document.getElementById('editBookingForm').offsetTop);
        }
    </script>
</body>
</html>