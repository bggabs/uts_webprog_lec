<?php
session_start();
require 'db_connection.php';

if (isset($_POST['forgot_password'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['reset_email'] = $email;
        header("Location: forgot_reset_password.php?reset=1");
        exit();
    } else {
        echo "Email tidak ditemukan.";
    }
}

if (isset($_POST['reset_password'])) {
    $email = $_SESSION['reset_email'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);
    if ($stmt->execute()) {
        unset($_SESSION['reset_email']);
        $_SESSION['success_message'] = "Password berhasil di-reset. Silakan login kembali.";
        header("Location: login.php");
        exit();
    } else {
        echo "Terjadi kesalahan saat mengatur ulang password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot/Reset Password</title>
    <link rel="stylesheet" type="text/css" href="css/forgot_password.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['reset'])): ?>
            <h2>Reset Password</h2>
            <form action="forgot_reset_password.php" method="POST">
                <label>New Password:</label><br>
                <input type="password" name="new_password" required><br><br>
                <input type="submit" name="reset_password" value="Reset Password">
            </form>
        <?php else: ?>
            <h2>Forgot Password</h2>
            <form action="forgot_reset_password.php" method="POST">
                <label>Email:</label><br>
                <input type="email" name="email" required><br><br>
                <input type="submit" name="forgot_password" value="Send Reset Link">
            </form>
        <?php endif; ?>

        <p class="back-to-login"><a href="login.php">Kembali ke halaman Login</a></p>
    </div>
</body>
</html>