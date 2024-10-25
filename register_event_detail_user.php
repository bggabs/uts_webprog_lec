<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $event = $conn->query("SELECT * FROM events WHERE event_id = $event_id")->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    $check_query = "SELECT COUNT(*) AS count FROM registrations WHERE user_id = '$user_id' AND event_id = '$event_id'";
    $check_result = $conn->query($check_query);
    $is_registered = $check_result->fetch_assoc()['count'] > 0;

    $current_participants_query = "SELECT COUNT(*) AS current_participants FROM registrations WHERE event_id = '$event_id'";
    $current_participants_result = $conn->query($current_participants_query);
    $current_participants = $current_participants_result->fetch_assoc()['current_participants'];

    if ($is_registered) {
        echo "<p>Anda sudah terdaftar untuk acara ini.</p>";
    } elseif ($current_participants >= $event['max_participants']) {
        echo "<p>Maaf, jumlah peserta sudah mencapai batas maksimum.</p>";
    } else {
        $query = "INSERT INTO registrations (user_id, event_id, registration_date) VALUES ('$user_id', '$event_id', CURRENT_TIMESTAMP)";
        
        if ($conn->query($query) === TRUE) {
            echo "<p>Pendaftaran berhasil!</p>";
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Event Details</title>
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

    <h2>Register for this Event</h2>
    <form method="POST" action="">
        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
        <input type="submit" value="Register">
    </form>
    
    <div>
        <a href="user_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
