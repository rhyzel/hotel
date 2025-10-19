<?php
include __DIR__ . '/../db.php';

if (!isset($conn) || $conn->connect_error) {
    include __DIR__ . '/../db.php';
}

if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    if (!isset($conn) || $conn->connect_error) {
        include __DIR__ . '/../db.php';
    }

    $delete_sql = "DELETE FROM maintenance_requests WHERE request_id = ?";
    $stmt_delete = $conn->prepare($delete_sql);
    $stmt_delete->bind_param("i", $delete_id);
    
    if ($stmt_delete->execute()) {
        header("Location: maintenance_requests.php?delete_success=1");
        exit();
    } else {
        $error_message = "Error deleting request: " . $stmt_delete->error;
    }
    $stmt_delete->close();
}

// 1. Fetch Busy Maintenance Staff IDs
$busy_staff_sql = "SELECT DISTINCT assigned_staff_id FROM maintenance_requests WHERE status IN ('pending', 'in progress') AND assigned_staff_id IS NOT NULL";
$busy_staff_result = $conn->query($busy_staff_sql);
$busy_staff_ids = [];
if ($busy_staff_result) {
    while ($row = $busy_staff_result->fetch_assoc()) {
        $busy_staff_ids[] = $row['assigned_staff_id'];
    }
}

// 2. Fetch Available Maintenance Staff
$staff_sql = "SELECT staff_id, first_name, last_name 
             FROM staff 
             WHERE department_name = 'Engineering / Maintenance' 
             AND employment_status = 'Active'";

if (!empty($busy_staff_ids)) {
    $placeholders = implode(',', array_fill(0, count($busy_staff_ids), '?'));
    $staff_sql .= " AND staff_id NOT IN (" . $placeholders . ")";
}
$staff_sql .= " ORDER BY last_name";

$stmt_staff = $conn->prepare($staff_sql);
if (!empty($busy_staff_ids)) {
    $param_types = str_repeat('s', count($busy_staff_ids));
    $stmt_staff->bind_param($param_types, ...$busy_staff_ids);
}
$stmt_staff->execute();
$staff_result = $stmt_staff->get_result();

$maintenance_staff = [];
if ($staff_result) {
    while ($row = $staff_result->fetch_assoc()) {
        $maintenance_staff[] = $row;
    }
} else {
    $error_message = "Error fetching available maintenance staff: " . $conn->error;
}
$stmt_staff->close();


// Fetch Housekeeping Staff (No change needed here)
$housekeeping_sql = "SELECT staff_id, first_name, last_name 
                     FROM staff 
                     WHERE department_name = 'Housekeeping' 
                     AND employment_status = 'Active' 
                     ORDER BY last_name";
$housekeeping_result = $conn->query($housekeeping_sql);
$housekeeping_staff = [];
if ($housekeeping_result) {
    while ($row = $housekeeping_result->fetch_assoc()) {
        $housekeeping_staff[] = $row;
    }
} else {
    $error_message = "Error fetching housekeeping staff: " . $conn->error;
}


// 3. Fetch ONLY 'under maintenance' room numbers
$rooms_sql = "SELECT room_number FROM rooms WHERE status = 'under maintenance' ORDER BY room_number";
$rooms_result = $conn->query($rooms_sql);
$room_numbers = [];
if ($rooms_result) {
    while ($row = $rooms_result->fetch_assoc()) {
        $room_numbers[] = $row['room_number'];
    }
} else {
    $error_message = "Error fetching under maintenance room numbers: " . $conn->error;
}


