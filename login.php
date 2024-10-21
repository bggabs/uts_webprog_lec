<?php
session_start();
require 'db_connection.php';  // Koneksi ke database

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek user di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Simpan session dan cookie
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        setcookie("user_id", $user['user_id'], time() + (86400 * 30), "/"); // 30 hari
        setcookie("role", $user['role'], time() + (86400 * 30), "/");

        // Arahkan ke dashboard sesuai role
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
    } else {
        echo "Email atau password salah.";
    }
}

// Menampilkan pesan sukses jika ada
if (isset($_SESSION['success_message'])) {
    echo "<p style='color: green;'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']);  // Hapus pesan setelah ditampilkan
}
?>

<!-- Form Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" name="login" value="Login">
    </form>
    <p><a href="forgot_reset_password.php">Forgot Password?</a></p>
    <p><a href="register.php">Registrasi</a></p>
</body>
</html>
