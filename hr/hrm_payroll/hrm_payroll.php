<?php
session_start();
if (empty($_SESSION['hrm_logged_in'])) {
  header('Location: login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll & Compensation | Hotel Management System</title>
  <link rel="stylesheet" href="css/payroll.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="overlay">
    <div class="container payroll-container">
      <header>
        <h1>Payroll & Compensation</h1>
        <p class="lead">Manages salaries, benefits, and deductions.</p>
      </header>

      <div class="payroll-modules">
      <a href="salary_processing/salary_processing.php" class="module-card">
          <i class="fas fa-user-tie"></i>
          <span>Salary Processing</span>
        </a>

        <a href="tax_deduction/tax_deduction.php" class="module-card">
          <i class="fas fa-receipt"></i>
          <span>Tax & Deductions</span>
        </a>

        <a href="bonus_incentives/bonus_incentives.php" class="module-card">
          <i class="fas fa-gift"></i>
          <span>Bonuses & Incentives</span>
        </a>

        <a href="expense_reimbursement/expense_reimbursement.php" class="module-card">
          <i class="fas fa-wallet"></i>
          <span>Expense Reimbursement</span>
        </a>

        <a href="payslip_generation/payslip_generation.php" class="module-card">
          <i class="fas fa-file-invoice"></i>
          <span>Payslip Generation</span>
        </a>
    </div>

     <div class="grid">
      <a href="../hr_dashboard.php" class="module back-module">
        <i class="fas fa-arrow-left"></i>
        <span>Back to HR Dashboard</span>
      </a>
    </div>

    </div>
  </div>

</body>
</html>
