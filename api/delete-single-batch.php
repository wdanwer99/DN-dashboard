<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $project_code = $_POST['project_code'];
    $batch_number = $_POST['batch_number'];

    $stmt = $pdo->prepare("DELETE FROM project_info WHERE Project_code_User = ? AND Batch_no_user = ?");
    $stmt->execute([$project_code, $batch_number]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Batch not found']);
    }

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>