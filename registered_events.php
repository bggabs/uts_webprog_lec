<?php 
session_start();
require 'db_connection.php';

if ($_SESSION['role'] != 'user') {
    header("Location: login.php");  // Jika bukan user, arahkan ke login
    exit();
}

$user_id = $_SESSION['user_id'];

// Get registered events for the user
$query = "SELECT r.registration_id, e.event_id, e.event_name, e.event_date, e.location, e.image       
          FROM registrations r 
          JOIN events e ON r.event_id = e.event_id 
          WHERE r.user_id = $user_id";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registered Events</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/registered_events.css">
</head>
<body>
<nav>
    <ul>
        <li><a href="registered_events.php">Event Registration</a></li>
        <li class="right"><a href="logout.php" class="logout">Logout</a></li>
        <li class="right"><a href="user_profile.php">User Profile</a></li>
        <li class="right"><a href="about_us.php">About Us</a></li>
        <li class="right"><a href="user_dashboard.php">Event Browsing</a></li>
    </ul>
</nav>

<div class="cards-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <img src="uploads/<?php echo $row['image']; ?>" alt="Event Image">
                <div class="card-details">
                    <h2><?php echo $row['event_name']; ?></h2>
                    <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
                    <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
                </div>
                <div class="card-actions">
                    <a href="view_event_details_user.php?event_id=<?php echo $row['event_id']; ?>">Event Detail</a>
                    <a href="cancel_registration.php?registration_id=<?php echo $row['registration_id']; ?>" class="cancel" onclick="return confirm('Are you sure?');">Cancel Register</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have not registered for any events yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
