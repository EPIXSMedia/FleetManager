<?php
require_once('config.php');
require_once('functions.php');
session_start();
check_login();

// Initialize settings if not already done
$settings = [
    'project_title' => 'Fleet Manager', // Replace with actual project title
    'menu_color' => '#333', // Replace with actual menu color
    'dashboard_color' => '#fff' // Replace with actual dashboard color
];

// Fetch existing Vehicle Maker Companies and Vehicle Types
$maker_companies = [];
$vehicle_types = [];
$query_makers = "SELECT id, name FROM vehicle_makers";
$query_types = "SELECT id, type FROM vehicle_types";

if ($result = mysqli_query($conn, $query_makers)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $maker_companies[] = $row;
    }
}
if ($result = mysqli_query($conn, $query_types)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $vehicle_types[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vehicle'])) {
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $maker_company_id = mysqli_real_escape_string($conn, $_POST['maker_company']);
    $model_name = mysqli_real_escape_string($conn, $_POST['model_name']);
    $model_year = mysqli_real_escape_string($conn, $_POST['model_year']);
    $vehicle_type_id = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $seating_capacity = mysqli_real_escape_string($conn, $_POST['seating_capacity']);
    $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
    $insurance_date = mysqli_real_escape_string($conn, $_POST['insurance_date']);

    $sql = "INSERT INTO vehicles (registration_number, maker_company_id, model_name, model_year, vehicle_type_id, seating_capacity, purchase_date, insurance_date) 
            VALUES ('$registration_number', '$maker_company_id', '$model_name', '$model_year', '$vehicle_type_id', '$seating_capacity', '$purchase_date', '$insurance_date')";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Vehicle added successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Handle new maker company submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_maker_company'])) {
    $new_maker_company = mysqli_real_escape_string($conn, $_POST['new_maker_company']);
    $sql = "INSERT INTO vehicle_makers (name) VALUES ('$new_maker_company')";

    if (mysqli_query($conn, $sql)) {
        header("Location: add_vehicle.php");
        exit();
    } else {
        $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Handle new vehicle type submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vehicle_type'])) {
    $new_vehicle_type = mysqli_real_escape_string($conn, $_POST['new_vehicle_type']);
    $sql = "INSERT INTO vehicle_types (type) VALUES ('$new_vehicle_type')";

    if (mysqli_query($conn, $sql)) {
        header("Location: add_vehicle.php");
        exit();
    } else {
        $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - <?php echo htmlspecialchars($settings['project_title']); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .popup-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        function showPopup(popupId) {
            document.getElementById(popupId).style.display = 'block';
        }
        function closePopup(popupId) {
            document.getElementById(popupId).style.display = 'none';
        }
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <h5 class="sidebar-heading"><?php echo htmlspecialchars($settings['project_title']); ?></h5>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="vehicles.php">Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link active" href="add_vehicle.php">Add Vehicle</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="drivers.php">Drivers</a></li>
                    <li class="nav-item"><a class="nav-link" href="managers.php">Managers</a></li>
                    <li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="bookings.php">Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="income_expense.php">Income & Expense</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php">Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_settings.php">Admin Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <h1 class="h2">Add Vehicle</h1>

            <!-- Display success or error messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success_message); ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Add Vehicle Form -->
            <form method="post">
                <div class="form-group">
                    <label for="registration_number">Vehicle Registration Number:</label>
                    <input type="text" class="form-control" name="registration_number" id="registration_number" required>
                </div>
                <div class="form-group">
                    <label for="maker_company">Vehicle Maker Company:</label>
                    <select class="form-control" name="maker_company" id="maker_company" required>
                        <option value="">Select Maker Company</option>
                        <?php foreach ($maker_companies as $company): ?>
                            <option value="<?php echo htmlspecialchars($company['id']); ?>">
                                <?php echo htmlspecialchars($company['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-secondary mt-2" onclick="showPopup('makerCompanyPopup')">Add New Maker Company</button>
                </div>
                <div class="form-group">
                    <label for="model_name">Vehicle Model Name:</label>
                    <input type="text" class="form-control" name="model_name" id="model_name" required>
                </div>
                <div class="form-group">
                    <label for="model_year">Vehicle Model Year:</label>
                    <input type="number" class="form-control" name="model_year" id="model_year" required>
                </div>
                <div class="form-group">
                    <label for="vehicle_type">Vehicle Type:</label>
                    <select class="form-control" name="vehicle_type" id="vehicle_type" required>
                    <option value="">Select Vehicle Type</option>
                        <?php foreach ($vehicle_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['id']); ?>">
                                <?php echo htmlspecialchars($type['type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-secondary mt-2" onclick="showPopup('vehicleTypePopup')">Add New Vehicle Type</button>
                </div>
                <div class="form-group">
                    <label for="seating_capacity">Vehicle Seating Capacity:</label>
                    <input type="number" class="form-control" name="seating_capacity" id="seating_capacity" required>
                </div>
                <div class="form-group">
                    <label for="purchase_date">Vehicle Purchase Date:</label>
                    <input type="date" class="form-control" name="purchase_date" id="purchase_date" required>
                </div>
                <div class="form-group">
                    <label for="insurance_date">Vehicle Insurance Date:</label>
                    <input type="date" class="form-control" name="insurance_date" id="insurance_date" required>
                </div>

                <button type="submit" name="add_vehicle" class="btn btn-primary">Add Vehicle</button>
            </form>
        </main>
    </div>
</div>

<!-- Maker Company Popup -->
<div id="makerCompanyPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup('makerCompanyPopup')">&times;</span>
        <form method="post">
            <div class="form-group">
                <label for="new_maker_company">New Vehicle Maker Company:</label>
                <input type="text" class="form-control" name="new_maker_company" id="new_maker_company" required>
            </div>
            <button type="submit" name="add_maker_company" class="btn btn-primary">Add Maker Company</button>
            <button type="button" class="btn btn-secondary" onclick="closePopup('makerCompanyPopup')">Cancel</button>
        </form>
    </div>
</div>

<!-- Vehicle Type Popup -->
<div id="vehicleTypePopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup('vehicleTypePopup')">&times;</span>
        <form method="post">
            <div class="form-group">
                <label for="new_vehicle_type">New Vehicle Type:</label>
                <input type="text" class="form-control" name="new_vehicle_type" id="new_vehicle_type" required>
            </div>
            <button type="submit" name="add_vehicle_type" class="btn btn-primary">Add Vehicle Type</button>
            <button type="button" class="btn btn-secondary" onclick="closePopup('vehicleTypePopup')">Cancel</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>