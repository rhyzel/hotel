<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "hotel");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$period = $_GET['period'] ?? 'monthly';
$value = $_GET['value'] ?? '';

// Define periods to fetch based on selected period and value
$periods = [];
$currentDate = new DateTime();

if ($value) {
    // Specific period selected - show intra-period breakdown
    switch ($period) {
        case 'daily':
            // Show hourly breakdown for the selected day
            $selectedDate = new DateTime($value);
            for ($hour = 0; $hour < 24; $hour++) {
                $ampm = $hour >= 12 ? 'PM' : 'AM';
                $displayHour = $hour % 12;
                $displayHour = $displayHour === 0 ? 12 : $displayHour;
                $periods[] = $selectedDate->format('Y-m-d') . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00 (' . $displayHour . ' ' . $ampm . ')';
            }
            break;
        case 'weekly':
            // Show daily breakdown for the selected week
            $selectedWeek = new DateTime($value);
            $weekStart = clone $selectedWeek;
            $weekStart->modify('monday this week');
            for ($day = 0; $day < 7; $day++) {
                $date = clone $weekStart;
                $date->modify("+$day days");
                $periods[] = $date->format('Y-m-d');
            }
            break;
        case 'monthly':
            // Show daily breakdown for the selected month (1-31 days)
            list($year, $month) = explode('-', $value);
            $firstDay = new DateTime("$year-$month-01");
            $lastDay = new DateTime("$year-$month-01");
            $lastDay->modify('last day of this month');

            $currentDay = clone $firstDay;
            while ($currentDay <= $lastDay) {
                $periods[] = $currentDay->format('Y-m-d');
                $currentDay->modify('+1 day');
            }
            break;
        case 'yearly':
            // Show monthly breakdown for the selected year
            for ($month = 1; $month <= 12; $month++) {
                $periods[] = $value . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            }
            break;
    }
} else {
    // No specific value - show historical trends (last periods)
    switch ($period) {
        case 'monthly':
            // Last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = clone $currentDate;
                $date->modify("-$i months");
                $periods[] = $date->format('Y-m');
            }
            break;
        case 'weekly':
            // Last 12 weeks
            for ($i = 11; $i >= 0; $i--) {
                $date = clone $currentDate;
                $date->modify("-$i weeks");
                $periods[] = $date->format('Y-W');
            }
            break;
        case 'daily':
            // Last 30 days
            for ($i = 29; $i >= 0; $i--) {
                $date = clone $currentDate;
                $date->modify("-$i days");
                $periods[] = $date->format('Y-m-d');
            }
            break;
        case 'yearly':
            // Last 5 years
            for ($i = 4; $i >= 0; $i--) {
                $date = clone $currentDate;
                $date->modify("-$i years");
                $periods[] = $date->format('Y');
            }
            break;
    }
}

// Define order types to display in chart
$displayOrderTypes = ['Room Service','Restaurant','Mini Bar','Gift Store','Lounge Bar'];

// 1️⃣ Revenue by order type for each period
$revenueData = [];
foreach ($periods as $p) {
    $dateFilterBilling = '';
    $dateFilter = '';

    if ($value) {
        // Specific period selected - detailed breakdown
        switch ($period) {
            case 'daily':
                // Hourly breakdown for selected day
                list($date, $hour) = explode(' ', $p);
                $dateFilterBilling = "WHERE DATE(created_at) = '$date' AND HOUR(created_at) = '$hour'";
                $dateFilter = "WHERE DATE(rp.created_at) = '$date' AND HOUR(rp.created_at) = '$hour'";
                break;
            case 'weekly':
                // Daily breakdown for selected week
                $dateFilterBilling = "WHERE DATE(created_at) = '$p'";
                $dateFilter = "WHERE DATE(rp.created_at) = '$p'";
                break;
            case 'monthly':
                // Daily breakdown for selected month
                $dateFilterBilling = "WHERE DATE(created_at) = '$p'";
                $dateFilter = "WHERE DATE(rp.created_at) = '$p'";
                break;
            case 'yearly':
                // Monthly breakdown for selected year
                list($year, $mon) = explode('-', $p);
                $dateFilterBilling = "WHERE YEAR(created_at) = '$year' AND MONTH(created_at) = '$mon'";
                $dateFilter = "WHERE YEAR(rp.created_at) = '$year' AND MONTH(rp.created_at) = '$mon'";
                break;
        }
    } else {
        // Historical trends
        switch ($period) {
            case 'monthly':
                list($year, $mon) = explode('-', $p);
                $dateFilterBilling = "WHERE YEAR(created_at) = '$year' AND MONTH(created_at) = '$mon'";
                $dateFilter = "WHERE YEAR(rp.created_at) = '$year' AND MONTH(rp.created_at) = '$mon'";
                break;
            case 'weekly':
                list($year, $week) = explode('-W', $p);
                $weekStart = date('Y-m-d', strtotime("$year-W$week-1"));
                $weekEnd = date('Y-m-d', strtotime("$year-W$week-7"));
                $dateFilterBilling = "WHERE DATE(created_at) BETWEEN '$weekStart' AND '$weekEnd'";
                $dateFilter = "WHERE DATE(rp.created_at) BETWEEN '$weekStart' AND '$weekEnd'";
                break;
            case 'daily':
                $dateFilterBilling = "WHERE DATE(created_at) = '$p'";
                $dateFilter = "WHERE DATE(rp.created_at) = '$p'";
                break;
            case 'yearly':
                $dateFilterBilling = "WHERE YEAR(created_at) = '$p'";
                $dateFilter = "WHERE YEAR(rp.created_at) = '$p'";
                break;
        }
    }

    $sqlRevenue = "SELECT order_type, SUM(total_amount) AS total
                   FROM guest_billing
                   $dateFilterBilling
                   GROUP BY order_type";
    $resultRevenue = $conn->query($sqlRevenue);
    $revenueByType = [];
    foreach($displayOrderTypes as $type) $revenueByType[$type] = 0;

    if ($resultRevenue) {
        while ($row = $resultRevenue->fetch_assoc()) {
            if (in_array($row['order_type'], $displayOrderTypes)) {
                $revenueByType[$row['order_type']] = floatval($row['total']);
            }
        }
    }

    // Room payments for this period
    $sqlRoomRevenue = "SELECT SUM(room_price + extended_price) AS total FROM room_payments rp $dateFilter";
    $resultRoomRevenue = $conn->query($sqlRoomRevenue);
    $roomPaymentsTotal = 0;
    if ($resultRoomRevenue) {
        $row = $resultRoomRevenue->fetch_assoc();
        $roomPaymentsTotal = floatval($row['total']);
    }
    $revenueByType['Room Payments'] = $roomPaymentsTotal;

    $revenueData[] = [
        'period' => $p,
        'revenue' => $revenueByType
    ];
}

