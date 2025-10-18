<?php
// api/add-project.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if batch already exists for this project
    $checkSql = "SELECT id FROM project_info WHERE Project_code_User = ? AND Batch_no_user = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([
        $_POST['Project_code_User'] ?? null,
        $_POST['Batch_no_user'] ?? null
    ]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'This batch number already exists for this project']);
        exit;
    }
    
    $sql = "INSERT INTO project_info (Project_code_User, Batch_no_user, project_status) VALUES (?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['Project_code_User'] ?? null,
        $_POST['Batch_no_user'] ?? null,
        $_POST['project_status'] ?? 'active'
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Project added successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>