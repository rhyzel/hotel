<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales and Revenue Reports - Hotel La Vista</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="sales_revenue.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>

<body>
<div class="overlay">
<header>
  <a href="../reports.php" style="position:fixed; top:20px; left:20px; padding:10px 18px; background:#e69419ff; color:#000; text-decoration:none; border-radius:8px; font-size:1rem; font-weight:600; z-index:1000; box-shadow:0 2px 10px rgba(0,0,0,0.3); display:flex; align-items:center; gap:8px;">&#8592; Back to Reports</a>
  <h1>Sales and Revenue Reports</h1>
  <p>Overview of sales and revenue at Hotel La Vista</p>
</header>

<div class="card">
   <!-- Period Filter -->
   <div style="text-align:center; margin-bottom:35px;">
     <label>Period:
       <select id="periodSelect" style="padding:8px; border-radius:6px; border:1px solid #444; background: rgba(255,255,255,0.1); color:#fff; margin-right:10px;">
         <option value="yearly">Yearly</option>
         <option value="monthly">Monthly</option>
         <option value="weekly">Weekly</option>
         <option value="daily">Daily</option>
       </select>
     </label>
     <label id="monthLabel">Month:
       <input type="month" id="monthSelect" style="padding:8px; border-radius:6px; border:1px solid #444; background: rgba(255,255,255,0.1); color:#fff;">
     </label>
     <label id="weekLabel" style="display:none;">Week:
       <input type="week" id="weekSelect" style="padding:8px; border-radius:6px; border:1px solid #444; background: rgba(255,255,255,0.1); color:#fff;">
     </label>
     <label id="dayLabel" style="display:none;">Day:
       <input type="date" id="daySelect" style="padding:8px; border-radius:6px; border:1px solid #444; background: rgba(255,255,255,0.1); color:#fff;">
     </label>
     <label id="yearLabel" style="display:none;">Year:
       <input type="number" id="yearSelect" style="padding:8px; border-radius:6px; border:1px solid #444; background: rgba(255,255,255,0.1); color:#fff;" min="2020" max="2030">
     </label>
   </div>

   <!-- Tabs -->
   <div class="tabs">
     <button class="tab-btn active" data-tab="chartTab">Chart</button>
     <button class="tab-btn" data-tab="salesTab">Sales Details</button>
     <button class="tab-btn" data-tab="roomTab">Room Payments</button>
   </div>

   <!-- Chart Tab -->
   <div id="chartTab" class="tab-content active">
     <div class="chart-container">
       <canvas id="revenueChart"></canvas>
     </div>

     <!-- Export Buttons below chart -->
     <div style="text-align:center; margin:20px 0;">
       <button onclick="exportToPDF()" style="padding: 10px 20px; background: #ff9800; color: #000; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-right: 10px;">Export PDF</button>
       <button onclick="exportToCSV()" style="padding: 10px 20px; background: #28a745; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Export CSV</button>
     </div>
   </div>

  <!-- Sales Details Tab -->
  <div id="salesTab" class="tab-content">
    <!-- Filters -->
    <div class="filters" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px;">
      <input type="text" id="salesGuestSearch" placeholder="Search Guest Name">
      <select id="salesOrderType"></select>
      <select id="salesPaymentOption"></select>
      <select id="salesPaymentMethod"></select>
    </div>
    <div style="overflow-x:auto; max-height:500px;">
      <table id="salesTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Guest Name</th>
            <th>Order Type</th>
            <th>Item</th>
            <th>Amount (₱)</th>
            <th>Payment Option</th>
            <th>Payment Method</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="salesTableBody"></tbody>
      </table>
    </div>
  </div>

  <!-- Room Payments Tab -->
  <div id="roomTab" class="tab-content">
    <!-- Filters -->
    <div class="filters" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px;">
      <input type="text" id="roomGuestSearch" placeholder="Search Guest Name">
      <select id="roomBookingType"></select>
      <select id="roomTypeFilter"></select>
    </div>
    <div style="overflow-x:auto; max-height:500px;">
      <table id="roomPaymentsTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Guest Name</th>
            <th>Booking Type</th>
            <th>Room Type</th>
            <th>Room Price (₱)</th>
            <th>Stay</th>
            <th>Extended Duration</th>
            <th>Extended Price (₱)</th>
            <th>Total (₱)</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="roomPaymentsBody"></tbody>
      </table>
    </div>
  </div>

</div>
</div>

<!-- External JS -->
<script src="sales_revenue.js"></script>
</body>
</html>
