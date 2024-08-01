<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

// Handle form submission for updating settings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_title = mysqli_real_escape_string($conn, $_POST['project_title']);
    $menu_color = mysqli_real_escape_string($conn, $_POST['menu_color']);
    $dashboard_color = mysqli_real_escape_string($conn, $_POST['dashboard_color']);

    // Save settings in database or a configuration file
    // For demonstration, we assume saving to a 'settings' table
    $sql = "UPDATE settings SET 
            project_title='$project_title', 
            menu_color='$menu_color', 
            dashboard_color='$dashboard_color'";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Settings updated successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Fetch current settings
$settings_sql = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_sql);
$settings = mysqli_fetch_assoc($settings_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Settings - Tirumala Tours & Travels</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        .form-container {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
        }
        form input[type="text"], form input[type="color"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            padding: 10px 15px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #575757;
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

    <div class="content" style="background-color: <?php echo $settings['dashboard_color']; ?>;">
        <h1>Admin Settings</h1>
        
        <div class="form-container">
            <?php if(isset($success_message)) { echo "<div class='success'>$success_message</div>"; } ?>
            <?php if(isset($error_message)) { echo "<div class='error'>$error_message</div>"; } ?>

            <form action="admin_settings.php" method="POST">
                <label for="project_title">Project Title:</label>
                <input type="text" id="project_title" name="project_title" value="<?php echo $settings['project_title']; ?>" required><br><br>
                
                <label for="menu_color">Menu Color:</label>
                <input type="color" id="menu_color" name="menu_color" value="<?php echo $settings['menu_color']; ?>" required><br><br>
                
                <label for="dashboard_color">Dashboard Color:</label>
                <input type="color" id="dashboard_color" name="dashboard_color" value="<?php echo $settings['dashboard_color']; ?>" required><br><br>
                
                <input type="submit" value="Update Settings">
            </form>
        </div>
    </div>
</body>
</html>