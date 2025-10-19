<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payslip Generation | Payroll & Compensation</title>
  <link rel="stylesheet" href="../css/css4.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="overlay">
    <div class="container payslip-container">
      <header>
        <h1><i class="fas fa-file-invoice"></i> Payslip Generation</h1>
        <p>Provides detailed electronic payslips for employees.</p>
      </header>

      <div class="payslip-content glass">
        <form id="payslipForm">
          <div id="payslipMessage" class="message" role="status" aria-live="polite" hidden></div>
          <div class="form-group">
            <label>Employee ID</label>
            <div style="display:flex;gap:.5rem;align-items:center">
              <input type="text" id="payslip_employee_id" placeholder="Enter Employee ID">
              <button type="button" id="payslip_fetch_btn" class="back-btn"><i class="fas fa-search"></i> Fetch</button>
            </div>
          </div>
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="payslip_employee_name" placeholder="Employee Name" readonly>
          </div>
          <div class="form-group">
            <label>Month & Year</label>
            <input type="month" id="payslip_period">
          </div>

          <div class="actions">
            <button type="button" class="calculate-btn" id="payslip_generate_btn"><i class="fas fa-file-alt"></i> Generate Payslip</button>
            <button type="button" class="save-btn" id="payslip_download_btn"><i class="fas fa-download"></i> Download PDF</button>
          </div>
        </form>

        <!-- Payslip preview container (printable) -->
  <div class="preview" id="payslip_preview" style="margin-top:20px; background:#ffffff; color:#111; padding:18px; border-radius:8px; display:none; box-shadow:0 6px 20px rgba(0,0,0,0.08);">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h3>Payslip Preview</h3>
            <div id="payslip_timestamp" style="font-size:12px;color:#555"></div>
          </div>
          <div style="margin-bottom:8px;"><strong>Employee:</strong> <span id="preview_name"></span> (<span id="preview_empid"></span>)</div>
          <div style="margin-bottom:8px;"><strong>Period:</strong> <span id="preview_period"></span></div>
          <hr />
          <div id="preview_lines" style="text-align:left;margin-top:8px;color:#111">
            <!-- dynamic payslip lines (gross, deductions, net) -->
          </div>
          <div style="margin-top:12px;display:flex;gap:8px;justify-content:flex-end;">
            <button id="payslip_print_btn" class="calculate-btn">Print</button>
            <button id="payslip_release_btn" class="save-btn">Release Payslip</button>
          </div>
        </div>
      </div>

      <div class="actions bottom">
        <button class="back-btn" onclick="window.location.href='../hrm_payroll.php'">
          <i class="fas fa-arrow-left"></i> Back to Payroll Module
        </button>
      </div>
    </div>
  </div>
</body>
<!-- prefer local copies of html2canvas and jsPDF (fallback to CDN is handled in JS) -->
<script src="/HRM/vendor/js/html2canvas.min.js"></script>
<script src="/HRM/vendor/js/jspdf.umd.min.js"></script>
<!-- Small shim to normalize globals for different builds (prevents "html2 is not defined" errors) -->
<script>
  try{
    if(window.html2canvas && !window.html2) window.html2 = window.html2canvas;
    if(window.jspdf && window.jspdf.jsPDF && !window.jsPDF) window.jsPDF = window.jspdf.jsPDF;
  }catch(e){ /* ignore */ }
</script>
<script src="../js/payslip_generation.js"></script>
</html>
