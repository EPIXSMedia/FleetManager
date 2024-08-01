<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

// Handle form submissions and actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_driver'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $license_number = mysqli_real_escape_string($conn, $_POST['license_number']);
        
        // Insert driver details into database
        $sql = "INSERT INTO drivers (name, phone, address, license_number) VALUES ('$name', '$phone', '$address', '$license_number')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Driver added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_driver'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $license_number = mysqli_real_escape_string($conn, $_POST['license_number']);
        
        // Update driver details in database
        $sql = "UPDATE drivers SET name='$name', phone='$phone', address='$address', license_number='$license_number' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Driver updated successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_driver'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        // Delete driver from database
        $sql = "DELETE FROM drivers WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Driver deleted successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['toggle_status'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Toggle driver status
        $new_status = ($status == 'enabled') ? 'disabled' : 'enabled';
        $sql = "UPDATE drivers SET status='$new_status' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Driver status updated successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Fetch drivers
$drivers_sql = "SELECT * FROM drivers";
$drivers_result = mysqli_query($conn, $drivers_sql);

// Fetch current settings
$settings_sql = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_sql);
$settings = mysqli_fetch_assoc($settings_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Drivers - <?php echo htmlspecialchars($settings['project_title']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        .form-container, .table-container {
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

    <div class="content" style="background-color: <?php echo htmlspecialchars($settings['dashboard_color']); ?>;">
        <h1>Drivers</h1>

        <div class="form-container">
            <h2>Add Driver</h2>
            <?php if(isset($success_message)) { echo "<div class='success'>$success_message</div>"; } ?>
            <?php if(isset($error_message)) { echo "<div class='error'>$error_message</div>"; } ?>

            <form action="drivers.php" method="POST">
                <label for="name">Driver Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required><br><br>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required><br><br>
                <label for="license_number">License Number:</label>
                <input type="text" id="license_number" name="license_number" required><br><br>
                <input type="submit" name="add_driver" value="Add Driver">
            </form>
        </div>

        <div class="table-container">
            <h2>Manage Drivers</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>License Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($drivers_result) > 0) {
                        while($driver = mysqli_fetch_assoc($drivers_result)) {
                            echo "<tr>";
                            echo "<td>{$driver['id']}</td>";
                            echo "<td>{$driver['name']}</td>";
                            echo "<td>{$driver['phone']}</td>";
                            echo "<td>{$driver['address']}</td>";
                            echo "<td>{$driver['license_number']}</td>";
                            echo "<td>{$driver['status']}</td>";
                            echo "<td>
                                    <button onclick=\"editDriver({$driver['id']}, '{$driver['name']}', '{$driver['phone']}', '{$driver['address']}', '{$driver['license_number']}')\">Edit</button>
                                    <form action='drivers.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='id' value='{$driver['id']}'>
                                        <input type='submit' name='delete_driver' value='Delete' onclick='return confirm(\"Are you sure you want to delete this driver?\")'>
                                    </form>
                                    <form action='drivers.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='id' value='{$driver['id']}'>
                                        <input type='hidden' name='status' value='{$driver['status']}'>
                                        <input type='submit' name='toggle_status' value='" . ($driver['status'] == 'enabled' ? 'Disable' : 'Enable') . "'>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No drivers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Driver Form -->
        <div class="form-container" id="editDriverForm" style="display:none;">
            <h2>Edit Driver</h2>
            <form action="drivers.php" method="POST">
                <input type="hidden" id="edit_driver_id" name="id">
                <label for="edit_name">Driver Name:</label>
                <input type="text" id="edit_name" name="name" required><br><br>
                <label for="edit_phone">Phone Number:</label>
                <input type="text" id="edit_phone" name="phone" required><br><br>
                <label for="edit_address">Address:</label>
                <input type="text" id="edit_address" name="address" required><br><br>
                <label for="edit_license_number">License Number:</label>
                <input type="text" id="edit_license_number" name="license_number" required><br><br>
                <input type="submit" name="edit_driver" value="Update Driver">
            </form>
        </div>
    </div>

    <script>
        function editDriver(id, name, phone, address, license_number) {
            document.getElementById('edit_driver_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_license_number').value = license_number;
            document.getElementById('editDriverForm').style.display = 'block';
            window.scrollTo(0, document.getElementById('editDriverForm').offsetTop);
        }
    </script>
</body>
</html>