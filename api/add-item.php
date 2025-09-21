<?php
// api/add-item.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "INSERT INTO items_info (item_code, item_description, item_Status, Company_code) VALUES (?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['item_code'] ?? null,
        $_POST['item_description'] ?? null,
        $_POST['item_Status'] ?? 'Active',
        $_POST['Company_code'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Item added successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>