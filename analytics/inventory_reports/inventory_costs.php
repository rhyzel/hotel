<?php
require_once('../db.php');

$pie_colors = ["#4CAF50","#F44336","#2196F3","#FF9800","#9C27B0","#00ACC1","#FFC107","#E91E63","#8BC34A","#3F51B5","#607D8B","#795548","#009688"];

$months = [];
for ($i = 0; $i < 3; $i++) {
    $months[] = date('Y-m', strtotime(sprintf('first day of %s month', -$i)));
}

$selected_month = $_REQUEST['month'] ?? $months[0];

$has_history_stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'inventory_history'");
$has_history_stmt->execute();
$has_history = (int)$has_history_stmt->fetchColumn() > 0;

$month_has_data = [];
if ($has_history) {
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM inventory_history WHERE DATE_FORMAT(snapshot_date, '%Y-%m') = :ym");
    foreach ($months as $m) {
        $check_stmt->execute([':ym' => $m]);
        $month_has_data[$m] = (int)$check_stmt->fetchColumn() > 0;
    }
} else {
    foreach ($months as $m) $month_has_data[$m] = false;
}

$can_compare = $has_history && $month_has_data[$months[0]] && $month_has_data[$months[1]] && $month_has_data[$months[2]];

function fetch_category_by_month($pdo, $ym, $is_current=false) {
    if ($is_current) {
        $sql = "
            SELECT COALESCE(NULLIF(TRIM(i.category),''),'Uncategorized') AS category,
                   SUM(ih.quantity_in_stock * COALESCE(ih.unit_price, i.unit_price,0)) AS value,
                   SUM(ih.quantity_in_stock) AS qty
            FROM inventory_history ih
            JOIN inventory i ON i.item_id = ih.item_id
            WHERE ih.snapshot_date >= DATE_FORMAT(NOW(), '%Y-%m-01')
              AND ih.snapshot_date <= NOW()
            GROUP BY category
            ORDER BY category ASC
        ";
        $s = $pdo->query($sql);
    } else {
        $sql = "
            SELECT COALESCE(NULLIF(TRIM(i.category),''),'Uncategorized') AS category,
                   SUM(ih.quantity_in_stock * COALESCE(ih.unit_price, i.unit_price,0)) AS value,
                   SUM(ih.quantity_in_stock) AS qty
            FROM inventory_history ih
            JOIN inventory i ON i.item_id = ih.item_id
            WHERE DATE_FORMAT(ih.snapshot_date, '%Y-%m') = :ym
            GROUP BY category
            ORDER BY category ASC
        ";
        $s = $pdo->prepare($sql);
        $s->execute([':ym' => $ym]);
    }
    return $s->fetchAll(PDO::FETCH_ASSOC);
}

$compare_data = [];
$labels = [];
$data_per_month = [];
$backgroundColors_per_month = [];
$total_per_month = [];
$rows = [];

