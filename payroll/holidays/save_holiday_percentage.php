<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['percentage'])) {
    foreach ($_POST['percentage'] as $id => $percent) {
        $id = intval($id);
        $percent = floatval($percent);
        $stmt = $conn->prepare("UPDATE holidays SET percentage=? WHERE id=?");
        $stmt->bind_param("di", $percent, $id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: holidays.php");
exit;
