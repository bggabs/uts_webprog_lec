<?php
// Pengaturan koneksi database
$servername = "localhost";
$username = "root";  // Sesuaikan dengan user MySQL Anda
$password = "";      // Sesuaikan dengan password MySQL Anda
$dbname = "db_event";  // Nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
