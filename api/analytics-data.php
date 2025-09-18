<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $analytics = [];
    
    // Delivery status distribution
    $stmt = $pdo->query("SELECT DN_Status, COUNT(*) as count FROM Delivery_Notes GROUP BY DN_Status");
    $analytics['status_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Monthly delivery trends
    $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM Delivery_Notes GROUP BY month ORDER BY month DESC LIMIT 12");
    $analytics['monthly_trends'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top customers
    $stmt = $pdo->query("SELECT Customer, COUNT(*) as delivery_count FROM Delivery_Notes GROUP BY Customer ORDER BY delivery_count DESC LIMIT 10");
    $analytics['top_customers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Truck utilization
    $stmt = $pdo->query("SELECT t.status, COUNT(*) as count FROM trucks_info t GROUP BY t.status");
    $analytics['truck_utilization'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Average delivery time (if dates available)
    $stmt = $pdo->query("SELECT AVG(DATEDIFF(Updated_at, created_at)) as avg_days FROM Delivery_Notes WHERE DN_Status = 'Delivered'");
    $analytics['avg_delivery_time'] = $stmt->fetch()['avg_days'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'analytics' => $analytics
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>