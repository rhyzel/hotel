<?php
require_once('db.php');
session_start();

$guest = $_SESSION['guest'] ?? null;
$guest_id = $guest['guest_id'] ?? null;

$billing = [];
$roomPayments = [];
$grandTotal = 0;
$totalPaid = 0;
$totalPartial = 0;
$pendingBalance = 0;
$loyaltyPoints = 0;
$premiumCharge = 200;
$successMessage = '';
$premiumSelected = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_pay']) && $guest_id) {
    $paymentMethod = $_POST['payment_method'] ?? '';

    $update = $conn->prepare("UPDATE guest_billing SET payment_option = 'Paid', payment_method = ? WHERE guest_id = ?");
    $update->execute([$paymentMethod, $guest_id]);

    $stmtBilling = $conn->prepare("SELECT * FROM guest_billing WHERE guest_id = ?");
    $stmtBilling->execute([$guest_id]);
    $billing = $stmtBilling->fetchAll(PDO::FETCH_ASSOC);

    $stmtRoom = $conn->prepare("SELECT * FROM room_payments WHERE guest_id = ?");
    $stmtRoom->execute([$guest_id]);
    $roomPayments = $stmtRoom->fetchAll(PDO::FETCH_ASSOC);

    $insertFolio = $conn->prepare("
        INSERT INTO folio (guest_id, guest_name, order_type, item, order_id, quantity, total_amount, paid_amount, payment_method, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");

    foreach ($billing as $b) {
        $insertFolio->execute([
            $guest_id,
            $guest['first_name'] . ' ' . $guest['last_name'],
            $b['order_type'],
            $b['item'],
            $b['order_id'] ?? null,
            $b['quantity'] ?? 1,
            $b['amount'],
            $b['amount'],
            $paymentMethod
        ]);
    }

    foreach ($roomPayments as $r) {
        $roomAmount = (float)($r['room_price'] ?? 0) + (float)($r['extended_price'] ?? 0);
        $insertFolio->execute([
            $guest_id,
            $guest['first_name'] . ' ' . $guest['last_name'],
            'Room',
            $r['room_type'] ?? 'Room Charge',
            $r['id'] ?? null,
            1,
            $roomAmount,
            $roomAmount,
            $paymentMethod
        ]);
    }
}

if ($guest_id) {
    $stmtBilling = $conn->prepare("SELECT * FROM guest_billing WHERE guest_id = ?");
    $stmtBilling->execute([$guest_id]);
    $billing = $stmtBilling->fetchAll(PDO::FETCH_ASSOC);

    $stmtRoom = $conn->prepare("SELECT * FROM room_payments WHERE guest_id = ?");
    $stmtRoom->execute([$guest_id]);
    $roomPayments = $stmtRoom->fetchAll(PDO::FETCH_ASSOC);

    foreach ($billing as $b) {
        $paymentOption = $b['payment_option'] ?? 'Pending';
        $amount = (float)$b['amount'];
        $partial = (float)($b['partial_payment'] ?? 0);
        $grandTotal += $amount;
        if ($paymentOption === 'Paid') {
            $totalPaid += $amount;
        } else {
            $totalPartial += $partial;
        }
    }

    foreach ($roomPayments as $r) {
        $paymentOption = $r['payment_option'] ?? 'Pending';
        $roomAmount = (float)($r['room_price'] ?? 0) + (float)($r['extended_price'] ?? 0);
        $partial = (float)($r['partial_payment'] ?? 0);
        $grandTotal += $roomAmount;
        if ($paymentOption === 'Paid') {
            $totalPaid += 0;
        } else {
            $totalPartial += $partial;
        }
    }

    $pendingBalance = max($grandTotal - ($totalPaid + $totalPartial), 0);
    $loyaltyPoints = floor($grandTotal / 100);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS Billing Summary</title>
<link rel="stylesheet" href="payment.css">
</head>
<body>
<div class="container">
<h2>POS Billing Summary</h2>

<form method="post" id="payForm">
<table border="1" id="billing_table">
<tr><th>Order Type</th><th>Item</th><th>Amount</th></tr>
<?php foreach($billing as $b): 
    $displayAmount = "₱" . number_format($b['amount'],2);
?>
<tr>
<td><?= htmlspecialchars($b['order_type']) ?></td>
<td><?= htmlspecialchars($b['item']) ?></td>
<td><?= $displayAmount ?></td>
</tr>
<?php endforeach; ?>

<?php foreach($roomPayments as $r): 
    $roomAmount = (float)($r['room_price'] ?? 0) + (float)($r['extended_price'] ?? 0);
?>
<tr>
<td>Room</td>
<td><?= htmlspecialchars($r['room_type'] ?? 'Room Charge') ?></td>
<td>₱<?= number_format($roomAmount,2) ?></td>
</tr>
<?php endforeach; ?>
</table>

<div class="summary-box">
    <div id="grand_total">Overall total: ₱<?= number_format($grandTotal,2) ?></div>
    <div id="partial_total">Paid: ₱<?= number_format($totalPaid + $totalPartial,2) ?></div>
    <div id="after_partial">Pending Balance: ₱<?= number_format($pendingBalance,2) ?></div>

    <div style="margin-top:10px;">
        <label>
            <input type="checkbox" name="premium_card" id="premium_check" onchange="updatePremium()"> Avail Hotel La Vista Premium Card
            <span class="tooltip">ⓘ
                <span class="tooltiptext">
                    Benefits:<br>
                    - Earn 1 point per ₱100 spent<br>
                    - Exclusive perks & priority service
                </span>
            </span>
        </label>
        <span style="margin-left:10px;">Points: <span id="premium_points"><?= $loyaltyPoints ?></span></span>
    </div>

    <label style="margin-top:10px; display:block;">Choose Payment Method:</label>
    <select name="payment_method" id="payment_method" onchange="toggleCashInput()" required>
        <option value="">-- Select Payment --</option>
        <option value="Cash">Cash</option>
        <option value="Card">Debit/Credit Card</option>
        <option value="Gcash">Gcash</option>
        <option value="PayMaya">PayMaya</option>
        <option value="BillEase">BillEase</option>
    </select>

    <div id="cash_input" style="display:none;">
        <label>Cash Given:</label>
        <input type="number" id="cash_amount" step="0.01" oninput="calculateChange()">
        <p>Change Due: ₱<span id="change_amount">0.00</span></p>
    </div>
</div>

<div class="button-row">
    <button type="submit" name="pay" id="payButton">Pay & Print Receipt</button>
    <a href="http://localhost/hotel/billing/billing.php">Back</a>
</div>
</form>
</div>

<div id="confirmModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
  <div style="background:white; padding:20px; border-radius:10px; text-align:center; width:300px;">
    <p>Are you sure you want to complete this payment?</p>
    <button type="button" id="confirmYes">Yes</button>
    <button type="button" id="confirmNo">Cancel</button>
  </div>
</div>

<script>
let grandTotal = <?= $grandTotal ?>;
let totalPartial = <?= $totalPartial ?>;
let premiumCharge = <?= $premiumCharge ?>;
let premiumSelected = false;

function updatePremium() {
    const checked = document.getElementById('premium_check').checked;
    premiumSelected = checked;
    const table = document.getElementById('billing_table');
    const existingRow = document.getElementById('premium_row');
    if(existingRow) table.removeChild(existingRow);
    let total = grandTotal;
    if(checked){
        const row = document.createElement('tr');
        row.id = 'premium_row';
        row.innerHTML = `<td>Premium</td><td>Hotel La Vista Card Charge</td><td>₱${premiumCharge.toFixed(2)}</td>`;
        table.appendChild(row);
        total += premiumCharge;
    }
    const pendingBalance = Math.max(total - totalPartial,0);
    document.getElementById('grand_total').textContent = 'Overall total: ₱' + total.toFixed(2);
    document.getElementById('after_partial').textContent = 'Pending Balance: ₱' + pendingBalance.toFixed(2);
    document.getElementById('premium_points').textContent = checked ? Math.floor(total/100) : Math.floor(grandTotal/100);
    calculateChange();
}

function toggleCashInput() {
    const method = document.getElementById('payment_method').value;
    document.getElementById('cash_input').style.display = (method === 'Cash') ? 'block' : 'none';
    calculateChange();
}

function calculateChange() {
    const cash = parseFloat(document.getElementById('cash_amount').value) || 0;
    const totalText = document.getElementById('after_partial').textContent.replace(/[^0-9.]/g,'');
    const totalAfter = parseFloat(totalText) || grandTotal;
    const change = Math.max(cash - totalAfter, 0);
    document.getElementById('change_amount').textContent = change.toLocaleString('en-PH',{minimumFractionDigits:2});
}

const modal = document.getElementById('confirmModal');
const yesBtn = document.getElementById('confirmYes');
const noBtn = document.getElementById('confirmNo');
const form = document.getElementById('payForm');

form.addEventListener('submit', function(e) {
    e.preventDefault();
    modal.style.display = 'flex';
});

yesBtn.addEventListener('click', function() {
    modal.style.display = 'none';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'confirm_pay';
    input.value = '1';
    form.appendChild(input);
    printReceipt();
    setTimeout(()=>form.submit(),500);
});

noBtn.addEventListener('click', function() {
    modal.style.display = 'none';
});

function printReceipt() {
    const totalWithPremium = grandTotal + (premiumSelected ? premiumCharge : 0);
    const cashGiven = parseFloat(document.getElementById('cash_amount')?.value) || 0;
    const paidAmount = document.getElementById('payment_method').value === 'Cash' ? cashGiven : totalWithPremium;
    const changeAmount = Math.max(paidAmount - totalWithPremium, 0);
    let receiptWindow = window.open('', 'Receipt', 'width=400,height=600');
    let html = `<html><head><title>Receipt</title><style>
        body { font-family: monospace; padding: 10px; white-space: pre; }
        .center { text-align: center; }
        img.logo { display: block; margin: 0 auto 5px auto; max-width: 120px; filter: brightness(0) contrast(1); }
    </style></head><body>`;
    html += `<img src="logo.png" class="logo" alt="Logo">`;
    html += `<div class="center">Hotel La Vista</div>`;
    html += `<div class="center">Guest: <?= htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']) ?></div>`;
    html += `\n------------------------------------------------\n`;
    html += `Order Type       Item                      Amount\n`;
    html += `------------------------------------------------\n`;
    <?php foreach($billing as $b): ?>
        html += "<?= str_pad($b['order_type'],15) . str_pad($b['item'],25) . str_pad('₱' . number_format($b['amount'],2),12,' ',STR_PAD_LEFT) ?>\n";
    <?php endforeach; ?>
    <?php foreach($roomPayments as $r):
        $roomAmount = (float)($r['room_price'] ?? 0) + (float)($r['extended_price'] ?? 0);
    ?>
        html += "Room".padEnd(15) + "<?= htmlspecialchars($r['room_type'] ?? 'Room Charge') ?>".padEnd(25) + `₱<?= number_format($roomAmount,2) ?>`.padStart(12) + '\n';
    <?php endforeach; ?>
    if(premiumSelected){
        html += 'Premium'.padEnd(15) + 'Hotel La Vista Card Charge'.padEnd(25) + `₱${premiumCharge.toFixed(2)}`.padStart(12) + '\n';
    }
    html += `------------------------------------------------\n`;
    html += 'Total'.padEnd(40) + `₱${totalWithPremium.toFixed(2)}`.padStart(12) + '\n';
    html += 'Paid'.padEnd(40) + `₱${paidAmount.toFixed(2)}`.padStart(12) + '\n';
    if(document.getElementById('payment_method').value === 'Cash') {
        html += 'Cash Given'.padEnd(40) + `₱${cashGiven.toFixed(2)}`.padStart(12) + '\n';
        html += 'Change'.padEnd(40) + `₱${changeAmount.toFixed(2)}`.padStart(12) + '\n';
    }
    html += 'Pending Balance'.padEnd(40) + `₱${Math.max(totalWithPremium - paidAmount,0).toFixed(2)}`.padStart(12) + '\n';
    html += `------------------------------------------------\n`;
    html += `\n             Thank you for staying with us!\n`;
    html += `</body></html>`;
    receiptWindow.document.write(html);
    receiptWindow.document.close();
    receiptWindow.focus();
    receiptWindow.print();
}
</script>
</body>
</html>
