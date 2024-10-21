<?php
session_start();
require 'db_connection.php';

// Pastikan hanya admin yang bisa mengakses
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Proses penambahan event baru
if (isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_location = $_POST['event_location'];
    $event_description = $_POST['event_description'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    // Penanganan upload gambar
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    // Pindahkan file gambar ke direktori upload
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Siapkan query untuk menambah data ke database
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_time, location, description, max_participants, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiss", $event_name, $event_date, $event_time, $event_location, $event_description, $max_participants, $status, $image);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Event added successfully.";
            header("Location: event_management.php");
            exit();
        } else {
            echo "Failed to add event.";
        }
    } else {
        echo "Failed to upload image.";
    }
}

// Proses filter pencarian berdasarkan kategori
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

// Ambil daftar event dari database dengan filter
$query = "SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) AS current_participants FROM events e WHERE 1=1 $filter ORDER BY event_date ASC";
$events = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Event Management</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link ke file CSS -->
</head>
<body>
    <h1>Event Management</h1>

    <!-- Tombol untuk menambahkan event -->
    <button onclick="document.getElementById('addEventForm').style.display='block'">Add New Event</button>

    <!-- Form filter pencarian -->
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

    <!-- Form untuk menambah event baru (tersembunyi sampai tombol ditekan) -->
    <div id="addEventForm" style="display:none;">
        <form action="event_management.php" method="POST" enctype="multipart/form-data">
            <h2>Add New Event</h2>
            <label for="event_name">Event Name:</label><br>
            <input type="text" id="event_name" name="event_name" required><br>
            <label for="event_date">Event Date:</label><br>
            <input type="date" id="event_date" name="event_date" required><br>
            <label for="event_time">Event Time:</label><br>
            <input type="time" id="event_time" name="event_time" required><br>
            <label for="event_location">Event Location:</label><br>
            <input type="text" id="event_location" name="event_location" required><br>
            <label for="event_description">Event Description:</label><br>
            <textarea id="event_description" name="event_description" required></textarea><br>
            <label for="max_participants">Max Participants:</label><br>
            <input type="number" id="max_participants" name="max_participants" required><br>
            <label for="image">Event Image:</label><br>
            <input type="file" id="image" name="image" accept="image/*" required><br>
            <label for="status">Status:</label><br>
            <select id="status" name="status" required>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
                <option value="canceled">Canceled</option>
            </select><br><br>
            <input type="submit" name="add_event" value="Add Event">
        </form>
    </div>

    <!-- Tabel untuk menampilkan event yang ada -->
    <h2>Existing Events</h2>
    <table border="1">
        <tr>
            <th>Event Name</th>
            <th>Description</th>
            <th>Date</th>
            <th>Time</th>
            <th>Location</th>
            <th>Max Participants</th>
            <th>Current Participants</th>
            <th>Status</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $events->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['event_name']; ?></td>
            <td><?php echo $row['description']; ?></td>            
            <td><?php echo $row['event_date']; ?></td>
            <td><?php echo $row['event_time']; ?></td>
            <td><?php echo $row['location']; ?></td>
            <td><?php echo $row['max_participants']; ?></td>
            <td><?php echo $row['current_participants']; ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td><img src="uploads/<?php echo $row['image']; ?>" width="100"></td>
            <td>
                <a href="edit_event.php?event_id=<?php echo $row['event_id']; ?>">Edit</a>
                <a href="delete_event.php?event_id=<?php echo $row['event_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="menu">
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
