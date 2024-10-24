<?php
session_start();
require 'db_connection.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil daftar pengguna dan registrasi event mereka
$query_users = "
    SELECT u.user_id, u.name, u.email, u.role, u.created_at, e.event_name, r.registration_date, r.registration_id
    FROM users u
    LEFT JOIN registrations r ON u.user_id = r.user_id
    LEFT JOIN events e ON r.event_id = e.event_id
";

$users = $conn->query($query_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>User Management</h1>

    <h2>View Users and Their Event Registrations</h2>
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Event Name</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users->num_rows > 0): ?>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td><?php echo $user['created_at']; ?></td>
                        <td><?php echo $user['event_name'] ?: 'No event registered'; ?></td>
                        <td><?php echo $user['registration_date'] ?: 'N/A'; ?></td>
                        <td>
                            <?php if ($user['event_name']): ?>
                                <a href="delete_registration.php?registration_id=<?php echo $user['registration_id']; ?>" onclick="return confirm('Are you sure you want to cancel this registration?');">Cancel Registration</a> | 
                            <?php endif; ?>
                            <a href="delete_user.php?user_id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</a>
                        </td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
