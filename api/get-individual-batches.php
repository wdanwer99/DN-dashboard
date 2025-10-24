<?php
// api/get-individual-batches.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT id, Project_code_User, Batch_no_user, created_at FROM project_info WHERE Project_code_User = ? ORDER BY Batch_no_user";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['project_code'] ?? null]);
    
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $batches]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>