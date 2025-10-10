<?php
require_once('../db.php');

$pie_colors = ["#4CAF50","#F44336","#2196F3","#FF9800","#9C27B0","#00ACC1","#FFC107","#E91E63","#8BC34A","#3F51B5","#607D8B","#795548","#009688"];

$months = [];
for ($i = 0; $i < 3; $i++) {
    $months[] = date('Y-m', strtotime("-$i month"));
}
$selected_month = $_REQUEST['month'] ?? $months[0];

$sql = "
    SELECT id,
           room_number,
           feedback,
           rating,
           date_submitted
    FROM guest_feedback
    WHERE DATE_FORMAT(date_submitted, '%Y-%m') = :ym
    ORDER BY date_submitted DESC
";
$s = $pdo->prepare($sql);
$s->execute([':ym' => $selected_month]);
$rows = $s->fetchAll(PDO::FETCH_ASSOC);

$count_sql = "
    SELECT COALESCE(NULLIF(TRIM(rating),''),'Unrated') AS rating,
           COUNT(*) AS total
    FROM guest_feedback
    WHERE DATE_FORMAT(date_submitted, '%Y-%m') = :ym
    GROUP BY rating
    ORDER BY total DESC
";
$c = $pdo->prepare($count_sql);
$c->execute([':ym' => $selected_month]);
$count_rows = $c->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];
$backgroundColors = [];
$legendItems = [];
$total_feedback = 0;
foreach ($count_rows as $idx => $r) {
    $labels[] = $r['rating'];
    $data[] = (int)$r['total'];
    $backgroundColors[] = $pie_colors[$idx % count($pie_colors)];
    $legendItems[] = [
        'label' => $r['rating'],
        'value' => (int)$r['total'],
        'color' => $backgroundColors[count($backgroundColors)-1]
    ];
    $total_feedback += (int)$r['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Guest Feedback Report</title>
<link rel="stylesheet" href="guest_feedback.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="overlay">
  <div class="container">
    <h1>Guest Feedback Report</h1>

    <div style="text-align:center;margin-bottom:12px;">
      <form method="get" style="display:inline-block;">
        <label style="margin-right:8px;color:#FF9800;font-weight:600;">Select Month</label>
        <select name="month" onchange="this.form.submit()" style="padding:6px 10px;border-radius:6px;border:none;">
          <?php foreach ($months as $m): ?>
            <option value="<?= htmlspecialchars($m) ?>" <?= $m === $selected_month ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <div class="chart-section">
      <div class="chart-container">
        <canvas id="singleBar"></canvas>
      </div>
      <div class="legend-box">
        <h3 style="margin-bottom:12px;font-size:18px">Legend</h3>
        <?php foreach ($legendItems as $li): ?>
          <div class="legend-item">
            <div class="legend-color" style="background-color: <?= htmlspecialchars($li['color']) ?>"></div>
            <div><?= htmlspecialchars($li['label']) ?> (<?= (int)$li['value'] ?> feedbacks)</div>
          </div>
        <?php endforeach; ?>
        <div class="total-count" style="margin-top:10px;font-weight:600">Total Feedback: <?= number_format($total_feedback) ?></div>
      </div>
    </div>

    <div class="button-group">
      <button type="button" id="toggleTable">View Table</button>
      <a href="../reports.php" style="position:absolute; top:0; right:0; padding:8px 15px; background:#e69419ff; color:#000; text-decoration:none; border-radius:8px; font-size:0.9rem; font-weight:600; margin:10px; display:flex; align-items:center; gap:6px;">&#8592; Back</a>
    </div>

    <div class="table-container" id="tableContainer" style="display:none;margin-top:18px">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Room Number</th>
            <th>Rating</th>
            <th>Submitted At</th>
            <th>Feedback</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['id']) ?></td>
              <td><?= htmlspecialchars($r['room_number']) ?></td>
              <td><?= htmlspecialchars($r['rating']) ?></td>
              <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($r['date_submitted']))) ?></td>
              <td><?= htmlspecialchars($r['feedback']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
new Chart(document.getElementById('singleBar').getContext('2d'), {
    type:'bar',
    data:{
        labels: <?= json_encode($labels) ?>,
        datasets:[{
            label: "Feedback Count",
            data: <?= json_encode($data) ?>,
            backgroundColor: <?= json_encode($backgroundColors) ?>,
            borderColor: <?= json_encode($backgroundColors) ?>,
            borderWidth: 1
        }]
    },
    options:{
        responsive:true,
        plugins:{ legend:{ display:false } },
        scales:{ y:{ beginAtZero:true } }
    }
});

const toggleBtn = document.getElementById('toggleTable');
if (toggleBtn) {
  const tableContainer = document.getElementById('tableContainer');
  let visible = false;
  toggleBtn.addEventListener('click', function(){
      visible = !visible;
      tableContainer.style.display = visible ? 'block' : 'none';
      toggleBtn.textContent = visible ? 'Close Table' : 'View Table';
  });
}
</script>
</body>
</html>
