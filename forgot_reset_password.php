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
        $token = bin2hex(random_bytes(50));

        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        $reset_link = "http://localhost/umns3/utslec/forgot_reset_password.php?token=" . $token;

        echo "Link reset password: <a href='$reset_link'>$reset_link</a>";
    } else {
        echo "Email tidak ditemukan.";
    }
}

if (isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset_data = $result->fetch_assoc();

    if ($reset_data) {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $reset_data['email']);
        if ($stmt->execute()) {
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->bind_param("s", $reset_data['email']);
            if ($stmt->execute()) {
            $_SESSION['success_message'] = "Password berhasil di-reset. Silakan login kembali.";
            header("Location: login.php");
            exit();
            }
        }
    } else {
        echo "Token tidak valid atau sudah kadaluarsa.";
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
    <?php if (isset($_GET['token'])): ?>
        <h2>Reset Password</h2>
        <form action="forgot_reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?= $_GET['token']; ?>">
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




