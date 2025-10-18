<?php
// api/get-projects.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT Project_code_User, 
                   GROUP_CONCAT(Batch_no_user ORDER BY Batch_no_user SEPARATOR ', ') as batches,
                   project_status,
                   MIN(created_at) as created_at
            FROM project_info 
            GROUP BY Project_code_User 
            ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $projects]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>