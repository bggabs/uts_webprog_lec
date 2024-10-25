<?php
session_start();
require 'db_connection.php';

$errors = array();

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $role = $_POST['role'];

    if (empty($name) || empty($email) || empty($password) || empty($password2)) {
        $errors[] = "Semua form harus diisi.";
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    if ($password !== $password2) {
        $errors[] = "Password dan konfirmasi password tidak sama.";
    }

    if (empty($errors)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($role == 'admin') {
            $token = $_POST['token'];
            if ($token == 'admin_token') { 
                $role = 'admin';
            } else {
                $errors[] = "Token admin salah.";
            }
        } else {
            $role = 'user';
        }

        if (empty($errors)) {
            $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $result = $check_email->get_result();

            if ($result->num_rows == 0) {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Registrasi berhasil. Silakan login.";
                    header("Location: login.php");
                    exit();
                } else {
                    $errors[] = "Registrasi gagal.";
                }
            } else {
                $errors[] = "Email sudah terdaftar.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <script>
        function toggleTokenInput() {
            var role = document.getElementById("role").value;
            var tokenField = document.getElementById("tokenField");
            if (role === "admin") {
                tokenField.style.display = "block";
            } else {
                tokenField.style.display = "none";
            }
        }

        function validateForm() {
            var name = document.forms["registerForm"]["name"].value;
            var email = document.forms["registerForm"]["email"].value;
            var password = document.forms["registerForm"]["password"].value;
            var password2 = document.forms["registerForm"]["password2"].value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            var errorMessage = "";

            if (name == "" || email == "" || password == "" || password2 == "") {
                errorMessage += "Semua form harus diisi.<br>";
            }

            if (!emailPattern.test(email)) {
                errorMessage += "Format email tidak valid.<br>";
            }

            if (password !== password2) {
                errorMessage += "Password dan konfirmasi password tidak sama.<br>";
            }

            if (errorMessage != "") {
                document.getElementById("errorMessages").innerHTML = errorMessage;
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="register-container">
        <form name="registerForm" action="register.php" method="POST" onsubmit="return validateForm()">
            <h2>Register</h2>
            <div class="input-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="input-group">
                <label for="password2">Konfirmasi Password:</label>
                <input type="password" name="password2" id="password2" required>
            </div>
            <div class="input-group">
                <label for="role">Role:</label>
                    <select name="role" id="role" onchange="toggleTokenInput()" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
            </div>

            <div id="tokenField" class="input-group" style="display:none;">
                <label for="token">Admin Token:</label>
                <input type="text" name="token" id="token" placeholder="Enter admin token">
            </div>

            <div id="errorMessages" class="error-messages">
                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <input type="submit" name="register" value="Register">
            </div>
        </form>
        <p><a href="login.php">Kembali ke login</a></p>
    </div>
</body>
</html>
