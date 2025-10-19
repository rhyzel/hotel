<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['percentage']) && !empty($_POST['name']) && !empty($_POST['date'])) {
        foreach ($_POST['percentage'] as $id => $percent) {
            $id = intval($id);
            $percent = floatval($percent);
            $name = trim($_POST['name'][$id]);
            $date = $_POST['date'][$id];

            $stmt = $conn->prepare("UPDATE holidays SET name=?, date=?, percentage=? WHERE id=?");
            $stmt->bind_param("ssdi", $name, $date, $percent, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (!empty($_POST['new_name']) && !empty($_POST['new_date']) && !empty($_POST['new_percentage'])) {
        $names = $_POST['new_name'];
        $dates = $_POST['new_date'];
        $percentages = $_POST['new_percentage'];

        $stmt = $conn->prepare("INSERT INTO holidays (name, date, percentage) VALUES (?, ?, ?)");
        for ($i = 0; $i < count($names); $i++) {
            $name = trim($names[$i]);
            $date = $dates[$i];
            $percent = floatval($percentages[$i]);

            if ($name && $date) {
                $stmt->bind_param("ssi", $name, $date, $percent);
                $stmt->execute();
            }
        }
        $stmt->close();
    }
}

header("Location: holidays.php");
exit;
?>
