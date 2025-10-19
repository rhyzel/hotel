<?php
session_start();
require 'db.php';
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (isset($_GET['delete_id'], $_GET['token'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id <= 0 || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        $_SESSION['error'] = "Invalid schedule ID or security token";
        header("Location: maintenance_requests.php");
        exit;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM maintenance_requests WHERE schedule_id = :id");
        $stmt->execute([':id' => $delete_id]);
        $_SESSION['success'] = "Maintenance schedule deleted.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting schedule: ".$e->getMessage();
    }
    header("Location: maintenance_requests.php");
    exit;
}
$search_task = $_POST['task'] ?? '';
$where = [];
$params = [];
if ($search_task) {
    $where[] = "issue_description LIKE :task";
    $params[':task'] = "%$search_task%";
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";
$stmt = $pdo->prepare("SELECT * FROM maintenance_requests $where_sql ORDER BY scheduled_date DESC");
$stmt->execute($params);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
$schedule_by_date = [];
foreach ($schedules as $sch) {
    $date = date('Y-m-d', strtotime($sch['scheduled_date']));
    $schedule_by_date[$date][] = $sch;
}
$recent_schedules = array_map(
    fn($sch) => $sch['issue_description']." (".date('M j', strtotime($sch['scheduled_date'])).")",
    $schedules
);
$recent_text = $recent_schedules
    ? implode(", ", array_slice($recent_schedules, 0, 5))
    : "No scheduled maintenance.";
$current_month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$current_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);
$first_day_of_month = date('w', strtotime("$current_year-$current_month-01"));
$month_name = date('F', strtotime("$current_year-$current_month-01"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maintenance Schedule</title>
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
header p.recent-items {
    font-size: 16px;
    color: #ccc;
    margin: 0 auto 30px auto;
    max-width: 90%;
    word-wrap: break-word;
}
.search-container {
    width: 95%;
    margin: 0 auto 20px;
    text-align: center;
}
.search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    align-items: center;
}
.search-form input,
.search-form select,
.search-form button,
.search-form a button {
    padding: 10px 14px;
    border-radius: 6px;
    border: none;
    font-size: 14px;
    cursor: pointer;
}
.search-form button,
.search-form a button {
    display: flex;
    align-items: center;
    gap: 5px;
    background-color: #FF9800;
    color: #fff;
    transition: background 0.3s;
}
.search-form button:hover,
.search-form a button:hover {
    background-color: #e67e22;
}
.search-form a {
    text-decoration: none;
}
table {
    width: 95%;
    margin: 0 auto 30px;
    border-collapse: separate;
    border-spacing: 0;
    background: #23272f;
    color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 18px rgba(0,0,0,0.15);
    opacity: 0.95;
}
th, td {
    padding: 14px 12px;
    text-align: center;
    font-size: 15px;
    border: none;
}
th {
    background: #303642;
    font-weight: 700;
    font-size: 16px;
    color: #FF9800;
}
tr:hover td {
    background: #2e3440;
    transition: background 0.2s;
}
td.actions {
    display: flex;
    justify-content: center;
    gap: 6px;
}
.delete-btn {
    display: inline-block;
    padding: 8px 12px;
    margin: 2px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    background-color: rgba(255, 255, 255, 0.25);
    color: #fff;
    text-decoration: none;
}
.delete-btn:hover {
    background-color: rgba(255, 0, 0, 0.2);
    transform: translateY(-1px);
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
@media (max-width: 900px) {
    table, thead, tbody, th, td, tr { display: block; }
    thead { display: none; }
    tr {
        background: #222;
        margin-bottom: 10px;
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
    }
    td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }
    td:before {
        position: absolute;
        left: 16px;
        top: 16px;
        white-space: nowrap;
        font-weight: bold;
        color: #FF9800;
        content: attr(data-label);
        font-size: 14px;
        text-align: left;
    }
}
</style>
</head>
<body>
<div class="overlay">
<header>
<h1>Maintenance Schedule</h1>
<p class="recent-items">Recent Scheduled Tasks: <?= htmlspecialchars($recent_text) ?></p>
</header>
<?php if(isset($_SESSION['success'])): ?>
<div class="message success"><?= htmlspecialchars($_SESSION['success']) ?></div>
<?php unset($_SESSION['success']); endif; ?>
<?php if(isset($_SESSION['error'])): ?>
<div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
<?php unset($_SESSION['error']); endif; ?>
<div class="search-container">
<form method="POST" class="search-form">
<a href="maintenance.php"><button type="button">← Back to Maintenance</button></a>
<input type="text" name="task" placeholder="Search by Task Description" value="<?= htmlspecialchars($search_task) ?>">
<button type="submit">Search</button>
</form>
</div>
<div class="calendar-nav" style="text-align: center; margin-bottom: 20px;">
<a href="?month=<?= $current_month == 1 ? 12 : $current_month - 1 ?>&year=<?= $current_month == 1 ? $current_year - 1 : $current_year ?>" style="color: #FF9800; text-decoration: none; margin-right: 20px;">&larr; Previous</a>
<h2 style="display: inline; color: #FF9800;"><?= $month_name ?> <?= $current_year ?></h2>
<a href="?month=<?= $current_month == 12 ? 1 : $current_month + 1 ?>&year=<?= $current_month == 12 ? $current_year + 1 : $current_year ?>" style="color: #FF9800; text-decoration: none; margin-left: 20px;">Next &rarr;</a>
</div>
<div class="calendar" style="width: 95%; margin: 0 auto; background: #23272f; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 18px rgba(0,0,0,0.15); opacity: 0.95;">
<div class="calendar-header" style="display: grid; grid-template-columns: repeat(7, 1fr); background: #303642; color: #FF9800; font-weight: 700; font-size: 16px;">
<div style="padding: 14px; text-align: center;">Sun</div>
<div style="padding: 14px; text-align: center;">Mon</div>
<div style="padding: 14px; text-align: center;">Tue</div>
<div style="padding: 14px; text-align: center;">Wed</div>
<div style="padding: 14px; text-align: center;">Thu</div>
<div style="padding: 14px; text-align: center;">Fri</div>
<div style="padding: 14px; text-align: center;">Sat</div>
</div>
<div class="calendar-body" style="display: grid; grid-template-columns: repeat(7, 1fr);">
<?php
for ($i = 0; $i < $first_day_of_month; $i++) {
    echo '<div style="padding: 20px; min-height: 100px; border: 1px solid #444;"></div>';
}
for ($day = 1; $day <= $days_in_month; $day++) {
    $date_str = sprintf('%04d-%02d-%02d', $current_year, $current_month, $day);
    $has_schedules = isset($schedule_by_date[$date_str]);
    $bg_color = $has_schedules ? '#2e3440' : '#23272f';
    echo '<div style="padding: 10px; min-height: 100px; border: 1px solid #444; background-color: ' . $bg_color . '; position: relative;">';
    echo '<div style="font-weight: bold; margin-bottom: 5px; color: #FF9800;">' . $day . '</div>';
    if ($has_schedules) {
        foreach ($schedule_by_date[$date_str] as $sch) {
            echo '<div style="font-size: 12px; margin-bottom: 2px; color: #ccc; cursor: pointer;" onclick="showScheduleDetails(' . $sch['schedule_id'] . ')">' . htmlspecialchars(substr($sch['issue_description'], 0, 20)) . '...</div>';
        }
    }
    echo '</div>';
}
$total_cells = $first_day_of_month + $days_in_month;
$remaining_cells = 42 - $total_cells;
for ($i = 0; $i < $remaining_cells; $i++) {
    echo '<div style="padding: 20px; min-height: 100px; border: 1px solid #444;"></div>';
}
?>
</div>
</div>
<h2 style="text-align:center; color:#FF9800;">All Maintenance Schedules</h2>
<table>
<thead>
<tr>
<th>ID</th>
<th>Task Description</th>
<th>Scheduled Date</th>
<th>Frequency</th>
<th>Assigned To</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php foreach($schedules as $sch): ?>
<tr>
<td data-label="ID"><?= htmlspecialchars($sch['schedule_id']) ?></td>
<td data-label="Task"><?= htmlspecialchars($sch['issue_description']) ?></td>
<td data-label="Date"><?= htmlspecialchars($sch['scheduled_date']) ?></td>
<td data-label="Frequency"><?= htmlspecialchars($sch['frequency']) ?></td>
<td data-label="Assigned To"><?= htmlspecialchars($sch['assigned_to']) ?></td>
<td data-label="Status"><?= htmlspecialchars($sch['status']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<div id="scheduleModal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
<div style="background-color: #23272f; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 12px; color: #fff;">
<span style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeScheduleModal()">&times;</span>
<h2 id="modalTitle">Schedule Details</h2>
<div id="modalContent"></div>
<div style="text-align: center; margin-top: 20px;">
<a id="deleteLink" href="#" class="delete-btn" onclick="return confirm('Delete this maintenance schedule?');">Delete</a>
</div>
</div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.message').forEach(msg => {
        setTimeout(() => { msg.style.opacity='0'; setTimeout(()=>msg.remove(),300); }, 5000);
    });
});
function showScheduleDetails(scheduleId) {
    fetch(`get_schedule_details.php?id=${scheduleId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Schedule Details';
            document.getElementById('modalContent').innerHTML = `
                <p><strong>Task:</strong> ${data.issue_description}</p>
                <p><strong>Date:</strong> ${data.scheduled_date}</p>
                <p><strong>Frequency:</strong> ${data.frequency}</p>
                <p><strong>Assigned To:</strong> ${data.assigned_to}</p>
                <p><strong>Status:</strong> ${data.status}</p>
            `;
            document.getElementById('deleteLink').href = `maintenance_requests.php?delete_id=${scheduleId}&token=<?= $_SESSION['csrf_token'] ?>`;
            document.getElementById('scheduleModal').style.display = 'block';
        });
}
function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}
window.onclick = function(event) {
    const modal = document.getElementById('scheduleModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
</body>
</html>
