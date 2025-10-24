<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "UPDATE truck_assignment_accounts SET 
                assignment_id = ?, cost = ?, advance_payment = ?, Gove_Fees = ?, 
                payment_status = ?, payment_date = ?, remarks = ?, updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $input['assignment_id'],
            $input['cost'],
            $input['advance_payment'],
            $input['Gove_Fees'],
            $input['payment_status'],
            $input['payment_date'] ?: null,
            $input['remarks'],
            $input['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Only POST method allowed']);
}
?>