<?php
include __DIR__ . '/../db.php';
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

$task_id = $_POST['task_id'] ?? null;

if (empty($task_id)) {
    echo json_encode(['success' => false, 'message' => 'Task ID is missing.']);
    $conn->close();
    exit;
}

$completed_at = date('Y-m-d H:i:s');

try {
    $conn->begin_transaction();

    $roomQuery = $conn->prepare("SELECT room_id FROM housekeeping_tasks WHERE task_id = ?");
    $roomQuery->bind_param("i", $task_id);
    $roomQuery->execute();
    $roomResult = $roomQuery->get_result();
    $task_data = $roomResult->fetch_assoc();
    $roomQuery->close();

    if (!$task_data) {
        throw new Exception("Task not found.");
    }
    $room_number = $task_data['room_id'];

    $updateTaskQuery = $conn->prepare("
        UPDATE housekeeping_tasks 
        SET task_status = 'completed', end_time = ? 
        WHERE task_id = ?
    ");
    $updateTaskQuery->bind_param("si", $completed_at, $task_id);
    
    if (!$updateTaskQuery->execute()) {
        throw new Exception("Database task update failed: " . $conn->error);
    }
    $updateTaskQuery->close();
    
    $updateRoomQuery = $conn->prepare("
        UPDATE rooms 
        SET status = 'available' 
        WHERE room_number = ?
    ");
    $updateRoomQuery->bind_param("s", $room_number);
    
    if (!$updateRoomQuery->execute()) {
        throw new Exception("Database room status update failed: " . $conn->error);
    }
    $updateRoomQuery->close();

    $conn->commit();

    $formatted_time = date('M d, Y h:i A', strtotime($completed_at));
    echo json_encode(['success' => true, 'message' => 'Task completed successfully.', 'new_end_time' => $formatted_time]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>