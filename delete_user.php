<?php
session_start();
require 'db_connection.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil user_id dari query string
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Hapus semua registrasi terkait dengan user
        $delete_registrations_query = "DELETE FROM registrations WHERE user_id = '$user_id'";
        if (!$conn->query($delete_registrations_query)) {
            throw new Exception("Error deleting registrations: " . $conn->error);
        }

        // Hapus pengguna dari database
        $delete_user_query = "DELETE FROM users WHERE user_id = '$user_id'";
        if (!$conn->query($delete_user_query)) {
            throw new Exception("Error deleting user: " . $conn->error);
        }

        // Commit transaksi jika semuanya sukses
        $conn->commit();
        echo "User and related registrations deleted successfully.";

    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        echo $e->getMessage();
    }

} else {
    header("Location: user_management.php");
    exit();
}

// Redirect kembali ke halaman manajemen pengguna setelah penghapusan
header("Location: user_management.php");
exit();
?>
