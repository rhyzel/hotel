<?php
include __DIR__ . '/../db.php';

if (isset($_POST['delete_performance']) && isset($_POST['performance_id'])) {
    $performanceId = $_POST['performance_id'];
    $stmt = $conn->prepare("DELETE FROM staff_performance WHERE id = ?");
    $stmt->bind_param("i", $performanceId);
    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

if (isset($_POST['add_performance'])) {
    $staffId = $_POST['staff_id'];
    $score = $_POST['score'];
    $selectedRemark = $_POST['remark_select'];
    $notes = trim($_POST['notes']);

    // Combine selected remark and notes into the single remarks column
    $remarks = $selectedRemark;
    if (!empty($notes)) {
        // If a remark was selected, add a separator; otherwise, just use the notes as the main remark
        $remarks .= !empty($selectedRemark) ? " - " . $notes : $notes;
    }
    
    // Ensure remarks is not empty if score is provided (though the database constraint might allow it)
    if (empty($remarks)) {
        $remarks = "No specific remarks provided.";
    }
    
    $stmt = $conn->prepare("INSERT INTO staff_performance (staff_id, score, remarks) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $staffId, $score, $remarks);
    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<script>alert('Error adding record: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

$sql = "
    SELECT 
        sp.id AS performance_id,
        sp.staff_id,
        CONCAT(s.first_name, ' ', s.last_name) AS full_name,
        sp.score,
        sp.remarks,
        sp.created_at
    FROM 
        staff_performance sp
    JOIN 
        staff s ON sp.staff_id = s.staff_id
    WHERE
        s.department_name = 'Housekeeping'
    ORDER BY
        sp.created_at DESC
";
$result = $conn->query($sql);
$staffPerformance = $result->fetch_all(MYSQLI_ASSOC);

$staffListSql = "SELECT staff_id, CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE employment_status = 'Active' AND department_name = 'Housekeeping' ORDER BY full_name ASC";
$staffListResult = $conn->query($staffListSql);
$staffList = $staffListResult->fetch_all(MYSQLI_ASSOC);

$scoreColors = [
    'Outstanding' => '#4CAF50',
    'Very Good' => '#d2b48c',
    'Good' => '#3498db',
    'Satisfactory' => '#e67e22',
    'Needs Improvement' => '#e74c3c'
];

// Predefined Remarks for the dropdown
$predefinedRemarks = [
    'Best Performance',
    'Great Job',
    'Good Effort',
    'Satisfactory Work',
    'Needs Improvement on Cleanliness',
    'Punctuality Issue'
];

function getScoreColor($score, $scoreColors) {
    if ($score >= 90) return $scoreColors['Outstanding'];
    if ($score >= 85) return $scoreColors['Very Good'];
    if ($score >= 80) return $scoreColors['Good'];
    if ($score >= 75) return $scoreColors['Satisfactory'];
    return $scoreColors['Needs Improvement'];
}

function getScoreCategory($score) {
    if ($score >= 90) return 'Outstanding';
    if ($score >= 85) return 'Very Good';
    if ($score >= 80) return 'Good';
    if ($score >= 75) return 'Satisfactory';
    return 'Needs Improvement';
}

$performanceByStaff = [];
foreach ($staffPerformance as $record) {
    $staffID = $record['staff_id'];
    if (!isset($performanceByStaff[$staffID])) {
        $performanceByStaff[$staffID] = [
            'full_name' => $record['full_name'],
            'records' => []
        ];
    }
    $performanceByStaff[$staffID]['records'][] = $record;
}

$lineChartData = [];
$monthLabels = [];
$now = new DateTime();

for ($i = 5; $i >= 0; $i--) {
    $month = (clone $now)->modify("-$i months");
    $label = $month->format('M Y');
    $monthLabels[] = $label;
    $lineChartData[$label] = array_fill_keys(array_keys($scoreColors), 0);
}

$trendSql = "
    SELECT 
        score, 
        DATE_FORMAT(created_at, '%b %Y') as month_year
    FROM 
        staff_performance sp
    JOIN
        staff s ON sp.staff_id = s.staff_id
    WHERE
        s.department_name = 'Housekeeping' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    ORDER BY
        created_at ASC
";
$trendResult = $conn->query($trendSql);
$trendData = $trendResult->fetch_all(MYSQLI_ASSOC);

foreach ($trendData as $row) {
    $category = getScoreCategory($row['score']);
    $month = $row['month_year'];
    if (isset($lineChartData[$month])) {
        $lineChartData[$month][$category]++;
    }
}

$chartDatasets = [];
foreach ($scoreColors as $category => $color) {
    $dataPoints = [];
    foreach ($monthLabels as $month) {
        $dataPoints[] = $lineChartData[$month][$category] ?? 0;
    }
    $chartDatasets[] = [
        'label' => $category,
        'data' => $dataPoints,
        'borderColor' => $color,
        'backgroundColor' => 'transparent',
        'tension' => 0.3,
        'borderWidth' => 3,
        'pointRadius' => 4,
        'pointHoverRadius' => 6,
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Housekeeping Performance Tracking</title>
<link rel="stylesheet" href="hp_performance.css">

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.modal.show { display: flex !important; }
#chartModal .modal-content-grid {
    display: block;
}
.stats-in-modal { 
    display: none;
}
#chartModal .modal-content.glass {
    max-width: 900px;
    width: 90%;
}
</style>
</head>
<body>
<div class="overlay">
    <div class="container">
        
        <h1 style="text-align: center; margin: 0 0 30px 0; font-size: 2.5rem; color: var(--color-light-accent);">Housekeeping Performance Tracking</h1>

        <div class="button-row-center">
            
            <a href="../housekeeping.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Back</a>

            <button class="chart-btn" id="showChartBtn" style="margin: 0;">Show Performance Dashboard</button>
            
            <button id="openModal" class="glass-btn add-record-btn">
                <i class="fas fa-plus"></i> Add Performance Record
            </button>
        </div>

        <div class="performance-grid-container">
            <?php foreach ($performanceByStaff as $staffID => $data): ?>
            <div class="staff-section">
                <div class="staff-title"><?= htmlspecialchars($data['full_name']) ?> (<?= count($data['records']) ?> Records)</div>
                <div class="record-grid">
                    <?php foreach ($data['records'] as $record): ?>
                    <div class="record-card">
                        <div class="record-id">#<?= $record['performance_id'] ?></div>
                        <div class="record-task"><i class="fas fa-user-tag"></i> <?= htmlspecialchars($record['staff_id']) ?></div>
                        <div class="record-date"><i class="fas fa-calendar-alt"></i> <?= date('M j, Y', strtotime($record['created_at'])) ?></div>
                        <div class="record-rating" style="background-color: <?= getScoreColor($record['score'], $scoreColors) ?>">
                            Score: <?= round($record['score']) ?>%
                        </div>
                        <div class="record-detail">Category: <?= getScoreCategory($record['score']) ?></div>
                        <div class="record-feedback">"<?= htmlspecialchars($record['remarks'] ?? 'No remarks provided.') ?>"</div>
                        
                        <div class="card-actions">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="performance_id" value="<?= $record['performance_id'] ?>">
                                <button type="submit" name="delete_performance" class="delete-btn" onclick="return confirm('Permanently delete this performance record?');"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                            <button class="edit-btn" data-id="<?= $record['performance_id'] ?>" data-staff-id="<?= $record['staff_id'] ?>" data-score="<?= $record['score'] ?>" data-remarks="<?= htmlspecialchars($record['remarks'] ?? '') ?>"><i class="fas fa-edit"></i> Edit</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="chartModal" class="modal">
            <div class="modal-content glass" style="max-width: 950px; width: 95%;">
                <span class="close-modal close">&times;</span>
                <h2>Housekeeping Performance Dashboard</h2>
                
                <div class="modal-content-grid">
                    <div class="stats-in-modal">
                        </div>
    
                    <div class="chart-section">
                        <h3 style="color:var(--text-light); border-bottom:1px dashed rgba(255,255,255,0.2); padding-bottom:10px;">6-Month Performance Trend by Category</h3>
                        <div class="chart-container" style="background: none; border: none; box-shadow: none; padding: 0;">
                            <canvas id="staffChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="legend-container">
                    <?php foreach ($scoreColors as $category => $color): ?>
                    <div class="legend-item"><span class="legend-color" style="background-color: <?= $color ?>"></span><span><?= $category ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="addModal" class="modal">
            <div class="modal-content glass">
                <span class="close add-close">&times;</span>
                <h2>Add Performance Record</h2>
                <form method="POST">
                    <label for="staff_id">Staff Name:</label>
                    <select name="staff_id" id="staff_id" required>
                        <option value="">Select Staff</option>
                        <?php foreach($staffList as $s) echo "<option value='{$s['staff_id']}'>{$s['full_name']}</option>"; ?>
                    </select>
                    <label for="score">Score (%):</label>
                    <input type="number" id="score" name="score" min="1" max="100" step="0.01" placeholder="Score (e.g., 85.50)" required>
                    
                    <label for="remark_select">Select Predefined Remark:</label>
                    <select name="remark_select" id="remark_select">
                        <option value="">-- Optional Predefined Remark --</option>
                        <?php foreach($predefinedRemarks as $remark) echo "<option value='" . htmlspecialchars($remark) . "'>{$remark}</option>"; ?>
                    </select>
                    
                    <label for="notes">Notes (Optional Free-Form Text):</label>
                    <textarea id="notes" name="notes" placeholder="Add specific details or additional notes..."></textarea>
                    
                    <button type="submit" name="add_performance"><i class="fas fa-plus"></i> Add Record</button>
                </form>
            </div>
        </div>
    </div> 
</div> 
<script>
document.addEventListener('DOMContentLoaded', ()=>{
    const chartModal = document.getElementById('chartModal');
    const addModal = document.getElementById('addModal');
    const showChartBtn = document.getElementById('showChartBtn');
    const openAddBtn = document.getElementById('openModal');
    const staffSections = document.querySelectorAll('.staff-section');
    const editBtns = document.querySelectorAll('.edit-btn'); 

    const showModal = (modalElement) => { modalElement.classList.add('show'); document.body.style.overflow = 'hidden'; };
    const hideModal = (modalElement) => { modalElement.classList.remove('show'); document.body.style.overflow = ''; };

    const initChart = () => {
        const ctx = document.getElementById('staffChart').getContext('2d');
        const existingChart = Chart.getChart(ctx);
        if (existingChart) existingChart.destroy();
        
        new Chart(ctx, {
            type:'line',
            data:{
                labels: <?= json_encode($monthLabels) ?>,
                datasets: <?= json_encode($chartDatasets) ?>
            },
            options:{
                responsive:true,
                maintainAspectRatio:true,
                aspectRatio: 2.2,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Records',
                            color: '#f7f3ef'
                        },
                        ticks: {
                            color: '#f7f3ef',
                            callback: function(value) { if (Number.isInteger(value)) return value; }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#f7f3ef'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                plugins:{ 
                    legend:{ 
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#f7f3ef'
                        }
                    }, 
                    tooltip:{ 
                        backgroundColor:'rgba(40, 28, 20, 0.9)', 
                        titleColor: '#d2b48c', 
                        bodyColor: '#f7f3ef',
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y + ' Records';
                                return label;
                            }
                        }
                    } 
                }
            }
        });
    };

    showChartBtn.addEventListener('click', () => { initChart(); showModal(chartModal); });
    chartModal.querySelector('.close-modal').addEventListener('click', () => hideModal(chartModal));
    window.addEventListener('click', e => { if(e.target === chartModal) hideModal(chartModal); });

    openAddBtn.addEventListener('click', () => showModal(addModal));
    addModal.querySelector('.add-close').addEventListener('click', () => hideModal(addModal));
    window.addEventListener('click', e => { if(e.target === addModal) hideModal(addModal); });

    staffSections.forEach(section => {
        const title = section.querySelector('.staff-title');
        title.addEventListener('click', () => {
            section.classList.toggle('active');
        });
    });
    
    // Edit Button Action (Placeholder)
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const staffId = this.getAttribute('data-staff-id');
            const score = this.getAttribute('data-score');
            const remarks = this.getAttribute('data-remarks');
            alert(`Edit Record ID: ${id}\nStaff ID: ${staffId}\nScore: ${score}%\nRemarks: "${remarks}"\n\n(A dedicated Edit Modal/Form is required for full functionality.)`);
        });
    });

});
</script>
</body>
</html>
<?php $conn->close(); ?>