<?php
function check_login() {
    if (!isset($_SESSION['login_user'])) {
        header("location: index.php");
        exit;
    }
}
?>