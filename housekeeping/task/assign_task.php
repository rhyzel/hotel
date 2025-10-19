<?php
include __DIR__ . '/../db.php';
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

$room_number = $_POST['room_number'] ?? null;
$assignee = $_POST['assignee'] ?? null;
$assigned_by = $_POST['assigned_by'] ?? null;

if (empty($room_number) || empty($assignee) || empty($assigned_by)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing data. Room Number, Assigned To, and Assigned By are required.']);
    $conn->close();
    exit;
}

try {
    $conn->begin_transaction();

    $checkQuery = $conn->prepare("SELECT task_id, task_status FROM housekeeping_tasks WHERE room_id = ? AND task_status != 'completed' LIMIT 1");
    $checkQuery->bind_param("s", $room_number);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    $existing_task = $result->fetch_assoc();
    $checkQuery->close();

    $task_status = 'assigned';
    $current_time = date('Y-m-d H:i:s');
    $success = false;
    $message = '';

    if ($existing_task) {
        $task_id = $existing_task['task_id'];
        $updateQuery = $conn->prepare("
            UPDATE housekeeping_tasks
            SET assigned_to = ?, assigned_by = ?, task_status = ?, start_time = ?, end_time = NULL
            WHERE task_id = ?
        ");
        $updateQuery->bind_param("ssssi", $assignee, $assigned_by, $task_status, $current_time, $task_id);
        
        if ($updateQuery->execute()) {
            $success = true;
            $message = "Task $task_id reassigned successfully to $assignee.";
        } else {
            throw new Exception("Task update failed: " . $conn->error);
        }
        $updateQuery->close();
    } else {
        $placeholder_staff_id = 1; 

        $insertQuery = $conn->prepare("
            INSERT INTO housekeeping_tasks (staff_id, room_id, assigned_to, assigned_by, task_status, assigned_at, start_time)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insertQuery->bind_param("issssss", $placeholder_staff_id, $room_number, $assignee, $assigned_by, $task_status, $current_time, $current_time);

        if ($insertQuery->execute()) {
            $success = true;
            $message = "New task assigned successfully for Room $room_number to $assignee.";
        } else {
            throw new Exception("Task insert failed: " . $conn->error);
        }
        $insertQuery->close();
    }
    
    if ($success) {
        $roomUpdateQuery = $conn->prepare("
            UPDATE rooms
            SET status = 'dirty'
            WHERE room_number = ? AND status IN ('available', 'dirty')
        ");
        $roomUpdateQuery->bind_param("s", $room_number);
        $roomUpdateQuery->execute();
        $roomUpdateQuery->close();
    }
    
    $conn->commit();
    echo json_encode(['success' => $success, 'message' => $message]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>