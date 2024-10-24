<?php
session_start();
require 'db_connection.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil registration_id dari query string
if (isset($_GET['registration_id'])) {
    $registration_id = $_GET['registration_id'];

    // Hapus registrasi dari database
    $delete_query = "DELETE FROM registrations WHERE registration_id = '$registration_id'";
    
    if ($conn->query($delete_query) === TRUE) {
        echo "Registration cancelled successfully.";
    } else {
        echo "Error cancelling registration: " . $conn->error;
    }
} else {
    header("Location: user_management.php");
    exit();
}

// Redirect kembali ke halaman manajemen pengguna setelah pembatalan
header("Location: user_management.php");
exit();
?>
