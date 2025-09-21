<?php
// api/delete-item.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception('Item ID is required');
    }
    
    $sql = "DELETE FROM items_info WHERE id=?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>