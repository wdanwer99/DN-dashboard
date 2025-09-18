<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT filename, sheet_data, merged_cells FROM excel_sheets WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $sheet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($sheet) {
            echo json_encode([
                'success' => true,
                'filename' => $sheet['filename'],
                'sheet_data' => json_decode($sheet['sheet_data'], true),
                'merged_cells' => json_decode($sheet['merged_cells'], true)
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Sheet not found']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Sheet ID required']);
}
?>