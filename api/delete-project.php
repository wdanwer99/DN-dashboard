<?php
// api/delete-project.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "DELETE FROM project_info WHERE Project_code_User = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['project_code'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => 'Project and all batches deleted successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>