<?php
include '../db.php';

$search = $_GET['search'] ?? '';
$search_sql = $search ? "AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR staff_id LIKE '%$search%')" : '';
$result = $conn->query("SELECT * FROM staff WHERE position_name NOT IN ('CEO','COO') $search_sql ORDER BY staff_id DESC");

if(isset($_POST['save_performance'])){
    $staff_id = $_POST['staff_id'];
    $score = $_POST['score'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("INSERT INTO staff_performance (staff_id, score, remarks, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sds", $staff_id, $score, $remarks);
    $stmt->execute();
    header("Location: staff_performance.php");
    exit;
}

function getLastMonthPerformance($conn, $staff_id) {
    $stmt = $conn->prepare("SELECT score, remarks, created_at FROM staff_performance WHERE staff_id=? AND MONTH(created_at)=MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(created_at)=YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ? $res : ['score'=>'-', 'remarks'=>'-'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Performance</title>
<link rel="stylesheet" href="../css/staff_perfomance.css">

<link rel="stylesheet" href="fontawesome-free-7.0.1-web/css/all.min.css">
</head>
<body>
<div class="container">
    <h1 style="text-align:center;">Staff Performance</h1>

    <div class="top-bar">
        <a href="employee_management.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search employees..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <h2>All Staff</h2>
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Department</th>
                <th>Last Month Score</th>
                <th>Last Month Remarks</th>
                <th>New Score</th>
                <th>New Remarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=$result->fetch_assoc()): 
                $last_perf = getLastMonthPerformance($conn, $row['staff_id']);
            ?>
            <tr>
                <td><?= $row['staff_id'] ?></td>
                <td><?= $row['first_name'].' '.$row['last_name'] ?></td>
                <td><?= $row['position_name'] ?></td>
                <td><?= $row['department_name'] ?></td>
                <td><?= $last_perf['score'] ?></td>
                <td><?= $last_perf['remarks'] ?></td>
                <td>
                    <form method="POST" class="performance-form">
                        <input type="hidden" name="staff_id" value="<?= $row['staff_id'] ?>">
                        <input type="number" step="0.01" name="score" placeholder="Score" required>
                </td>
                <td>
                        <input type="text" name="remarks" placeholder="Remarks">
                </td>
                <td>
                        <button type="submit" name="save_performance">Save</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
