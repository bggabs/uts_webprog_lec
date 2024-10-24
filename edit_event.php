<?php
session_start();
require 'db_connection.php';

// Pastikan hanya admin yang bisa mengakses
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data event berdasarkan event_id
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $event = $conn->query("SELECT * FROM events WHERE event_id = $event_id")->fetch_assoc();
}

// Proses penyimpanan hasil edit event
if (isset($_POST['edit_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_location = $_POST['event_location'];
    $event_description = $_POST['event_description'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];
    $image_lama = $_POST['image_lama'];

    // Cek apakah user pilih gambar baru atau tidak
    if ($_FILES['image']['error'] === 4) {
        $image = $image_lama; // Jika tidak pilih, pakai gambar lama
    } else {
        $image = upload_image(); // Jika pilih, upload gambar baru
        if (!$image) {
            $image = $image_lama;
        }
    }

    // Query untuk update data event
    $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, event_time = ?, location = ?, description = ?, max_participants = ?, status = ?, image = ? WHERE event_id = ?");
    $stmt->bind_param("sssssissi", $event_name, $event_date, $event_time, $event_location, $event_description, $max_participants, $status, $image, $event_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Event updated successfully.";
        header("Location: event_management.php");
        exit();
    } else {
        echo "Failed to update event.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Event</h1>

    <form action="edit_event.php?event_id=<?php echo $event['event_id']; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="image_lama" value="<?php echo $event['image']; ?>"> <!-- Menyimpan gambar lama -->
        <label for="event_name">Event Name:</label><br>
        <input type="text" id="event_name" name="event_name" value="<?php echo $event['event_name']; ?>" required><br>
        
        <label for="event_date">Event Date:</label><br>
        <input type="date" id="event_date" name="event_date" value="<?php echo $event['event_date']; ?>" required><br>
        
        <label for="event_time">Event Time:</label><br>
        <input type="time" id="event_time" name="event_time" value="<?php echo $event['event_time']; ?>" required><br>
        
        <label for="event_location">Event Location:</label><br>
        <input type="text" id="event_location" name="event_location" value="<?php echo $event['location']; ?>" required><br>
        
        <label for="event_description">Event Description:</label><br>
        <textarea id="event_description" name="event_description" required><?php echo $event['description']; ?></textarea><br>
        
        <label for="max_participants">Max Participants:</label><br>
        <input type="number" id="max_participants" name="max_participants" value="<?php echo $event['max_participants']; ?>" required><br>
        
        <label for="status">Status:</label><br>
        <select id="status" name="status" required>
            <option value="open" <?php echo ($event['status'] == 'open') ? 'selected' : ''; ?>>Open</option>
            <option value="closed" <?php echo ($event['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
            <option value="canceled" <?php echo ($event['status'] == 'canceled') ? 'selected' : ''; ?>>Canceled</option>
        </select><br><br>
    
        <!-- Tambahkan input untuk file gambar -->
       <label for="image">Change Event Image:</label><br>
       <input type="file" id="image" name="image" accept="image/*"><br><br>

        <input type="submit" name="edit_event" value="Update Event">
    </form>

    <div class="menu">
        <a href="event_management.php">Back to Event Management</a>
    </div>
</body>
</html>
