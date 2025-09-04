<?php
require 'db.php';

$pie_colors = ["#4CAF50","#F44336","#2196F3","#FF9800","#9C27B0","#00ACC1","#FFC107","#E91E63","#8BC34A","#3F51B5","#607D8B","#795548","#009688"];

$months = [];
for ($i = 0; $i < 3; $i++) {
    $months[] = date('Y-m', strtotime(sprintf('first day of %s month', -$i)));
}
$selected_month = $_REQUEST['month'] ?? $months[0];

$has_feedback_stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'guest_feedback'");
$has_feedback_stmt->execute();
$has_feedback = (int)$has_feedback_stmt->fetchColumn() > 0;

$month_has_data = [];
if ($has_feedback) {
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM guest_feedback WHERE DATE_FORMAT(submitted_at, '%Y-%m') = :ym");
    foreach ($months as $m) {
        $check_stmt->execute([':ym' => $m]);
        $month_has_data[$m] = (int)$check_stmt->fetchColumn() > 0;
    }
} else {
    foreach ($months as $m) $month_has_data[$m] = false;
}

$can_compare = $has_feedback && $month_has_data[$months[0]] && $month_has_data[$months[1]] && $month_has_data[$months[2]];

function fetch_feedback_by_month($pdo, $ym) {
    $sql = "
        SELECT COALESCE(NULLIF(TRIM(rating),''),'Unrated') AS rating,
               COUNT(*) AS total
        FROM guest_feedback
        WHERE DATE_FORMAT(submitted_at, '%Y-%m') = :ym
        GROUP BY rating
        ORDER BY rating ASC
    ";
    $s = $pdo->prepare($sql);
    $s->execute([':ym' => $ym]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}

$compare_data = [];
$labels = [];
$data_per_month = [];
$backgroundColors_per_month = [];
$total_per_month = [];
$rows = [];

if ($can_compare) {
    foreach ($months as $m) {
        $list = fetch_feedback_by_month($pdo, $m);
        $compare_data[$m] = [];
        foreach ($list as $r) $compare_data[$m][$r['rating']] = (int)$r['total'];
        $total_per_month[$m] = array_sum($compare_data[$m]);
    }
    $all_ratings = [];
    foreach ($compare_data as $m => $map) foreach ($map as $c => $_) if (!in_array($c, $all_ratings, true)) $all_ratings[] = $c;
    sort($all_ratings, SORT_STRING);
    $labels = $all_ratings;
    foreach ($months as $mi => $m) {
        $arr = [];
        foreach ($labels as $c) $arr[] = $compare_data[$m][$c] ?? 0;
        $data_per_month[$m] = $arr;
        $backgroundColors_per_month[$m] = array_map(fn($i)=> $pie_colors[$i % count($pie_colors)], array_keys($labels));
    }
} else {
    $sql = "
        SELECT gf.rating,
               gf.feedback_text,
               gf.booking_id AS room_number
        FROM guest_feedback gf
        WHERE DATE_FORMAT(gf.submitted_at, '%Y-%m') = :ym
        ORDER BY gf.rating DESC
    ";
    $s = $pdo->prepare($sql);
    $s->execute([':ym' => $selected_month]);
    $rows = $s->fetchAll(PDO::FETCH_ASSOC);

    $count_sql = "
        SELECT COALESCE(NULLIF(TRIM(rating),''),'Unrated') AS rating,
               COUNT(*) AS total
        FROM guest_feedback
        WHERE DATE_FORMAT(submitted_at, '%Y-%m') = :ym
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
        $legendItems[] = ['label'=>$r['rating'],'value'=>(int)$r['total'],'color'=>$backgroundColors[count($backgroundColors)-1]];
        $total_feedback += (int)$r['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Guest Feedback Report</title>
<link rel="stylesheet" href="inventory_cost.css">
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

    <?php if ($can_compare): ?>
      <div class="chart-section">
        <?php foreach ($months as $mi => $m): ?>
          <div class="chart-container">
            <canvas id="bar_<?= $mi ?>"></canvas>
            <div style="text-align:center;margin-top:8px;color:#FF9800;font-weight:600;"><?= htmlspecialchars($m) ?></div>
          </div>
        <?php endforeach; ?>
        <div class="legend-box">
          <h3 style="margin-bottom:12px;font-size:18px">Legend (Ratings)</h3>
          <?php foreach ($labels as $i => $c): ?>
            <div class="legend-item">
              <div class="legend-color" style="background: <?= $pie_colors[$i % count($pie_colors)] ?>"></div>
              <div><?= htmlspecialchars($c) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="button-group">
        <button type="button" id="toggleTable">View Table</button>
        <a href="reports.php"><button type="button">⬅️ Back to Dashboard</button></a>
      </div>
      <div class="table-container" id="tableContainer" style="display:none;margin-top:18px">
        <table>
          <thead>
            <tr>
              <th>Rating</th>
              <?php foreach ($months as $m): ?><th><?= htmlspecialchars($m) ?></th><?php endforeach; ?>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($labels as $i => $rating): 
              $sum = 0;
            ?>
              <tr>
                <td><?= htmlspecialchars($rating) ?></td>
                <?php foreach ($months as $m): $v = $compare_data[$m][$rating] ?? 0; $sum += $v; ?>
                  <td><?= (int)$v ?></td>
                <?php endforeach; ?>
                <td><?= (int)$sum ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
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
        <a href="reports.php"><button type="button">⬅️ Back to Dashboard</button></a>
      </div>
      <div class="table-container" id="tableContainer" style="display:none;margin-top:18px">
        <table>
          <thead><tr><th>Rating</th><th>Room Number</th><th>Feedback</th></tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['rating']) ?></td>
                <td><?= htmlspecialchars($r['room_number']) ?></td>
                <td><?= htmlspecialchars($r['feedback_text']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>

<script>
<?php if ($can_compare): ?>
<?php foreach ($months as $mi => $m): ?>
new Chart(document.getElementById('bar_<?= $mi ?>').getContext('2d'), {
    type:'bar',
    data:{
        labels: <?= json_encode($labels) ?>,
        datasets:[{
            label: "Feedback Count",
            data: <?= json_encode($data_per_month[$m]) ?>,
            backgroundColor: <?= json_encode($backgroundColors_per_month[$m]) ?>,
            borderColor: <?= json_encode($backgroundColors_per_month[$m]) ?>,
            borderWidth: 1
        }]
    },
    options:{
        responsive:true,
        plugins:{ legend:{ display:false } },
        scales:{ y:{ beginAtZero:true } }
    }
});
<?php endforeach; ?>
<?php else: ?>
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
<?php endif; ?>

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
