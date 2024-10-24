<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update user profile in the database
    $update_query = "UPDATE users SET name = '$name', email = '$email' WHERE user_id = '$user_id'";

    if ($conn->query($update_query) === TRUE) {
        echo "<p>Profile updated successfully!</p>";
    } else {
        echo "<p>Error updating profile: " . $conn->error . "</p>";
    }
}
?>

<div>
    <a href="user_profile.php">Back to Profile</a>
</div>
