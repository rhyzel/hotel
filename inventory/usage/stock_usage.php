<?php
session_start();
require '../db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_GET['delete_id'], $_GET['type'], $_GET['token'])) {
    $id = (int)$_GET['delete_id'];
    $type = $_GET['type'];
    if ($id > 0 && hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        if ($type === 'ingredient') {
            $stmt = $pdo->prepare("DELETE FROM ingredient_usage WHERE usage_id = :id");
        } else {
            $stmt = $pdo->prepare("DELETE FROM stock_usage WHERE usage_id = :id");
        }
        $stmt->execute([':id' => $id]);
        $_SESSION['success'] = ucfirst($type) . " usage log deleted.";
    } else {
        $_SESSION['error'] = "Invalid deletion request.";
    }
    header("Location: ingredient_usage.php");
    exit;
}

$search_item = $_POST['item'] ?? '';
$params = [];
$search_sql = '';

if ($search_item) {
    $search_sql = "WHERE i.item LIKE :item";
    $params[':item'] = "%$search_item%";
}

$query = "
    SELECT 
        iu.usage_id,
        i.item,
        iu.used_qty AS quantity,
        i.category,
        iu.date_used AS used_date
    FROM ingredient_usage iu
    LEFT JOIN inventory i ON iu.item_id = i.item_id
    $search_sql
    UNION ALL
    SELECT
        su.usage_id,
        su.item,
        su.quantity_used AS quantity,
        su.category,
        su.created_at AS used_date
    FROM stock_usage su
    " . ($search_item ? "WHERE su.item LIKE :item" : '') . "
    ORDER BY used_date DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$usage_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$recent_deductions = array_map(
    fn($log) => ($log['item'] ?? 'Unknown') . " (Qty: " . $log['quantity'] . ")",
    array_slice($usage_logs, 0, 5)
);
$recent_text = $recent_deductions ? implode(", ", $recent_deductions) : "No recent usage logs.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usage Logs</title>
<style>
body, html {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('/hotel/homepage/hotel_room.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    color: #fff;
}
.overlay {
    background-color: rgba(0,0,0,0.88);
    min-height: 100vh;
    padding: 40px 20px;
}
header {
    text-align: center;
    margin-bottom: 20px;
}
header h1 {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 10px;
}
header p {
    font-size: 16px;
    color: #ccc;
    max-width: 90%;
    margin: 0 auto 30px auto;
    word-wrap: break-word;
}
.search-container {
    width: 95%;
    margin: 0 auto 20px;
    text-align: center;
}
.search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    align-items: center;
}
.search-form input,
.search-form button,
.search-form a button {
    padding: 10px 14px;
    border-radius: 6px;
    border: none;
    font-size: 14px;
    cursor: pointer;
}
.search-form button,
.search-form a button {
    display: flex;
    align-items: center;
    gap: 5px;
    background-color: #FF9800;
    color: #fff;
    transition: background 0.3s;
}
.search-form button:hover,
.search-form a button:hover {
    background-color: #e67e22;
}
table {
    width: 95%;
    margin: 0 auto;
    border-collapse: separate;
    border-spacing: 0;
    background: #23272f;
    color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 18px rgba(0,0,0,0.15);
}
th, td {
    padding: 14px 12px;
    text-align: center;
    font-size: 15px;
    border: none;
}
th {
    background: #303642;
    font-weight: 700;
    font-size: 16px;
    color: #FF9800;
}
tr:hover td {
    background: #2e3440;
    transition: background 0.2s;
}
.delete-btn {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    background-color: rgba(255, 255, 255, 0.25);
    color: #fff;
    text-decoration: none;
}
.delete-btn:hover {
    background-color: rgba(255, 0, 0, 0.2);
}
.message {
    position: fixed;
    top: 10%;
    left: 50%;
    transform: translate(-50%, -50%);
    min-width: 200px;
    max-width: 300px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 500;
    text-align: center;
    z-index: 9999;
}
.message.success { background-color: #FF9800; }
.message.error { background-color: #e74c3c; }
</style>
</head>
<body>
<div class="overlay">
    <header>
        <h1>Usage Logs</h1>
        <p>Recently Used: <?= htmlspecialchars($recent_text) ?></p>
    </header>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="message success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="search-container">
        <form method="POST" class="search-form">
            <a href="../inventory.php"><button type="button">← Back to Inventory</button></a>
            <input type="text" name="item" placeholder="Search Item" value="<?= htmlspecialchars($search_item) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Usage ID</th>
                <th>Item</th>
                <th>Category</th>
                <th>Quantity Used</th>
                <th>Date Used</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if($usage_logs): ?>
            <?php foreach($usage_logs as $log): ?>
            <tr>
                <td><?= (int)$log['usage_id'] ?></td>
                <td><?= htmlspecialchars($log['item'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($log['category'] ?? '-') ?></td>
                <td><?= (int)$log['quantity'] ?></td>
                <td><?= date('M j, Y g:i A', strtotime($log['used_date'])) ?></td>
                <td>
                    <a href="?delete_id=<?= (int)$log['usage_id'] ?>&type=<?= 'ingredient' ?>&token=<?= $_SESSION['csrf_token'] ?>" class="delete-btn" onclick="return confirm('Delete this log?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No usage logs found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.message').forEach(m=>{setTimeout(()=>{m.style.opacity='0';setTimeout(()=>m.remove(),300)},5000)})})
</script>
</body>
</html>
