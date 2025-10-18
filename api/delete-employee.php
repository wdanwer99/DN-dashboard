<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "DELETE FROM employees WHERE employee_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['employee_id'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => 'Employee deleted successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>