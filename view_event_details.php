<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil detail event berdasarkan ID
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $event = $conn->query("SELECT * FROM events WHERE event_id = $event_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Event Details</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Event Details</h1>
    <h2><strong>Event ID</strong> <?php echo $event['event_id']; ?></h2>

    <p><strong>Event Name:</strong> <?php echo $event['event_name']; ?></p>
    <p><strong>Date:</strong> <?php echo $event['event_date']; ?></p>
    <p><strong>Time:</strong> <?php echo $event['event_time']; ?></p>
    <p><strong>Location:</strong> <?php echo $event['location']; ?></p>
    <p><strong>Description:</strong> <?php echo $event['description']; ?></p>
    <p><strong>Max Participants:</strong> <?php echo $event['max_participants']; ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($event['status']); ?></p>

    <img src="uploads/<?php echo $event['image']; ?>" width="200">

    <div>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
