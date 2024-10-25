<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] != 'user') {
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
    <title>User Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_dashboard.css">
</head>
<body>
<nav>
    <ul>
        <li><a href="user_dashboard.php">Event Browsing</a></li>
        <li class="right"><a href="logout.php" class="logout">Logout</a></li>
        <li class="right"><a href="user_profile.php">User Profile</a></li>
        <li class="right"><a href="about_us.php">About Us</a></li>
        <li class="right"><a href="registered_events.php">Event Registration</a></li>
    </ul>
</nav>


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

        <form action="user_dashboard.php" method="POST">
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

        <h2>Available Events</h2>

        <div class="event-list">
            <?php $no_urut = 1;
            while ($row = $events->fetch_assoc()) { ?>
                <div class="event-card">
                    <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['event_name']; ?>">

                    <div class="event-details">
                        <h3><?php echo $row['event_name']; ?></h3>
                        <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
                        <p><strong>Max Participants:</strong> <?php echo $row['max_participants']; ?></p>
                        <p><strong>Current Participants:</strong> <?php echo $row['current_participants']; ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst($row['status']); ?></p>
                    </div>

                    <div class="event-action">
                        <a href="register_event_detail_user.php?event_id=<?php echo $row['event_id']; ?>">
                            Detail event and Register!
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </body>
</html>
