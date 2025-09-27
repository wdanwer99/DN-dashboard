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
        
        // Add payment to advance_payment and update payment_date
        $stmt = $pdo->prepare("
            UPDATE truck_assignment_accounts 
            SET advance_payment = advance_payment + ?, 
                payment_date = ?, 
                remarks = CONCAT(COALESCE(remarks, ''), ' | Payment: $', ?, ' on ', ?, COALESCE(CONCAT(' - ', ?), ''))
            WHERE assignment_id = ?
        ");
        
        $stmt->execute([$amount, $paymentDate, $amount, $paymentDate, $notes, $assignmentId]);
        
        echo json_encode(['success' => true, 'message' => 'Payment updated successfully']);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>