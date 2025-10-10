<?php
// guests.php - Extended version
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$method = $_SERVER['REQUEST_METHOD'] ?? null;

if ($method === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid execution context. Must be called via HTTP request.']);
    exit();
}

if ($method === 'OPTIONS') { 
    http_response_code(200); 
    exit(); 
}

require_once __DIR__ . '/../bootstrap.php';

use CRM\Lib\ApiDatabase as CRMDatabase;
use CRM\Lib\GuestRepository;
use CRM\Lib\GuestService;
use CRM\Lib\LoyaltyProgramRepository;
use CRM\Lib\LoyaltyProgramService;
use CRM\Lib\GuestController;

$crmDb = new CRMDatabase();
$conn = $crmDb->getConnection();
// instantiate repository and service
$guestRepo = new GuestRepository($conn);
$guestService = new GuestService($guestRepo);
$loyaltyRepo = new LoyaltyProgramRepository($conn);
$loyaltyService = new LoyaltyProgramService($loyaltyRepo);

// controller
$guestController = new GuestController($guestService);

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Check table structure dynamically
function getTableColumns($conn, $table) {
    try {
        $stmt = $conn->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    } catch (Exception $e) {
        return [];
    }
}

// Get guest table structure
$guestColumns = getTableColumns($conn, 'guests');
$primaryKey = 'guest_id';

try {
    switch ($method) {
        case 'GET':
            // ✅ Unified guest history handler
            if (!empty($_GET['guest_id']) && !empty($_GET['history_type'])) {
                $guest_id = (int)$_GET['guest_id'];
                $type = $_GET['history_type'];
                $result = $guestController->getHistory($guest_id, $type);
                if (!$result['success']) respond($result, 400);
                respond($result);
            }

            // ✅ Normal guest list
            $result = $guestController->list($_GET);
            respond($result);
            break;

        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            // Validate required fields
            if (empty($input['first_name']) || empty($input['last_name']) || empty($input['email']) || empty($input['first_phone'])) {
                respond(['success' => false, 'error' => 'Missing required fields: first_name, last_name, email, first_phone'], 400);
            }
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                respond(['success' => false, 'error' => 'Invalid email address'], 400);
            }
            // loyalty_tier default
            if (empty($input['loyalty_tier'])) $input['loyalty_tier'] = 'bronze';
            // Check for existing email
            $checkStmt = $conn->prepare("SELECT $primaryKey FROM guests WHERE email = :email");
            $checkStmt->execute([':email' => $input['email']]);
            if ($checkStmt->fetch()) {
                respond(['success' => false, 'error' => 'Guest with this email already exists'], 409);
            }
            try {
                $result = $guestController->create($input, $loyaltyService);
                respond($result, 201);
            } catch (Exception $e) {
                respond(['success' => false, 'error' => $e->getMessage()], 400);
            }
            break;

        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['guest_id'])) respond(['success' => false, 'error' => 'Missing guest ID'], 400);
            if (isset($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                respond(['success' => false, 'error' => 'Invalid email address'], 400);
            }
            // loyalty_tier default
            if (empty($input['loyalty_tier'])) $input['loyalty_tier'] = 'bronze';
            $result = $guestController->update($input, $loyaltyService);
            if ($result['success']) respond($result);
            else respond($result, 400);
            break;

        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['guest_id'])) respond(['success' => false, 'error' => 'Missing guest ID'], 400);
            $guestId = (int)$input['guest_id'];
            $result = $guestController->delete($guestId, $loyaltyService);
            if ($result['success']) respond($result);
            else respond($result, 404);
            break;

        default:
            respond(['success' => false, 'error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    respond(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], 500);
}
