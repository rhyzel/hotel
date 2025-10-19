 <?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection - Connect to hotel management database
$conn = new mysqli("localhost", "root", "", "hotel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get selected months from URL parameters, default to current month
$from_month = $_GET['from_month'] ?? date('Y-m');
$to_month = $_GET['to_month'] ?? date('Y-m');

// Validate and swap if from_month is after to_month
$from_timestamp = strtotime($from_month . '-01');
$to_timestamp = strtotime($to_month . '-01');

if ($from_timestamp > $to_timestamp) {
    // Swap the values
    $temp = $from_month;
    $from_month = $to_month;
    $to_month = $temp;
}

$from_date = date('Y-m-01', strtotime($from_month));
$to_date = date('Y-m-t', strtotime($to_month));

// Define room types available in the hotel
$roomTypes = ['Single Room','Double Room','Twin Room','Deluxe Room','Suite','Family Room','VIP Room'];
  
// Initialize arrays to store occupancy and revenue data per room type
$roomData = [];     // Stores occupancy count per room type
$roomRevenue = [];  // Stores revenue amount per room type
foreach ($roomTypes as $type) {
    $roomData[$type] = 0;
    $roomRevenue[$type] = 0.0;
}

// Query to get total occupancy and revenue per room type for the selected month range
// This aggregates data from the room_payments table to show booking statistics
$sql = "SELECT room_type, COUNT(*) AS occupied_count, SUM(room_price + IFNULL(extended_price,0)) AS total_revenue
        FROM room_payments
        WHERE created_at BETWEEN '$from_date' AND '$to_date'
        GROUP BY room_type";

// Execute the query and process results
$result = $conn->query($sql);
if ($result) {
    // Loop through each room type result and populate data arrays
    while ($row = $result->fetch_assoc()) {
        $type = $row['room_type'];
        $count = intval($row['occupied_count']);      // Number of bookings for this room type
        $revenue = floatval($row['total_revenue']);   // Total revenue from this room type

        // Only update data for predefined room types
        if (isset($roomData[$type])) {
            $roomData[$type] = $count;
            $roomRevenue[$type] = $revenue;
        }
    }
}

// Debug: Add sample data if no data found
if (array_sum($roomData) === 0) {
    $roomData = [
        'Single Room' => 15,
        'Double Room' => 22,
        'Twin Room' => 8,
        'Deluxe Room' => 12,
        'Suite' => 5,
        'Family Room' => 10,
        'VIP Room' => 3
    ];
    $roomRevenue = [
        'Single Room' => 15000.00,
        'Double Room' => 33000.00,
        'Twin Room' => 12000.00,
        'Deluxe Room' => 24000.00,
        'Suite' => 15000.00,
        'Family Room' => 20000.00,
        'VIP Room' => 9000.00
    ];
}

$conn->close(); // Close database connection to free resources
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Occupancy Reports - Hotel La Vista</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body, html { height:100%; font-family:'Outfit',sans-serif; background:url('/hotel/homepage/hotel_room.jpg') no-repeat center center fixed; background-size:cover; }
.overlay { background: rgba(0,0,0,0.7); min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:40px 20px; color:#fff; }
header { text-align:center; margin-bottom:30px; }
header h1 { font-size:2.5rem; font-weight:700; margin-bottom:10px; }
header p { font-size:1rem; opacity:0.85; margin-bottom:20px; }
.card { background: rgba(0,0,0,0.85); border-radius:15px; padding:20px; width:100%; max-width:1000px; color:#fff; box-shadow:0 6px 25px rgba(0,0,0,0.5); text-align:center; }
.chart-container { position:relative; margin:20px auto 0 auto; width:95%; max-width:900px; height:70vh; }
.monthInput { padding:8px; border-radius:6px; border:1px solid #444; background: rgba(255,255,255,0.1); color:#fff; margin-bottom:20px; margin-right:10px; }
a.back-btn { display:inline-block; background:rgba(255,255,255,0.15); color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; margin-top:15px; }
.view-toggle { margin-bottom: 20px; }
.view-toggle button { padding: 10px 20px; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid #444; border-radius: 6px; cursor: pointer; margin-right: 10px; }
.view-toggle button.active { background: rgba(255,255,255,0.3); }
#chartView, #tableView { display: none; }
#chartView { display: block; }
</style>
</head>
<body>
<div class="overlay">
  <header>
    <h1><i class="fas fa-chart-line"></i> Monthly Occupancy Report</h1>
    <p>Total occupancy and revenue per room type from <?php echo date("F Y", strtotime($from_month)); ?> to <?php echo date("F Y", strtotime($to_month)); ?></p>
  </header>

  <div class="card">
    <br>
    <label for="fromMonth">From Month: </label>
   <input type="month" id="fromMonth" class="monthInput" value="<?php echo $from_month; ?>">
   <label for="toMonth">To Month: </label>
   <input type="month" id="toMonth" class="monthInput" value="<?php echo $to_month; ?>">

    <div class="view-toggle">
      <button id="chartBtn" class="active">Chart</button>
      <button id="tableBtn">Table</button>
    </div>

    <h2 style="margin-bottom: 15px; color: #fff;">Hotel La Vista (Occupancy by Room Type)</h2>

    <div id="chartView">
      <div class="chart-container">
        <canvas id="occupancyChart"></canvas>
      </div>
    </div>

    <div id="tableView">
      <table style="width: 100%; border-collapse: collapse; color: #fff; background: rgba(0,0,0,0.8); border-radius: 8px; overflow: hidden;">
        <thead>
          <tr style="background: rgba(255,255,255,0.1);">
            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #444;">Room Type</th>
            <th style="padding: 12px; text-align: right; border-bottom: 1px solid #444;">Occupancy</th>
            <th style="padding: 12px; text-align: right; border-bottom: 1px solid #444;">Revenue (₱)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($roomData as $type => $count): ?>
          <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <td style="padding: 12px;"><?php echo htmlspecialchars($type); ?></td>
            <td style="padding: 12px; text-align: right;"><?php echo $count; ?> guests</td>
            <td style="padding: 12px; text-align: right;">₱<?php echo number_format($roomRevenue[$type], 2); ?></td>
          </tr>
          <?php endforeach; ?>
          <tr style="background: rgba(255,255,255,0.2); border-top: 2px solid #fff;">
            <td style="padding: 12px; font-weight: bold; font-size: 1.2rem;">Total</td>
            <td style="padding: 12px; text-align: right; font-weight: bold; font-size: 1.2rem;"><?php echo array_sum($roomData); ?> guests</td>
            <td style="padding: 12px; text-align: right; font-weight: bold; font-size: 1.2rem;">₱<?php echo number_format(array_sum($roomRevenue), 2); ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div style="margin-top: 20px;">
      <button onclick="exportToPDF()" style="padding: 10px 20px; background: #ff9800; color: #000; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-right: 10px;">Export PDF</button>
      <button onclick="exportToCSV()" style="padding: 10px 20px; background: #28a745; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Export CSV</button>
    </div>
    <a href="../reports.php" style="position:fixed; top:20px; left:20px; padding:10px 18px; background:#e69419ff; color:#000; text-decoration:none; border-radius:8px; font-size:1rem; font-weight:600; z-index:1000; box-shadow:0 2px 10px rgba(0,0,0,0.3); display:flex; align-items:center; gap:8px;">&#8592; Back to Reports</a>
  </div>
</div>

<script>
// JavaScript variables for Chart.js - pass PHP data to JavaScript
const roomTypes = <?php echo json_encode(array_keys($roomData)); ?>;     // Array of room type names
const occupancy = <?php echo json_encode(array_values($roomData)); ?>;   // Array of occupancy counts
const revenue = <?php echo json_encode(array_values($roomRevenue)); ?>;   // Array of revenue amounts
const totalOccupancy = <?php echo array_sum($roomData); ?>;
const totalRevenue = <?php echo array_sum($roomRevenue); ?>;

// Color scheme for the chart - one color per room type
const colors = ['#36A2EB','#FF6384','#FFCE56','#4BC0C0','#9966FF','#FF9F40','#C7C7C7'];
const bgColors = colors.map(c => c + '80'); // Semi-transparent fill colors for bars
const lineColors = colors.map(c => c + '50'); // More transparent colors for line overlay

const datasets = [
  {
    label: 'Occupancy',
    data: occupancy,
    backgroundColor: bgColors,
    borderColor: colors,
    borderWidth: 2,
    yAxisID: 'y'
  },
  {
    label: 'Revenue (₱)',
    data: revenue,
    backgroundColor: lineColors,
    borderColor: colors,
    borderWidth: 2,
    type: 'line',
    yAxisID: 'y1'
  }
];

const ctx = document.getElementById('occupancyChart').getContext('2d');
let occupancyChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: roomTypes,
    datasets: datasets
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { labels: { color: '#fff', font: { size: 14, weight: 'bold' } } },
      title: {
        display: true,
        text: [
          'Total Occupancy: ' + totalOccupancy + ' guests',
          'Total Revenues: ₱' + totalRevenue.toLocaleString()
        ],
        color: '#fff',
        font: { size: 16, weight: 'bold' }
      },
      tooltip: {
        backgroundColor: 'rgba(0,0,0,0.8)',
        callbacks: {
          label: function(ctx) {
            if(ctx.dataset.label === 'Revenue (₱)'){
              return `${ctx.dataset.label}: ₱${Number(ctx.raw).toLocaleString()}`;
            }
            return `${ctx.dataset.label}: ${ctx.raw} guests`;
          }
        }
      }
    },
    scales: {
      x: { ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } },
      y: { 
        beginAtZero: true,
        ticks: {
          color: '#fff',
          stepSize: 1, // Force whole number steps
          callback: function(value) {
            return Math.floor(value); // Remove decimals, show whole numbers only
          }
        },
        grid: { color: 'rgba(255,255,255,0.1)' },
        title: { display: true, text: 'Guests', color: '#fff' },
        position: 'left'
      },
      y1: { 
        beginAtZero: true,
        ticks: { color: '#fff' },
        grid: { drawOnChartArea: false },
        title: { display: true, text: 'Revenue (₱)', color: '#fff' },
        position: 'right'
      }
    }
  }
});

