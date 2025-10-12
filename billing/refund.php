<?php
require_once('db.php');
session_start();

$search = $_GET['search'] ?? '';
$transactions = [];
$managers = $conn->query("SELECT staff_id, CONCAT(first_name,' ',last_name) as name FROM staff WHERE position_name LIKE '%Manager%'")->fetchAll(PDO::FETCH_ASSOC);

if ($search) {
    $stmt = $conn->prepare("SELECT gb.*, CONCAT(g.first_name,' ',g.last_name) as guest_name
        FROM guest_billing gb
        JOIN guests g ON g.guest_id = gb.guest_id
        WHERE (gb.order_id=? OR g.guest_id=? OR CONCAT(g.first_name,' ',g.last_name) LIKE ?)
        ORDER BY gb.created_at DESC");
    $stmt->execute([$search, $search, "%$search%"]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['submit_refund'])) {
    $order_id = $_POST['order_id'];
    $refund_amount = $_POST['refund_amount'];
    $refund_method = $_POST['refund_method'];
    $manager_id = $_POST['manager_id'];
    $reason = $_POST['reason'];
    $other_reason = $_POST['other_reason'] ?? '';

    $stmt = $conn->prepare("SELECT gb.*, CONCAT(g.first_name,' ',g.last_name) as guest_name FROM guest_billing gb JOIN guests g ON g.guest_id=gb.guest_id WHERE gb.order_id=? LIMIT 1");
    $stmt->execute([$order_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction && $transaction['payment_option'] !== 'Paid') {
        $final_reason = $reason === 'Other' ? $other_reason : $reason;
        $insert = $conn->prepare("INSERT INTO refunds (guest_id, guest_name, order_id, item, refund_amount, payment_method, status, created_at, updated_at, reason) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW(), NOW(), ?)");
        $insert->execute([$transaction['guest_id'], $transaction['guest_name'], $transaction['order_id'], $transaction['item'], $refund_amount, $refund_method, $final_reason]);
        $update = $conn->prepare("UPDATE guest_billing SET payment_option='Pending Refund' WHERE order_id=?");
        $update->execute([$order_id]);
        header("Location: refund.php?search=$search");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Refund Transactions</title>
<link rel="stylesheet" href="refund.css">
<script>
function openRefundModal(orderId, guestName, amount, status) {
    if (status === 'Paid') {
        alert('Refund processing is disabled for fully paid transactions.');
        return;
    }
    document.getElementById('modal').style.display = 'flex';
    document.getElementById('order_id').value = orderId;
    document.getElementById('guest_info').innerText = guestName;
    document.getElementById('refund_amount').value = amount;
}
function closeModal() {
    document.getElementById('modal').style.display = 'none';
}
function showOtherReason(value){
    const other = document.getElementById('other_reason');
    if(value === 'Other'){
        other.style.display = 'block';
    } else {
        other.style.display = 'none';
        other.value = '';
    }
}
</script>
</head>
<body>
<div class="overlay"></div>
<header>
    <div class="title-group">
        <div class="main-title">Hotel La Vista</div>
        <div class="subtitle">Refund Transactions</div>
    </div>
</header>

<form method="get" class="filter-form">
    <a href="billing.php" class="header-btn">Back</a>
    <input type="text" name="search" placeholder="Order ID, Guest ID or Guest Name" value="<?= htmlspecialchars($search) ?>" required>
    <button type="submit">Search</button>
    <a href="refund.php" class="clear-btn">Clear</a>
</form>


<?php if ($transactions): ?>
<table class="transaction-table">
<tr>
    <th>Order ID</th>
    <th>Guest</th>
    <th>Item</th>
    <th>Quantity</th>
    <th>Total Amount</th>
    <th>Partial Payment</th>
    <th>Remaining Amount</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>
<?php foreach($transactions as $t): ?>
<tr>
    <td><?= $t['order_id'] ?></td>
    <td><?= htmlspecialchars($t['guest_name']) ?></td>
    <td><?= htmlspecialchars($t['item']) ?></td>
    <td><?= $t['quantity'] ?></td>
    <td>₱<?= number_format($t['amount'],2) ?></td>
    <td>₱<?= number_format($t['partial_payment'],2) ?></td>
    <td>₱<?= number_format($t['remaining_amount'],2) ?></td>
    <td><?= htmlspecialchars($t['payment_option']) ?></td>
    <td><?= date('Y-m-d H:i', strtotime($t['created_at'])) ?></td>
    <td>
        <button type="button" 
            onclick="openRefundModal('<?= $t['order_id'] ?>','<?= htmlspecialchars($t['guest_name']) ?>','<?= $t['amount'] ?>','<?= $t['payment_option'] ?>')" 
            <?= $t['payment_option']==='Paid'?'disabled style="background:#999;cursor:not-allowed;"':'' ?>>
            Process Refund
        </button>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php elseif ($search): ?>
<p style="text-align:center; margin-top:20px;">No transactions found for "<?= htmlspecialchars($search) ?>"</p>
<?php endif; ?>

<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Process Refund</h2>
        <p id="guest_info"></p>
        <form method="post">
            <input type="hidden" id="order_id" name="order_id">
            <label>Refund Amount</label>
            <input type="number" id="refund_amount" name="refund_amount" step="0.01" required>
            <label>Refund Method</label>
            <select name="refund_method" required>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="PayMaya">PayMaya</option>
                <option value="BillEase">BillEase</option>
                <option value="Gcash">Gcash</option>
            </select>
            <label>Reason</label>
            <select name="reason" required onchange="showOtherReason(this.value)">
                <option value="">Select Reason</option>
                <option value="Overcharge">Overcharge</option>
                <option value="Cancelled Order">Cancelled Order</option>
                <option value="Damaged Item">Damaged Item</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" id="other_reason" name="other_reason" placeholder="Explain reason" style="display:none; margin-top:5px;">
            <label>Manager Approval</label>
            <select name="manager_id" required>
                <?php foreach($managers as $m): ?>
                <option value="<?= $m['staff_id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="submit_refund">Submit Refund</button>
        </form>
    </div>
</div>
</body>
</html>
