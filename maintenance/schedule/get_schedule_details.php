<?php
require 'db.php';

if (isset($_GET['id'])) {
    $schedule_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM maintenance_schedule WHERE schedule_id = :id");
    $stmt->execute([':id' => $schedule_id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($schedule) {
        echo json_encode($schedule);
    } else {
        echo json_encode(['error' => 'Schedule not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>