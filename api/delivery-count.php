<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT COUNT(DISTINCT dn_no) as total_count FROM Delivery_Notes WHERE dn_no IS NOT NULL AND dn_no != ''");
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'count' => $result['total_count'] ?? 0
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>