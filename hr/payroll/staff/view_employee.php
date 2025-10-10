<?php
include '../db.php';

$staff_id = $_GET['staff_id'] ?? '';
if (!$staff_id) die("Employee ID required");

$res = $conn->query("SELECT * FROM `staff` WHERE `staff_id`='$staff_id'") or die($conn->error);
$res = $res->fetch_assoc();
if (!$res) die("Employee not found");

$attendance = $conn->query("SELECT * FROM `attendance` WHERE `staff_id`='$staff_id' ORDER BY attendance_date DESC");
$overtime = $conn->query("SELECT * FROM `overtime` WHERE `staff_id`='$staff_id' ORDER BY overtime_date DESC");

if (isset($_POST['update_profile'])) {
    $bank_name = $_POST['bank_name'];
    $account_name = $_POST['account_name'];
    $account_number = $_POST['account_number'];
    $stmt = $conn->prepare("UPDATE `staff` SET bank_name=?, account_name=?, account_number=? WHERE staff_id=?");
    $stmt->bind_param("ssss", $bank_name, $account_name, $account_number, $staff_id);
    $stmt->execute();
    $res = $conn->query("SELECT * FROM `staff` WHERE `staff_id`='$staff_id'")->fetch_assoc();
}

$hourly_rate = ($res['base_salary'] ?? 0) / (22*8);
$total_attendance_hours = 0;
$attendance_rows = [];
while($a=$attendance->fetch_assoc()){
    $time_in = strtotime($a['time_in']);
    $time_out = strtotime($a['time_out']);
    $hours_worked = 0;
    if($time_in && $time_out){
        $hours_worked = ($time_out - $time_in)/3600;
        $total_attendance_hours += $hours_worked;
    }
    $a['hours_worked'] = round($hours_worked,2);
    $attendance_rows[] = $a;
}

$total_overtime_hours = 0;
$overtime_rows = [];
while($o=$overtime->fetch_assoc()){
    $hours = floatval($o['hours'] ?? 0);
    $total_overtime_hours += $hours;
    $o['hours'] = $hours;
    $overtime_rows[] = $o;
}

$overtime_pay = $total_overtime_hours * $hourly_rate * 1.25;

