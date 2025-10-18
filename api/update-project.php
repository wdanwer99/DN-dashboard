<?php
// api/update-project.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if new project code already exists
    $checkSql = "SELECT id FROM project_info WHERE Project_code_User = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$_POST['new_project_code'] ?? null]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'New project code already exists']);
        exit;
    }
    
    $sql = "UPDATE project_info SET Project_code_User = ? WHERE Project_code_User = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['new_project_code'] ?? null,
        $_POST['old_project_code'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Project code updated successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>