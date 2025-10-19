<?php
// feedback.php - Delegates to FeedbackController (OOP) while preserving behavior
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$overrideMethod = $_REQUEST['_method'] ?? null;
if ($overrideMethod) {
    $method = strtoupper($overrideMethod);
}

if ($method === 'OPTIONS') { http_response_code(200); exit(); }

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../bootstrap.php';
use CRM\Lib\ApiDatabase as CRMDatabase;
use CRM\Lib\FeedbackRepository;
use CRM\Lib\FeedbackController;

$db = new Database();
$conn = $db->getConnection();

$repo = new FeedbackRepository($conn);
$controller = new FeedbackController($repo);

// FRIENDLY SYSTEM: All users see the same feedback data

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

$role = 'admin'; // force friendly system
$user_guest_id = null;

try {
    // Get table structures
    $feedbackColumns = $controller->getTableColumns('feedback');
    $guestColumns = $controller->getTableColumns('guests');

    switch ($method) {
        case 'GET':
            // FRIENDLY SYSTEM: All users see the same feedback data
            $params = [];
            if (isset($_GET['type'])) $params['type'] = $_GET['type'];
            if (isset($_GET['status'])) $params['status'] = $_GET['status'];
            if (isset($_GET['search'])) $params['search'] = $_GET['search'];
            $items = $controller->list($params);
            foreach ($items as &$item) {
                if (!empty($item['guest_id'])) {
                    $item['guest_profile_url'] = 'guests.php?guest_id=' . urlencode($item['guest_id']);
                }
            }
            unset($item);
            respond(['success' => true, 'data' => $items]);
            break;

        case 'POST':
            // FRIENDLY SYSTEM: allow all users to add feedback
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['guest_name']) || empty($input['comment'])) {
                respond(['success' => false, 'error' => 'Missing required fields: guest_name and comment'], 400);
            }

            $new = $controller->create($input, $feedbackColumns);
            respond(['success' => true, 'data' => $new], 201);
            break;

        case 'PUT':
            // FRIENDLY SYSTEM: allow all users to update feedback
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $controller->update($input, $feedbackColumns);
            if (isset($result['success']) && $result['success'] === false) {
                respond($result, 400);
            }
            respond($result);
            break;
        case 'DELETE':
            // FRIENDLY SYSTEM: allow all users to delete feedback
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $id = $input['id'] ?? $_GET['id'] ?? $_REQUEST['id'] ?? null;
            if (empty($id)) respond(['success' => false, 'error' => 'Missing feedback ID'], 400);
            $result = $controller->delete(['id' => (int)$id]);
            if (isset($result['success']) && $result['success'] === true) {
                respond($result);
            } else {
                respond($result, 404);
            }
            break;

        default:
            respond(['success' => false, 'error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    respond(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], 500);
}
?>