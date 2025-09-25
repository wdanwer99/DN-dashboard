<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $dnNo = $_GET['dn_no'] ?? '';
    
    if (empty($dnNo)) {
        throw new Exception('DN number is required');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM Delivery_Note_Items WHERE dn_no = ? ORDER BY id");
    $stmt->execute([$dnNo]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $items]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>