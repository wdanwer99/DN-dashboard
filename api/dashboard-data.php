<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    // Get dashboard statistics
    $stats = [];
    
    // Total delivery notes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Delivery_Notes");
    $stats['total_delivery_notes'] = $stmt->fetch()['total'] ?? 0;
    
    // Active delivery notes
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM Delivery_Notes WHERE DN_Status IN ('Created', 'Received', 'Delivered')");
    $stats['active_delivery_notes'] = $stmt->fetch()['active'] ?? 0;
    
    // Total trucks
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM trucks_info");
    $stats['total_trucks'] = $stmt->fetch()['total'] ?? 0;
    
    // Available trucks
    $stmt = $pdo->query("SELECT COUNT(*) as available FROM trucks_info WHERE status = 'Available'");
    $stats['available_trucks'] = $stmt->fetch()['available'] ?? 0;
    
    // Delivery completion rate
    $stmt = $pdo->query("SELECT 
        COUNT(CASE WHEN DN_Status = 'Delivered' THEN 1 END) as delivered,
        COUNT(*) as total
        FROM Delivery_Notes");
    $completion = $stmt->fetch();
    $stats['completion_rate'] = $completion['total'] > 0 ? round(($completion['delivered'] / $completion['total']) * 100, 1) : 0;
    
    // Recent activities
    $stmt = $pdo->query("SELECT dn_no, Customer, DN_Status, created_at FROM Delivery_Notes ORDER BY created_at DESC LIMIT 10");
    $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recent_activities' => $recent_activities
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>