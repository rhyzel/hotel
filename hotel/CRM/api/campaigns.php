<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$method = $_SERVER['REQUEST_METHOD'] ?? null;
if ($method === 'OPTIONS') { http_response_code(200); exit(); }

require_once __DIR__ . '/../bootstrap.php';

require_once __DIR__ . '/database.php';
use CRM\Lib\ApiDatabase as CRMDatabase;
use CRM\Lib\CampaignRepository;
use CRM\Lib\CampaignService;

// Database connection
$db = new Database();
$conn = $db->getConnection();

// JSON response helper
function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Initialize repository + service
$repo = new CampaignRepository($conn);
$service = new CampaignService($repo);

try {
    if ($method === null) {
        respond(['success' => false, 'error' => 'Invalid execution context. Must be called via HTTP request.'], 400);
    }

    $validTypes = ['email', 'sms', 'both'];
    $validStatuses = ['draft', 'scheduled', 'active', 'completed'];

    // --- Loyalty tiers for campaign targeting ---
    if ($method === 'GET' && isset($_GET['tiers']) && $_GET['tiers'] == '1') {
        $stmt = $conn->query("SELECT tier, name FROM loyalty_programs ORDER BY tier ASC");
        $tiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond(['success' => true, 'data' => $tiers]);
    }

    /* -----------------------
       GET — list campaigns
    ------------------------ */
    if ($method === 'GET') {
        $campaigns = $service->listCampaigns();
        $data = array_map(fn($c) => $c->toArray(), $campaigns);
        respond(['success' => true, 'data' => $data]);
    }

    /* -----------------------
       POST — create campaign
    ------------------------ */
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($input['name']) || empty($input['type']) || empty($input['target_audience']) || empty($input['message'])) {
            respond(['success' => false, 'error' => 'Missing required fields'], 400);
        }

        if (!in_array($input['type'], $validTypes)) $input['type'] = 'email';
        if (!in_array($input['status'] ?? '', $validStatuses)) $input['status'] = 'draft';

        $campaign = $service->createCampaign($input);
        respond(['success' => true, 'message' => 'Campaign created', 'data' => $campaign->toArray()], 201);
    }

    /* -----------------------
       PUT — update campaign
    ------------------------ */
    if ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($input['id'])) {
            respond(['success' => false, 'error' => 'Missing campaign ID'], 400);
        }

        if (!in_array($input['type'] ?? 'email', $validTypes)) $input['type'] = 'email';
        if (!in_array($input['status'] ?? 'draft', $validStatuses)) $input['status'] = 'draft';

        $ok = $service->updateCampaign($input);
        if (!$ok) respond(['success' => false, 'error' => 'Update failed'], 400);

        $updated = $service->getCampaign((int)$input['id']);
        respond([
            'success' => true,
            'message' => 'Campaign updated',
            'data' => $updated ? $updated->toArray() : null
        ]);
    }

    /* -----------------------
       DELETE — delete campaign
    ------------------------ */
    if ($method === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($input['id'])) {
            respond(['success' => false, 'error' => 'Missing campaign ID'], 400);
        }

        $ok = $service->deleteCampaign((int)$input['id']);
        if ($ok) respond(['success' => true, 'message' => 'Campaign deleted']);
        respond(['success' => false, 'error' => 'Campaign not found'], 404);
    }

    // If none matched
    respond(['success' => false, 'error' => 'Invalid method'], 405);

} catch (Exception $e) {
    respond(['success' => false, 'error' => $e->getMessage()], 500);
}
