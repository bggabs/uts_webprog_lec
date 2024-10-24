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
</head>
<body>
<nav>
    <ul>
        <li><a href="user_dashboard.php">Event Browsing</a></li>
        <li><a href="registered_events.php">Event Registration</a></li>
        <li><a href="user_profile.php">User Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

    <h1>Your Registered Events</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['event_id']; ?></td>
                        <td><?php echo $row['event_name']; ?></td>
                        <td><?php echo $row['event_date']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><img src ="uploads/<?php echo $row['image']; ?>" width="100"></td>
                        <td>
                        <a href="view_event_details_user.php?event_id=<?php echo $row['event_id']; ?>">Event Detail </a> | 
                        <a href="cancel_registration.php?registration_id=<?php echo $row['registration_id']; ?>" onclick="return confirm('Are you sure?');">Cancel Register</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not registered for any events yet.</p>
    <?php endif; ?>
</body>
</html>
