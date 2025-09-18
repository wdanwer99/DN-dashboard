<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT id, filename, created_at FROM excel_sheets ORDER BY created_at DESC");
    $sheets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'sheets' => $sheets
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>