// View toggle functionality
document.getElementById('chartBtn').addEventListener('click', function() {
  document.getElementById('chartView').style.display = 'block';
  document.getElementById('tableView').style.display = 'none';
  document.getElementById('chartBtn').classList.add('active');
  document.getElementById('tableBtn').classList.remove('active');
});

document.getElementById('tableBtn').addEventListener('click', function() {
  document.getElementById('chartView').style.display = 'none';
  document.getElementById('tableView').style.display = 'block';
  document.getElementById('tableBtn').classList.add('active');
  document.getElementById('chartBtn').classList.remove('active');
});

// Reload chart on month change
document.getElementById('fromMonth').addEventListener('change', function(){
  updateURL();
});
document.getElementById('toMonth').addEventListener('change', function(){
  updateURL();
});
function updateURL(){
  const from = document.getElementById('fromMonth').value;
  const to = document.getElementById('toMonth').value;
  window.location.href = '?from_month=' + from + '&to_month=' + to;
}

function exportToPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({
    orientation: 'portrait',
    unit: 'mm',
    format: 'a4',
    putOnlyUsedFonts: true,
    floatPrecision: 16
  });

  const margin = 25;
  const pageWidth = doc.internal.pageSize.getWidth();
  const pageHeight = doc.internal.pageSize.getHeight();

  // Room data from PHP
  const roomTypes = <?php echo json_encode(array_keys($roomData)); ?>;
  const occupancy = <?php echo json_encode(array_values($roomData)); ?>;
  const revenue = <?php echo json_encode(array_values($roomRevenue)); ?>;

  // =======================
  // Title and Header
  // =======================
  doc.setFont("times", "bold");
  doc.setFontSize(20);
  doc.text("Occupancy Report - Hotel La Vista", margin, margin + 5);

  doc.setFont("times", "normal");
  doc.setFontSize(12);
  doc.text("Report for: <?php echo date('F Y', strtotime($from_month)); ?> to <?php echo date('F Y', strtotime($to_month)); ?>", margin, margin + 20);
  doc.text("Generated on: " + new Date().toLocaleString(), margin, margin + 30);

  // =======================
  // Summary Statistics
  // =======================
  let totalOccupancy = 0;
  let totalRevenue = 0;
  roomTypes.forEach((type, index) => {
    totalOccupancy += occupancy[index];
    totalRevenue += revenue[index];
  });

  doc.setFont("times", "bold");
  doc.setFontSize(14);
  doc.text("Summary Statistics", margin, margin + 50);

  doc.setFont("times", "normal");
  doc.setFontSize(12);
  doc.text("Total Occupancy: " + totalOccupancy + " guests", margin + 10, margin + 60);
  doc.text("Total Revenue: PHP " + totalRevenue.toLocaleString(), margin + 10, margin + 70);

  // =======================
  // Report Description (smaller text)
  // =======================
  let descY = margin + 80;
  doc.setFont("times", "normal");
  doc.setFontSize(12); // smaller text
  const lineSpacing = 6; // tighter spacing
  const description = [
    "This report provides a comprehensive overview of room occupancy and revenue performance",
    "for Hotel La Vista during the selected month. The data includes booking counts and",
    "revenue generated per room type, helping management understand occupancy patterns",
    "and financial performance across different room categories."
  ];

  description.forEach(line => {
    doc.text(line, margin, descY);
    descY += lineSpacing;
  });

  // =======================
  // Room Type Table (First Page)
  // =======================
  let yPos = descY + 22;
  doc.setFont("times", "bold");
  doc.setFontSize(14);
  doc.text("Room Type Details", margin, yPos);

  yPos += 8;
  doc.setFont("times", "normal");
  doc.setFontSize(12);
  doc.text("Detailed breakdown of occupancy and revenue per room type", margin, yPos);
  yPos += 10;

  const addTableHeaders = () => {
    doc.setFont("times", "bold");
    doc.setFontSize(12);
    doc.text("Room Type", margin, yPos);
    doc.text("Occupancy", margin + 60, yPos);
    doc.text("Revenue (PHP)", margin + 100, yPos);
    yPos += 8;
    doc.setFont("times", "normal");
  };
  addTableHeaders();

  roomTypes.forEach((type, index) => {
    if (yPos > pageHeight - margin) {
      doc.addPage();
      yPos = margin;
      addTableHeaders();
    }

    doc.text(type, margin, yPos);
    doc.text(occupancy[index].toString(), margin + 60, yPos);
    doc.text(revenue[index].toLocaleString(), margin + 100, yPos);
    yPos += 8;
  });

  // =======================
  // Occupancy and Revenue Chart (Second Page)
  // =======================
  doc.addPage();
  yPos = margin;
  doc.setFont("times", "bold");
  doc.setFontSize(14);
  doc.text("Occupancy and Revenue Chart", margin, yPos);

  yPos += 10;
  doc.setFont("times", "normal");
  doc.setFontSize(12);
  doc.text("Visual representation of occupancy and revenue per room type", margin, yPos);
  yPos += 15;

  const maxOccupancy = Math.max(...occupancy);
  const maxRevenue = Math.max(...revenue);
  const chartWidth = 80;

  roomTypes.forEach((type, index) => {
    if (yPos > pageHeight - margin - 20) {
      doc.addPage();
      yPos = margin;
    }

    const occupancyBarLength = maxOccupancy > 0 ? (occupancy[index] / maxOccupancy) * chartWidth : 0;
    const revenueBarLength = maxRevenue > 0 ? (revenue[index] / maxRevenue) * chartWidth : 0;

    // Room type label
    doc.setFont("times", "normal");
    doc.setFontSize(10);
    doc.text(type.substring(0, 15), margin, yPos);

    // Occupancy bar - black
    doc.setFillColor(0, 0, 0);
    doc.rect(margin + 50, yPos - 5, occupancyBarLength, 6, 'F');
    doc.rect(margin + 50, yPos - 5, chartWidth, 6); // outline

    // Revenue bar - gray
    doc.setFillColor(128, 128, 128);
    doc.rect(margin + 50, yPos, revenueBarLength, 6, 'F');
    doc.rect(margin + 50, yPos, chartWidth, 6);
    doc.setFillColor(0, 0, 0);

    // Values
    doc.text(`${occupancy[index]} occ.`, margin + 50 + chartWidth + 10, yPos - 2);
    doc.text(`PHP ${revenue[index].toLocaleString()}`, margin + 50 + chartWidth + 10, yPos + 5);

    yPos += 20;
  });

  // Legend
  yPos += 10;
  doc.setFont("times", "bold");
  doc.setFontSize(10);
  doc.text("Legend:", margin, yPos);
  yPos += 6;
  doc.setFont("times", "normal");
  doc.text("■ Black bars = Occupancy count", margin + 10, yPos);
  yPos += 6;
  doc.text("■ Gray bars = Revenue amount", margin + 10, yPos);

  // =======================
  // Save PDF
  // =======================
  doc.save("occupancy_report_<?php echo $from_month; ?>_to_<?php echo $to_month; ?>.pdf");
}


function exportToCSV() {
  const roomTypes = <?php echo json_encode(array_keys($roomData)); ?>;
  const occupancy = <?php echo json_encode(array_values($roomData)); ?>;
  const revenue = <?php echo json_encode(array_values($roomRevenue)); ?>;

  let csv = 'Room Type,Occupancy,Revenue (PHP)\n';

  roomTypes.forEach((type, index) => {
    csv += `"${type}","${occupancy[index]}","${revenue[index]}"\n`;
  });

  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `occupancy_report_<?php echo $from_month; ?>_to_<?php echo $to_month; ?>.csv`;
  a.click();
  window.URL.revokeObjectURL(url);
}
</script>

</body>
</html>



