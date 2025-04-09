<?php
session_start();

$admin_id = "admin";
$admin_password = "admin123";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['admin_id'];
    $pass = $_POST['password'];

    if ($id === $admin_id && $pass === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: /RealTimeTrackerProject/frontend/admin_dashboard.html");
        exit();
    } else {
        echo "<script>alert('Invalid credentials!'); window.location.href='/RealTimeTrackerProject/frontend/admin_login.html';</script>";
        exit();
    }
}
?>
