<?php
// api/get-sites-by-batch.php (Complete Diagnostic Version)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['batch_number']) || empty($input['batch_number'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Batch number is required'
            ]);
            exit;
        }
        
        $batchNumber = $input['batch_number'];
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // First, let's check the table structure to verify field names
        $describeStmt = $pdo->prepare("DESCRIBE Site_Details");
        $describeStmt->execute();
        $tableStructure = $describeStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check what batch numbers exist in the table
        $batchCheckStmt = $pdo->prepare("SELECT DISTINCT Batch_no_user FROM Site_Details WHERE Batch_no_user IS NOT NULL AND Batch_no_user != '' LIMIT 10");
        $batchCheckStmt->execute();
        $existingBatches = $batchCheckStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Try to find sites with the exact batch number
        $stmt = $pdo->prepare("SELECT * FROM Site_Details WHERE Batch_no_user = ? ORDER BY Created_At DESC");
        $stmt->execute([$batchNumber]);
        $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Also try case-insensitive search
        $caseInsensitiveStmt = $pdo->prepare("SELECT * FROM Site_Details WHERE LOWER(Batch_no_user) = LOWER(?) ORDER BY Created_At DESC");
        $caseInsensitiveStmt->execute([$batchNumber]);
        $caseInsensitiveSites = $caseInsensitiveStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Diagnostic complete',
            'data' => $sites,
            'debug_info' => [
                'batch_searched' => $batchNumber,
                'exact_match_count' => count($sites),
                'case_insensitive_count' => count($caseInsensitiveSites),
                'table_structure' => array_column($tableStructure, 'Field'),
                'existing_batches_sample' => $existingBatches,
                'first_site_data' => count($sites) > 0 ? $sites[0] : null
            ]
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'message' => 'Database error occurred'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid request method',
        'message' => 'Only POST method allowed'
    ]);
}
?>