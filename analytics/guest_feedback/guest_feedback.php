<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "hotel");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$selected_tab = $_GET['tab'] ?? 'campaigns';
$year_filter = $_GET['year'] ?? date('Y');

$campaigns = $conn->query("
    SELECT 
        name,
        status,
        sent_count,
        open_rate,
        click_rate,
        schedule
    FROM campaigns
    ORDER BY created_at DESC
");

$feedback_query = $conn->prepare("
    SELECT 
        type,
        COUNT(*) AS total_feedback,
        AVG(rating) AS avg_rating
    FROM feedback
    WHERE YEAR(created_at) = ?
    GROUP BY type
");
$feedback_query->bind_param("i", $year_filter);
$feedback_query->execute();
$feedback_data = $feedback_query->get_result();

$loyalty_data = $conn->query("
    SELECT 
        tier,
        members_count,
        points_redeemed,
        rewards_given,
        revenue_impact,
        discount_rate
    FROM loyalty_programs
    ORDER BY FIELD(tier, 'bronze','silver','gold','platinum')
");

$labels = [];
$open_rate = [];
$click_rate = [];
$sent_count = [];

if ($campaigns && $campaigns->num_rows > 0) {
    while ($c = $campaigns->fetch_assoc()) {
        $labels[] = $c['name'];
        $open_rate[] = (float)$c['open_rate'];
        $click_rate[] = (float)$c['click_rate'];
        $sent_count[] = (int)$c['sent_count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CRM Reports - Hotel La Vista</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="guest_feedback.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="overlay">
    <div style="text-align:left; margin-bottom:20px;">
        <a href="../reports.php" class="back-link">⬅ Back to Reports</a>
    </div>

    <header>
        <h1>📈 CRM Reports</h1>
        <p>Analyze campaigns, guest engagement, and loyalty performance</p>
    </header>

    <div class="tabs">
        <button class="tab-btn <?= $selected_tab == 'campaigns' ? 'active' : '' ?>" onclick="switchTab('campaigns')">📣 Campaign Performance</button>
        <button class="tab-btn <?= $selected_tab == 'feedback' ? 'active' : '' ?>" onclick="switchTab('feedback')">💬 Guest Feedback</button>
        <button class="tab-btn <?= $selected_tab == 'loyalty' ? 'active' : '' ?>" onclick="switchTab('loyalty')">🎖️ Loyalty Insights</button>
    </div>

   <?php if ($selected_tab == 'campaigns'): ?>
<div id="campaigns" class="tab-content active">
    <div class="card">
        <h2>📊 Campaign Performance Overview</h2>
        <div class="chart-container" style="height:500px;">
            <canvas id="campaignChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>📋 Campaign Details</h2>
            <button id="exportCampaigns" class="export-btn">Export CSV</button>
        </div>
        <table id="campaignTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th>Open Rate (%)</th>
                    <th>Click Rate (%)</th>
                    <th>Scheduled</th>
                </tr>
            </thead>
            <tbody>
                <?php $campaigns->data_seek(0); while ($c = $campaigns->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= $c['status'] ?></td>
                    <td><?= $c['sent_count'] ?></td>
                    <td><?= $c['open_rate'] ?></td>
                    <td><?= $c['click_rate'] ?></td>
                    <td><?= $c['schedule'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php if ($selected_tab == 'feedback'): ?>
<div id="feedback" class="tab-content active">
    <form method="GET" class="filter-form">
        <input type="hidden" name="tab" value="feedback">

        <?php
        $year_filter = $_GET['year'] ?? date('Y');
        $month_filter = $_GET['month'] ?? date('m');
        ?>

        <label for="year">Select Year:</label>
        <select name="year" id="year" onchange="this.form.submit()">
            <?php
            $years = $conn->query("SELECT DISTINCT YEAR(created_at) AS year FROM feedback ORDER BY year DESC");
            while ($y = $years->fetch_assoc()):
            ?>
                <option value="<?= $y['year'] ?>" <?= $year_filter == $y['year'] ? 'selected' : '' ?>><?= $y['year'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="month">Select Month:</label>
        <select name="month" id="month" onchange="this.form.submit()">
            <?php
            $months = [
                '01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June',
                '07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'
            ];
            foreach ($months as $num => $name):
            ?>
                <option value="<?= $num ?>" <?= $month_filter == $num ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php
    $month_name = date('F', mktime(0,0,0,$month_filter,10));

    $ratings = [1, 2, 3, 4, 5];
    $rating_counts = array_fill(1, 5, 0);

    $feedback_stats = $conn->prepare("
        SELECT rating, COUNT(*) AS total
        FROM feedback
        WHERE YEAR(created_at)=? AND MONTH(created_at)=?
        GROUP BY rating
    ");
    $feedback_stats->bind_param("ss", $year_filter, $month_filter);
    $feedback_stats->execute();
    $result = $feedback_stats->get_result();
    while ($row = $result->fetch_assoc()) {
        $rating_counts[$row['rating']] = (int)$row['total'];
    }

    $datasets = [
        [
            'label' => 'Number of Ratings',
            'data' => array_values($rating_counts),
            'backgroundColor' => [
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 159, 64, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(54, 162, 235, 0.7)'
            ],
            'borderRadius' => 8,
            'barPercentage' => 0.6
        ]
    ];
    ?>

    <div class="card">
        <h2>⭐ Guest Ratings Breakdown — <?= $month_name ?> <?= $year_filter ?></h2>
        <div class="chart-container" style="height:400px;">
            <canvas id="feedbackChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>📋 Guest Feedback — <?= $month_name ?> <?= $year_filter ?></h2>
            <button id="exportFeedback" class="export-btn">Export CSV</button>
        </div>
        <table id="feedbackTable">
            <thead>
                <tr><th>Guest</th><th>Rating</th><th>Message</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php
            $feedback_list = $conn->prepare("
                SELECT guest_name, rating, message, created_at
                FROM feedback
                WHERE YEAR(created_at)=? AND MONTH(created_at)=?
                ORDER BY created_at DESC
            ");
            $feedback_list->bind_param("ss", $year_filter, $month_filter);
            $feedback_list->execute();
            $feedback_result = $feedback_list->get_result();
            while ($f = $feedback_result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($f['guest_name']) ?></td>
                    <td><?= str_repeat('⭐', $f['rating']) ?></td>
                    <td><?= htmlspecialchars($f['message']) ?></td>
                    <td><?= date('M d, Y', strtotime($f['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('feedbackChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
            datasets: <?= json_encode($datasets) ?>
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#f7f3ef' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: {
                    ticks: { color: '#f7f3ef' },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Export CSV for feedback table
    document.getElementById('exportFeedback').addEventListener('click', function() {
        const table = document.getElementById('feedbackTable');
        let csv = [];
        for (let row of table.querySelectorAll('tr')) {
            let cols = Array.from(row.querySelectorAll('th, td')).map(cell => `"${cell.innerText}"`);
            csv.push(cols.join(','));
        }
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'feedback_<?= strtolower($month_name) ?>_<?= $year_filter ?>.csv';
        a.click();
    });
    </script>
</div>
<?php endif; ?>

<?php if ($selected_tab == 'loyalty'): ?>
<div id="loyalty" class="tab-content active">
    <div class="card">
        <h2>🎖️ Loyalty Program Overview</h2>
        <div class="chart-container" style="height:450px;">
            <canvas id="loyaltyChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>📋 Tier Details</h2>
            <button id="exportLoyaltyBtn" class="export-btn">Export CSV</button>
        </div>
        <table id="loyaltyTable">
            <thead>
                <tr>
                    <th>Tier</th>
                    <th>Members</th>
                    <th>Points Redeemed</th>
                    <th>Rewards Given</th>
                    <th>Revenue Impact</th>
                    <th>Discount (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($l = $loyalty_data->fetch_assoc()): ?>
                    <tr>
                        <td><?= ucfirst($l['tier']) ?></td>
                        <td><?= $l['members_count'] ?></td>
                        <td><?= $l['points_redeemed'] ?></td>
                        <td><?= $l['rewards_given'] ?></td>
                        <td>₱<?= number_format($l['revenue_impact'], 2) ?></td>
                        <td><?= $l['discount_rate'] ?>%</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById("exportLoyaltyBtn").addEventListener("click", function() {
    const table = document.getElementById("loyaltyTable");
    let csv = [];
    for (let i = 0; i < table.rows.length; i++) {
        let row = [], cols = table.rows[i].querySelectorAll("th, td");
        for (let j = 0; j < cols.length; j++)
            row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
        csv.push(row.join(","));
    }

    const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.download = "loyalty_tier_details.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.click();
});
</script>
<?php endif; ?>



    <button id="backToTop" onclick="scrollToTop()">Back to Top</button>
</div>

<script>
function switchTab(tab){window.location.href='?tab='+tab}
const campaignCtx=document.getElementById('campaignChart');
if(campaignCtx){
    new Chart(campaignCtx,{type:'bar',data:{labels:<?= json_encode($labels) ?>,datasets:[{label:'Open Rate (%)',data:<?= json_encode($open_rate) ?>,backgroundColor:'#17a2b8'},{label:'Click Rate (%)',data:<?= json_encode($click_rate) ?>,backgroundColor:'#e69419'}]},options:{responsive:true,plugins:{legend:{labels:{color:'white'}}},scales:{x:{ticks:{color:'white'}},y:{ticks:{color:'white'}}}}});
}
const feedbackCtx=document.getElementById('feedbackChart');
if(feedbackCtx){
    new Chart(feedbackCtx,{type:'pie',data:{labels:[<?php $feedback_query->execute();$feedback_data=$feedback_query->get_result();$types=[];while($f=$feedback_data->fetch_assoc()){$types[]="'".$f['type']."'";}echo implode(',',$types);?>],datasets:[{data:[<?php $feedback_query->execute();$feedback_data=$feedback_query->get_result();$vals=[];while($f=$feedback_data->fetch_assoc()){$vals[]=$f['total_feedback'];}echo implode(',',$vals);?>],backgroundColor:['#17a2b8','#e69419','#dc3545','#28a745']}]},options:{plugins:{legend:{labels:{color:'white'}}}}});
}
const loyaltyCtx=document.getElementById('loyaltyChart');
if(loyaltyCtx){
    new Chart(loyaltyCtx,{type:'bar',data:{labels:['Bronze','Silver','Gold','Platinum'],datasets:[{label:'Members',data:[<?php $loyalty_data->data_seek(0);$m=[];while($l=$loyalty_data->fetch_assoc()){$m[]=$l['members_count'];}echo implode(',',$m);?>],backgroundColor:'#e69419'}]},options:{plugins:{legend:{display:false}},scales:{x:{ticks:{color:'white'}},y:{ticks:{color:'white'}}}}});
}
function scrollToTop(){window.scrollTo({top:0,behavior:'smooth'})}

document.getElementById('exportCampaigns').addEventListener('click', function() {
    const table = document.getElementById('campaignTable');
    let csv = [];
    for (let row of table.querySelectorAll('tr')) {
        let cols = Array.from(row.querySelectorAll('th, td')).map(cell => `"${cell.innerText}"`);
        csv.push(cols.join(','));
    }
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'campaign_details.csv';
    a.click();
});


</script>
</body>
</html>
