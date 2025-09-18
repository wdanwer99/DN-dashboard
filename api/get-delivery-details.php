<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if (isset($_GET['dn_no'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Delivery_Notes WHERE dn_no = ?");
        $stmt->execute([$_GET['dn_no']]);
        $delivery = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($delivery) {
            echo json_encode([
                'success' => true,
                'data' => $delivery
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Delivery note not found']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'DN number required']);
}
?>