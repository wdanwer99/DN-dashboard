<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get delivery notes count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Delivery_Notes");
    $deliveryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get sites count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Site_Details");
    $sitesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get trucks count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM trucks_info");
    $trucksCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get items count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM items_info");
    $itemsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    echo json_encode([
        'success' => true,
        'data' => [
            'delivery_notes' => $deliveryCount,
            'sites' => $sitesCount,
            'trucks' => $trucksCount,
            'items' => $itemsCount
        ]
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>