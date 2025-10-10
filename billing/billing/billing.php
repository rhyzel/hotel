<?php
require_once('../db.php');

$guest_name = $_GET['guest_name'] ?? '';
$guestBilling = [];
$guestDetails = null;
$loyaltyPoints = 0;
$totalSpent = 0;
$groupedBilling = [];

if ($guest_name) {
    $stmt = $conn->prepare("
        SELECT * FROM guests 
        WHERE CONCAT(first_name, ' ', last_name) LIKE :name OR guest_id = :id
    ");
    $stmt->execute([':name' => "%$guest_name%", ':id' => $guest_name]);
    $guestDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($guestDetails) {
        $billingStmt = $conn->prepare("
            SELECT order_id, order_type, item, amount, quantity, payment_option, payment_method, partial_payment, created_at
            FROM guest_billing
            WHERE guest_name LIKE :name OR guest_id = :id
            ORDER BY created_at DESC
        ");
        $billingStmt->execute([':name' => "%$guest_name%", ':id' => $guest_name]);
        $allBills = $billingStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allBills as $bill) {
            $groupedBilling[$bill['order_id']]['summary']['order_type'] = $bill['order_type'];
            $groupedBilling[$bill['order_id']]['summary']['payment_option'] = $bill['payment_option'];
            $groupedBilling[$bill['order_id']]['summary']['payment_method'] = $bill['payment_method'];
            $groupedBilling[$bill['order_id']]['summary']['total_amount'] = ($groupedBilling[$bill['order_id']]['summary']['total_amount'] ?? 0) + ($bill['amount'] * $bill['quantity']);
            $groupedBilling[$bill['order_id']]['summary']['total_paid'] = ($groupedBilling[$bill['order_id']]['summary']['total_paid'] ?? 0) + $bill['partial_payment'];
            $groupedBilling[$bill['order_id']]['summary']['last_date'] = $bill['created_at'];
            $groupedBilling[$bill['order_id']]['items'][] = $bill;
        }

        $sumStmt = $conn->prepare("
            SELECT SUM(total_amount) AS total_spent 
            FROM guest_billing 
            WHERE guest_name LIKE :name OR guest_id = :id
        ");
        $sumStmt->execute([':name' => "%$guest_name%", ':id' => $guest_name]);
        $totalSpent = $sumStmt->fetchColumn() ?? 0;

        $loyaltyPoints = floor($totalSpent / 100);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Guest Billing Records</title>
<link rel="stylesheet" href="billing.css">
<style>
body { font-family: Arial, Helvetica, sans-serif; }
.sidebar { width: 220px; float: left; }
.main { margin-left: 240px; padding: 20px; }
.summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
.order-cards { margin-top: 15px; }
.order-card { border: 1px solid #e0e0e0; padding: 12px; margin-bottom: 12px; border-radius: 6px; }
.sub-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.sub-table th, .sub-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
.no-data { padding: 20px; color: #777; }
.print-hidden { display: none; }
@media print {
  body * { visibility: hidden; }
  #receipt-print-area, #receipt-print-area * { visibility: visible; }
  #receipt-print-area { position: absolute; left: 0; top: 0; width: 100%; }
  .no-print { display: none !important; }
}
</style>
</head>
<body>
<div class="sidebar no-print">
    <div class="logo-container"><img src="../logo.png" alt="Hotel Logo" class="sidebar-logo" style="max-width:180px;"></div>
    <ul>
        <li><a href="../billing_dashboard.php">Dashboard</a></li>
        <li><a href="billing.php" class="active">Billing Records</a></li>
        <li><a href="../../reservation/reservation.php">Reservations</a></li>
        <li><a href="../../housekeeping/housekeeping.php">Housekeeping</a></li>
        <li><a href="../../pointofsale/pos.php">POS - Restaurant</a></li>
        <li><a href="../../giftstore/">Gift Store</a></li>
        <li><a href="../../loungebar/">Lounge Bar</a></li>
        <li><a href="../../minibar/">Mini Bar</a></li>
        <li><a href="../../hr/employee_login.php">HR & Staff</a></li>
        <li><a href="../../analytics/reports.php">Reports</a></li>
    </ul>
</div>

<div class="main">
    <header class="no-print">
        <div class="header-top">
            <h1>Billing Overview</h1>
            <div class="header-actions">
                <form method="GET" action="" style="display:inline-block;">
                    <input type="text" name="guest_name" placeholder="Enter Guest Name or ID" value="<?= htmlspecialchars($guest_name) ?>" required>
                    <button type="submit">Search</button>
                    <button type="button" onclick="window.location.href='billing.php'">Clear</button>
                </form>
                <button type="button" onclick="printGuestReceipt()">🖨️ Print Receipt</button>
            </div>
        </div>
    </header>

    <?php if ($guestDetails): ?>
    <section class="guest-summary no-print" id="guest-summary-visible">
        <h2>Guest Information</h2>
        <div class="summary-grid">
            <div><strong>Guest ID:</strong> <?= htmlspecialchars($guestDetails['guest_id']) ?></div>
            <div><strong>Name:</strong> <?= htmlspecialchars($guestDetails['first_name'] . ' ' . $guestDetails['last_name']) ?></div>
            <div><strong>Email:</strong> <?= htmlspecialchars($guestDetails['email']) ?></div>
            <div><strong>First Phone:</strong> <?= htmlspecialchars($guestDetails['first_phone']) ?></div>
            <div><strong>Second Phone:</strong> <?= htmlspecialchars($guestDetails['second_phone']) ?></div>
            <div><strong>Status:</strong> <?= htmlspecialchars(ucfirst($guestDetails['status'])) ?></div>
            <div><strong>Total Spent:</strong> ₱<?= number_format($totalSpent, 2) ?></div>
            <div><strong>Loyalty Points:</strong> <?= $loyaltyPoints ?></div>
        </div>
    </section>

    <section class="billing-section no-print">
        <h2>Transaction History</h2>
        <div class="order-cards">
        <?php foreach ($groupedBilling as $order_id => $data):
            $summary = $data['summary'];
            $remaining = $summary['total_amount'] - $summary['total_paid'];
        ?>
            <div class="order-card">
                <div class="order-header">
                    <div><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></div>
                    <div><strong>Type:</strong> <?= htmlspecialchars($summary['order_type']) ?></div>
                    <div><strong>Total:</strong> ₱<?= number_format($summary['total_amount'],2) ?></div>
                    <div><strong>Paid:</strong> ₱<?= number_format($summary['total_paid'],2) ?></div>
                    <?php if($remaining > 0): ?>
                    <div><strong>Remaining:</strong> ₱<?= number_format($remaining,2) ?></div>
                    <?php endif; ?>
                    <div><strong>Payment Option:</strong> <?= htmlspecialchars($summary['payment_option']) ?></div>
                </div>
                <table class="sub-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Payment Method</th>
                            <th>Partial Payment</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalAmount = 0;
                        $totalPartial = 0;
                        foreach($data['items'] as $item):
                            $lineTotal = $item['amount'] * $item['quantity'];
                            $totalAmount += $lineTotal;
                            $totalPartial += $item['partial_payment'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= htmlspecialchars($item['payment_method'] ?? '-') ?></td>
                            <td>₱<?= number_format($item['partial_payment'],2) ?></td>
                            <td>₱<?= number_format($lineTotal,2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>-</td>
                            <td>-</td>
                            <td>₱<?= number_format($totalPartial,2) ?></td>
                            <td>₱<?= number_format($totalAmount,2) ?> - ₱<?= number_format($totalPartial,2) ?> = ₱<?= number_format($totalAmount - $totalPartial,2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endforeach; ?>
        </div>
    </section>
    <?php elseif ($guest_name && !$guestDetails): ?>
        <p class="no-data">No records found for this guest.</p>
    <?php endif; ?>
</div>

<div id="receipt-print-area" class="print-hidden">
  <div style="max-width:480px;margin:0 auto;padding:12px;font-family:monospace;color:#000;">
    <div style="text-align:center;margin-bottom:6px;">
      <div style="font-size:18px;font-weight:700;">HOTEL LA VISTA</div>
      <div style="font-size:12px;">SJDM, 3023 Bulacan</div>
      <div style="font-size:12px;">Tel: (02) 555-0123 • Email: info@hotellavista.com</div>
      <div style="margin-top:8px;border-top:1px dashed #000;padding-top:6px;"></div>
    </div>

    <div id="receipt-meta" style="font-size:12px;margin-top:6px;"></div>

    <div id="receipt-guest" style="font-size:12px;margin-top:8px;"></div>

    <div id="receipt-lines" style="margin-top:8px;"></div>

    <div id="receipt-totals" style="margin-top:8px;font-size:13px;"></div>

    <div style="margin-top:10px;border-top:1px dashed #000;padding-top:8px;text-align:center;font-size:12px;">
      Thank you for staying with us
      <div style="margin-top:4px;font-size:11px;">Please visit again • www.hotellavista.com</div>
    </div>
  </div>
</div>

<script>
const receiptData = {
  hotel: {
    name: "HOTEL LA VISTA",
    address: "SJDM, 3023 Bulacan",
    contact: "Tel: (02) 555-0123 • info@hotellavista.com"
  },
  generated_at: new Date().toLocaleString(),
  guest: <?= json_encode($guestDetails ?: (object)[]) ?>,
  orders: <?= json_encode($groupedBilling) ?>,
  total_spent: <?= json_encode((float)$totalSpent) ?>,
  loyalty_points: <?= json_encode($loyaltyPoints) ?>
};

function currency(v){ return '₱' + Number(v).toFixed(2); }

function printGuestReceipt(){
  const area = document.getElementById('receipt-print-area');
  const meta = document.getElementById('receipt-meta');
  const guest = document.getElementById('receipt-guest');
  const lines = document.getElementById('receipt-lines');
  const totals = document.getElementById('receipt-totals');

  meta.innerHTML = '';
  guest.innerHTML = '';
  lines.innerHTML = '';
  totals.innerHTML = '';

  meta.innerHTML += '<div style="display:flex;justify-content:space-between;"><div>Receipt</div><div>' + receiptData.generated_at + '</div></div>';

  if(receiptData.guest && receiptData.guest.guest_id){
    guest.innerHTML += '<div><strong>Guest ID:</strong> ' + (receiptData.guest.guest_id || '') + '</div>';
    guest.innerHTML += '<div><strong>Name:</strong> ' + ((receiptData.guest.first_name||'') + ' ' + (receiptData.guest.last_name||'')) + '</div>';
    if(receiptData.guest.email) guest.innerHTML += '<div><strong>Email:</strong> ' + receiptData.guest.email + '</div>';
    if(receiptData.guest.first_phone) guest.innerHTML += '<div><strong>Phone:</strong> ' + receiptData.guest.first_phone + '</div>';
  }

  let grandSubtotal = 0;
  let grandPaid = 0;
  let bodyHtml = '<table style="width:100%;font-size:12px;border-collapse:collapse;">';
  bodyHtml += '<thead><tr><th style="text-align:left;padding:6px;border-bottom:1px solid #000;">Item</th><th style="text-align:center;padding:6px;border-bottom:1px solid #000;">Qty</th><th style="text-align:right;padding:6px;border-bottom:1px solid #000;">Price</th><th style="text-align:right;padding:6px;border-bottom:1px solid #000;">Line</th></tr></thead><tbody>';

  const orders = receiptData.orders || {};
  Object.keys(orders).forEach(orderId => {
    const o = orders[orderId];
    (o.items || []).forEach(it => {
      const qty = Number(it.quantity) || 1;
      const price = Number(it.amount) || 0;
      const line = qty * price;
      grandSubtotal += line;
      grandPaid += Number(it.partial_payment) || 0;
      bodyHtml += '<tr>';
      bodyHtml += '<td style="padding:6px;border-bottom:1px dashed #ddd;">' + (it.item || '') + '</td>';
      bodyHtml += '<td style="padding:6px;text-align:center;border-bottom:1px dashed #ddd;">' + qty + '</td>';
      bodyHtml += '<td style="padding:6px;text-align:right;border-bottom:1px dashed #ddd;">' + currency(price) + '</td>';
      bodyHtml += '<td style="padding:6px;text-align:right;border-bottom:1px dashed #ddd;">' + currency(line) + '</td>';
      bodyHtml += '</tr>';
    });
  });

  bodyHtml += '</tbody></table>';
  lines.innerHTML = bodyHtml;

  const balanceDue = grandSubtotal - grandPaid;
  totals.innerHTML = '<div style="font-size:13px;">' +
    '<div style="display:flex;justify-content:space-between;padding:4px 0;"><div>Subtotal</div><div>' + currency(grandSubtotal) + '</div></div>' +
    '<div style="display:flex;justify-content:space-between;padding:4px 0;"><div>Paid</div><div>' + currency(grandPaid) + '</div></div>' +
    '<div style="display:flex;justify-content:space-between;padding:6px 0;font-weight:700;border-top:1px solid #000;margin-top:6px;"><div>Total Due</div><div>' + currency(balanceDue) + '</div></div>' +
    '<div style="margin-top:8px;font-size:11px;">Loyalty Points: ' + receiptData.loyalty_points + '</div>' +
    '</div>';

  area.classList.remove('print-hidden');
  window.print();
  area.classList.add('print-hidden');
}

</script>
</body>
</html>
