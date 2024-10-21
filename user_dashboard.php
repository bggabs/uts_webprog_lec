<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: login.php");  // Jika bukan user, arahkan ke login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome User</h1>
    <p>Ini adalah halaman user. Hanya user yang bisa mengakses halaman ini.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
