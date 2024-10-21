<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Query untuk mendapatkan daftar peserta
    $registrants = $conn->query("SELECT u.username, u.email, r.registration_date FROM registrations r JOIN users u ON r.user_id = u.user_id WHERE r.event_id = $event_id");

    // Fungsi untuk mengekspor ke CSV
    if (isset($_POST['export_csv'])) {
        $filename = "registrations_event_$event_id.csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen("php://output", "w");
        fputcsv($output, array('Username', 'Email', 'Registration Date'));
        
        while ($row = $registrants->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registrants</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Registrants for Event ID: <?php echo $event_id; ?></h1>

    <form action="" method="POST">
        <input type="submit" name="export_csv" value="Export as CSV">
    </form>

    <table border="1">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Registration Date</th>
        </tr>
        <?php while ($row = $registrants->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['registration_date']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <div>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
