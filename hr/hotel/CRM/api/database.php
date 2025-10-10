<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../lib/ApiDatabase.php';

use CRM\Lib\ApiDatabase;

// Backwards-compatible Database class wrapper (keeps procedural compatibility)
class Database {
    private $db;

    public function __construct(array $config = []) {
        $this->db = new ApiDatabase($config);
    }

    public function getConnection() {
        return $this->db->getConnection();
    }
}

// Helper function to get database connection (unchanged API)
function getDBConnection() {
    $database = new Database();
    return $database->getConnection();
}

// Helper function to send JSON response
function sendJsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Helper function to validate required fields
function validateRequiredFields($data, $required_fields) {
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            return false;
        }
    }
    return true;
}
?>