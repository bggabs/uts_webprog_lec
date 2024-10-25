<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query_total_events = $conn->query("SELECT COUNT(*) AS total_events FROM events");
$total_events = $query_total_events->fetch_assoc()['total_events'];

$query_open_events = $conn->query("SELECT COUNT(*) AS open_events FROM events WHERE status = 'open'");
$open_events = $query_open_events->fetch_assoc()['open_events'];

$query_closed_events = $conn->query("SELECT COUNT(*) AS closed_events FROM events WHERE status = 'closed'");
$closed_events = $query_closed_events->fetch_assoc()['closed_events'];

$query_canceled_events = $conn->query("SELECT COUNT(*) AS canceled_events FROM events WHERE status = 'canceled'");
$canceled_events = $query_canceled_events->fetch_assoc()['canceled_events'];

$query_registrations = $conn->query("SELECT COUNT(*) AS total_registrations FROM registrations");
$total_registrations = $query_registrations->fetch_assoc()['total_registrations'];

$filter = "";
if (isset($_POST['filter'])) {
    $filter_date = $_POST['filter_date'];
    $filter_status = $_POST['filter_status'];
    
    if (!empty($filter_date)) {
        $filter .= " AND event_date = '$filter_date'";
    }
    if (!empty($filter_status)) {
        $filter .= " AND status = '$filter_status'";
    }
}

$query = "SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) AS current_participants FROM events e WHERE 1=1 $filter ORDER BY event_date ASC";
$events = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\admin_dashboard.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="event_management.php">Events Management</a></li>
            <li><a href="user_management.php">Users Registration</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <h1>Admin Dashboard</h1>

    <div class="dashboard-stats">
        <div class="stat-box">
            <h2>Total Events</h2>
            <p><?php echo $total_events; ?></p>
        </div>
        <div class="stat-box">
            <h2>Open Events</h2>
            <p><?php echo $open_events; ?></p>
        </div>
        <div class="stat-box">
            <h2>Closed Events</h2>
            <p><?php echo $closed_events; ?></p>
        </div>
        <div class="stat-box">
            <h2>Canceled Events</h2>
            <p><?php echo $canceled_events; ?></p>
        </div>
    </div>

    <form action="event_management.php" method="POST">
        <h2>Filter Events</h2>
        <label for="filter_date">Filter by Date:</label>
        <input type="date" id="filter_date" name="filter_date">
        
        <label for="filter_status">Filter by Status:</label>
        <select id="filter_status" name="filter_status">
            <option value="">-- Select Status --</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
            <option value="canceled">Canceled</option>
        </select>
        <input type="submit" name="filter" value="Filter">
    </form>

    <h2>Existing Events</h2>
    <table>
        <tr>
            <th>No</th>
            <th>Event Name</th>
            <th>Date</th>
            <th>Max Participants</th>
            <th>Current Participants</th>
            <th>Status</th>
            <th>Image</th>
            <th>Actions</th>    
        </tr>
        <?php     
        $no_urut = 1;
        while ($row = $events->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $no_urut++; ?></td>
            <td><?php echo $row['event_name']; ?></td>
            <td><?php echo $row['event_date']; ?></td>
            <td><?php echo $row['max_participants']; ?></td>
            <td><?php echo $row['current_participants']; ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td><img src="uploads/<?php echo $row['image']; ?>" width="100"></td>
            <td>
                <a href="view_event_details.php?event_id=<?php echo $row['event_id']; ?>">View Details</a> |
                <a href="view_registrations.php?event_id=<?php echo $row['event_id']; ?>">View Registrations</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
