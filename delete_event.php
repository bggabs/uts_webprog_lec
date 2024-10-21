<?php
session_start();
require 'db_connection.php';

// Pastikan hanya admin yang bisa mengakses
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Hapus event berdasarkan event_id
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    
    // Periksa apakah event benar-benar ada
    $event_check = $conn->query("SELECT * FROM events WHERE event_id = $event_id");
    if ($event_check->num_rows > 0) {
        // Hapus event
        $delete_event = $conn->query("DELETE FROM events WHERE event_id = $event_id");
        
        if ($delete_event) {
            $_SESSION['success_message'] = "Event deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete event.";
        }
    } else {
        $_SESSION['error_message'] = "Event not found.";
    }

    header("Location: event_management.php");
    exit();
}
?>