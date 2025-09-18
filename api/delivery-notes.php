<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT dn_no, Customer, Project_Name, DN_Status, request_arrived_date, Site_Address FROM Delivery_Notes ORDER BY created_at DESC");
    $delivery_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $delivery_notes
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>