<?php
require_once 'api/database.php';

try {
    $db = getDBConnection();
    
    // Read and execute the SQL cleanup script
    $sql = file_get_contents(__DIR__ . '/clean_guest_billing.sql');
    $db->exec($sql);
    
    echo "Successfully cleaned up guest billing history.\n";
    echo "A backup of the original data has been created in guest_billing_backup table.\n";
    
} catch (PDOException $e) {
    echo "Error cleaning up guest billing: " . $e->getMessage() . "\n";
}