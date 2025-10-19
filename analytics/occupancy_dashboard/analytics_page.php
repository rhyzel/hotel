<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Room Analytics - Hotel La Vista</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body, html { height: 100%; font-family: 'Outfit', sans-serif;
  background: url('../../homepage/hotel_room.jpg') no-repeat center center fixed;
  background-size: cover; }
.overlay { background: rgba(0, 0, 0, 0.7); min-height: 100vh;
  display: flex; flex-direction: column; align-items: center; padding: 40px 20px; }
header { text-align: center; color: #fff; margin-bottom: 30px; }
header h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 10px; }
header p { font-size: 1rem; opacity: 0.85; }
.card { background: rgba(0, 0, 0, 0.85); border-radius: 15px; padding: 20px;
  color: white; box-shadow: 0 6px 25px rgba(0, 0, 0, 0.5); margin-bottom: 25px;
  width: 100%; max-width: 900px; }
.chart-container { 
  position: relative; 
  margin: auto; 
  height: 500px;
  width: 500px;
}
.legend { display: flex; justify-content: center; flex-wrap: wrap; margin-top: 15px; gap: 15px; }
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.9rem; }
.legend-color { width: 14px; height: 14px; border-radius: 50%; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; color: #fff;
  background: rgba(0, 0, 0, 0.7); }
table th, table td { padding: 12px; text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.1); }
table th { background: rgba(255, 255, 255, 0.1); font-weight: 600; }
table tr:hover { background: rgba(255, 255, 255, 0.1); }
#allRoomsBtn { display:none; padding:8px 15px; background:#ffc107; color:#000; border:none; border-radius:8px; cursor:pointer; font-size:0.9rem; font-weight:600; margin-top:10px; }
#searchInput { padding:8px; border-radius:6px; width:100%; margin-bottom:10px; font-size:0.95rem; }
#backToTop {
  margin: 20px auto 0 auto;  /* centers horizontally */
  display: block;            /* makes auto margins work */
  padding: 10px 18px;
  background: #17a2b8;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 600;
  transition: background 0.2s ease-in-out;
}

#backToTop:hover {
  background: #138496;
}

.search-input {
  width: 100%;
  padding: 6px;
  font-size: 0.85rem;
  border-radius: 5px;
  border: none;
  outline: none;
  margin-top: 5px;
}#searchBarContainer {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 15px;
}

.search-input {
  flex: 1;
  min-width: 150px;
  padding: 8px;
  font-size: 0.9rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  outline: none;
}
#searchBarContainer {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin: 15px 0; /* separates it from the table card */
}.search-box {
  padding: 20px;
  background: rgba(0, 0, 0, 0.85);
  border-radius: 15px;
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.5);
  color: white;
  margin-bottom: 25px;
  width: 100%;
  max-width: 900px;
}

#searchBarContainer {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.search-input {
  flex: 1;
  min-width: 150px;
  padding: 10px 12px;
  font-size: 0.95rem;
  border-radius: 8px;
  border: 1px solid #444;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  transition: all 0.2s ease-in-out;
}

.search-input:focus {
  border-color: #ffc107;
  background: rgba(255, 255, 255, 0.2);
  outline: none;
}
.search-wrapper {
  position: relative;
  flex: 1;
  min-width: 150px;
}

.search-input {
  width: 100%;
  padding: 10px 38px 10px 12px; /* leave space for icon */
  font-size: 0.95rem;
  border-radius: 8px;
  border: 2px solid #ff9800;
  background: rgba(255, 152, 0, 0.15);
  color: #fff;
  transition: all 0.2s ease-in-out;
}

.search-input::placeholder {
  color: #ffcc80;
}

.search-input:focus {
  border-color: #ffa726;
  background: rgba(255, 152, 0, 0.25);
  outline: none;
}

