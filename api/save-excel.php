<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("INSERT INTO excel_sheets (filename, sheet_data, merged_cells, created_at) VALUES (?, ?, ?, NOW())");
        
        $result = $stmt->execute([
            $data['filename'],
            json_encode($data['sheet_data']),
            json_encode($data['merged_cells'])
        ]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Excel sheet saved successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save Excel sheet']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>