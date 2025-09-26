<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("SELECT taa.*, ta.site_Code, ta.dn_no, t.truck_no, t.driver_name 
                              FROM truck_assignment_accounts taa 
                              LEFT JOIN truck_assignments ta ON taa.assignment_id = ta.assignment_id 
                              LEFT JOIN trucks_info t ON ta.truck_id = t.truck_id 
                              ORDER BY taa.created_at DESC");
        $stmt->execute();
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $accounts]);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>