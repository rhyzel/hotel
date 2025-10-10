<?php
include '../db.php';

$positions = [];
$departments = [];
$positionToDepartment = [];

$posResult = $conn->query("SELECT position_name, department_name FROM positions ORDER BY position_name ASC");
if ($posResult && $posResult->num_rows > 0) {
    while ($row = $posResult->fetch_assoc()) {
        $positions[] = $row['position_name'];
        if (!in_array($row['department_name'], $departments)) {
            $departments[] = $row['department_name'];
        }
        $positionToDepartment[$row['position_name']] = $row['department_name'];
    }
} else {
    $positions = ['Front Office Manager','Assistant Front Office Manager','Concierge','Room Attendant','Laundry Supervisor','Assistant Housekeeper','Cashier','Bartender','Baker','Sous Chef','F&B Manager','Chef de Partie','Demi Chef de Partie','Assistant F&B Manager','Waiter / Waitress','Restaurant Manager','Chief Engineer','Assistant Engineer','Inventory Manager','Inventory Inspector'];
    $departments = ['Front Office','Housekeeping','Food & Beverage','Engineering','Inventory'];
    foreach ($positions as $p) $positionToDepartment[$p] = $departments[0];
}

$employment_statuses = ['Active','Inactive','Probation','Resigned','Terminated','Floating','Lay Off'];
$employment_types = ['Full-time','Part-time','Contract','Internship'];

