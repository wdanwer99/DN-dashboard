<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    // Return all delivery notes with Rep fields for client-side filtering
    $stmt = $pdo->query("
        SELECT dn_no, Customer, Project_Name, DN_Status, 
               request_arrived_date, Site_Address,
               Collect_Rep, Delivery_Rep, Receive_Rep
        FROM Delivery_Notes 
        ORDER BY created_at DESC
    ");
    
    $delivery_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $delivery_notes
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>