.search-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #ffb74d;
  font-size: 1.1rem;
  pointer-events: none; /* so it doesn’t block typing */
}
</style>
<a href="../reports.php" style="position:fixed; top:20px; left:20px; padding:10px 18px; background:#e69419ff; color:#000; text-decoration:none; border-radius:8px; font-size:1rem; font-weight:600; z-index:1000; box-shadow:0 2px 10px rgba(0,0,0,0.3); display:flex; align-items:center; gap:8px;">&#8592; Back to Reports</a>
</head>
<body>
<div class="overlay">
<header>
  <h1>Room Analytics</h1>
  <p>Overview of current rooms at Hotel La Vista</p>
</header>

<div style="display:flex; gap:20px; flex-wrap:wrap;">
  <div class="card" style="height:520px; width:500px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
    <div class="chart-container" style="margin:auto; max-width:350px;">
      <canvas id="statusChart"></canvas>
    </div>
    <div style="display:flex; gap:10px; margin-top:15px;">
      <button id="PercentageBtn" style="padding:8px 15px; background:#28a745; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:0.9rem; font-weight:600;">Percentage</button>
      <button id="RoomsBtn" style="padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:0.9rem; font-weight:600;">Rooms</button>
    </div>
    <button id="allRoomsBtn" style="display:none;">All Rooms</button>
    <div id="totalRoomsDisplay" style="margin-top:15px; font-size:1.1rem; font-weight:600; color:#fff;">
      Total Rooms: 0
    </div>
  </div>

  <div class="card" style="flex:0.5; min-width:200px; height:400px;">
    <h2 style="margin-bottom:15px;">Legend</h2>
    <br>
    <div class="legend" style="display:flex; flex-direction:column; gap:10px; font-size:0.95rem;">
      <div class="legend-item"><span class="legend-color" style="background:#28a745"></span>Available</div>
      <div class="legend-item"><span class="legend-color" style="background:#6f42c1"></span>Reserved</div>
      <div class="legend-item"><span class="legend-color" style="background:#007bff"></span>Under Maintenance </div>
      <div class="legend-item"><span class="legend-color" style="background:#fd7e14"></span>Dirty</div>
      <div class="legend-item"><span class="legend-color" style="background:#dc3545"></span>Occupied</div>
      <div class="legend-item" style="margin-bottom: 15px;"><span class="legend-color" style="background:#ff69b4"></span>Cleaning</div>
      <button id="exportPdfBtn" style="padding:8px 15px; background:#ff9800; color:#000; border:none; border-radius:8px; cursor:pointer; font-size:0.9rem; font-weight:600; margin-top:15px;">Export to PDF</button>
      <br><br>
    </div>
  </div>
</div>

<!-- Table card -->
<div class="card">
  <h2 id="tableLabel" style="margin-bottom:15px;">Room Details</h2>

  <!-- Search -->
  <div id="searchBarContainer">
    <input type="text" class="search-input" data-col="0" placeholder="Search Room ID">
    <input type="text" class="search-input" data-col="1" placeholder="Search Room Number">
    <input type="text" class="search-input" data-col="2" placeholder="Search Room Type">
    <input type="text" class="search-input" data-col="4" placeholder="Search Status">

    <button id="searchBtn" style="padding:8px 15px; background:#ff9800; color:#000; border:none; border-radius:8px; cursor:pointer; font-size:0.9rem; font-weight:600;">
      Search
    </button>
  </div>

  <table>
    <thead>
          <tr id="tableHeaders">
      <th>Room ID</th>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Max Occupancy</th>
        <th>Status</th>
      <th>Price Rate</th>
      <th>Created At</th>
      <th>Updated At</th>
    </tr>
    </thead>

    <tbody id="roomTableBody">
      <!-- Table rows will be inserted here by JS -->
    </tbody>
  </table>
</div>

<button id="backToTop" style="margin: 20px auto 0 auto; display: block; padding: 10px 18px; background: #17a2b8; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 0.95rem; font-weight: 600; transition: background 0.2s ease-in-out;">Back to Top</button>