$photoFile = $res['photo'] ?? '';
$photoWebPath = !empty($photoFile) ? "http://localhost/hotel/hr/uploads/".basename($photoFile) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Profile</title>
<link rel="stylesheet" href="view_employee.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="profile-header">
        <?php
        if($photoWebPath) {
            echo '<img class="profile-photo" src="'.$photoWebPath.'" alt="Profile Photo">';
        } else {
            $initials = strtoupper(substr($res['first_name'],0,1).substr($res['last_name'],0,1));
            echo '<div class="profile-photo">'.$initials.'</div>';
        }
        ?>
        <h1><?= htmlspecialchars($res['first_name'].' '.$res['last_name']) ?></h1>
        <p><?= htmlspecialchars($res['position_name'].' | '.$res['department_name']) ?></p>
        <p><?= htmlspecialchars($res['email'].' | '.$res['phone']) ?></p>
    </div>

    <div class="tab">
        <a href="http://localhost/hotel/hr/payroll/salary/salary_processing.php" class="tablinks back-btn">&larr; Back</a>
        <button class="tablinks" onclick="openTab(event,'Personal')">Personal Info</button>
        <button class="tablinks" onclick="openTab(event,'Bank')">Bank Info</button>
        <button class="tablinks" onclick="openTab(event,'Job')">Job Info</button>
        <button class="tablinks" onclick="openTab(event,'Attendance')">Attendance</button>
        <button class="tablinks" onclick="openTab(event,'Overtime')">Overtime</button>
    </div>

    <form method="POST" id="profileForm" action="view_employee.php?staff_id=<?= urlencode($staff_id) ?>">
        <div id="Personal" class="tabcontent">
            <div class="personal-field"><label>Employee ID</label><input type="text" value="<?= $res['staff_id'] ?>" readonly></div>
            <div class="personal-field"><label>First Name</label><input type="text" value="<?= $res['first_name'] ?>" readonly></div>
            <div class="personal-field"><label>Last Name</label><input type="text" value="<?= $res['last_name'] ?>" readonly></div>
            <div class="personal-field"><label>Gender</label><input type="text" value="<?= $res['gender'] ?>" readonly></div>
            <div class="personal-field"><label>Email</label><input type="text" value="<?= $res['email'] ?>" readonly></div>
            <div class="personal-field"><label>Phone</label><input type="text" value="<?= $res['phone'] ?>" readonly></div>
            <div class="personal-field full-width"><label>Address</label><input type="text" value="<?= $res['address'] ?>" readonly></div>
        </div>

        <div id="Bank" class="tabcontent">
            <label>Bank Name</label>
            <select name="bank_name" required>
                <option value="BDO" <?= ($res['bank_name']=='BDO' || !$res['bank_name'])?'selected':'' ?>>BDO</option>
                <option value="BPI" <?= ($res['bank_name']=='BPI')?'selected':'' ?>>BPI</option>
                <option value="Metrobank" <?= ($res['bank_name']=='Metrobank')?'selected':'' ?>>Metrobank</option>
                <option value="Other" <?= ($res['bank_name']=='Other')?'selected':'' ?>>Other</option>
            </select>
            <label>Account Name</label>
            <input type="text" name="account_name" value="<?= $res['account_name'] ?? $res['first_name'].' '.$res['last_name'] ?>" required>
            <label>Account Number</label>
            <input type="text" name="account_number" value="<?= $res['account_number'] ?? '' ?>" required>
        </div>

        <div id="Job" class="tabcontent">
            <div class="job-field"><label>Position</label><input type="text" value="<?= $res['position_name'] ?>" readonly></div>
            <div class="job-field"><label>Department</label><input type="text" value="<?= $res['department_name'] ?>" readonly></div>
            <div class="job-field"><label>Employment Type</label><input type="text" value="<?= $res['employment_type'] ?>" readonly></div>
            <div class="job-field"><label>Base Salary</label><input type="text" value="<?= number_format($res['base_salary'],2) ?>" readonly></div>
        </div>

    <div id="Attendance" class="tabcontent">
    <h3>Attendance Records</h3>
    <div class="table-wrapper">
                <table>
                    <tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Hours Worked</th><th>Status</th></tr>
                    <?php foreach($attendance_rows as $a): ?>
                    <tr>
                        <td><?= $a['attendance_date'] ?></td>
                        <td><?= $a['time_in'] ?? '-' ?></td>
                        <td><?= $a['time_out'] ?? '-' ?></td>
                        <td><?= $a['hours_worked'] ?></td>
                        <td><?= $a['status'] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3"><strong>Total Hours Worked</strong></td>
                        <td colspan="2"><?= round($total_attendance_hours,2) ?></td>
                    </tr>
                </table>
            </div>
        </div>

    <div id="Overtime" class="tabcontent">
    <h3>Overtime Records</h3>
    <div class="table-wrapper">
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Scheduled Start</th>
                        <th>Scheduled End</th>
                        <th>Hourly Rate</th>
                        <th>Overtime Hours</th>
                        <th>Overtime Pay</th>
                    </tr>
                    <?php foreach($overtime_rows as $o): ?>
                    <tr>
                        <td><?= $o['overtime_date'] ?></td>
                        <td><?= $res['schedule_start_time'] ?? '-' ?></td>
                        <td><?= $res['schedule_end_time'] ?? '-' ?></td>
                        <td>₱<?= number_format($hourly_rate, 2) ?></td>
                        <td><?= $o['hours'] ?></td>
                        <td>₱<?= number_format($o['hours'] * $hourly_rate * 1.25, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4"><strong>Total Overtime Hours</strong></td>
                        <td><?= round($total_overtime_hours, 2) ?></td>
                        <td>₱<?= number_format($overtime_pay, 2) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <button type="submit" name="update_profile" class="update-btn">Update Info</button>
    </form>
</div>

<script>
function openTab(evt, tabName) {
    let tabcontent = document.getElementsByClassName("tabcontent");
    for(let i=0;i<tabcontent.length;i++) tabcontent[i].style.display="none";
    let tablinks = document.getElementsByClassName("tablinks");
    for(let i=0;i<tablinks.length;i++) tablinks[i].classList.remove("active");
    document.getElementById(tabName).style.display="grid";
    evt.currentTarget.classList.add("active");
}
window.onload=function(){ document.getElementsByClassName('tablinks')[1].click(); }
</script>

</body>
</html>
