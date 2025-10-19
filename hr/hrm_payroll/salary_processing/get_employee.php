<?php
include_once(__DIR__ . '/../db_connector.php');

header('Content-Type: application/json');

if (!isset($conn) || $conn === null) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$staff_id = $_GET['staff_id'] ?? '';
if (empty($staff_id)) {
    echo json_encode(["status"=>"error","message"=>"Staff ID required"]);
    exit;
}

$sql = "SELECT staff_id, first_name, last_name, base_salary FROM staff WHERE staff_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $row['allowances'] = 0;
    $row['overtime'] = 0;
    echo json_encode(["status"=>"success","data"=>$row]);
} else {
    echo json_encode(["status"=>"error","message"=>"Employee not found"]);
}

$stmt->close();
$conn->close();
?>
