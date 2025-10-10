<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'All';

$sql = "SELECT s.staff_id, s.first_name, s.last_name, s.department_name, s.position_name, s.base_salary
        FROM staff s";

if ($filter !== 'All') {
    $status_safe = $conn->real_escape_string($filter);
    $sql .= " WHERE s.employment_status = '$status_safe'";
}

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $sql .= ($filter === 'All' ? " WHERE " : " AND ");
    $sql .= "(s.first_name LIKE '%$search_safe%' OR s.last_name LIKE '%$search_safe%' OR s.staff_id LIKE '%$search_safe%')";
}

$sql .= " ORDER BY s.department_name ASC, s.position_name ASC, s.last_name ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Employees</title>
<link rel="stylesheet" href="all_employees.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="container">
        <header class="page-header">
            <h2>HOTEL LA VISTA</h2>
            <div class="header-controls">
                <a href="payroll.php" class="nav-btn">&#8592; Back To Dashboard</a>
                <form method="get" class="filter-form">
                    <select name="filter" onchange="this.form.submit()">
                        <option value="All" <?= $filter=='All' ? 'selected' : '' ?>>All</option>
                        <option value="Active" <?= $filter=='Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive" <?= $filter=='Inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="Probation" <?= $filter=='Probation' ? 'selected' : '' ?>>Probation</option>
                        <option value="Resigned" <?= $filter=='Resigned' ? 'selected' : '' ?>>Resigned</option>
                        <option value="Terminated" <?= $filter=='Terminated' ? 'selected' : '' ?>>Terminated</option>
                        <option value="Floating" <?= $filter=='Floating' ? 'selected' : '' ?>>Floating</option>
                        <option value="Lay Off" <?= $filter=='Lay Off' ? 'selected' : '' ?>>Lay Off</option>
                    </select>
                    <input type="text" name="search" placeholder="Search employee..." value="<?= htmlspecialchars($search); ?>">
                    <button type="submit">Search</button>
                </form>
            
            </div>
        </header>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Base Salary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($e = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['staff_id']) ?></td>
                        <td><?= htmlspecialchars($e['first_name']) ?></td>
                        <td><?= htmlspecialchars($e['last_name']) ?></td>
                        <td><?= htmlspecialchars($e['department_name']) ?></td>
                        <td><?= htmlspecialchars($e['position_name']) ?></td>
                        <td><?= number_format($e['base_salary'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
</div>
</body>
</html>
