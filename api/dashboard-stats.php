<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize counts
    $deliveryCount = 0;
    $sitesCount = 0;
    $trucksCount = 0;
    $itemsCount = 0;
    $projectsCount = 0;
    $batchesCount = 0;

    // Get delivery notes count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Delivery_Notes");
        $deliveryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch(PDOException $e) {
        error_log("Error getting delivery notes count: " . $e->getMessage());
    }

    // Get sites count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Site_Details");
        $sitesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch(PDOException $e) {
        error_log("Error getting sites count: " . $e->getMessage());
    }

    // Get trucks count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM trucks_info");
        $trucksCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch(PDOException $e) {
        error_log("Error getting trucks count: " . $e->getMessage());
    }

    // Get items count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM items_info");
        $itemsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch(PDOException $e) {
        error_log("Error getting items count: " . $e->getMessage());
    }

    // Get projects count
    try {
        $stmt = $pdo->query("SELECT COUNT(DISTINCT Project_code_User) as count FROM project_info");
        $projectsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch(PDOException $e) {
        error_log("Error getting projects count: " . $e->getMessage());
    }

    // Get batches count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM project_info");
        $batchesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch(PDOException $e) {
        error_log("Error getting batches count: " . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'delivery_notes' => (int)$deliveryCount,
            'sites' => (int)$sitesCount,
            'trucks' => (int)$trucksCount,
            'items' => (int)$itemsCount,
            'projects' => (int)$projectsCount,
            'batches' => (int)$batchesCount
        ]
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>