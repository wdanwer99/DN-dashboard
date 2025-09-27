<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $assignmentId = $data['assignment_id'] ?? '';
        
        if (empty($assignmentId)) {
            throw new Exception('Assignment ID is required');
        }
        
        $stmt = $pdo->prepare("
            SELECT * FROM payments 
            WHERE assignment_id = ? 
            ORDER BY payment_date DESC, created_at DESC
        ");
        $stmt->execute([$assignmentId]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $payments]);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>