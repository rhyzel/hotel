<?php
include 'kleishdb.php';
session_start();

// 1. Make sure employee_id is set in session
if (!isset($_SESSION['employee_id'])) {
    die("Employee ID not found in session.");
}

$employee_id = $_SESSION['employee_id'];

// 2. Fetch employee details
$employee_query = "SELECT employee_id, first_name, last_name, role, mobile_number, email, position, hire_date, salary, department, is_admin, created_at, background
                   FROM employee WHERE employee_id = '$employee_id'";
$employee_result = mysqli_query($conn, $employee_query);
if (!$employee_result) {
    die("Employee query failed: " . mysqli_error($conn));
}

$employee = mysqli_fetch_assoc($employee_result);
if (!$employee) {
    die("No employee found with ID: $employee_id");
}

// 3. Fetch schedule (if stored in attendance)
$attendance_query = "SELECT day, shift_start, shift_end FROM attendance WHERE employee_id = '$employee_id'";
$attendance_result = mysqli_query($conn, $attendance_query);
if (!$attendance_result) {
    die("Schedule query failed: " . mysqli_error($conn));
}

// 4. Fetch attendance
$work_hours_query = "SELECT date, status, check_in, check_out FROM attendance WHERE employee_id = '$employee_id'";
$work_hours_result = mysqli_query($conn, $work_hours_query);
if (!$work_hours_result) {
    die("Work hours query failed: " . mysqli_error($conn));
}

// 5. Fetch salary info
$salary_query = "SELECT * FROM salaries WHERE employee_id = '$employee_id'";
$salary_result = mysqli_query($conn, $salary_query);
$salary = $salary_result ? mysqli_fetch_assoc($salary_result) : null;

// 6. Fetch detailed salaries
$allowances_result = mysqli_query($conn, "SELECT * FROM salaries WHERE employee_id = '$employee_id'");
$total_allowance = 0;
if ($allowances_result && mysqli_num_rows($allowances_result) > 0) {
    while ($row = mysqli_fetch_assoc($allowances_result)) {
        $total_allowance += $row['amount'];
    }
    mysqli_data_seek($allowances_result, 0); // rewind result for display later
}

// 7. Fetch detailed salaries
$deductions_result = mysqli_query($conn, "SELECT * FROM salaries WHERE employee_id = '$employee_id'");
$total_deductions = 0;
if ($deductions_result && mysqli_num_rows($deductions_result) > 0) {
    while ($row = mysqli_fetch_assoc($deductions_result)) {
        $total_deductions += $row['amount'];
    }
    mysqli_data_seek($deductions_result, 0); // rewind result for display later
}

// Net Salary Calculation
$basic_salary = $salary['basic_salary'] ?? 0;
$net_salary = $basic_salary - $total_deductions + $total_allowance;
$last_paid = $salary['last_paid'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="employee_profile.css">
</head>
<body>

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($employee['first_name'] . " " . $employee['last_name']); ?></h1>
        
        <div class="profile-section">
            <h2>Employee Profile</h2>
            <div class="profile-details">
                <p><strong>Email:</strong> <?php echo $employee['email']; ?></p>
                <p><strong>Position:</strong> <?php echo $employee['position']; ?></p>
                <p><strong>Department:</strong> <?php echo $employee['department']; ?></p>
                <p><strong>Hire Date:</strong> <?php echo $employee['hire_date']; ?></p>
                <p><strong>Salary:</strong> ₱<?php echo number_format($basic_salary, 2); ?></p>
                <p><strong>Account Created At:</strong> <?php echo $employee['created_at']; ?></p>
            </div>
        </div>

        <div class="schedule-section">
            <h2>Your Work Schedule</h2>
            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Shift Start</th>
                        <th>Shift End</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($schedule = mysqli_fetch_assoc($attendance_result)): ?>
                    <tr>
                        <td><?php echo $schedule['day']; ?></td>
                        <td><?php echo $schedule['shift_start']; ?></td>
                        <td><?php echo $schedule['shift_end']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="attendance-section">
            <h2>Your Attendance</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($attendance = mysqli_fetch_assoc($work_hours_result)): ?>
                    <tr>
                        <td><?php echo $attendance['date']; ?></td>
                        <td><?php echo $attendance['status']; ?></td>
                        <td><?php echo $attendance['check_in']; ?></td>
                        <td><?php echo $attendance['check_out']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="salary-section">
            <h2>Your Payslip</h2>
            <p><strong>Monthly Salary:</strong> ₱<?php echo number_format($basic_salary, 2); ?></p>
            <p><strong>Last Paid:</strong> <?php echo $last_paid; ?></p>

            <!-- Allowances -->
            <?php if ($allowances_result && mysqli_num_rows($allowances_result) > 0): ?>
                <h3>Allowances:</h3>
                <ul>
                    <?php while ($allow = mysqli_fetch_assoc($allowances_result)): ?>
                        <li><?php echo $allow['allowance_name']; ?>: ₱<?php echo number_format($allow['amount'], 2); ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>

            <!-- Deductions -->
            <?php if ($deductions_result && mysqli_num_rows($deductions_result) > 0): ?>
                <h3>Deductions:</h3>
                <ul>
                    <?php while ($deduct = mysqli_fetch_assoc($deductions_result)): ?>
                        <li><?php echo $deduct['deduction_name']; ?>: ₱<?php echo number_format($deduct['amount'], 2); ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>

            <p><strong>Total Deductions:</strong> ₱<?php echo number_format($total_deductions, 2); ?></p>
            <p><strong>Total Allowance:</strong> ₱<?php echo number_format($total_allowance, 2); ?></p>
            <p><strong>Net Salary:</strong> ₱<?php echo number_format($net_salary, 2); ?></p>

            <button onclick="window.print()">Print Payslip</button>
        </div>

        <div class="work-clock-section">
            <h2>Clock-in and Clock-out</h2>
            <form action="clockinclockout.php" method="POST">
                <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">
                <button type="submit" name="action" value="clock_in">Clock In</button>
                <button type="submit" name="action" value="clock_out">Clock Out</button>
            </form>
        </div>
    </div>

</body>
</html>

<?php
mysqli_close($conn);
?>
