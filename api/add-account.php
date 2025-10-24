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
        
        if (!$data) {
            throw new Exception('Invalid JSON data');
        }
        
        // Calculate balance and truck remaining
        $balance = $data['cost'] - $data['advance_payment'] - $data['Gove_Fees'];
        $truckRemaining = $data['cost'] - $data['advance_payment'] - $data['Gove_Fees'];
        
        $stmt = $pdo->prepare("INSERT INTO truck_assignment_accounts (
            assignment_id, cost, advance_payment, Gove_Fees, balance, 
            payment_status, payment_date, remarks, Company_code, Truck_Remaining,
            loading_amount, distribution_amount, off_loading_cost
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $result = $stmt->execute([
            $data['assignment_id'],
            $data['cost'],
            $data['advance_payment'],
            $data['Gove_Fees'],
            $balance,
            $data['payment_status'],
            $data['payment_date'] ?: null,
            $data['remarks'],
            $data['Company_code'],
            $truckRemaining,
            $data['loading_amount'],
            $data['distribution_amount'],
            $data['off_loading_cost']
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Account record created successfully',
                'id' => $pdo->lastInsertId()
            ]);
        } else {
            throw new Exception('Failed to create account record');
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>