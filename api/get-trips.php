<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("SELECT ta.*, s.Site_Name, t.truck_no, t.driver_name 
                              FROM truck_assignments ta 
                              LEFT JOIN Site_Details s ON ta.site_Code = s.site_Code 
                              LEFT JOIN trucks_info t ON ta.truck_id = t.truck_id 
                              ORDER BY ta.assigned_date DESC");
        $stmt->execute();
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $trips]);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>