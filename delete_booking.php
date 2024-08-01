<?php
include('config.php');
include('functions.php');
session_start();
check_login();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM bookings WHERE id='$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("location: bookings.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("location: bookings.php");
}
?>