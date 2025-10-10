<?php
include '../../db.php';

$staffFilter = $_GET['staff_id'] ?? '';

$bonusesQuery = "
    SELECT b.*, CONCAT(s.first_name, ' ', s.last_name) AS fullname
    FROM bonuses_incentives b
    JOIN staff s ON b.staff_id = s.staff_id
";
if($staffFilter) {
    $bonusesQuery .= " WHERE b.staff_id = '". $conn->real_escape_string($staffFilter) ."'";
}
$bonusesQuery .= " ORDER BY b.created_at DESC";

$result = $conn->query($bonusesQuery);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=bonuses_incentives.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Employee', 'Type', 'Amount', 'Date']);

if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        fputcsv($output, [
            $row['fullname'],
            $row['type'],
            number_format($row['amount'], 2),
            date('F j, Y', strtotime($row['created_at']))
        ]);
    }
}
fclose($output);
exit;
