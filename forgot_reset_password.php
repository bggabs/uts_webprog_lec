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
        // Buat token unik untuk reset password
        $token = bin2hex(random_bytes(50));

        // Simpan token ke database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        // Buat link reset password (biasanya dikirim via email)
        $reset_link = "http://localhost/umns3/utslec/forgot_reset_password.php?token=" . $token;

        // Kirim token ke email (pada contoh ini hanya ditampilkan)
        echo "Link reset password: <a href='$reset_link'>$reset_link</a>";
    } else {
        echo "Email tidak ditemukan.";
    }
}

// Proses reset password jika ada token
if (isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Cek token valid
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset_data = $result->fetch_assoc();

    if ($reset_data) {
        // Update password di tabel users
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $reset_data['email']);
        if ($stmt->execute()) {
            // Hapus token setelah berhasil reset password
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

<!-- Form Lupa dan Reset Password -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot/Reset Password</title>
</head>
<body>
    <?php if (isset($_GET['token'])): ?>
        <!-- Form Reset Password -->
        <h2>Reset Password</h2>
        <form action="forgot_reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?= $_GET['token']; ?>">
            <label>New Password:</label><br>
            <input type="password" name="new_password" required><br><br>
            <input type="submit" name="reset_password" value="Reset Password">
        </form>
    <?php else: ?>
        <!-- Form Lupa Password -->
        <h2>Forgot Password</h2>
        <form action="forgot_reset_password.php" method="POST">
            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>
            <input type="submit" name="forgot_password" value="Send Reset Link">
        </form>
    <?php endif; ?>
</body>
</html>
