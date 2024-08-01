<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

// Handle form submissions and actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_manager'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $capabilities = mysqli_real_escape_string($conn, $_POST['capabilities']);
        
        // Insert manager details into database
        $sql = "INSERT INTO managers (name, email, capabilities) VALUES ('$name', '$email', '$capabilities')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Manager added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_manager'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $capabilities = mysqli_real_escape_string($conn, $_POST['capabilities']);
        
        // Update manager details in database
        $sql = "UPDATE managers SET name='$name', email='$email', capabilities='$capabilities' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Manager updated successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_manager'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        // Delete manager from database
        $sql = "DELETE FROM managers WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Manager deleted successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Fetch managers
$managers_sql = "SELECT * FROM managers";
$managers_result = mysqli_query($conn, $managers_sql);

// Fetch current settings
$settings_sql = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_sql);
$settings = mysqli_fetch_assoc($settings_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Managers - <?php echo htmlspecialchars($settings['project_title']); ?></title>
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
        <h1>Managers</h1>

        <div class="form-container">
            <h2>Add Manager</h2>
            <?php if(isset($success_message)) { echo "<div class='success'>$success_message</div>"; } ?>
            <?php if(isset($error_message)) { echo "<div class='error'>$error_message</div>"; } ?>

            <form action="managers.php" method="POST">
                <label for="name">Manager Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>
                <label for="capabilities">Capabilities:</label>
                <input type="text" id="capabilities" name="capabilities" required><br><br>
                <input type="submit" name="add_manager" value="Add Manager">
            </form>
        </div>

        <div class="table-container">
            <h2>Manage Managers</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Capabilities</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                         if (mysqli_num_rows($managers_result) > 0) {
                            while($manager = mysqli_fetch_assoc($managers_result)) {
                                echo "<tr>";
                                echo "<td>{$manager['id']}</td>";
                                echo "<td>{$manager['name']}</td>";
                                echo "<td>{$manager['email']}</td>";
                                echo "<td>{$manager['capabilities']}</td>";
                                echo "<td>
                                        <button onclick=\"editManager({$manager['id']}, '{$manager['name']}', '{$manager['email']}', '{$manager['capabilities']}')\">Edit</button>
                                        <form action='managers.php' method='POST' style='display:inline-block;'>
                                            <input type='hidden' name='id' value='{$manager['id']}'>
                                            <input type='submit' name='delete_manager' value='Delete' onclick='return confirm(\"Are you sure you want to delete this manager?\")'>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No managers found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
    
            <!-- Edit Manager Form -->
            <div class="form-container" id="editManagerForm" style="display:none;">
                <h2>Edit Manager</h2>
                <form action="managers.php" method="POST">
                    <input type="hidden" id="edit_manager_id" name="id">
                    <label for="edit_name">Manager Name:</label>
                    <input type="text" id="edit_name" name="name" required><br><br>
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required><br><br>
                    <label for="edit_capabilities">Capabilities:</label>
                    <input type="text" id="edit_capabilities" name="capabilities" required><br><br>
                    <input type="submit" name="edit_manager" value="Update Manager">
                </form>
            </div>
        </div>
    
        <script>
            function editManager(id, name, email, capabilities) {
                document.getElementById('edit_manager_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_capabilities').value = capabilities;
                document.getElementById('editManagerForm').style.display = 'block';
                window.scrollTo(0, document.getElementById('editManagerForm').offsetTop);
            }
        </script>
    </body>
    </html>