$common_issues = [
    'Plumbing Leak/Clog',
    'HVAC/AC Not Cooling',
    'TV/Cable Problem',
    'Lighting Fixture Out',
    'Electrical Outlet Not Working',
    'Furniture Damage',
    'Broken Door/Lock',
    'Pest Control Required',
    'Water Heater Issue',
    'Other/Specify Below'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    
    if (!isset($conn) || $conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $room_number = $_POST['room_number']; 
    $priority = $_POST['priority'];
    $main_issue = $_POST['main_issue'];
    $details = trim($_POST['details']);
    $reported_by_id = $_POST['reported_by_id']; 
    $assigned_staff_id = $_POST['assigned_staff_id'] ?: null; 

    $description = $main_issue;
    if (!empty($details)) {
        $description .= " (Details: " . $details . ")";
    }
    
    if (empty($room_number) || empty($priority) || empty($main_issue) || empty($reported_by_id)) {
        $error_message = "Please select a room, staff member, a main issue, and a priority.";
    } 
    else {
        $reported_by_name = '';
        foreach ($housekeeping_staff as $staff) {
            if ($staff['staff_id'] == $reported_by_id) {
                $reported_by_name = $staff['first_name'] . ' ' . $staff['last_name'];
                break;
            }
        }

        $insert_sql = "INSERT INTO maintenance_requests 
                       (room_number, issue_description, priority, requested_by, requester_staff_id, assigned_staff_id, status) 
                       VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt_insert = $conn->prepare($insert_sql);
        
        $stmt_insert->bind_param("ssssss", $room_number, $description, $priority, $reported_by_name, $reported_by_id, $assigned_staff_id);
        
        if ($stmt_insert->execute()) {
            header("Location: maintenance_requests.php?success=1");
            exit();
        } else {
            $error_message = "Error submitting request: " . $stmt_insert->error;
        }
        
        $stmt_insert->close();
    }
}

$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Maintenance request successfully submitted! It is now pending.";
}
if (isset($_GET['delete_success']) && $_GET['delete_success'] == 1) {
    $success_message = "Maintenance request successfully deleted.";
}

$statusColors = [
    'pending' => '#f39c12',
    'in progress' => '#3498db',
    'completed' => '#27ae60',
    'closed' => '#95a5a6'
];

$status_filter = $_GET['status'] ?? 'all';
$search_query = $_GET['search'] ?? '';

$sql = "SELECT 
            mr.request_id AS id, 
            mr.room_number, 
            mr.issue_description AS description, 
            mr.status, 
            mr.requested_by, 
            mr.requested_at,
            mr.priority,
            r.room_type 
        FROM maintenance_requests mr
        JOIN rooms r ON mr.room_number = r.room_number";

$whereClauses = [];
$paramTypes = '';
$params = [];

if ($status_filter !== 'all') {
    $whereClauses[] = "mr.status = ?";
    $paramTypes .= 's';
    $params[] = $status_filter;
}

if (!empty($search_query)) {
    $whereClauses[] = "(mr.room_number LIKE ? OR mr.issue_description LIKE ?)";
    $paramTypes .= 'ss';
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
}

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}

$sql .= " ORDER BY mr.requested_at DESC";

if (!isset($conn) || $conn->connect_error) {
    include __DIR__ . '/../db.php'; 
}

$stmt = $conn->prepare($sql);

