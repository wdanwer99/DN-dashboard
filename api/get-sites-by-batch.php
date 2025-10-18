<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM Site_Details WHERE Project_code_User = ? AND Batch_no_user = ? ORDER BY Site_Name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['project_code'] ?? null,
        $_POST['batch_number'] ?? null
    ]);
    
    $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $sites]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>