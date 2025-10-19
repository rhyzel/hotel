<?php
include __DIR__ . '/../db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: maintenance_requests.php");
    exit();
}

$request_id = $_GET['id'];
$success_message = '';
$error_message = '';

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed.");
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

function get_main_issue_and_details($description, $common_issues) {
    $main_issue = 'Other/Specify Below';
    $details = $description;

    foreach ($common_issues as $issue) {
        if (strpos($description, $issue) === 0) {
            $main_issue = $issue;
            
            $details = trim(substr($description, strlen($issue)));
            if (strpos($details, '(Details:') === 0) {
                $details = trim(substr($details, 9, -1));
            } else {
                $details = '';
            }
            break;
        }
    }
    
    if ($main_issue === 'Other/Specify Below' && !in_array($description, $common_issues)) {
        $details = $description;
    } elseif ($main_issue === 'Other/Specify Below') {
        $details = '';
    }

    return ['main_issue' => $main_issue, 'details' => $details];
}

$maintenance_staff_sql = "SELECT staff_id, first_name, last_name FROM staff WHERE department_name = 'Engineering / Maintenance' AND employment_status = 'Active' ORDER BY last_name";
$maintenance_staff_result = $conn->query($maintenance_staff_sql);
$maintenance_staff = $maintenance_staff_result->fetch_all(MYSQLI_ASSOC);

$housekeeping_staff_sql = "SELECT staff_id, first_name, last_name FROM staff WHERE department_name = 'Housekeeping' AND employment_status = 'Active' ORDER BY last_name";
$housekeeping_staff_result = $conn->query($housekeeping_staff_sql);
$housekeeping_staff = $housekeeping_staff_result->fetch_all(MYSQLI_ASSOC);

$rooms_sql = "SELECT room_number FROM rooms ORDER BY room_number";
$rooms_result = $conn->query($rooms_sql);
$room_numbers = $rooms_result->fetch_all(MYSQLI_ASSOC);

$status_options = ['pending', 'in progress', 'completed', 'closed'];
$priority_options = ['Low', 'Medium', 'High', 'Critical'];

$fetch_sql = "SELECT 
    mr.room_number, 
    mr.issue_description, 
    mr.priority, 
    mr.status, 
    mr.requester_staff_id, 
    mr.assigned_staff_id 
    FROM maintenance_requests mr WHERE mr.request_id = ?";
    
$stmt_fetch = $conn->prepare($fetch_sql);
$stmt_fetch->bind_param("i", $request_id);
$stmt_fetch->execute();
$request_data = $stmt_fetch->get_result()->fetch_assoc();
$stmt_fetch->close();

if (!$request_data) {
    $error_message = "Maintenance request not found.";
}

