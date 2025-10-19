<?php
include __DIR__ . '/../db.php';

$status_filter = $_GET['status'] ?? 'all';

if ($status_filter === 'all') {
    $result = $conn->prepare("SELECT * FROM rooms ORDER BY room_number ASC");
} else {
    $result = $conn->prepare("SELECT * FROM rooms WHERE status=? ORDER BY room_number ASC");
    $result->bind_param("s", $status_filter);
}
$result->execute();
$rooms = $result->get_result();

$statusColors = [
    'available' => '#f5f0e1',
    'occupied' => '#5d3a1a',
    'reserved' => '#d8b59e',
    'under maintenance' => '#8b4513',
    'dirty' => '#cfa16f'
];

$statuses = [];
$totals = [];
$countResult = $conn->query("SELECT status, COUNT(*) as total FROM rooms GROUP BY status");
while ($row = $countResult->fetch_assoc()) {
    $statuses[] = ucfirst($row['status']);
    $totals[] = $row['total'];
}

$roomsByFloor = [];
while ($row = $rooms->fetch_assoc()) {
    $floor = intval(substr($row['room_number'], 0, 1));
    $roomsByFloor[$floor][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Room Status | Housekeeping</title>
<link rel="stylesheet" href="room_status.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-bed"></i> Room Status</h1>
    <p>View and manage the status of hotel rooms.</p>
    <div class="filter-row">
        <a href="http://localhost/hotel/housekeeping/housekeeping.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
        <form method="GET" class="filter-form">
            <select name="status" onchange="this.form.submit()">
                <option value="all" <?= ($status_filter == 'all') ? 'selected' : '' ?>>All</option>
                <?php foreach (array_keys($statusColors) as $status): ?>
                    <option value="<?= $status ?>" <?= ($status_filter == $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php foreach ($roomsByFloor as $floor => $floorRooms): ?>
    <div class="floor-section">
        <div class="floor-title">Floor <?= $floor ?></div>
        <div class="room-grid">
            <?php foreach ($floorRooms as $room): ?>
            <div class="room-card">
                <div class="room-number"><?= htmlspecialchars($room['room_number']) ?></div>
                <div class="room-type"><?= htmlspecialchars($room['room_type']) ?></div>
                <div class="room-status" style="background-color: <?= $statusColors[$room['status']] ?>"><?= ucfirst($room['status']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div id="statusModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <canvas id="roomChart"></canvas>
        <div class="legend-container">
            <?php foreach ($statuses as $i => $s): ?>
            <div class="legend-item">
                <span class="legend-color" style="background-color: <?= $statusColors[strtolower($s)] ?>"></span>
                <span><?= $s ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById("statusModal");
const span = document.getElementsByClassName("close")[0];
modal.style.display = "flex";

span.onclick = function() { modal.style.display = "none"; }
window.onclick = function(e) { if (e.target == modal) modal.style.display = "none"; }

const ctx = document.getElementById('roomChart').getContext('2d');
const roomChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($statuses) ?>,
        datasets: [{
            data: <?= json_encode($totals) ?>,
            backgroundColor: <?= json_encode(array_map(fn($s) => $statusColors[strtolower($s)], $statuses)) ?>,
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: { plugins: { legend: { display: false } } }
});

document.addEventListener('DOMContentLoaded', () => {
  const floorSections = document.querySelectorAll('.floor-section');
  const modal = document.getElementById('statusModal');
  const closeBtn = document.querySelector('.close');
  const chartBtn = document.createElement('button');
  chartBtn.textContent = 'Show Overview';
  chartBtn.classList.add('chart-btn');
  document.querySelector('.container').insertBefore(chartBtn, document.querySelector('.filter-row').nextSibling);

  floorSections.forEach(section => {
    const title = section.querySelector('.floor-title');
    title.addEventListener('click', () => {
      section.classList.toggle('active');
    });
  });

  chartBtn.addEventListener('click', () => {
    modal.style.display = 'flex';
  });

  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  window.addEventListener('click', e => {
    if (e.target === modal) modal.style.display = 'none';
  });
});
</script>
</body>
</html>
<?php $conn->close(); ?>
