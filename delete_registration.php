<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['registration_id'])) {
    $registration_id = $_GET['registration_id'];

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

header("Location: user_management.php");
exit();
?>