if ($can_compare) {
    foreach ($months as $mi => $m) {
        $is_current = ($mi === 0);
        $list = fetch_category_by_month($pdo, $m, $is_current);
        $compare_data[$m] = [];
        foreach ($list as $r) $compare_data[$m][$r['category']] = ['value' => (float)$r['value'], 'qty' => (int)$r['qty']];
        $total_per_month[$m] = array_sum(array_map(fn($v)=>$v['value'], $compare_data[$m]));
    }
    $all_categories = [];
    foreach ($compare_data as $m => $map) foreach ($map as $c => $_) if (!in_array($c, $all_categories, true)) $all_categories[] = $c;
    sort($all_categories, SORT_STRING);
    $labels = $all_categories;
    foreach ($months as $mi => $m) {
        $arr = [];
        foreach ($labels as $c) $arr[] = round($compare_data[$m][$c]['value'] ?? 0.0, 2);
        $data_per_month[$m] = $arr;
        $backgroundColors_per_month[$m] = array_map(fn($i)=> $pie_colors[$i % count($pie_colors)], array_keys($labels));
    }
} else {
    $sql = "
        SELECT COALESCE(NULLIF(TRIM(category),''),'Uncategorized') AS category,
               SUM(quantity_in_stock * COALESCE(unit_price,0)) AS value,
               SUM(quantity_in_stock) AS qty
        FROM inventory
        GROUP BY category
        ORDER BY value DESC, category ASC
    ";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $labels = [];
    $data = [];
    $backgroundColors = [];
    $legendItems = [];
    $total_value = 0.0;
    foreach ($rows as $idx => $r) {
        $labels[] = $r['category'];
        $data[] = round((float)$r['value'], 2);
        $backgroundColors[] = $pie_colors[$idx % count($pie_colors)];
        $legendItems[] = ['label'=>$r['category'],'value'=>(float)$r['value'],'qty'=>(int)$r['qty'],'color'=>$backgroundColors[count($backgroundColors)-1]];
        $total_value += (float)$r['value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Inventory & Cost Report</title>
<link rel="stylesheet" href="inventory_cost.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>

<body>
<div class="overlay">
  <div class="container">
    <h1>Inventory & Cost Report</h1>

    <div style="text-align:center;margin-bottom:12px;">
      <form method="get" style="display:inline-block;">
        <label style="margin-right:8px;color:#FF9800;font-weight:600;">Select Month</label>
        <select name="month" onchange="this.form.submit()" style="padding:6px 10px;border-radius:6px;border:none;">
          <?php foreach ($months as $m): ?>
            <option value="<?= htmlspecialchars($m) ?>" <?= $m === $selected_month ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
          <?php endforeach; ?>
        </select>
        <noscript><button type="submit" style="margin-left:8px;padding:6px 12px;">Go</button></noscript>
      </form>
    </div>

    <?php if ($can_compare): ?>
      <div class="chart-section">
        <?php foreach ($months as $mi => $m): ?>
          <div class="chart-container">
            <canvas id="bar_<?= $mi ?>"></canvas>
            <div style="text-align:center;margin-top:8px;color:#FF9800;font-weight:600;"><?= htmlspecialchars($m) ?><?= $mi === 0 ? " (MTD)" : "" ?></div>
          </div>
        <?php endforeach; ?>
        <div class="legend-box">
          <h3 style="margin-bottom:12px;font-size:18px">Legend (categories)</h3>
          <?php foreach ($labels as $i => $c): ?>
            <div class="legend-item">
              <div class="legend-color" style="background: <?= $pie_colors[$i % count($pie_colors)] ?>"></div>
              <div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis"><?= htmlspecialchars($c) ?></div>
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
              <th>Category</th>
              <?php foreach ($months as $mi => $m): ?><th><?= htmlspecialchars($m) ?><?= $mi === 0 ? " (MTD)" : "" ?></th><?php endforeach; ?>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($labels as $i => $cat): 
              $sum = 0;
            ?>
              <tr>
                <td><?= htmlspecialchars($cat) ?></td>
                <?php foreach ($months as $m): $v = $compare_data[$m][$cat]['value'] ?? 0.0; $sum += $v; ?>
                  <td>₱<?= number_format($v,2) ?></td>
                <?php endforeach; ?>
                <td>₱<?= number_format($sum,2) ?></td>
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
              <div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis">
                <?= htmlspecialchars($li['label']) ?> (Qty: <?= (int)$li['qty'] ?>, ₱<?= number_format($li['value'],2) ?>)
              </div>
            </div>
          <?php endforeach; ?>
          <div class="total-count" style="margin-top:10px;font-weight:600">Total Value: ₱<?= number_format($total_value,2) ?></div>
        </div>
      </div>
      <div class="button-group">
        <button type="button" id="toggleTable">View Table</button>
        <a href="../reports.php" style="position:absolute; top:0; right:0; padding:8px 15px; background:#e69419ff; color:#000; text-decoration:none; border-radius:8px; font-size:0.9rem; font-weight:600; margin:10px; display:flex; align-items:center; gap:6px;">&#8592; Back</button></a>
  
      </div>
      <div class="table-container" id="tableContainer" style="display:none;margin-top:18px">
        <table>
          <thead><tr><th>Category</th><th>Quantity</th><th>Total Value</th></tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr><td><?= htmlspecialchars($r['category']) ?></td><td><?= (int)$r['qty'] ?></td><td>₱<?= number_format((float)$r['value'],2) ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>

<script>
<?php if ($can_compare): ?>
<?php foreach ($months as $mi => $m):
    $vals = $data_per_month[$m];
?>
new Chart(document.getElementById('bar_<?= $mi ?>').getContext('2d'), {
    type:'bar',
    data:{
        labels: <?= json_encode($labels) ?>,
        datasets:[{
            label: "₱ Value",
            data: <?= json_encode($vals) ?>,
            backgroundColor: <?= json_encode($backgroundColors_per_month[$m]) ?>,
            borderColor: <?= json_encode($backgroundColors_per_month[$m]) ?>,
            borderWidth: 1
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{ position:'top' },
            tooltip:{ callbacks:{ label: function(context){ return "₱" + Number(context.raw).toLocaleString(); } } }
        },
        scales:{
            y:{ beginAtZero:true, ticks:{ callback: function(v){ return "₱" + v.toLocaleString(); } } }
        }
    }
});
<?php endforeach; ?>
<?php else: ?>
new Chart(document.getElementById('singleBar').getContext('2d'), {
    type:'bar',
    data:{
        labels: <?= json_encode($labels) ?>,
        datasets:[{
            label: "₱ Value",
            data: <?= json_encode($data) ?>,
            backgroundColor: <?= json_encode($backgroundColors) ?>,
            borderColor: <?= json_encode($backgroundColors) ?>,
            borderWidth: 1
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{ position:'top' },
            tooltip:{ callbacks:{ label: function(context){ return "₱" + Number(context.raw).toLocaleString(); } } }
        },
        scales:{
            y:{ beginAtZero:true, ticks:{ callback: function(v){ return "₱" + v.toLocaleString(); } } }
        }
    }
});
<?php endif; ?>

const toggleBtn = document.getElementById('toggleTable');
const tableContainer = document.getElementById('tableContainer');
let visible = false;
toggleBtn.addEventListener('click', function(){
    visible = !visible;
    tableContainer.style.display = visible ? 'block' : 'none';
    toggleBtn.textContent = visible ? 'Close Table' : 'View Table';
});
</script>
</body>
</html>
