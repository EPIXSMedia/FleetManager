<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

if (!isset($_GET['id'])) {
    header("Location: bookings.php");
    exit;
}

$id = $_GET['id'];

// Handle form submission for updating the booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_id = mysqli_real_escape_string($conn, $_POST['vehicle_id']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

    $sql = "UPDATE bookings SET vehicle_id='$vehicle_id', customer_name='$customer_name', start_date='$start_date', end_date='$end_date' WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Booking updated successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Fetch current booking data
$booking_sql = "SELECT * FROM bookings WHERE id='$id'";
$booking_result = mysqli_query($conn, $booking_sql);
$booking = mysqli_fetch_assoc($booking_result);

// Fetch vehicles for the dropdown
$vehicles_sql = "SELECT id, make, model FROM vehicles";
$vehicles_result = mysqli_query($conn, $vehicles_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking - Tirumala Tours & Travels</title>
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

    <div class="content">
        <h1>Edit Booking</h1>
        
        <div class="form-container">
            <?php if(isset($success_message)) { echo "<div class='success'>$success_message</div>"; } ?>
            <?php if(isset($error_message)) { echo "<div class='error'>$error_message</div>"; } ?>
            
            <form action="edit_booking.php?id=<?php echo $id; ?>" method="POST">
                <label for="vehicle_id">Vehicle:</label>
                <select id="vehicle_id" name="vehicle_id" required>
                    <?php
                    if (mysqli_num_rows($vehicles_result) > 0) {
                        while($vehicle = mysqli_fetch_assoc($vehicles_result)) {
                            $selected = ($vehicle['id'] == $booking['vehicle_id']) ? 'selected' : '';
                            echo "<option value='{$vehicle['id']}' $selected>{$vehicle['make']} {$vehicle['model']}</option>";
                        }
                    } else {
                        echo "<option value=''>No vehicles available</option>";
                    }
                    ?>
                </select><br><br>
                
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" value="<?php echo $booking['customer_name']; ?>" required><br><br>
                
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $booking['start_date']; ?>" required><br><br>
                
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $booking['end_date']; ?>" required><br><br>
                
                <input type="submit" value="Update Booking">
            </form>
        </div>
    </div>
</body>
</html>