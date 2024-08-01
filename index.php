<?php
include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from form 
    $myusername = mysqli_real_escape_string($conn, $_POST['username']);
    $mypassword = mysqli_real_escape_string($conn, $_POST['password']); 

    $sql = "SELECT id, password FROM users WHERE username = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $myusername);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            
            if (password_verify($mypassword, $hashed_password)) {
                // Password is correct, start a new session and save the username
                $_SESSION['login_user'] = $myusername;
                header("location: dashboard.php");
                exit;
            } else {
                // Display an error message if password is not valid
                $error = "The password you entered was not valid.";
            }
        } else {
            // Display an error message if username doesn't exist
            $error = "No account found with that username.";
        }

        $stmt->close();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fleet Manager Login</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="" method="post">
            <label for="username">Username :</label>
            <input type="text" name="username" id="username" required><br><br>
            <label for="password">Password :</label>
            <input type="password" name="password" id="password" required><br><br>
            <input type="submit" value="Login"><br>
        </form>
        <?php
        if (isset($error)) {
            echo "<div class='error'>$error</div>";
        }
        ?>
    </div>
</body>
</html>