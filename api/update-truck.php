<?php
// api/update-truck.php
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
    
    $sql = "UPDATE trucks_info SET truck_no=?, driver_name=?, driver_phone=?, capacity=?, status=?, Company_code=? WHERE truck_id=?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['truck_no'] ?? null,
        $_POST['driver_name'] ?? null,
        $_POST['driver_phone'] ?? null,
        $_POST['capacity'] ?? null,
        $_POST['status'] ?? 'Available',
        $_POST['Company_code'] ?? null,
        $truck_id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Truck updated successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>