<?php
include 'kleishdb.php';

session_start();
$employee_id = $_SESSION['employee_id']; 

// Employee query
$employee_query = "SELECT employee_id, first_name, last_name, role, mobile_number, email, position, hire_date, salary, department, is_admin, created_at
                   FROM employee WHERE employee_id = '$employee_id'"; 
$employee_result = mysqli_query($conn, $employee_query);
$employee = mysqli_fetch_assoc($employee_result);

// Salary query
$salary_query = "SELECT * FROM salaries WHERE employee_id = '$employee_id'";
$salary_result = mysqli_query($conn, $salary_query);
$salary = mysqli_fetch_assoc($salary_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <link rel="stylesheet" href="payslip.css">
</head>
<body>
    <div class="payslip-container">
        <h1>Payslip for <?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></h1>
        
        <div class="payslip-details">
            <p><strong>Employee ID:</strong> <?php echo $employee['employee_id']; ?></p>
            <p><strong>Position:</strong> <?php echo $employee['position']; ?></p>
            <p><strong>Department:</strong> <?php echo $employee['department']; ?></p>
            <p><strong>Salary:</strong> ₱<?php echo number_format($salary['amount'], 2); ?></p>
            <p><strong>Last Paid:</strong> <?php echo $salary['last_paid']; ?></p>
        </div>

        <button onclick="window.print()">Print Payslip</button>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
