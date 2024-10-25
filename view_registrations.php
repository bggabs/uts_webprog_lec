<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Query untuk mendapatkan nama event
     $event_query = "SELECT event_name FROM events WHERE event_id = $event_id";
     $event_result = $conn->query($event_query);
 
     if ($event_result && $event_result->num_rows > 0) {
         $event = $event_result->fetch_assoc();
         $event_name = $event['event_name'];
     } else {
         $event_name = "Unknown Event";
     }
 

    // Query untuk mendapatkan daftar peserta
    $registrants_query = "SELECT u.name AS username, u.email, r.registration_date 
                          FROM registrations r 
                          JOIN users u ON r.user_id = u.user_id 
                          WHERE r.event_id = $event_id";
    $registrants = $conn->query($registrants_query);

    // Jika query gagal, tampilkan pesan error
    if (!$registrants) {
        die("Query error: " . $conn->error);
    }

    // Fungsi untuk mengekspor ke CSV dan menyimpannya di folder csv_registration
    if (isset($_POST['export_csv'])) {
        $directory = 'csv_registration';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // Buat folder jika belum ada
        }

        // Path file CSV
        $filename = "$directory/registrations_event_$event_id.csv";

        // Buka file di mode write
        $output = fopen($filename, "w");

        // Tulis header kolom
        fputcsv($output, array('Username', 'Email', 'Registration_Date'));

        // Tulis data registrants ke CSV
        while ($row = $registrants->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);

        // Tampilkan pesan sukses dan link ke file CSV
        $_SESSION['success_message'] = "Registrations exported successfully to '$filename'.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registrants</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Registrants for Event <?php echo $event_name ?></h1>
    <h2>Event id : <?php echo $event_id; ?></h2>

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
