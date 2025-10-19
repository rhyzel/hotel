<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bonuses & Incentives | Payroll & Compensation</title>
  <link rel="stylesheet" href="../css/css4.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="overlay">
    <div class="container bonuses-container">
      <header>
        <h1><i class="fas fa-gift"></i> Bonuses & Incentives</h1>
        <p>Allocates rewards and performance-based bonuses to employees.</p>
      </header>

      <div class="bonuses-content glass">
        <form id="bonusesForm" onsubmit="return false;">
          <div class="form-group id-search-group" style="display:flex;gap:8px;align-items:center;">
            <div style="flex:1;">
              <label>Employee ID</label>
              <input id="b_employee_id" type="text" placeholder="Enter Employee ID">
            </div>
            <button id="b_search_toggle" class="search-toggle" title="Toggle search by ID" aria-pressed="false" style="margin-top:22px;padding:8px 10px;border-radius:8px;background:#ffd700;border:none;cursor:pointer;">
              <i class="fas fa-search"></i>
            </button>
          </div>
          <div class="form-group">
            <label>Full Name</label>
            <input id="b_full_name" type="text" placeholder="Employee Name" readonly>
          </div>
          <div class="form-group">
            <label>Basic Salary</label>
            <input id="b_basic_salary" type="text" placeholder="₱ 0.00" readonly>
          </div>
          <div class="form-group">
            <label></label>Performance Bonus</label>
            <input id="b_performance_bonus" type="number" placeholder="₱ 0.00" value="0">
          </div>
          <div class="form-group">
            <label>Bonus Type</label>
            <select id="b_bonus_type">
              <option value="custom" data-amount="0">Custom (manual)</option>
              <option value="holiday" data-amount="1000">Holiday Bonus (₱1,000)</option>
              <option value="13th_month" data-amount="0">13th Month Pay (calc)</option>
            </select>
          </div>
          <div class="form-group">
            <label>Incentives</label>
            <input id="b_incentives" type="number" placeholder="₱ 0.00" value="0">
          </div>
          <div class="form-group">
            <label>Remarks</label>
            <textarea id="b_remarks" placeholder="Bonus reason or performance note"></textarea>
          </div>

          <div class="actions">
            <button id="b_compute" class="calculate-btn"><i class="fas fa-check"></i> Compute Total</button>
            <button id="b_save" class="save-btn"><i class="fas fa-save"></i> Save Record</button>
          </div>
        </form>

        <div id="b_message" class="inline-message" style="display:none;"></div>
      </div>

      <div class="actions bottom">
        <button class="back-btn" onclick="window.location.href='../hrm_payroll.php'">
          <i class="fas fa-arrow-left"></i> Back to Payroll Module
        </button>
      </div>
    </div>
  </div>
  </body>
    <script src="../js/bonus_incentives.js"></script>
  </html>
</html>
