<?php 
session_start();
require 'db_connection.php';

if ($_SESSION['role'] != 'user') {
    header("Location: login.php");  // Jika bukan user, arahkan ke login
    exit();
}

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = $_POST['registration_id'];

    // Delete registration from database
    $query = "DELETE FROM registrations WHERE registration_id = $registration_id";
    
    if ($conn->query($query) === TRUE) {
        echo "<p>Registration cancelled successfully!</p>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }

    echo '<a href="view_registered_events.php">Back to Registered Events</a>';
}
?>
