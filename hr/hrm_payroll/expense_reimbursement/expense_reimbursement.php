<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expense Reimbursement | Payroll & Compensation</title>
  <link rel="stylesheet" href="../css/css4.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="overlay">
    <div class="container reimbursement-container">
      <header>
        <h1><i class="fas fa-wallet"></i> Expense Reimbursement</h1>
        <p>Manages employee claims for work-related expenses.</p>
      </header>

      <div id="expenseMessage" class="message" role="status" aria-live="polite" hidden></div>

      <div class="reimbursement-content glass">
        <form id="expenseForm">
          <div class="form-group">
            <label>Employee ID</label>
            <div style="display:flex;gap:.5rem;align-items:center">
              <input type="text" id="exp_employee_id" placeholder="Enter Employee ID">
              <button type="button" id="exp_fetch_btn" class="back-btn"><i class="fas fa-search"></i> Fetch</button>
            </div>
          </div>
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="exp_employee_name" placeholder="Employee Name" readonly>
          </div>
          <div class="form-group">
            <label>Expense Type</label>
            <input type="text" id="exp_type" placeholder="e.g., Travel, Meals, Supplies">
          </div>
          <div class="form-group">
            <label>Amount</label>
            <input type="number" id="exp_amount" placeholder="₱ 0.00">
          </div>
          <div class="form-group">
            <label>Remarks</label>
            <textarea id="exp_remarks" placeholder="Provide description or justification"></textarea>
          </div>

          <div class="actions">
            <button type="button" id="exp_verify_btn" class="calculate-btn"><i class="fas fa-check"></i> Verify Expense</button>
            <button type="button" id="exp_save_btn" class="save-btn"><i class="fas fa-save"></i> Save Record</button>
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
<script src="../js/expense_reimbursement.js"></script>
</html>
