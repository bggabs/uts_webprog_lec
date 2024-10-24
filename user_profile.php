<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil informasi profil pengguna
$user_query = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'");
$user = $user_query->fetch_assoc();

// Ambil histori registrasi event
$registration_query = $conn->query("SELECT e.event_id, e.event_name, r.registration_date 
                                     FROM registrations r 
                                     JOIN events e ON r.event_id = e.event_id 
                                     WHERE r.user_id = '$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>
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

    <h1>User Profile</h1>
    
    <h2>Profile Information</h2>
    <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
    
    <h2>Event Registration History</h2>
<table border="1">
    <thead>
        <tr>
            <th>Event Name</th>
            <th>Registration Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($registration_query->num_rows > 0): ?>
            <?php while ($row = $registration_query->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['event_name']; ?></td>
                    <td><?php echo $row['registration_date']; ?></td>
                    <td>
                       <a href="view_event_details_user.php?event_id=<?php echo $row['event_id']; ?>">Event Detail </a> | 
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No event registrations found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

    <h2>Edit Profile</h2>
    <form method="POST" action="update_profile.php">
        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
        
        <input type="submit" value="Update Profile">
    </form>

    <p><a href="forgot_reset_password.php">Change Password</a></p>

    <div>
        <a href="user_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
