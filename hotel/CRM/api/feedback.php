<?php
// feedback.php - Delegates to FeedbackController (OOP) while preserving behavior
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

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

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

try {
    // Get table structures
    $feedbackColumns = $controller->getTableColumns('feedback');
    $guestColumns = $controller->getTableColumns('guests');

    switch ($method) {
        case 'GET':
            $params = [];
            if (isset($_GET['type'])) $params['type'] = $_GET['type'];
            if (isset($_GET['status'])) $params['status'] = $_GET['status'];
            if (isset($_GET['search'])) $params['search'] = $_GET['search'];

            $items = $controller->list($params);

            // Add guest_profile_url if guest_id is present
            foreach ($items as &$item) {
                if (!empty($item['guest_id'])) {
                    // This URL is just a placeholder, it does not actually open a guest profile modal/page in the CRM UI.
                    // It points to guests.php?guest_id=xxx, which is not a user-facing profile page.
                    $item['guest_profile_url'] = 'guests.php?guest_id=' . urlencode($item['guest_id']);
                }
            }
            unset($item);

            respond(['success' => true, 'data' => $items]);
            break;

        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['guest_name']) || empty($input['comment'])) {
                respond(['success' => false, 'error' => 'Missing required fields: guest_name and comment'], 400);
            }

            $new = $controller->create($input, $feedbackColumns);
            respond(['success' => true, 'data' => $new], 201);
            break;

        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $controller->update($input, $feedbackColumns);
            if (isset($result['success']) && $result['success'] === false) {
                respond($result, 400);
            }
            respond($result);
            break;

        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $controller->delete($input);
            if (isset($result['success']) && $result['success'] === false) {
                respond($result, 404);
            }
            respond($result);
            break;

        default:
            respond(['success' => false, 'error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    respond(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], 500);
}
?>