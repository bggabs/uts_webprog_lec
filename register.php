<?php
session_start();
require 'db_connection.php';  // Koneksi ke database

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];  // Konfirmasi password
    $role = $_POST['role'];
    $role = $_POST['role'];

    // Cek apakah password dan konfirmasi password sama
    if ($password !== $password2) {
        echo "Password dan konfirmasi password tidak sama. <a href='register.php'>Coba lagi</a>";
        exit();
    }

    // Lanjutkan dengan hashing password setelah konfirmasi
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Token untuk admin
    if ($role == 'admin') {
        $token = $_POST['token'];
        if ($token == 'admin_token') {  // 'admin_token' adalah token yang harus diketahui untuk register jadi admin
            $role = 'admin';
        } else {
            echo "Token admin salah. <br/><a href='register.php'>Coba lagi</a>";
            exit();
        }
    } else {
        $role = 'user';
    }

    // Cek jika email sudah ada di database
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows == 0) {
        // Simpan data user ke database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Registrasi berhasil. Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            echo "Registrasi gagal.";
        }
    } else {
        echo "Email sudah terdaftar.";
    }
}
?>

<!-- Form Registrasi -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <script>
        // JavaScript untuk menampilkan atau menyembunyikan input token
        function toggleTokenInput() {
            var role = document.getElementById("role").value;
            var tokenField = document.getElementById("tokenField");

            if (role === "admin") {
                tokenField.style.display = "block"; // Tampilkan input token
            } else {
                tokenField.style.display = "none";  // Sembunyikan input token
            }
        }
    </script>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" required><br>
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br>
        <label>Konfirmasi Password:</label><br>
        <input type="password" name="password2" required><br>

        <!-- Dropdown untuk memilih role -->
        <label>Role:</label><br>
        <select name="role" id="role" onchange="toggleTokenInput()" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br>

        <!-- Input token yang hanya muncul jika role admin dipilih -->
        <div id="tokenField" style="display:none;">
            <label>Admin Token:</label><br>
            <input type="text" name="token" placeholder="Enter admin token"><br>
        </div><br>

        <input type="submit" name="register" value="Register">
    </form>
    <p><a href="login.php">Kembali ke login</a></p>
</body>
</html>
