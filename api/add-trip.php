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
        
        $stmt = $pdo->prepare("INSERT INTO truck_assignments (
            assignment_id, site_Code, dn_no, truck_id, status, Company_code
        ) VALUES (?, ?, ?, ?, ?, ?)");
        
        $result = $stmt->execute([
            $data['assignment_id'],
            $data['site_Code'],
            $data['dn_no'],
            $data['truck_id'],
            $data['status'],
            $data['Company_code']
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Trip assignment created successfully',
                'id' => $pdo->lastInsertId()
            ]);
        } else {
            throw new Exception('Failed to create trip assignment');
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>