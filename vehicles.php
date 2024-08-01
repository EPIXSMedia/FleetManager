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
    if (isset($_POST['add_vehicle'])) {
        $maker_id = mysqli_real_escape_string($conn, $_POST['maker_id']);
        $model_id = mysqli_real_escape_string($conn, $_POST['model_id']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
        $type_id = mysqli_real_escape_string($conn, $_POST['type_id']);
        $color_id = mysqli_real_escape_string($conn, $_POST['color_id']);
        $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
        $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
        $insurance_date = mysqli_real_escape_string($conn, $_POST['insurance_date']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Insert vehicle details into database
        $sql = "INSERT INTO vehicles (maker_id, model_id, year, type_id, color_id, purchase_date, registration_number, insurance_date, status) VALUES ('$maker_id', '$model_id', '$year', '$type_id', '$color_id', '$purchase_date', '$registration_number', '$insurance_date', '$status')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Vehicle added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_vehicle'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $maker_id = mysqli_real_escape_string($conn, $_POST['maker_id']);
        $model_id = mysqli_real_escape_string($conn, $_POST['model_id']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
        $type_id = mysqli_real_escape_string($conn, $_POST['type_id']);
        $color_id = mysqli_real_escape_string($conn, $_POST['color_id']);
        $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
        $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
        $insurance_date = mysqli_real_escape_string($conn, $_POST['insurance_date']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Update vehicle details in database
        $sql = "UPDATE vehicles SET maker_id='$maker_id', model_id='$model_id', year='$year', type_id='$type_id', color_id='$color_id', purchase_date='$purchase_date', registration_number='$registration_number', insurance_date='$insurance_date', status='$status' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Vehicle updated successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_vehicle'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        // Delete vehicle from database
        $sql = "DELETE FROM vehicles WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Vehicle deleted successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    // Handle adding, editing, deleting Vehicle Maker, Model, Type, and Color
    if (isset($_POST['add_maker'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $sql = "INSERT INTO vehicle_makers (name) VALUES ('$name')";
        if (!mysqli_query($conn, $sql)) {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_maker'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $sql = "UPDATE vehicle_makers SET name='$name' WHERE id='$id'";
        if (!mysqli_query($conn, $sql)) {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_maker'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $sql = "DELETE FROM vehicle_makers WHERE id='$id'";
        if (!mysqli_query($conn, $sql)) {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    // Repeat similar blocks for models, types, and colors...
}

// Fetch data for dropdowns
$makers_sql = "SELECT * FROM vehicle_makers";
$makers_result = mysqli_query($conn, $makers_sql);

$models_sql = "SELECT * FROM vehicle_models";
$models_result = mysqli_query($conn, $models_sql);

$types_sql = "SELECT * FROM vehicle_types";
$types_result = mysqli_query($conn, $types_sql);

$colors_sql = "SELECT * FROM vehicle_colors";
$colors_result = mysqli_query($conn, $colors_sql);

// Fetch vehicles from the database
$vehicles_sql = "SELECT v.*, vm.name AS model_name, vt.name AS type_name, vc.name AS color_name
                FROM vehicles v
                JOIN vehicle_models vm ON v.model_id = vm.id
                JOIN vehicle_types vt ON v.type_id = vt.id
                JOIN vehicle_colors vc ON v.color_id = vc.id";
$vehicles_result = mysqli_query($conn, $vehicles_sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Vehicles - <?php echo htmlspecialchars($settings['project_title']); ?></title>
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
        <h1>Vehicles</h1>

        <div class="form-container">
            <h2>Add Vehicle</h2>
            <?php if(isset($success_message)) { echo "<div class='success'>$success_message</div>"; } ?>
            <?php if(isset($error_message)) { echo "<div class='error'>$error_message</div>"; } ?>

            <form action="vehicles.php" method="POST">
                <label for="maker_id">Maker:</label>
                <select id="maker_id" name="maker_id" required>
                    <?php while ($maker = mysqli_fetch_assoc($makers_result)) { ?>
                        <option value="<?php echo $maker['id']; ?>"><?php echo $maker['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('maker')">Add Maker</button>
                <br><br>
                
                <label for="model_id">Model:</label>
                <select id="model_id" name="model_id" required>
                    <?php while ($model = mysqli_fetch_assoc($models_result)) { ?>
                        <option value="<?php echo $model['id']; ?>"><?php echo $model['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('model')">Add Model</button>
                <br><br>

                <label for="type_id">Type:</label>
                <select id="type_id" name="type_id" required>
                    <?php while ($type = mysqli_fetch_assoc($types_result)) { ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('type')">Add Type</button>
                <br><br>

                <label for="color_id">Color:</label>
                <select id="color_id" name="color_id" required>
                    <?php while ($color = mysqli_fetch_assoc($colors_result)) { ?>
                        <option value="<?php echo $color['id']; ?>"><?php echo $color['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('color')">Add Color</button>
                <br><br>

                <label for="year">Year:</label>
                <input type="text" id="year" name="year" required><br><br>

                <label for="purchase_date">Purchase Date:</label>
                <input type="date" id="purchase_date" name="purchase_date" required><br><br>

                <label for="registration_number">Registration Number:</label>
                <input type="text" id="registration_number" name="registration_number" required><br><br>

                <label for="insurance_date">Insurance Date:</label>
                <input type="date" id="insurance_date" name="insurance_date" required><br><br>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="available">Available</option>
                    <option value="not available">Not Available</option>
                    <option value="in service">In Service</option>
                </select><br><br>

                <input type="submit" name="add_vehicle" value="Add Vehicle">
            </form>
        </div>

        <div class="table-container">
            <h2>Manage Vehicles</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Maker</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Type</th>
                        <th>Color</th>
                        <th>Purchase Date</th>
                        <th>Registration Number</th>
                        <th>Insurance Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($vehicles_result) > 0) {
                        while($vehicle = mysqli_fetch_assoc($vehicles_result)) {
                            echo "<tr>";
                            echo "<td>{$vehicle['id']}</td>";
                            echo "<td>{$vehicle['model_name']}</td>";
                            echo "<td>{$vehicle['year']}</td>";
                            echo "<td>{$vehicle['type_name']}</td>";
                            echo "<td>{$vehicle['color_name']}</td>";
                            echo "<td>{$vehicle['purchase_date']}</td>";
                            echo "<td>{$vehicle['registration_number']}</td>";
                            echo "<td>{$vehicle['insurance_date']}</td>";
                            echo "<td>{$vehicle['status']}</td>";
                            echo "<td>
                                    <button onclick=\"editVehicle({$vehicle['id']}, '{$vehicle['model_id']}', '{$vehicle['year']}', '{$vehicle['type_id']}', '{$vehicle['color_id']}', '{$vehicle['purchase_date']}', '{$vehicle['registration_number']}', '{$vehicle['insurance_date']}', '{$vehicle['status']}')\">Edit</button>
                                    <form action='vehicles.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='id' value='{$vehicle['id']}'>
                                        <input type='submit' name='delete_vehicle' value='Delete' onclick='return confirm(\"Are you sure you want to delete this vehicle?\")'>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11'>No vehicles found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Vehicle Form -->
        <div class="form-container" id="editVehicleForm" style="display:none;">
            <h2>Edit Vehicle</h2>
            <form action="vehicles.php" method="POST">
                <input type="hidden" id="edit_vehicle_id" name="id">
                <label for="edit_maker_id">Maker:</label>
                <select id="edit_maker_id" name="maker_id" required>
                    <?php while ($maker = mysqli_fetch_assoc($makers_result)) { ?>
                        <option value="<?php echo $maker['id']; ?>"><?php echo $maker['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('maker')">Add Maker</button>
                <br><br>
                
                <label for="edit_model_id">Model:</label>
                <select id="edit_model_id" name="model_id" required>
                    <?php while ($model = mysqli_fetch_assoc($models_result)) { ?>
                        <option value="<?php echo $model['id']; ?>"><?php echo $model['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('model')">Add Model</button>
                <br><br>

                <label for="edit_type_id">Type:</label>
                <select id="edit_type_id" name="type_id" required>
                    <?php while ($type = mysqli_fetch_assoc($types_result)) { ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('type')">Add Type</button>
                <br><br>

                <label for="edit_color_id">Color:</label>
                <select id="edit_color_id" name="color_id" required>
                    <?php while ($color = mysqli_fetch_assoc($colors_result)) { ?>
                        <option value="<?php echo $color['id']; ?>"><?php echo $color['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" onclick="showAddForm('color')">Add Color</button>
                <br><br>

                <label for="edit_year">Year:</label>
                <input type="text" id="edit_year" name="year" required><br><br>

                <label for="edit_purchase_date">Purchase Date:</label>
                <input type="date" id="edit_purchase_date" name="purchase_date" required><br><br>

                <label for="edit_registration_number">Registration Number:</label>
                <input type="text" id="edit_registration_number" name="registration_number" required><br><br>

                <label for="edit_insurance_date">Insurance Date:</label>
                <input type="date" id="edit_insurance_date" name="insurance_date" required><br><br>

                <label for="edit_status">Status:</label>
                <select id="edit_status" name="status" required>
                    <option value="available">Available</option>
                    <option value="not available">Not Available</option>
                    <option value="in service">In Service</option>
                </select><br><br>

                <input type="submit" name="edit_vehicle" value="Update Vehicle">
            </form>
        </div>
    </div>

    <!-- Forms to add maker, model, type, and color dynamically -->
    <div class="popup-form-container" id="addMakerForm" style="display:none;">
        <h2>Add Maker</h2>
        <form action="vehicles.php" method="POST">
            <label for="maker_name">Maker Name:</label>
            <input type="text" id="maker_name" name="name" required><br><br>
            <input type="submit" name="add_maker" value="Add Maker">
            <button type="button" onclick="hideAddForm('maker')">Cancel</button>
        </form>
    </div>

    <div class="popup-form-container" id="addModelForm" style="display:none;">
        <h2>Add Model</h2>
        <form action="vehicles.php" method="POST">
            <label for="model_name">Model Name:</label>
            <input type="text" id="model_name" name="name" required><br><br>
            <label for="model_maker_id">Maker:</label>
            <select id="model_maker_id" name="maker_id" required>
                <?php while ($maker = mysqli_fetch_assoc($makers_result)) { ?>
                    <option value="<?php echo $maker['id']; ?>"><?php echo $maker['name']; ?></option>
                <?php } ?>
            </select><br><br>
            <input type="submit" name="add_model" value="Add Model">
            <button type="button" onclick="hideAddForm('model')">Cancel</button>
        </form>
    </div>

    <div class="popup-form-container" id="addTypeForm" style="display:none;">
        <h2>Add Type</h2>
        <form action="vehicles.php" method="POST">
            <label for="type_name">Type Name:</label>
            <input type="text" id="type_name" name="name" required><br><br>
            <input type="submit" name="add_type" value="Add Type">
            <button type="button" onclick="hideAddForm('type')">Cancel</button>
        </form>
    </div>

    <div class="popup-form-container" id="addColorForm" style="display:none;">
        <h2>Add Color</h2>
        <form action="vehicles.php" method="POST">
            <label for="color_name">Color Name:</label>
            <input type="text" id="color_name" name="name" required><br><br>
            <input type="submit" name="add_color" value="Add Color">
            <button type="button" onclick="hideAddForm('color')">Cancel</button>
        </form>
    </div>

    <!-- JavaScript Functions -->
    <script>
        function editVehicle(id, model_id, year, type_id, color_id, purchase_date, registration_number, insurance_date, status) {
            document.getElementById('edit_vehicle_id').value = id;
            document.getElementById('edit_model_id').value = model_id;
            document.getElementById('edit_year').value = year;
            document.getElementById('edit_type_id').value = type_id;
            document.getElementById('edit_color_id').value = color_id;
            document.getElementById('edit_purchase_date').value = purchase_date;
            document.getElementById('edit_registration_number').value = registration_number;
            document.getElementById('edit_insurance_date').value = insurance_date;
            document.getElementById('edit_status').value = status;
            document.getElementById('editVehicleForm').style.display = 'block';
            window.scrollTo(0, document.getElementById('editVehicleForm').offsetTop);
        }

        function showAddForm(type) {
            document.getElementById('add' + capitalizeFirstLetter(type) + 'Form').style.display = 'block';
        }

        function hideAddForm(type) {
            document.getElementById('add' + capitalizeFirstLetter(type) + 'Form').style.display = 'none';
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
</body>
</html>