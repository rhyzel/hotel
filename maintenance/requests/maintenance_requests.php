<?php
session_start();
require '../db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_GET['delete_id'], $_GET['token'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id <= 0 || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        $_SESSION['error'] = "Invalid request ID or security token";
        header("Location: maintenance_requests.php");
        exit;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM maintenance_requests WHERE request_id = :id");
        $stmt->execute([':id' => $delete_id]);
        $_SESSION['success'] = "Maintenance request deleted.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting request: ".$e->getMessage();
    }
    header("Location: maintenance_requests.php");
    exit;
}

if (isset($_GET['take_id'], $_GET['token'])) {
    $take_id = (int)$_GET['take_id'];
    if ($take_id <= 0 || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        $_SESSION['error'] = "Invalid request ID or security token";
        header("Location: maintenance_requests.php");
        exit;
    }
    try {
        $stmt = $pdo->prepare("UPDATE maintenance_requests SET status = 'In Progress' WHERE request_id = :id");
        $stmt->execute([':id' => $take_id]);
        $_SESSION['success'] = "Maintenance request taken.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating request: ".$e->getMessage();
    }
    header("Location: maintenance_requests.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_request'])) {
    $schedule_id = (int)$_POST['request_id'];
    $scheduled_date = $_POST['scheduled_date'];
    $scheduled_time = $_POST['scheduled_time'];
    if ($schedule_id <= 0 || empty($scheduled_date) || empty($scheduled_time)) {
        $_SESSION['error'] = "Invalid request or missing date/time";
        header("Location: maintenance_requests.php");
        exit;
    }
    $scheduled_at = $scheduled_date . ' ' . $scheduled_time . ':00';
    try {
        $stmt = $pdo->prepare("UPDATE maintenance_requests SET status = 'In Progress' WHERE request_id = :id");
        $stmt->execute([':id' => $schedule_id]);
        $_SESSION['success'] = "Maintenance request scheduled.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating request: ".$e->getMessage();
    }
    header("Location: maintenance_requests.php");
    exit;
}

$search_room = $_POST['room'] ?? '';
$where = [];
$params = [];
if ($search_room) {
    $where[] = "room_number LIKE :room";
    $params[':room'] = "%$search_room%";
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$stmt = $pdo->prepare("SELECT mr.*, r.room_type FROM maintenance_requests mr LEFT JOIN rooms r ON mr.room_id = r.room_id $where_sql ORDER BY mr.requested_at DESC");
$stmt->execute($params);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$recent_requests = array_map(
    fn($req) => $req['room_number']." (".($req['issue_description'] ?? 'N/A').")",
    $requests
);
$recent_text = $recent_requests
    ? implode(", ", array_slice($recent_requests, 0, 5))
    : "No recent requests.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maintenance Requests</title>
<link rel="stylesheet" href="maintenance_requests.css">
</head>
<body>
<div class="overlay">
    <header>
        <h1>Maintenance Requests</h1>
        <p class="recent-items">Recent Requests: <?= htmlspecialchars($recent_text) ?></p>
    </header>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="message success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="search-container">
        <form method="POST" class="search-form">
            <a href="../maintenance.php"><button type="button">← Back to Maintenance</button></a>
            <input type="text" name="room" placeholder="Search by Room Number" value="<?= htmlspecialchars($search_room) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Issue Type</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Reported By</th>
                <th>Date Reported</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($requests): ?>
                <?php foreach($requests as $req): ?>
                <tr>
                    <td data-label="Request ID"><?= (int)$req['request_id'] ?></td>
                    <td data-label="Room Number"><?= htmlspecialchars($req['room_number']) ?></td>
                    <td data-label="Room Type"><?= htmlspecialchars($req['room_type'] ?? 'N/A') ?></td>
                    <td data-label="Issue Type"><?= htmlspecialchars($req['issue_type'] ?? 'N/A') ?></td>
                    <td data-label="Description"><?= htmlspecialchars($req['issue_description'] ?? 'N/A') ?></td>
                    <td data-label="Priority"><?= htmlspecialchars($req['priority']) ?></td>
                    <td data-label="Status"><?= htmlspecialchars($req['status']) ?></td>
                    <td data-label="Reported By"><?= htmlspecialchars($req['requested_by']) ?></td>
                    <td data-label="Date Reported"><?= date('M j, Y g:i A', strtotime($req['requested_at'])) ?></td>
                    <td data-label="Actions" class="actions">
                        <a href="maintenance_requests.php?take_id=<?= (int)$req['request_id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="action-btn take-btn"
                           onclick="return confirm('Take this maintenance request for room <?= htmlspecialchars($req['room_number']) ?>?');">
                           Take
                        </a>
                        <button type="button" class="action-btn schedule-btn" onclick="openScheduleModal(<?= (int)$req['request_id'] ?>, '<?= htmlspecialchars($req['room_number']) ?>')">Schedule</button>
                        <a href="maintenance_requests.php?delete_id=<?= (int)$req['request_id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="action-btn delete-btn"
                           onclick="return confirm('Delete this maintenance request for room <?= htmlspecialchars($req['room_number']) ?>?');">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align:center; font-style:italic; color:#666;">
                    No maintenance requests yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<div id="scheduleModal" class="modal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #23272f; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 95%; max-width: 300px; border-radius: 12px; color: #fff;">
        <span class="close" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeScheduleModal()">&times;</span>
        <h2 style="margin-top: 0;">Schedule Maintenance Request</h2>
        <p id="modalRoomNumber"></p>
        <form method="POST">
            <input type="hidden" name="request_id" id="modalRequestId">
            <div style="display: flex; gap: 20px; align-items: center; margin: 10px 0;">
                <div style="flex: 1;">
                    <label for="scheduled_date">Date:</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" required style="width: 95%; padding: 10px; border: none; border-radius: 6px;">
                </div>
                <div style="flex: 0.6;">
                    <label for="scheduled_time">Time:</label>
                    <input type="time" name="scheduled_time" id="scheduled_time" required style="width: 95%; padding: 10px; border: none; border-radius: 6px;">
                </div>
            </div>
            <button type="submit" name="schedule_request" style="background-color: #FF9800; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; width: 100%; margin-top: 10px;">Schedule</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.message').forEach(msg => {
        setTimeout(() => { msg.style.opacity='0'; setTimeout(()=>msg.remove(),300); }, 5000);
    });
});

function openScheduleModal(requestId, roomNumber) {
    document.getElementById('modalRequestId').value = requestId;
    document.getElementById('modalRoomNumber').textContent = 'Room: ' + roomNumber;
    document.getElementById('scheduleModal').style.display = 'block';
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('scheduleModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
</body>
</html>
