<?php
// api/update-item.php
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
    
    $sql = "UPDATE items_info SET item_code=?, item_description=?, item_Status=?, Company_code=? WHERE id=?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['item_code'] ?? null,
        $_POST['item_description'] ?? null,
        $_POST['item_Status'] ?? 'Active',
        $_POST['Company_code'] ?? null,
        $id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
