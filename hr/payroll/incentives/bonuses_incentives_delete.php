<?php
include '../../db.php';

$id = $_GET['id'] ?? '';
if($id){
    $stmt = $conn->prepare("DELETE FROM bonuses_incentives WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: bonuses_incentives.php");
exit;
?>
