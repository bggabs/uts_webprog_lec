<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");  // Jika bukan admin, arahkan ke login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome Admin</h1>
    <p>Ini adalah halaman admin. Hanya admin yang bisa mengakses halaman ini.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
