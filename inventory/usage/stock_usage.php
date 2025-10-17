<?php
session_start();
require '../db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_GET['delete_id'], $_GET['token'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id <= 0 || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        $_SESSION['error'] = "Invalid usage record ID or security token";
        header("Location: stock_usage.php");
        exit;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM stock_usage WHERE usage_id = :id");
        $stmt->execute([':id' => $delete_id]);
        $_SESSION['success'] = "Stock usage log deleted.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting usage log: ".$e->getMessage();
    }
    header("Location: stock_usage.php");
    exit;
}

$search_item = $_POST['item'] ?? '';
$where = [];
$params = [];
if ($search_item) {
    $where[] = "item LIKE :item";
    $params[':item'] = "%$search_item%";
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$stmt = $pdo->prepare("SELECT su.*, i.category FROM stock_usage su LEFT JOIN inventory i ON su.item = i.item $where_sql ORDER BY su.created_at DESC");
$stmt->execute($params);
$usage_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$recent_deductions = array_map(
    fn($log) => $log['item']." (Qty: ".$log['quantity_used'].")",
    $usage_logs
);
$recent_text = $recent_deductions
    ? implode(", ", array_slice($recent_deductions, 0, 5))
    : "No items were recently used.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stock Usage</title>
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
    box-sizing: border-box;
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
header p.recent-items {
    font-size: 16px;
    color: #ccc;
    margin: 0 auto 30px auto;
    max-width: 90%;
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
.search-form select,
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
.search-form a {
    text-decoration: none;
}
table {
    width: 95%;
    margin: 0 auto 30px;
    border-collapse: separate;
    border-spacing: 0;
    background: #23272f;
    color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 18px rgba(0,0,0,0.15);
    opacity: 0.95;
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
td.actions {
    display: flex;
    justify-content: center;
    gap: 6px;
}
.delete-btn {
    display: inline-block;
    padding: 8px 12px;
    margin: 2px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    background-color: rgba(255, 255, 255, 0.25);
    color: #fff;
    text-decoration: none;
}
.delete-btn:hover {
    background-color: rgba(255, 0, 0, 0.2);
    transform: translateY(-1px);
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
    opacity: 1;
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.message.success {
    background-color: #FF9800;
    color: #fff;
}
.message.error {
    background-color: #e74c3c;
    color: #fff;
}
@media (max-width: 900px) {
    table, thead, tbody, th, td, tr { display: block; }
    thead { display: none; }
    tr {
        background: #222;
        margin-bottom: 10px;
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
    }
    td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }
    td:before {
        position: absolute;
        left: 16px;
        top: 16px;
        white-space: nowrap;
        font-weight: bold;
        color: #FF9800;
        content: attr(data-label);
        font-size: 14px;
        text-align: left;
    }
}
</style>
</head>
<body>
<div class="overlay">
    <header>
        <h1>Stock Usage</h1>
        <p class="recent-items">Recently Used Items: <?= htmlspecialchars($recent_text) ?></p>
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
            <input type="text" name="item" placeholder="Search by Item Name" value="<?= htmlspecialchars($search_item) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Usage ID</th>
                <th>Order ID</th>
                <th>Item Name</th>
                  <th>Category</th>
                <th>Quantity Used</th>
                <th>Date Used</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($usage_logs): ?>
                <?php foreach($usage_logs as $log): ?>
                <tr>
                    <td data-label="Usage ID"><?= (int)$log['usage_id'] ?></td>
                    <td data-label="Order ID"><?= (int)$log['order_id'] ?></td>
                    <td data-label="Item Name"><?= htmlspecialchars($log['item']) ?></td>
                    <td data-label="Category"><?= htmlspecialchars($log['category'] ?? 'N/A') ?></td>
                    <td data-label="Quantity Used"><?= (int)$log['quantity_used'] ?></td>
                    <td data-label="Date Used"><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></td>
                    <td data-label="Actions" class="actions">
                        <a href="stock_usage.php?delete_id=<?= (int)$log['usage_id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="delete-btn"
                           onclick="return confirm('Delete this stock usage log of <?= htmlspecialchars($log['item']) ?>?');">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; font-style:italic; color:#666;">
                    <?php if($search_item): ?>
                    No stock usage logs found. <a href="stock_usage.php" style="color:#3498db;">Clear search</a>
                    <?php else: ?>
                    No stock usage logs yet.
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.message').forEach(msg => {
        setTimeout(() => { msg.style.opacity='0'; setTimeout(()=>msg.remove(),300); }, 5000);
    });
});
</script>
</body>
</html>
