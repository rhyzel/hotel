<?php
// loyalty.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$method = $_SERVER['REQUEST_METHOD'] ?? null;
if ($method === 'OPTIONS') { http_response_code(200); exit(); }
if ($method === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid execution context']);
    exit();
}

require_once __DIR__ . '/../bootstrap.php'; // Include bootstrap to access services

// Initialize database connection
$db = new \CRM\Lib\ApiDatabase();
$conn = $db->getConnection();

// Initialize the repository and service
$loyaltyProgramRepo = new \CRM\Lib\LoyaltyProgramRepository($conn);
$loyaltyProgramService = new \CRM\Lib\LoyaltyProgramService($loyaltyProgramRepo);

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// --- Role detection (for demo: via ?role=guest|admin|staff, or use session in real app) ---
$role = $_GET['role'] ?? ($_POST['role'] ?? 'admin'); // default to admin for backward compatibility
$user_guest_id = isset($_GET['guest_id']) ? intval($_GET['guest_id']) : null;

// FRIENDLY SYSTEM: No role-based filtering, all users see the same loyalty data

try {
    switch ($method) {
        case 'GET':
            // --- Friendly system: always return all programs and stats ---
            if (isset($_GET['points_earning']) && isset($_GET['tier'])) {
                $tier = strtolower(trim($_GET['tier']));
                $guestsPoints = $loyaltyProgramService->getPointsEarnedPerGuestByTier($tier);
                respond(['success' => true, 'data' => $guestsPoints]);
            }

            if (isset($_GET['tiers']) && $_GET['tiers'] == '1') {
                $stmt = $conn->query("SELECT tier, name FROM loyalty_programs ORDER BY tier ASC");
                $tiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                respond(['success' => true, 'data' => $tiers]);
            }

            if (isset($_GET['stats'])) {
                // Use actual guest counts for each tier for "members"
                $tierCounts = [];
                $stmt = $conn->query("SELECT LOWER(loyalty_tier) as tier, COUNT(*) as count FROM guests GROUP BY loyalty_tier");
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $tierCounts[$row['tier']] = (int)$row['count'];
                }

                // Get all programs to sum up other stats
                $programs = $conn->query("SELECT * FROM loyalty_programs")->fetchAll(PDO::FETCH_ASSOC);

                // --- Compute revenue_impact from all POS tables for all guests in all tiers ---
                $revenue_impact = 0.0;
                $tables = [
                    ['table' => 'guest_billing', 'alias' => 'gb'],
                    ['table' => 'lounge_orders', 'alias' => 'lo'],
                    ['table' => 'giftshop_sales', 'alias' => 'gs'],
                    ['table' => 'restaurant_orders', 'alias' => 'ro'],
                    ['table' => 'room_dining_orders', 'alias' => 'rd'],
                ];
                foreach ($tables as $tbl) {
                    if ($conn->query("SHOW TABLES LIKE '{$tbl['table']}'")->rowCount()) {
                        $sql = "SELECT SUM({$tbl['alias']}.total_amount)
                                FROM {$tbl['table']} {$tbl['alias']}
                                INNER JOIN guests g ON {$tbl['alias']}.guest_id = g.guest_id
                                WHERE g.loyalty_tier IS NOT NULL AND g.loyalty_tier != ''";
                        $stmt = $conn->query($sql);
                        $revenue_impact += (float)($stmt->fetchColumn() ?: 0);
                    }
                }

                $current = [
                    'members' => array_sum($tierCounts),
                    'points_redeemed' => array_sum(array_column($programs, 'points_redeemed')),
                    'rewards_given' => array_sum(array_column($programs, 'rewards_given')),
                    'revenue_impact' => $revenue_impact,
                ];
                $previous = [
                    'members' => max(0, $current['members'] - 2),
                    'points_redeemed' => max(0, $current['points_redeemed'] - 5),
                    'rewards_given' => max(0, $current['rewards_given'] - 1),
                    'revenue_impact' => max(0, $current['revenue_impact'] - 50),
                ];
                respond(['success' => true, 'data' => [
                    'current' => $current,
                    'previous' => $previous
                ]]);
            }

            // Always return all programs, with members_count updated
            $tierCounts = [];
            $stmt = $conn->query("SELECT LOWER(loyalty_tier) as tier, COUNT(*) as count FROM guests GROUP BY loyalty_tier");
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $tierCounts[$row['tier']] = (int)$row['count'];
            }

            $stmt = $conn->query("SELECT * FROM loyalty_programs");
            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($programs as &$p) {
                $tier = strtolower($p['tier']);
                $p['members_count'] = $tierCounts[$tier] ?? 0;
            }
            respond(['success' => true, 'data' => $programs]);
            break;

        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['name']) || empty($input['tier']) || !isset($input['points_rate'])) {
                respond(['success' => false, 'error' => 'Missing required fields: name, tier, points_rate'], 400);
            }

            // ✅ Check unique name
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM loyalty_programs WHERE name = :name");
            $checkStmt->execute([':name' => trim($input['name'])]);
            if ($checkStmt->fetchColumn() > 0) {
                respond(['success' => false, 'error' => 'A loyalty program with this name already exists.'], 400);
            }

            // Use the service to create a loyalty program
            $newProgram = $loyaltyProgramService->createProgram($input);
            // After creation, sync members count for all programs
            $loyaltyProgramService->syncMembersCount();
            respond(['success' => true, 'data' => $newProgram]);
            break;

        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['id'])) respond(['success' => false, 'error' => 'Missing program ID'], 400);

            $fields = [];
            $params = [':id' => $input['id']];

            if (isset($input['name'])) {
                $fields[] = "name = :name";
                $params[':name'] = $input['name'];
            }
            if (isset($input['tier'])) {
                $fields[] = "tier = :tier"; 
                $params[':tier'] = strtolower(trim($input['tier']));
            }
            if (isset($input['points_rate'])) {
                $fields[] = "points_rate = :points_rate"; 
                $params[':points_rate'] = $input['points_rate'];
            }
            if (isset($input['benefits'])) {
                $fields[] = "benefits = :benefits"; 
                $params[':benefits'] = is_array($input['benefits']) ? implode(',', $input['benefits']) : $input['benefits'];
            }
            if (isset($input['status'])) {
                $fields[] = "status = :status"; 
                $params[':status'] = $input['status'];
            }
            if (isset($input['points_redeemed'])) {
                $fields[] = "points_redeemed = :points_redeemed"; 
                $params[':points_redeemed'] = $input['points_redeemed'];
            }

            if (!$fields) respond(['success' => false, 'error' => 'No fields to update'], 400);
            $sql = "UPDATE loyalty_programs SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            // After update, sync members count for all programs
            $loyaltyProgramService->syncMembersCount();

            respond(['success' => true, 'message' => 'Program updated successfully']);
            break;

        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['id'])) respond(['success' => false, 'error' => 'Missing program ID'], 400);

            $stmt = $conn->prepare("DELETE FROM loyalty_programs WHERE id = :id");
            $stmt->execute([':id' => $input['id']]);
            if ($stmt->rowCount()) {
                // After delete, sync members count for all programs
                $loyaltyProgramService->syncMembersCount();
                respond(['success' => true, 'message' => 'Program deleted successfully']);
            }
            respond(['success' => false, 'error' => 'Program not found'], 404);
            break;

        default:
            respond(['success' => false, 'error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    respond(['success' => false, 'error' => $e->getMessage()], 500);
}