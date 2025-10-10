<?php
include '../db.php';

$staff_id = $_GET['staff_id'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$stmt = $conn->prepare("SELECT reason_type, description, amount, proof_image FROM deductions WHERE staff_id=? AND month=? AND year=?");
$stmt->bind_param("sii", $staff_id, $month, $year);
$stmt->execute();
$res = $stmt->get_result();

$deductions = [];
while ($row = $res->fetch_assoc()) {
    if (!empty($row['proof_image'])) {
        $row['proof_image'] = str_replace('\\', '/', $row['proof_image']); 
        $row['proof_image'] = str_replace('C:/xampp/htdocs/hotel/hr', 'http://localhost/hotel/hr', $row['proof_image']); 
    }
    $deductions[] = $row;
}

header('Content-Type: application/json');
echo json_encode($deductions);
?>
