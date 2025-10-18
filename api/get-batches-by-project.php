<?php
// api/get-batches-by-project.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['project_code']) || empty($input['project_code'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Project code is required'
            ]);
            exit;
        }
        
        $projectCode = $input['project_code'];
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT * FROM project_info WHERE Project_code_User = ? ORDER BY created_at DESC");
        $stmt->execute([$projectCode]);
        $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true, 
            'data' => $batches,
            'message' => 'Batches loaded successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'message' => 'Failed to load batches'
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