$parsed_issue = get_main_issue_and_details($request_data['issue_description'], $common_issues);
$current_main_issue = $parsed_issue['main_issue'];
$current_details = $parsed_issue['details'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request'])) {
    
    $room_number = $_POST['room_number'];
    $priority = $_POST['priority'];
    $main_issue = $_POST['main_issue'];
    $details = trim($_POST['details']);
    $reported_by_id = $_POST['reported_by_id'];
    $assigned_staff_id = $_POST['assigned_staff_id'] ?: null;
    $status = $_POST['status'];

    $new_description = $main_issue;
    if (!empty($details)) {
        $new_description .= " (Details: " . $details . ")";
    }

    if (empty($room_number) || empty($priority) || empty($main_issue) || empty($reported_by_id) || empty($status)) {
        $error_message = "All fields except 'Additional Details' and 'Assign To' are required.";
    } else {
        $reported_by_name = '';
        foreach ($housekeeping_staff as $staff) {
            if ($staff['staff_id'] == $reported_by_id) {
                $reported_by_name = $staff['first_name'] . ' ' . $staff['last_name'];
                break;
            }
        }
        
        $update_sql = "UPDATE maintenance_requests SET 
                       room_number = ?, 
                       issue_description = ?, 
                       priority = ?, 
                       status = ?, 
                       requester_staff_id = ?, 
                       requested_by = ?,
                       assigned_staff_id = ?
                       WHERE request_id = ?";
        
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("sssssssi", 
                                 $room_number, 
                                 $new_description, 
                                 $priority, 
                                 $status, 
                                 $reported_by_id,
                                 $reported_by_name,
                                 $assigned_staff_id,
                                 $request_id);
        
        if ($stmt_update->execute()) {
            $success_message = "Request ID " . $request_id . " updated successfully!";
            $request_data = $conn->query("SELECT room_number, issue_description, priority, status, requester_staff_id, assigned_staff_id FROM maintenance_requests WHERE request_id = $request_id")->fetch_assoc();
            
            $parsed_issue = get_main_issue_and_details($request_data['issue_description'], $common_issues);
            $current_main_issue = $parsed_issue['main_issue'];
            $current_details = $parsed_issue['details'];

        } else {
            $error_message = "Error updating request: " . $stmt_update->error;
        }
        $stmt_update->close();
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Request <?= $request_id ?> | Housekeeping</title>
    <link rel="stylesheet" href="edit_request.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .container {
            margin-left: auto;
            margin-right: auto;
            max-width: 1100px; 
            padding-left: 20px;
            padding-right: 20px;
            margin-top: 20px;
        }
        
        .edit-request-section {
            background-color: rgb(40 28 20 / 0%); 
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px); 
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <header>
        <div class="title-group">
            <h1><i class="fas fa-edit"></i> Edit Request #<?= $request_id ?></h1>
            <p>Update details and change status of a maintenance issue.</p>
        </div>
        
        <div class="header-controls">
            <a href="maintenance_requests.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </header>

    <div class="container">
        <?php if ($error_message): ?>
            <div id="status-message" style="padding: 15px; margin-bottom: 20px; background-color: #e74c3c; color: white; border-radius: 8px;">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php elseif ($success_message): ?>
            <div id="status-message" style="padding: 15px; margin-bottom: 20px; background-color: #27ae60; color: white; border-radius: 8px;">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($request_data): ?>
        <div class="edit-request-section">
            <form method="POST" class="request-form">
                
                <div class="form-group">
                    <label for="room_number">Room Number:</label>
                    <select id="room_number" name="room_number" required>
                        <option value="" disabled>Select Room</option>
                        <?php foreach ($room_numbers as $room): ?>
                            <option value="<?= htmlspecialchars($room['room_number']) ?>" <?= ($room['room_number'] == $request_data['room_number']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($room['room_number']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Priority:</label>
                    <select id="priority" name="priority" required>
                        <?php foreach ($priority_options as $priority): ?>
                            <option value="<?= $priority ?>" <?= ($priority == $request_data['priority']) ? 'selected' : '' ?>>
                                <?= ucfirst($priority) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group full-width">
                    <label for="main_issue">Main Issue:</label>
                    <select id="main_issue" name="main_issue" required>
                        <option value="" disabled>Select a Problem Type</option>
                        <?php foreach ($common_issues as $issue): ?>
                            <option value="<?= htmlspecialchars($issue) ?>" <?= ($issue == $current_main_issue) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($issue) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="details">Additional Details (Optional):</label>
                    <textarea id="details" name="details" rows="2"><?= htmlspecialchars($current_details) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="reported_by_id">Reported By (Housekeeping Staff):</label>
                    <select id="reported_by_id" name="reported_by_id" required>
                        <option value="" disabled>Select Staff</option>
                        <?php foreach ($housekeeping_staff as $staff): ?>
                            <option value="<?= htmlspecialchars($staff['staff_id']) ?>" <?= ($staff['staff_id'] == $request_data['requester_staff_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="assigned_staff_id">Assign To (Maintenance Staff):</label>
                    <select id="assigned_staff_id" name="assigned_staff_id">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($maintenance_staff as $staff): ?>
                            <option value="<?= htmlspecialchars($staff['staff_id']) ?>" <?= ($staff['staff_id'] == $request_data['assigned_staff_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <?php foreach ($status_options as $status): ?>
                            <option value="<?= $status ?>" <?= ($status == $request_data['status']) ? 'selected' : '' ?>>
                                <?= ucfirst($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group submit-group full-width">
                    <button type="submit" name="update_request" class="submit-btn"><i class="fas fa-sync-alt"></i> Update Request</button>
                </div>
            </form>
        </div>
        <?php else: ?>
            <p class="no-requests">The requested maintenance item could not be loaded.</p>
        <?php endif; ?>
    </div>

    <?php if ($success_message || $error_message): ?>
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