</div>
<script>
let rooms = [];
let statusCounts = {};
let totalRooms = 0;

const ctx = document.getElementById('statusChart').getContext('2d');
let showRooms = false;
let showPercentage = true;
let currentFilter = "all";
const allRoomsBtn = document.getElementById('allRoomsBtn');
const tableBody = document.getElementById('roomTableBody');
const tableLabel = document.getElementById('tableLabel');
const totalRoomsDisplay = document.getElementById('totalRoomsDisplay');

const statusColors = {
  'available': '#28a745',
  'reserved': '#6f42c1',
  'under maintenance': '#007bff',
  'dirty': '#fd7e14',
  'occupied': '#dc3545',
  'cleaning': '#ff69b4'
};

// Render table
function renderTable(data, label = "Room Details") {
  tableBody.innerHTML = "";

  if (data.length === 0) {
    tableBody.innerHTML = `<tr><td colspan="8">No room data found</td></tr>`;
    return;
  }

  // Update headers
  const headers = document.getElementById("tableHeaders");
  headers.innerHTML = `
    <th>Room ID</th>
    <th>Room Number</th>
    <th>Room Type</th>
    <th>Max Occupancy</th>
    <th>Status</th>
    <th>Price Rate</th>
    <th>Created At</th>
    <th>Updated At</th>
  `;

  for (const r of data) {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${r.room_id}</td>
      <td>${r.room_number}</td>
      <td>${r.room_type}</td>
      <td>${r.max_occupancy}</td>
      <td>${r.status}</td>
      <td>${r.price_rate}</td>
      <td>${r.created_at}</td>
      <td>${r.updated_at}</td>
    `;
    tableBody.appendChild(tr);
  }

  tableLabel.textContent = `${label} (${data.length})`;
}

// Chart helpers
function getChartData(statusCounts) {
  const labels = [], data = [], colors = [];
  for (const [status, count] of Object.entries(statusCounts)) {
    if (count > 0) {
      labels.push(status);
      data.push(count);
      colors.push(statusColors[status]);
    }
  }
  return { labels, data, colors };
}

let statusChart;

function initChart() {
  const initialData = getChartData(statusCounts);

  statusChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: initialData.labels,
      datasets: [{
        data: initialData.data,
        backgroundColor: initialData.colors
      }]
    },
    options: {
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(context) {
              let value = context.raw;
              let percentage = totalRooms ? ((value/totalRooms)*100).toFixed(1) : 0;
              let parts = [];
              if (showRooms) parts.push(value + " rooms");
              if (showPercentage) parts.push(percentage + "%");
              return context.label + ": " + parts.join(" | ");
            }
          }
        },
        datalabels: {
          display: true,
          color: "#fff",
          font: { weight: "700", size: 13 },
          formatter: function(value, context) {
            let percentage = totalRooms ? ((value/totalRooms)*100).toFixed(1) : 0;
            let parts = [];
            if (showRooms) parts.push(value + " rooms");
            if (showPercentage) parts.push(percentage + "%");
            return parts.join("\n");
          }
        }
      }
    },
    plugins: [ChartDataLabels]
  });

  ctx.canvas.onclick = function(evt) {
    const points = statusChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
    if (points.length) {
      const label = statusChart.data.labels[points[0].index].toLowerCase();
      currentFilter = label;
      let filtered = rooms.filter(r => r.status.toLowerCase() === label);
      renderTable(filtered, label.charAt(0).toUpperCase() + label.slice(1) + " Rooms");
      allRoomsBtn.style.display = "inline-block";
    }
  };
}

// Toggle buttons
document.getElementById("RoomsBtn").addEventListener("click", function() {
  showRooms = !showRooms;
  statusChart.update();
});
document.getElementById("PercentageBtn").addEventListener("click", function() {
  showPercentage = !showPercentage;
  statusChart.update();
});

allRoomsBtn.addEventListener("click", () => {
  currentFilter = "all";
  renderTable(rooms, "Room Details");
  allRoomsBtn.style.display = "none";
});

// Search functionality
document.getElementById("searchBtn").addEventListener("click", function() {
  const inputs = document.querySelectorAll(".search-input");
  const rows = document.querySelectorAll("#roomTableBody tr");

  rows.forEach(row => {
    let showRow = true;
    inputs.forEach(input => {
      const colIndex = input.getAttribute("data-col");
      const filter = input.value.toLowerCase().trim();
      const cell = row.getElementsByTagName("td")[colIndex];
      if (filter && cell && !cell.textContent.toLowerCase().includes(filter)) {
        showRow = false;
      }
    });
    row.style.display = showRow ? "" : "none";
  });
});

// Back to top
document.getElementById("backToTop").addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});

// Export to PDF
document.getElementById("exportPdfBtn").addEventListener("click", () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({
    orientation: 'portrait',
    unit: 'mm',
    format: 'a4',
    putOnlyUsedFonts: true,
    floatPrecision: 16
  });

  // Set margins (25mm on all sides)
  const margin = 25;
  const pageWidth = doc.internal.pageSize.getWidth();
  const pageHeight = doc.internal.pageSize.getHeight();
  const contentWidth = pageWidth - (margin * 2);

  // Set Times Roman font
  doc.setFont("times", "normal");

  // Add title
  doc.setFontSize(20);
  doc.setFont("times", "bold");
  doc.text("Room Occupancy Dashboard - Hotel La Vista", margin, margin + 5);
  
  // Add total rooms (prominent)
  doc.setFontSize(18);
  doc.setFont("times", "bold");
  doc.text("Total Rooms: " + totalRooms, margin, margin + 40);

  // Add summary section
  doc.setFontSize(14);
  doc.setFont("times", "bold");
  doc.text("Summary", margin, margin + 60);

  // Add timestamp
  doc.setFontSize(12);
  doc.setFont("times", "normal");
  doc.text("Generated on: " + new Date().toLocaleString(), margin, margin + 20);

  // Add report description
  let descY = margin + 70;
  doc.setFontSize(12);
  doc.setFont("times", "normal");
  const description = [
    "This report provides a comprehensive overview of room status distribution at Hotel La Vista.",
    "The data includes current room statuses, counts, and percentages for each category,",
    "helping management understand room availability and maintenance requirements."
  ];

  description.forEach(line => {
    doc.text(line, margin, descY);
    descY += 10;
  });

  // Create simple bar chart representation in PDF (black and white)
  let yPos = descY + 10;
  doc.setFontSize(14);
  doc.setFont("times", "bold");
  doc.text("Room Status Distribution:", margin, yPos);
  doc.setFontSize(12);
  doc.setFont("times", "normal");
  doc.text("Visual representation of room status percentages", margin, yPos + 8);
  yPos += 25;

  const maxCount = Math.max(...Object.values(statusCounts));
  const chartWidth = 120;
  const barHeight = 10;
  const barSpacing = 15;

  let barY = yPos;
  for (const [status, count] of Object.entries(statusCounts)) {
    if (count > 0) {
      const percentage = totalRooms ? ((count / totalRooms) * 100).toFixed(1) : 0;
      const barLength = maxCount > 0 ? (count / maxCount) * chartWidth : 0;

      // Status label
      doc.setFontSize(10);
      doc.text(`${status.charAt(0).toUpperCase() + status.slice(1)} (${percentage}%)`, margin, barY + 7);

      // Bar
      doc.rect(margin + 60, barY, barLength, barHeight, 'F'); // Filled black rectangle
      doc.rect(margin + 60, barY, chartWidth, barHeight); // Outline

      // Count outside the bar (to the right)
      doc.text(`${count}`, margin + 60 + chartWidth + 10, barY + 7);

      barY += barSpacing;
    }
  }

  // Add new page for status summary
  doc.addPage();
  yPos = margin;

  // Add status summary
  doc.setFontSize(14);
  doc.setFont("times", "bold");
  doc.text("Room Status Summary:", margin, yPos);
  doc.setFontSize(12);
  doc.setFont("times", "normal");
  doc.text("Detailed breakdown of room counts and percentages by status", margin, yPos + 8);
  yPos += 25;

  for (const [status, count] of Object.entries(statusCounts)) {
    if (count > 0) {
      const percentage = totalRooms ? ((count / totalRooms) * 100).toFixed(1) : 0;
      doc.setFontSize(12);
      doc.setFont("times", "normal");
      doc.text(`${status.charAt(0).toUpperCase() + status.slice(1)}: ${count} rooms (${percentage}%)`, margin + 10, yPos);
      yPos += 15;
    }
  }

  // Add all rooms table
  yPos += 20;
  doc.setFontSize(14);
  doc.setFont("times", "bold");
  doc.text("All Room Details:", margin, yPos);
  doc.setFontSize(12);
  doc.setFont("times", "normal");
  doc.text("Complete list of all rooms with their current status and pricing information", margin, yPos + 8);
  yPos += 25;

  // Table headers
  const headers = ["Room ID", "Room Number", "Room Type", "Status", "Price Rate"];
  let xPos = margin;
  doc.setFontSize(10);
  doc.setFont("times", "bold");
  headers.forEach(header => {
    doc.text(header, xPos, yPos);
    xPos += 30;
  });

  yPos += 10;

  // All table rows (show all rooms)
  rooms.forEach(room => {
    xPos = margin;
    const rowData = [
      room.room_id.toString(),
      room.room_number.toString(),
      room.room_type,
      room.status + " ", // Add space after status
      room.price_rate.toString()
    ];

    rowData.forEach(data => {
      doc.setFont("times", "normal");
      doc.text(data, xPos, yPos);
      xPos += 30;
    });
    yPos += 8;

    // Add new page if needed
    if (yPos > pageHeight - margin) {
      doc.addPage();
      yPos = margin;
      // Re-add headers on new page
      xPos = margin;
      doc.setFontSize(10);
      doc.setFont("times", "bold");
      headers.forEach(header => {
        doc.text(header, xPos, yPos);
        xPos += 30;
      });
      yPos += 15;
    }
  });

  // Save the PDF
  doc.save("room_analytics_report.pdf");
});


// Fetch rooms and update chart & table
async function fetchDataAndUpdate() {
  try {
    const response = await fetch("fetch_analytics.php");
    const data = await response.json();

    rooms = data.rooms;
    statusCounts = data.statusCounts;
    totalRooms = data.totalRooms;

    if (!statusChart) {
      initChart();
    } else {
      const chartData = getChartData(statusCounts);
      statusChart.data.labels = chartData.labels;
      statusChart.data.datasets[0].data = chartData.data;
      statusChart.data.datasets[0].backgroundColor = chartData.colors;
      statusChart.update();
    }

    totalRoomsDisplay.textContent = "Total Rooms: " + totalRooms;

    // Show all rooms initially with guest info where applicable
    let filtered = rooms;
    if (currentFilter !== "all") {
      filtered = filtered.filter(r => r.status.toLowerCase() === currentFilter);
      allRoomsBtn.style.display = "inline-block";
      renderTable(filtered, currentFilter.charAt(0).toUpperCase() + currentFilter.slice(1) + " Rooms");
    } else {
      allRoomsBtn.style.display = "inline-block"; // show immediately
      renderTable(filtered, "Room Details");
    }

  } catch (err) {
    console.error("Error fetching rooms:", err);
  }
}

setInterval(fetchDataAndUpdate, 5000);
fetchDataAndUpdate();
</script>

</body>
</html>