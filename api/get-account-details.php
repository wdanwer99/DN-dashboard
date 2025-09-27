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
            SELECT 
                taa.*, 
                ta.site_Code, 
                ta.dn_no, 
                t.truck_no,
                t.driver_name,
                (taa.cost - taa.advance_payment - COALESCE(taa.Gove_Fees, 0)) as balance
            FROM truck_assignment_accounts taa
            LEFT JOIN truck_assignments ta ON taa.assignment_id = ta.assignment_id
            LEFT JOIN trucks_info t ON ta.truck_id = t.truck_id
            WHERE taa.assignment_id = ?
        ");
        $stmt->execute([$assignmentId]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Ensure balance is calculated
        if ($account && !isset($account['balance'])) {
            $account['balance'] = $account['cost'] - $account['advance_payment'] - ($account['Gove_Fees'] ?? 0);
        }

        if ($account) {
            echo json_encode(['success' => true, 'data' => $account]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Account record not found']);
        }

    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>