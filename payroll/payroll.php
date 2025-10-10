<?php
include 'db.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$totalEmployeesResult = $conn->query("SELECT COUNT(*) as total FROM staff");
$totalEmployees = $totalEmployeesResult->fetch_assoc()['total'] ?? 0;

$month_num = date('m');
$year = date('Y');

$conn->query("UPDATE payslip SET status='void' WHERE net_salary <= 0 AND status='pending' AND month='$month_num' AND year='$year'");

$pendingPayslipsResult = $conn->query("SELECT COUNT(*) as total 
    FROM payslip 
    WHERE status='pending' AND net_salary > 0 AND month='$month_num' AND year='$year'");
$pendingPayslips = $pendingPayslipsResult->fetch_assoc()['total'] ?? 0;

$salaryDisputeResult = $conn->query("SELECT COUNT(*) as total FROM salary_dispute");
$salaryDisputes = $salaryDisputeResult->fetch_assoc()['total'] ?? 0;

$announcementsResult = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = [];
if ($announcementsResult && $announcementsResult->num_rows > 0) {
    while ($row = $announcementsResult->fetch_assoc()) {
        $category = $row['category'] ?? 'Uncategorized';
        $announcements[$category][] = $row;
    }
}

$holidayResult = $conn->query("SELECT * FROM holidays ORDER BY date ASC");
$holidays = [];
if ($holidayResult && $holidayResult->num_rows > 0) {
    while ($row = $holidayResult->fetch_assoc()) {
        $holidays[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payroll Dashboard - Hotel La Vista</title>
<link rel="stylesheet" href="payroll.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="logo.png" alt="Hotel La Vista Logo">
        </div>
        <a href="salary/salary_processing.php"><i class="fas fa-calculator"></i> Salary Processing</a>
        <a href="deductions/tax_deductions.php"><i class="fas fa-file-invoice-dollar"></i> Tax & Deductions</a>
        <a href="incentives/bonuses_incentives.php"><i class="fas fa-gift"></i> Bonuses & Incentives</a>
        <a href="reimbursement/expense_reimbursement.php"><i class="fas fa-receipt"></i> Expense Reimbursement</a>
        <a href="payslip/login.php"><i class="fas fa-file-pdf"></i> Payslip Portal</a>
      <a href="http://localhost/hotel/hr/payroll/dispute/salary_dispute.php">
    <i class="fas fa-exclamation-triangle"></i> Salary Dispute
</a>

        <a href="holidays/holidays.php"><i class="fas fa-exclamation-triangle"></i> Holidays</a>
        <a href="http://localhost/hotel/hr/employee_login.php" class="back"><i class="fas fa-arrow-left"></i> Back to HR Dashboard</a>
    </aside>
    <div class="main-content">
        <div class="header-kpi">
            <div class="header-left">
                <div class="header-logo flicker">
    <img src="brand_logo.png" alt="Hotel La Vista Logo">
</div>
            </div>
            <div class="kpi-container">
                <a href="all_employees.php" class="kpi-card">
                    <h3><?= $totalEmployees ?></h3>
                    <p>Total Employees</p>
                </a>
                <a href="http://localhost/hotel/hr/payroll/salary/generate_all_payslips.php" class="kpi-card">
                    <h3><?= $pendingPayslips ?></h3>
                    <p>Pending Payslips</p>
                </a>
                <a href="http://localhost/hotel/hr/payroll/holidays/holidays.php" class="kpi-card">
                    <h3><?= count($holidays) ?></h3>
                    <p>Upcoming Holidays</p>
                </a>
                <a href="http://localhost/hotel/hr/payroll/dispute/salary_dispute.php" class="kpi-card">
                    <h3><?= $salaryDisputes ?></h3>
                    <p>Salary Disputes</p>
                </a>
            </div>
        </div>

       <div class="announcement-grid">
    <div class="announcement">
        <h2 class="collapsible">Payroll Reminders</h2>
        <ul class="content">
            <?php if (!empty($announcements['Payroll Reminders'])): ?>
                <?php foreach ($announcements['Payroll Reminders'] as $a): ?>
                    <li><strong><?= htmlspecialchars($a['title']) ?></strong>: <?= htmlspecialchars($a['content']) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No payroll reminders available.</li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="announcement">
        <h2 class="collapsible">HR & Policy Updates</h2>
        <ul class="content">
            <?php if (!empty($announcements['HR & Policy Updates'])): ?>
                <?php foreach ($announcements['HR & Policy Updates'] as $a): ?>
                    <li><strong><?= htmlspecialchars($a['title']) ?></strong>: <?= htmlspecialchars($a['content']) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No HR & Policy updates available.</li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="announcement">
        <h2 class="collapsible">Holidays & Events</h2>
        <ul class="content">
            <?php if (!empty($holidays)): ?>
                <?php foreach ($holidays as $holiday): ?>
                    <li><strong><?= htmlspecialchars($holiday['name']) ?></strong> on <?= date('F j, Y', strtotime($holiday['date'])) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No holidays found.</li>
            <?php endif; ?>
            <?php if (!empty($announcements['Holidays & Events'])): ?>
                <?php foreach ($announcements['Holidays & Events'] as $a): ?>
                    <li><strong><?= htmlspecialchars($a['title']) ?></strong>: <?= htmlspecialchars($a['content']) ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
<script>
    document.querySelectorAll(".collapsible").forEach(button => {
        button.addEventListener("click", () => {
            button.classList.toggle("active");
            const content = button.nextElementSibling;
            if (content.style.maxHeight && content.style.maxHeight !== "none") {
                content.style.maxHeight = "200px";
            } else {
                content.style.maxHeight = "none";
            }
        });
    });
</script>


       <div class="button-container manage-announcement-btn">
    <a href="announcement/announcement_management.php" class="edit-btn"><i class="fas fa-bullhorn"></i> Manage Announcements</a>
</div>
    </div>
</div>
</body>
</html>
