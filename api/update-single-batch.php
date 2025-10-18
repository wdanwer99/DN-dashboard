<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $project_code = $_POST['project_code'];
    $old_batch = $_POST['old_batch'];
    $new_batch = $_POST['new_batch'];

    $stmt = $pdo->prepare("UPDATE project_info SET Batch_no_user = ? WHERE Project_code_User = ? AND Batch_no_user = ?");
    $stmt->execute([$new_batch, $project_code, $old_batch]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Batch not found or no changes made']);
    }

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>