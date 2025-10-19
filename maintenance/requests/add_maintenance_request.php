<?php
session_start();
require '../db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch rooms for dropdown
$rooms_stmt = $pdo->query("SELECT room_number FROM rooms ORDER BY room_number");
$rooms = $rooms_stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch staff for reported by dropdown (Housekeeping and Room Attendant positions)
$staff_stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) as full_name FROM staff WHERE position_name IN ('Room Attendant', 'Housekeeping Manager', 'Assistant Housekeeper') AND employment_status = 'Active' ORDER BY first_name");
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid security token";
        header("Location: add_maintenance_request.php");
        exit;
    }

    $room_number = trim($_POST['room_number'] ?? '');
    $issue_type = trim($_POST['issue_type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = trim($_POST['priority'] ?? '');
    $reported_by = trim($_POST['reported_by'] ?? '');

    if (empty($room_number) || empty($issue_type) || empty($description) || empty($priority) || empty($reported_by)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add_maintenance_request.php");
        exit;
    }

    try {
        // Get room_id from rooms table
        $room_stmt = $pdo->prepare("SELECT room_id FROM rooms WHERE room_number = :room");
        $room_stmt->execute([':room' => $room_number]);
        $room_data = $room_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room_data) {
            $_SESSION['error'] = "Invalid room number selected";
            header("Location: add_maintenance_request.php");
            exit;
        }

        $room_id = $room_data['room_id'];

        // Get staff_id from staff table
        $staff_stmt = $pdo->prepare("SELECT staff_id FROM staff WHERE CONCAT(first_name, ' ', last_name) = :name AND position_name IN ('Room Attendant', 'Housekeeping Manager', 'Assistant Housekeeper') AND employment_status = 'Active'");
        $staff_stmt->execute([':name' => $reported_by]);
        $staff_data = $staff_stmt->fetch(PDO::FETCH_ASSOC);

        $requester_staff_id = $staff_data ? $staff_data['staff_id'] : null;

        $stmt = $pdo->prepare("INSERT INTO maintenance_requests (room_id, room_number, issue_type, issue_description, priority, status, requested_by, requester_staff_id, requested_at) VALUES (:room_id, :room_number, :issue_type, :issue_desc, :priority, 'Pending', :reported, :staff_id, NOW())");
        $stmt->execute([
            ':room_id' => $room_id,
            ':room_number' => $room_number,
            ':issue_type' => $issue_type,
            ':issue_desc' => $description,
            ':priority' => $priority,
            ':reported' => $reported_by,
            ':staff_id' => $requester_staff_id
        ]);
        $_SESSION['success'] = "Maintenance request added successfully.";
        header("Location: maintenance_requests.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error adding request: ".$e->getMessage();
        header("Location: add_maintenance_request.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Maintenance Request</title>
<style>
body, html {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('/hotel/homepage/hotel_room.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    color: #fff;
}
.overlay {
    background-color: rgba(0,0,0,0.88);
    min-height: 100vh;
    padding: 40px 20px;
    box-sizing: border-box;
}
header {
    text-align: center;
    margin-bottom: 20px;
}
header h1 {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 10px;
}
.form-container {
    width: 95%;
    max-width: 600px;
    margin: 0 auto;
    background: #23272f;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 18px rgba(0,0,0,0.15);
    opacity: 0.95;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #FF9800;
}
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    border: none;
    font-size: 14px;
    background: #2e3440;
    color: #fff;
}
.form-group textarea {
    resize: vertical;
    min-height: 100px;
}
.button-group {
    text-align: center;
    margin-top: 30px;
}
.button-group button,
.button-group a button {
    padding: 12px 24px;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    cursor: pointer;
    margin: 0 10px;
    transition: background 0.3s;
}
.button-group button {
    background-color: #FF9800;
    color: #fff;
}
.button-group button:hover {
    background-color: #e67e22;
}
.button-group a {
    text-decoration: none;
}
.button-group a button {
    background-color: #666;
    color: #fff;
}
.button-group a button:hover {
    background-color: #555;
}
.message {
    position: fixed;
    top: 10%;
    left: 50%;
    transform: translate(-50%, -50%);
    min-width: 200px;
    max-width: 300px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 500;
    text-align: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.message.success {
    background-color: #FF9800;
    color: #fff;
}
.message.error {
    background-color: #e74c3c;
    color: #fff;
}
</style>
</head>
<body>
<div class="overlay">
    <header>
        <h1>Add Maintenance Request</h1>
    </header>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="message success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label for="room_number">Room Number:</label>
                <select id="room_number" name="room_number" required>
                    <option value="">Select Room</option>
                    <?php foreach($rooms as $room): ?>
                        <option value="<?= htmlspecialchars($room) ?>"><?= htmlspecialchars($room) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="issue_type">Issue Type:</label>
                <select id="issue_type" name="issue_type" required>
                    <option value="">Select Issue Type</option>
                    <option value="Electrical">Electrical</option>
                    <option value="Plumbing">Plumbing</option>
                    <option value="HVAC">HVAC</option>
                    <option value="Structural">Structural</option>
                    <option value="Cleaning">Cleaning</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Appliance">Appliance</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="priority">Priority:</label>
                <select id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>

            <div class="form-group">
                <label for="reported_by">Reported By:</label>
                <select id="reported_by" name="reported_by" required>
                    <option value="">Select Staff Member</option>
                    <?php foreach($staff as $member): ?>
                        <option value="<?= htmlspecialchars($member) ?>"><?= htmlspecialchars($member) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="button-group">
                <button type="submit">Add Request</button>
                <a href="maintenance_requests.php"><button type="button">Cancel</button></a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.message').forEach(msg => {
        setTimeout(() => { msg.style.opacity='0'; setTimeout(()=>msg.remove(),300); }, 5000);
    });
});
</script>
</body>
</html>