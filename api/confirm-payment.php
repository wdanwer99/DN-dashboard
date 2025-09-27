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
        
        $paymentId = $data['payment_id'] ?? '';
        $assignmentId = $data['assignment_id'] ?? '';
        
        if (empty($paymentId) || empty($assignmentId)) {
            throw new Exception('Payment ID and Assignment ID are required');
        }
        
        $pdo->beginTransaction();
        
        // Get payment amount
        $paymentStmt = $pdo->prepare("SELECT amount FROM payments WHERE id = ? AND status = 'pending'");
        $paymentStmt->execute([$paymentId]);
        $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            throw new Exception('Payment not found or already confirmed');
        }
        
        // Update payment status
        $updatePaymentStmt = $pdo->prepare("UPDATE payments SET status = 'confirmed' WHERE id = ?");
        $updatePaymentStmt->execute([$paymentId]);
        
        // Update account balance
        $updateAccountStmt = $pdo->prepare("
            UPDATE truck_assignment_accounts 
            SET advance_payment = advance_payment + ? 
            WHERE assignment_id = ?
        ");
        $updateAccountStmt->execute([$payment['amount'], $assignmentId]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Payment confirmed and balance updated']);
        
    } catch(Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>