$managers = $conn->query("
    SELECT first_name, last_name, position_name FROM staff WHERE position_name LIKE '%Manager%'
    UNION
    SELECT first_name, last_name, position_name FROM ceo
");

if (isset($_POST['update_employee'])) {
    $staff_id = $_POST['staff_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $hire_date = $_POST['hire_date'];
    $employment_status = $_POST['employment_status'];
    $position_name = $_POST['position_name'];
    $department_name = $positionToDepartment[$position_name] ?? $_POST['department_name'];
    $manager = $_POST['manager'];
    $employment_type = $_POST['employment_type'];
    $base_salary = $_POST['base_salary'];
    $hourly_rate = $_POST['hourly_rate'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) die("Invalid email format");
    if ($employment_type === 'Full-time') $hourly_rate = $base_salary / (22 * 8);

    $stmt = $conn->prepare("UPDATE staff SET first_name=?, last_name=?, gender=?, email=?, phone=?, address=?, hire_date=?, employment_status=?, position_name=?, department_name=?, manager=?, employment_type=?, base_salary=?, hourly_rate=? WHERE staff_id=?");
    $stmt->bind_param("sssssssssssddis", $first_name, $last_name, $gender, $email, $phone, $address, $hire_date, $employment_status, $position_name, $department_name, $manager, $employment_type, $base_salary, $hourly_rate, $staff_id);
    $stmt->execute();
    header("Location: employee_management.php");
    exit;
}

if (isset($_GET['delete'])) {
    $staff_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id=?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    header("Location: employee_management.php");
    exit;
}

$search = $_GET['search'] ?? '';
if ($search) {
    $search_param = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM staff WHERE (first_name LIKE ? OR last_name LIKE ? OR staff_id LIKE ? OR position_name LIKE ? OR department_name LIKE ?) ORDER BY staff_id DESC");
    $stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM staff ORDER BY staff_id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Management</title>
<link rel="stylesheet" href="../css/employee_management.css">
<link rel="stylesheet" href="fontawesome-free-7.0.1-web/css/all.min.css">
</head>
<body>
<div class="container">
    <h1>Employee Management</h1>
    <div class="top-bar">
        <a href="http://localhost/hotel/hr/employee_management/hr_employee_management.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search employees..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <a href="staff_performance.php"><button type="button">Staff Performance</button></a>
        </form>
    </div>

<?php if(isset($_GET['edit'])): 
$res = $conn->query("SELECT * FROM staff WHERE staff_id='".$_GET['edit']."'")->fetch_assoc(); ?>
<h2>Edit Employee</h2>
<form method="POST" class="edit-form">
    <input type="hidden" name="staff_id" value="<?= $res['staff_id'] ?>">
    <input type="text" name="first_name" value="<?= $res['first_name'] ?>" placeholder="First Name" required>
    <input type="text" name="last_name" value="<?= $res['last_name'] ?>" placeholder="Last Name" required>
    <select name="gender" required>
        <option value="Male" <?= ($res['gender']=='Male')?'selected':'' ?>>Male</option>
        <option value="Female" <?= ($res['gender']=='Female')?'selected':'' ?>>Female</option>
    </select>
    <input type="email" name="email" value="<?= $res['email'] ?>" placeholder="Email" required>
    <input type="text" name="phone" value="<?= $res['phone'] ?>" placeholder="Phone" required pattern="^(\+63|09)\d{9}$">
    <input type="text" name="address" value="<?= $res['address'] ?>" placeholder="Address" required>
    <input type="date" name="hire_date" value="<?= $res['hire_date'] ?>" required>
    <select name="employment_status" required>
        <?php foreach($employment_statuses as $status): ?>
        <option value="<?= $status ?>" <?= ($res['employment_status']==$status)?'selected':'' ?>><?= $status ?></option>
        <?php endforeach; ?>
    </select>
    <select name="position_name" id="positionSelect" required>
        <?php foreach($positions as $p): ?>
        <option value="<?= $p ?>" <?= ($res['position_name']==$p)?'selected':'' ?>><?= $p ?></option>
        <?php endforeach; ?>
    </select>
    <select name="department_name" id="departmentSelect" required>
        <?php foreach($departments as $dname): ?>
        <option value="<?= $dname ?>" <?= ($res['department_name']==$dname)?'selected':'' ?>><?= $dname ?></option>
        <?php endforeach; ?>
    </select>
    <select name="manager" required>
        <?php mysqli_data_seek($managers,0); while($m=$managers->fetch_assoc()): $name=$m['first_name'].' '.$m['last_name']; ?>
        <option value="<?= $name ?>" <?= ($res['manager']==$name)?'selected':'' ?>><?= $name ?></option>
        <?php endwhile; ?>
    </select>
    <select name="employment_type" required>
        <?php foreach($employment_types as $type): ?>
        <option value="<?= $type ?>" <?= ($res['employment_type']==$type)?'selected':'' ?>><?= $type ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" step="0.01" name="base_salary" value="<?= $res['base_salary'] ?>" placeholder="Base Salary">
    <input type="number" step="0.01" name="hourly_rate" value="<?= $res['hourly_rate'] ?>" placeholder="Hourly Rate">
    <button type="submit" name="update_employee">Update Employee</button>
</form>
<?php endif; ?>

<h2>Employee List</h2>
<table>
    <thead>
    <tr>
        <th>Employee ID</th>
        <th>Name</th>
        <th>Position</th>
        <th>Department</th>
        <th>Manager</th>
        <th>Base Salary</th>
        <th>Hourly Rate</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php while($row=$result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['staff_id'] ?></td>
        <td><a href="view_employee.php?staff_id=<?= $row['staff_id'] ?>"><?= $row['first_name'].' '.$row['last_name'] ?></a></td>
        <td><?= $row['position_name'] ?></td>
        <td><?= $row['department_name'] ?></td>
        <td><?= $row['manager'] ?></td>
        <td><?= $row['base_salary'] ?></td>
        <td><?= $row['hourly_rate'] ?></td>
        <td>
            <a href="employee_management.php?edit=<?= $row['staff_id'] ?>">Edit</a> |
            <a href="employee_management.php?delete=<?= $row['staff_id'] ?>" onclick="return confirm('Delete this employee?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<script>
const positionToDepartment = <?= json_encode($positionToDepartment) ?>;
const positionSelect = document.getElementById('positionSelect');
const departmentSelect = document.getElementById('departmentSelect');
positionSelect?.addEventListener('change', ()=>{
    const dept = positionToDepartment[positionSelect.value];
    if(dept) departmentSelect.value = dept;
});
</script>
</body>
</html>
