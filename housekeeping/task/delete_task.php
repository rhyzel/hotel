<?php
include __DIR__ . '/../db.php'; 

header('Content-Type: application/json');

if (!isset($_POST['task_id'])) {
    echo json_encode(['success' => false, 'message' => 'Task ID is missing.']);
    exit;
}

$taskId = $_POST['task_id'];

$deleteQuery = $conn->prepare("DELETE FROM housekeeping_tasks WHERE task_id = ?");

if ($deleteQuery === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    exit;
}

$deleteQuery->bind_param("i", $taskId);

if ($deleteQuery->execute()) {
    if ($deleteQuery->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Task deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Task not found or already deleted.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $deleteQuery->error]);
}

$deleteQuery->close();
$conn->close();
?>