<?php
include('config.php');

$username = 'admin'; // Set your admin username here
$password = 'admin123'; // Set your admin password here
$email = 'admin@example.com'; // Set your admin email here
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sss", $username, $hashed_password, $email);
    $stmt->execute();
    echo "Admin user created successfully!";
    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>