if (!empty($paramTypes)) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$requests = $stmt->get_result();

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Requests | Housekeeping</title>
    <link rel="stylesheet" href="../housekeeping.css">
    <link rel="stylesheet" href="maintenance_requests.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="title-group">
            <h1><i class="fas fa-tools"></i> Maintenance Requests</h1>
            <p>File a new request or view the status of existing issues.</p>
        </div>
        
        <div class="header-controls">
            <a href="../housekeeping.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>

            <form method="GET" class="filter-form">
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?= ($status_filter == 'all') ? 'selected' : '' ?>>All Statuses</option>
                    <?php foreach (array_keys($statusColors) as $status): ?>
                        <option value="<?= $status ?>" <?= ($status_filter == $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="search" placeholder="Room or Description" value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <div class="container">
        <?php if (isset($error_message)): ?>
            <div id="status-message" style="padding: 15px; margin-bottom: 20px; background-color: #e74c3c; color: white; border-radius: 8px;">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php elseif ($success_message): ?>
            <div id="status-message" style="padding: 15px; margin-bottom: 20px; background-color: #27ae60; color: white; border-radius: 8px;">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <div id="new-request" class="new-request-section">
            <h2><i class="fas fa-file-invoice"></i> File a New Request</h2>
            <form method="POST" class="request-form">
                <div class="form-group">
                    <label for="room_number">Room Number (Under Maintenance):</label>
                    <select id="room_number" name="room_number" required>
                        <option value="" disabled selected>Select Room</option>
                        <?php foreach ($room_numbers as $room): ?>
                            <option value="<?= htmlspecialchars($room) ?>"><?= htmlspecialchars($room) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="priority">Priority:</label>
                    <select id="priority" name="priority" required>
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
                
                <div class="form-group full-width">
                    <label for="main_issue">Main Issue:</label>
                    <select id="main_issue" name="main_issue" required>
                        <option value="" disabled selected>Select a Problem Type</option>
                        <?php foreach ($common_issues as $issue): ?>
                            <option value="<?= htmlspecialchars($issue) ?>"><?= htmlspecialchars($issue) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="details">Additional Details (Optional):</label>
                    <textarea id="details" name="details" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="assigned_staff_id">Assign To (Available Maintenance Staff):</label>
                    <select id="assigned_staff_id" name="assigned_staff_id">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($maintenance_staff as $staff): ?>
                            <option value="<?= htmlspecialchars($staff['staff_id']) ?>">
                                <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reported_by_id">Reported By (Housekeeping Staff):</label>
                    <select id="reported_by_id" name="reported_by_id" required>
                        <option value="" disabled selected>Select Staff</option>
                        <?php foreach ($housekeeping_staff as $staff): ?>
                            <option value="<?= htmlspecialchars($staff['staff_id']) ?>">
                                <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group submit-group full-width">
                    <button type="submit" name="submit_request" class="submit-btn"><i class="fas fa-paper-plane"></i> Submit Request</button>
                </div>
            </form>
        </div>
        
        <h2 class="dashboard-heading"><i class="fas fa-list-check"></i> Existing Requests Dashboard</h2>

        <?php if ($requests->num_rows > 0): ?>
        <div class="requests-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Reported By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $requests->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['id']) ?></td>
                        <td><?= htmlspecialchars($request['room_number']) ?></td>
                        <td><?= htmlspecialchars($request['room_type']) ?></td>
                        <td class="description-cell"><?= htmlspecialchars(substr($request['description'], 0, 50)) ?><?= (strlen($request['description']) > 50 ? '...' : '') ?></td>
                        <td class="priority-cell priority-<?= strtolower($request['priority']) ?>"><?= htmlspecialchars(ucfirst($request['priority'])) ?></td>
                        <td><?= htmlspecialchars($request['requested_by']) ?></td>
                        <td><?= date('Y-m-d', strtotime($request['requested_at'])) ?></td>
                        <td class="status-cell">
                            <span class="status-badge" style="background-color: <?= $statusColors[strtolower($request['status'])] ?? '#ccc' ?>;">
                                <?= htmlspecialchars(ucfirst($request['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_request.php?id=<?= $request['id'] ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                            <a href="maintenance_requests.php?delete_id=<?= $request['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete Request #<?= $request['id'] ?>? This action cannot be undone.');" 
                               class="action-btn delete-btn">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="no-requests">No maintenance requests found matching your criteria.</p>
        <?php endif; ?>
    </div>

    <?php if ($success_message || isset($error_message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageElement = document.getElementById('status-message');
            
            if (messageElement && messageElement.style.backgroundColor === 'rgb(39, 174, 96)') { 
                setTimeout(function() {
                    messageElement.style.opacity = '0';
                    setTimeout(function() {
                        messageElement.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>