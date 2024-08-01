<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

// Handle form submissions and actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_customer'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $revenue_generated = mysqli_real_escape_string($conn, $_POST['revenue_generated']);
        
        // Insert customer details into database
        $sql = "INSERT INTO customers (name, email, phone, whatsapp, address, revenue_generated) VALUES ('$name', '$email', '$phone', '$whatsapp', '$address', '$revenue_generated')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Customer added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_customer'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $revenue_generated = mysqli_real_escape_string($conn, $_POST['revenue_generated']);
        
        // Update customer details in database
        $sql = "UPDATE customers SET name='$name', email='$email', phone='$phone', whatsapp='$whatsapp', address='$address', revenue_generated='$revenue_generated' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Customer updated successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_customer'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        // Delete customer from database
        $sql = "DELETE FROM customers WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Customer deleted successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Fetch customers
$customers_sql = "SELECT * FROM customers";
$customers_result = mysqli_query($conn, $customers_sql);

// Fetch current settings
$settings_sql = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_sql);
$settings = mysqli_fetch_assoc($settings_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customers - <?php echo htmlspecialchars($settings['project_title']); ?></title>
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
        <h1>Customers</h1>

        <div class="form-container">
            <h2>Add Customer</h2>
            <?php if(isset($success_message)) { echo "<div class='success'>$success_message</div>"; } ?>
            <?php if(isset($error_message)) { echo "<div class='error'>$error_message</div>"; } ?>

            <form action="customers.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required><br><br>
                <label for="whatsapp">Whatsapp Number:</label>
                <input type="text" id="whatsapp" name="whatsapp" required><br><br>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required><br><br>
                <label for="revenue_generated">Revenue Generated:</label>
                <input type="text" id="revenue_generated" name="revenue_generated" required><br><br>
                <input type="submit" name="add_customer" value="Add Customer">
            </form>
        </div>

        <div class="table-container">
            <h2>Manage Customers</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Whatsapp</th>
                        <th>Address</th>
                        <th>Revenue Generated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($customers_result) > 0) {
                        while($customer = mysqli_fetch_assoc($customers_result)) {
                            echo "<tr>";
                            echo "<td>{$customer['id']}</td>";
                            echo "<td>{$customer['name']}</td>";
                            echo "<td>{$customer['email']}</td>";
                            echo "<td>{$customer['phone']}</td>";
                            echo "<td>{$customer['whatsapp']}</td>";
                            echo "<td>{$customer['address']}</td>";
                            echo "<td>{$customer['revenue_generated']}</td>";
                            echo "<td>
                                    <button onclick=\"editCustomer({$customer['id']}, '{$customer['name']}', '{$customer['email']}', '{$customer['phone']}', '{$customer['whatsapp']}', '{$customer['address']}', '{$customer['revenue_generated']}')\">Edit</button>
                                    <form action='customers.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='id' value='{$customer['id']}'>
                                        <input type='submit' name='delete_customer' value='Delete' onclick='return confirm(\"Are you sure you want to delete this customer?\")'>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No customers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Customer Form -->
        <div class="form-container" id="editCustomerForm" style="display:none;">
            <h2>Edit Customer</h2>
            <form action="customers.php" method="POST">
                <input type="hidden" id="edit_customer_id" name="id">
                <label for="edit_name">Name:</label>
                <input type="text" id="edit_name" name="name" required><br><br>
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required><br><br>
                <label for="edit_phone">Phone Number:</label>
                <input type="text" id="edit_phone" name="phone" required><br><br>
                <label for="edit_whatsapp">Whatsapp Number:</label>
                <input type="text" id="edit_whatsapp" name="whatsapp" required><br><br>
                <label for="edit_address">Address:</label>
                <input type="text" id="edit_address" name="address" required><br><br>
                <label for="edit_revenue_generated">Revenue Generated:</label>
                <input type="text" id="edit_revenue_generated" name="revenue_generated" required><br><br>
                <input type="submit" name="edit_customer" value="Update Customer">
            </form>
        </div>
    </div>

    <script>
        function editCustomer(id, name, email, phone, whatsapp, address, revenue_generated) {
            document.getElementById('edit_customer_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_whatsapp').value = whatsapp;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_revenue_generated').value = revenue_generated;
            document.getElementById('editCustomerForm').style.display = 'block';
            window.scrollTo(0, document.getElementById('editCustomerForm').offsetTop);
        }
    </script>
</body>
</html>
