<?php
// api/update-batch.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if new batch number already exists for this project
    $checkSql = "SELECT id FROM project_info WHERE Project_code_User = ? AND Batch_no_user = ? AND id != ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([
        $_POST['project_code'] ?? null,
        $_POST['new_batch'] ?? null,
        $_POST['batch_id'] ?? null
    ]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'Batch number already exists for this project']);
        exit;
    }
    
    $sql = "UPDATE project_info SET Batch_no_user = ? WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['new_batch'] ?? null,
        $_POST['batch_id'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Batch number updated successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>