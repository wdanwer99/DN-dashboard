<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("INSERT INTO Delivery_Notes (dn_no, Customer, Project_Name, Site_Address, DN_Status, created_at) VALUES (?, ?, ?, ?, 'Created', NOW())");
        
        $result = $stmt->execute([
            $data['dn_no'],
            $data['customer'],
            $data['project_name'],
            $data['site_address']
        ]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Delivery note uploaded successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload delivery note']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>