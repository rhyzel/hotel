<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salary Processing | Payroll & Compensation</title>
<link rel="stylesheet" href="../css/salary_processing.css">
<link rel="stylesheet" href="../css/index.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="overlay">
<div class="container salary-container">
<header>
<h1><i class="fas fa-calculator"></i> Salary Processing</h1>
<p>Automates monthly salary calculations for all employees.</p>
</header>
<div id="salaryMessage" class="message" role="status" aria-live="polite" hidden></div>
<div class="salary-content">
<div class="form-section glass">
<h2><i class="fas fa-user-tie"></i> Employee Details</h2>
<form id="salaryForm">
<div class="form-group">
<label>Employee ID</label>
<input type="text" id="employee_id" name="employee_id" placeholder="Enter Employee ID">
</div>
<div class="form-group">
<label>Full Name</label>
<input type="text" id="employee_name" name="employee_name" placeholder="Employee Name" readonly>
</div>
<div class="form-group">
<label>Basic Salary</label>
<input type="number" id="basic_salary" name="basic_salary" readonly>
</div>
<div class="form-group">
<label>Allowances</label>
<input type="number" id="allowances" name="allowances" placeholder="₱ 0.00">
</div>
<div class="form-group">
<label>Overtime Pay</label>
<input type="number" id="overtime" name="overtime" placeholder="₱ 0.00">
</div>
<div class="form-group">
<button type="button" id="fetch_btn" class="back-btn"><i class="fas fa-search"></i> Fetch Employee</button>
</div>
</form>
</div>
<div class="summary-section glass">
<h2><i class="fas fa-wallet"></i> Salary Summary</h2>
<div class="summary-row"><span>Gross Pay:</span> <span id="gross_pay">₱ 0.00</span></div>
<div class="breakdown">
<h3>Deduction Breakdown</h3>
<div class="summary-row"><span>Absences / Undertime:</span> <span id="break_absences">₱ 0.00</span></div>
<div class="summary-row"><span>Tax & Social:</span> <span id="break_tax">₱ 0.00</span></div>
<div class="summary-row"><span>SSS Contribution:</span> <span id="break_sss">₱ 0.00</span></div>
<div class="summary-row"><span>PhilHealth Contribution:</span> <span id="break_phil">₱ 0.00</span></div>
<div class="summary-row"><span>Pag-IBIG Contribution:</span> <span id="break_pagibig">₱ 0.00</span></div>
<div class="summary-row"><span>Withholding Tax:</span> <span id="break_withholding">₱ 0.00</span></div>
<div class="summary-row"><span>Bonuses:</span> <span id="break_bonuses">₱ 0.00</span></div>
<div class="summary-row"><span>Expense Reimbursements:</span> <span id="break_expenses">₱ 0.00</span></div>
</div>
<div class="summary-row"><span>Total Deductions:</span> <span id="total_deductions">₱ 0.00</span></div>
<div class="summary-row netpay"><span>Net Pay:</span> <span id="net_pay">₱ 0.00</span></div>
<div class="actions">
<button type="button" class="calculate-btn" id="calculate_btn"><i class="fas fa-equals"></i> Calculate</button>
<button type="button" class="save-btn" id="save_btn"><i class="fas fa-save"></i> Save Record</button>
</div>
</div>
</div>
<div class="actions bottom">
<button class="back-btn" onclick="window.location.href='../hrm_payroll.php'"><i class="fas fa-arrow-left"></i> Back to Payroll Module</button>
</div>
</div>
</div>
<script src="../js/salary_processing.js"></script>
</body>
</html>
