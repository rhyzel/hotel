<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tax & Deductions | Payroll & Compensation</title>
  <link rel="stylesheet" href="../css/css4.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
  <div class="overlay">
    <div class="container deductions-container">
      <header>
        <h1><i class="fas fa-receipt"></i> Tax & Deductions</h1>
        <p>Applies income tax, social security, and mandatory deductions.</p>
      </header>

      <!-- Inline message box (success/error) -->
      <div id="taxMessage" class="message" role="status" aria-live="polite" hidden></div>

      <div class="deductions-content glass">
        <form id="taxForm">
          <div class="form-group">
            <label>Employee ID</label>
            <div style="display:flex;gap:.5rem;align-items:center">
              <input type="text" id="tax_employee_id" placeholder="Enter Employee ID">
              <button type="button" id="tax_fetch_btn" class="back-btn"><i class="fas fa-search"></i> Fetch</button>
            </div>
          </div>
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="tax_employee_name" placeholder="Employee Name" readonly>
          </div>
          <div class="form-group">
            <label>SSS Contribution</label>
            <input type="number" id="tax_sss" placeholder="₱ 0.00">
          </div>
          <div class="form-group">
            <label>PhilHealth Contribution</label>
            <input type="number" id="tax_phil" placeholder="₱ 0.00">
          </div>
          <div class="form-group">
            <label>Pag-IBIG Contribution</label>
            <input type="number" id="tax_pagibig" placeholder="₱ 0.00">
          </div>
          <div class="form-group">
            <label>Income Tax</label>
            <input type="number" id="tax_withholding" placeholder="₱ 0.00">
          </div>

          <div class="actions">
            <button type="button" class="calculate-btn" id="tax_compute_btn"><i class="fas fa-calculator"></i> Compute Total</button>
            <button type="button" class="save-btn" id="tax_save_btn"><i class="fas fa-save"></i> Save Record</button>
          </div>
        </form>
      </div>

      <div class="actions bottom">
        <button class="back-btn" onclick="window.location.href='../hrm_payroll.php'">
          <i class="fas fa-arrow-left"></i> Back to Payroll Module
        </button>
      </div>
    </div>
  </div>
</body>
  <script src="../js/tax_deduction.js"></script>
</html>
