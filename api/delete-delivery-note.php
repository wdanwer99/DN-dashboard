<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $id = $data['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Delivery note ID is required');
        }
        
        $pdo->beginTransaction();
        
        // Delete related items first
        $deleteItemsStmt = $pdo->prepare("DELETE FROM dn_items WHERE dn_no = (SELECT dn_no FROM Delivery_Notes WHERE id = ?)");
        $deleteItemsStmt->execute([$id]);
        
        // Delete delivery note
        $deleteNoteStmt = $pdo->prepare("DELETE FROM Delivery_Notes WHERE id = ?");
        $deleteNoteStmt->execute([$id]);
        
        if ($deleteNoteStmt->rowCount() > 0) {
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Delivery note deleted successfully']);
        } else {
            $pdo->rollBack();
            throw new Exception('Delivery note not found');
        }
        
    } catch(Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>