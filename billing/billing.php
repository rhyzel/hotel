<?php
require_once('db.php');
session_start();

$guest_input = $_GET['guest'] ?? $_POST['guest_id'] ?? null;
$guest = null;

if (isset($_GET['clear_cart'])) {
    $guest_input = null;
    $guest = null;
    $_GET = [];
    unset($_SESSION['guest'], $_SESSION['billing'], $_SESSION['total_all'], $_SESSION['total_partial_paid'], $_SESSION['remaining'], $_SESSION['loyalty_points'], $_SESSION['premium_card']);
}

if ($guest_input) {
    if (is_numeric($guest_input)) {
        $stmt = $conn->prepare("SELECT * FROM guests WHERE guest_id = ? LIMIT 1");
        $stmt->execute([$guest_input]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM guests WHERE CONCAT(first_name,' ',last_name) LIKE ? LIMIT 1");
        $stmt->execute(["%$guest_input%"]);
    }
    $guest = $stmt->fetch(PDO::FETCH_ASSOC);
}

$billing = [];
$roomPayments = [];
$allPaid = false;

if ($guest) {
    $paidOrders = [];
    $folioStmt = $conn->prepare("SELECT DISTINCT order_id FROM folio WHERE guest_id = ?");
    $folioStmt->execute([$guest['guest_id']]);
    $paidOrders = $folioStmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt1 = $conn->prepare("SELECT * FROM guest_billing WHERE guest_id = ? ORDER BY created_at DESC");
    $stmt1->execute([$guest['guest_id']]);
    $guestBilling = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare("SELECT * FROM room_payments WHERE guest_id = ? ORDER BY created_at DESC");
    $stmt2->execute([$guest['guest_id']]);
    $roomPayments = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($guestBilling as $b) {
        $isPaid = in_array($b['order_id'] ?? '', $paidOrders);
        $billing[] = [
            'guest_id' => $b['guest_id'] ?? 0,
            'order_id' => $b['order_id'] ?? '',
            'order_type' => $b['order_type'] ?? '',
            'item' => $b['item'] ?? '',
            'quantity' => $b['quantity'] ?? 1,
            'amount' => (float)($b['amount'] ?? 0),
            'paid_amount' => (float)($b['partial_payment'] ?? 0),
            'payment_option' => $isPaid ? 'Paid' : ($b['payment_option'] ?? 'To be billed'),
            'created_at' => $b['created_at'] ?? '',
            'billing_id' => isset($b['id']) ? $b['id'] : null
        ];
    }

    foreach ($roomPayments as $r) {
        $totalStay = ($r['stay'] ?? 0) + ($r['extended_duration'] ?? 0);
        $totalPrice = ($r['room_price'] ?? 0) + ($r['extended_price'] ?? 0);
        $isPaid = in_array($r['id'] ?? '', $paidOrders);
        $billing[] = [
            'guest_id' => $r['guest_id'] ?? 0,
            'order_id' => $r['id'] ?? '',
            'order_type' => 'Room',
            'item' => $r['room_type'] ?? '',
            'quantity' => $totalStay,
            'amount' => (float)$totalPrice,
            'paid_amount' => 0,
            'payment_option' => $isPaid ? 'Paid' : 'To be billed',
            'created_at' => $r['created_at'] ?? '',
            'room_payment_id' => isset($r['id']) ? $r['id'] : null
        ];
    }

    $allPaid = array_reduce($billing, fn($c, $b) => $c && (($b['payment_option'] ?? '') === 'Paid' || ($b['payment_option'] ?? '') === 'Refunded'), true);
}

$total_spent = array_sum(array_map(fn($b) => $b['amount'] ?? 0, $billing));
$total_partial_paid = array_sum(array_map(fn($b) => $b['paid_amount'] ?? 0, $billing));
$remaining = array_sum(array_map(fn($b) => (($b['payment_option'] ?? '') === 'Paid' || ($b['payment_option'] ?? '') === 'Refunded') ? 0 : (($b['amount'] ?? 0) - ($b['paid_amount'] ?? 0)), $billing));
$loyalty_points = floor($total_spent / 100);

$card_stmt = $conn->prepare("SELECT * FROM premium_cards WHERE guest_id = ? AND status='active' LIMIT 1");
$card_stmt->execute([$guest['guest_id'] ?? 0]);
$card = $card_stmt->fetch(PDO::FETCH_ASSOC);

if ($guest) {
    $_SESSION['guest'] = $guest;
    $_SESSION['billing'] = $billing;
    $_SESSION['total_all'] = $total_spent;
    $_SESSION['total_partial_paid'] = $total_partial_paid;
    $_SESSION['remaining'] = $remaining;
    $_SESSION['loyalty_points'] = $loyalty_points;
    $_SESSION['premium_card'] = $card ?? null;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Guest Billing Summary</title>
<link rel="stylesheet" href="billing.css">
</head>
<body>
<header>
<div class="title-group">
    <div class="main-title">Hotel La Vista</div>
    <div class="subtitle">Guest Billing</div>
</div>
<div class="header-buttons">
    <a href="http://localhost/hotel/homepage/index.php" class="header-btn">Back</a>
    <a href="transactions.php" class="header-btn">Transactions</a>
    <a href="refund.php" class="header-btn">Refund</a>
</div>
</header>
<div class="grid-container">
<div class="left-container">
<form method="get" class="guest-load-form">
    <input type="text" name="guest" placeholder="Guest ID or Name" value="<?= htmlspecialchars($guest_input ?? '') ?>">
    <button type="submit">Load Guest</button>
    <button type="submit" name="clear_cart">Clear</button>
</form>
<?php if ($guest): ?>
<?php
$reservation_stmt = $conn->prepare("SELECT * FROM reservations WHERE guest_id = ? ORDER BY check_in DESC LIMIT 1");
$reservation_stmt->execute([$guest['guest_id'] ?? 0]);
$reservation = $reservation_stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="combined-container">
<div class="guest-info-section">
<table class="guest-info-table">
<caption>Guest Information</caption>
<tr><td><strong>Name:</strong></td><td colspan="2"><?= htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']) ?></td></tr>
<tr><td><strong>Loyalty Points:</strong></td><td colspan="2"><?= $loyalty_points ?></td></tr>
<?php if ($reservation): ?>
<tr><td><strong>Room Number:</strong></td><td colspan="2"><?= htmlspecialchars($reservation['room_id'] ?? '') ?></td></tr>
<tr><td><strong>Remarks:</strong></td><td colspan="2"><?= htmlspecialchars($reservation['remarks'] ?? '') ?></td></tr>
<tr><td><strong>Check-in Date:</strong></td><td colspan="2"><?= !empty($reservation['check_in']) ? date('Y-m-d H:i', strtotime($reservation['check_in'])) : '' ?></td></tr>
<?php endif; ?>
<?php if ($card): ?>
<tr><td><strong>Premium Card Number:</strong></td><td colspan="2"><?= htmlspecialchars($card['card_number'] ?? '') ?></td></tr>
<tr><td><strong>Issued Date:</strong></td><td colspan="2"><?= htmlspecialchars($card['issued_date'] ?? '') ?></td></tr>
<tr><td><strong>Benefits:</strong></td><td colspan="2"><?= htmlspecialchars($card['benefits'] ?? '') ?></td></tr>
<?php endif; ?>
</table>
</div>
<div class="grand-total-section">
<div class="grand-total">
<h3>Grand Total Summary</h3>
<table class="grand-total-table">
<tr><td class="payment-total-label">Total Amount:</td><td class="payment-total-value">₱<?= number_format($total_spent, 2) ?></td></tr>
<tr><td class="payment-total-label">Paid:</td><td class="payment-total-value">₱<?= number_format($total_partial_paid, 2) ?></td></tr>
<tr><td class="payment-total-label">Remaining:</td><td class="payment-total-value">₱<?= number_format($remaining, 2) ?></td></tr>
</table>
</div>
<form method="post" action="payment.php" style="width:100%; margin-top:15px;">
<input type="hidden" name="guest_id" value="<?= $guest['guest_id'] ?? 0 ?>">
<?php if ($card): ?>
<label><input type="checkbox" name="use_premium_card" value="1" <?= $allPaid ? 'disabled' : '' ?>> Use Premium Card (₱200 extra per item)</label>
<?php endif; ?>
<button type="submit" class="payment-btn <?= $allPaid ? 'grayed-out' : '' ?>" <?= $allPaid ? 'disabled' : '' ?>>Proceed to Payment</button>
</form>
</div>
</div>
<?php endif; ?>
</div>
<div class="right-container">
<?php if ($guest): ?>
<?php
$groupedBilling = [];
foreach ($billing as $b) { $groupedBilling[$b['order_type']][] = $b; }
?>
<?php foreach ($groupedBilling as $orderType => $items): ?>
<div class="payment-group">
<div class="content">
<table>
<tr><th colspan="5"><?= htmlspecialchars($orderType) ?></th></tr>
<tr><th>Order ID</th><th>Item/Room</th><th>Qty/Stay</th><th>Amount</th><th>Status</th></tr>
<?php
$group_total = $group_paid = $group_remaining = 0;
foreach ($items as $b):
$group_total += $b['amount'] ?? 0;
$group_paid += ($b['payment_option'] ?? '') === 'Paid' ? ($b['amount'] ?? 0) : ($b['paid_amount'] ?? 0);
$group_remaining += (($b['payment_option'] ?? '') === 'Paid' || ($b['payment_option'] ?? '') === 'Refunded') ? 0 : (($b['amount'] ?? 0) - ($b['paid_amount'] ?? 0));
$statusClass = (($b['payment_option'] ?? '') === 'Paid' || ($b['payment_option'] ?? '') === 'Refunded') ? 'paid-status' : '';
$statusText = $b['payment_option'] ?? 'To be billed';
?>
<tr>
<td><?= htmlspecialchars($b['order_id'] ?? '') ?></td>
<td><?= htmlspecialchars($b['item'] ?? '') ?></td>
<td><?= $b['quantity'] ?? 1 ?></td>
<td>₱<?= number_format($b['amount'] ?? 0, 2) ?></td>
<td class="<?= $statusClass ?>"><?= $statusText ?></td>
</tr>
<?php endforeach; ?>
</table>
<div class="calculation">
Total: ₱<?= number_format($group_total, 2) ?> | Paid: ₱<?= number_format($group_paid, 2) ?> | Remaining: ₱<?= number_format($group_remaining, 2) ?>
</div>
</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
</div>
</body>
</html>
