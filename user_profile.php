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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_profile.css">
</head>
<body>
<nav>
    <ul>
        <li><a href="user_profile.php">User Profile</a></li>
        <li class="right"><a href="logout.php" class="logout">Logout</a></li>
        <li class="right"><a href="user_dashboard.php">Event Browsing</a></li>
        <li class="right"><a href="about_us.php">About Us</a></li>
        <li class="right"><a href="registered_events.php">Event Registration</a></li>
    </ul>
</nav>


<div class="container">
    <div class="profile-info">
        <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
    </div>

    <div class="edit-profile">
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
    </div>
</div>

<div>
    <a href="user_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
