<?php
// api/delete-truck.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $truck_id = $_POST['truck_id'] ?? null;
    
    if (!$truck_id) {
        throw new Exception('Truck ID is required');
    }
    
    $sql = "DELETE FROM trucks_info WHERE truck_id=?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$truck_id]);
    
    echo json_encode(['success' => true, 'message' => 'Truck deleted successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>