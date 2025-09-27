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
        $amount = $data['amount'] ?? 0;
        $paymentDate = $data['payment_date'] ?? '';
        $notes = $data['notes'] ?? '';
        
        if (empty($assignmentId) || empty($amount) || empty($paymentDate)) {
            throw new Exception('Assignment ID, amount, and payment date are required');
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO payments (assignment_id, amount, payment_date, notes, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        
        $stmt->execute([$assignmentId, $amount, $paymentDate, $notes]);
        
        echo json_encode(['success' => true, 'message' => 'Payment added successfully']);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>