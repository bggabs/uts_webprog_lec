<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $conn->begin_transaction();

    try {
        $delete_registrations_query = "DELETE FROM registrations WHERE user_id = '$user_id'";
        if (!$conn->query($delete_registrations_query)) {
            throw new Exception("Error deleting registrations: " . $conn->error);
        }

        $delete_user_query = "DELETE FROM users WHERE user_id = '$user_id'";
        if (!$conn->query($delete_user_query)) {
            throw new Exception("Error deleting user: " . $conn->error);
        }

        $conn->commit();
        echo "User and related registrations deleted successfully.";

    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }

} else {
    header("Location: user_management.php");
    exit();
}

header("Location: user_management.php");
exit();
?>