// 3️⃣ Sales details (for current period only)
$dateFilterBilling = '';
switch ($period) {
    case 'monthly':
        if ($value) {
            list($year, $mon) = explode('-', $value);
        } else {
            $year = date('Y');
            $mon = date('m');
        }
        $dateFilterBilling = "WHERE YEAR(created_at) = '$year' AND MONTH(created_at) = '$mon'";
        break;
    case 'weekly':
        if ($value) {
            $weekStart = date('Y-m-d', strtotime($value));
            $weekEnd = date('Y-m-d', strtotime($value . ' +6 days'));
        } else {
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        }
        $dateFilterBilling = "WHERE DATE(created_at) BETWEEN '$weekStart' AND '$weekEnd'";
        break;
    case 'daily':
        $date = $value ?: date('Y-m-d');
        $dateFilterBilling = "WHERE DATE(created_at) = '$date'";
        break;
    case 'yearly':
        $year = $value ?: date('Y');
        $dateFilterBilling = "WHERE YEAR(created_at) = '$year'";
        break;
}

$sqlSales = "SELECT guest_name, order_type, item, total_amount, payment_option, payment_method, created_at
             FROM guest_billing
             $dateFilterBilling
             ORDER BY created_at DESC LIMIT 100";
$resultSales = $conn->query($sqlSales);
$sales = $resultSales ? $resultSales->fetch_all(MYSQLI_ASSOC) : [];

// 4️⃣ Room payments details (for current period only)
$dateFilter = '';
switch ($period) {
    case 'monthly':
        $dateFilter = "WHERE YEAR(rp.created_at) = '$year' AND MONTH(rp.created_at) = '$mon'";
        break;
    case 'weekly':
        $dateFilter = "WHERE DATE(rp.created_at) BETWEEN '$weekStart' AND '$weekEnd'";
        break;
    case 'daily':
        $dateFilter = "WHERE DATE(rp.created_at) = '$date'";
        break;
    case 'yearly':
        $dateFilter = "WHERE YEAR(rp.created_at) = '$year'";
        break;
}

$sqlRoomPayments = "
SELECT rp.payment_id, rp.guest_id, rp.walkin_id, rp.reservation_id, rp.room_type, rp.room_price, rp.stay,
       rp.extended_price, rp.extended_duration, rp.created_at,
       g.first_name, g.last_name
FROM room_payments rp
LEFT JOIN guests g ON rp.guest_id = g.guest_id
$dateFilter
ORDER BY rp.created_at DESC
LIMIT 100
";
$resultRoomPayments = $conn->query($sqlRoomPayments);
$roomPayments = [];
if ($resultRoomPayments) {
    while ($row = $resultRoomPayments->fetch_assoc()) {
        $guestName = (!empty($row['first_name']) || !empty($row['last_name']))
                        ? trim($row['first_name'].' '.$row['last_name'])
                        : 'System';
        $roomPayments[] = [
            'guest_name' => $guestName,
            'reservation_id' => $row['reservation_id'],
            'walkin_id' => $row['walkin_id'],
            'room_type' => $row['room_type'],
            'room_price' => floatval($row['room_price']),
            'stay' => $row['stay'],
            'extended_duration' => $row['extended_duration'],
            'extended_price' => floatval($row['extended_price']),
            'total' => floatval($row['room_price']) + floatval($row['extended_price']),
            'created_at' => $row['created_at']
        ];
    }
}

$response = [
    'revenueData' => $revenueData,
    'sales' => $sales,
    'room_payments' => $roomPayments
];

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
