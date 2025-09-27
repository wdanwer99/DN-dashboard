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
        
        $itemId = $data['item_id'] ?? '';
        //$imageType = $data['image_type'] ?? '';
        $imagePath = $data['https://arpusoft.com/basesystem/resources/images'] ?? '';
        
        if (empty($itemId) || empty($imageType) || empty($imagePath)) {
            throw new Exception('Item ID, image type, and image path are required');
        }
        
        // Define status progression based on image type
        $statusMap = [
            'received' => 'Received',
            'delivered' => 'Delivered', 
            'collected' => 'Collected'
        ];
        
        $imageColumnMap = [
            'received' => 'Item_received_Image',
            'delivered' => 'Item_Delivered_Image',
            'collected' => 'Item_Collected_Image'
        ];
        
        if (!isset($statusMap[$imageType]) || !isset($imageColumnMap[$imageType])) {
            throw new Exception('Invalid image type');
        }
        
        // Check current status first
        $checkStmt = $pdo->prepare("SELECT item_status FROM dn_items WHERE id = ?");
        $checkStmt->execute([$itemId]);
        $currentItem = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentItem) {
            throw new Exception('Item not found');
        }
        
        $currentStatus = $currentItem['item_status'];
        $newStatus = $statusMap[$imageType];
        
        // Validate status progression
        $validProgression = [
            'Created' => ['received'],
            'Received' => ['delivered'],
            'Delivered' => ['collected']
        ];
        
        if (!isset($validProgression[$currentStatus]) || !in_array($imageType, $validProgression[$currentStatus])) {
            throw new Exception("Cannot update from $currentStatus to $newStatus. Invalid status progression.");
        }
        
        $imageColumn = $imageColumnMap[$imageType];
        
        $stmt = $pdo->prepare("
            UPDATE dn_items 
            SET item_status = ?, $imageColumn = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$newStatus, $imagePath, $itemId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => "Item status updated to $newStatus",
                'new_status' => $newStatus
            ]);
        } else {
            throw new Exception('Item not found or no changes made');
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>