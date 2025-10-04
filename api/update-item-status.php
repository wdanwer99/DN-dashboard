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
        
        // Check if JSON decode was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON format');
        }
        
        $itemId = $data['item_id'] ?? '';
        $imageType = $data['image_type'] ?? '';
        $imagePath = $data['image_path'] ?? '';
        $userUpdate = $data['user_update'] ?? null;
        
        // Validate required fields
        if (empty($itemId) || empty($imageType) || empty($imagePath)) {
            throw new Exception('Item ID, image type, and image path are required');
        }
        
        // Validate item ID is numeric
        if (!is_numeric($itemId)) {
            throw new Exception('Invalid item ID format');
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
        
        // Validate image type
        if (!isset($statusMap[$imageType]) || !isset($imageColumnMap[$imageType])) {
            throw new Exception('Invalid image type. Must be: received, delivered, or collected');
        }
        
        // Check if item exists and get current status
        $checkStmt = $pdo->prepare("SELECT item_status, dn_no, item_code FROM Dn_items WHERE id = ?");
        $checkStmt->execute([$itemId]);
        $currentItem = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentItem) {
            throw new Exception('Item not found');
        }
        
        $currentStatus = $currentItem['item_status'];
        $newStatus = $statusMap[$imageType];
        $dnNo = $currentItem['dn_no'];
        $itemCode = $currentItem['item_code'];
        
        // Validate status progression
        $validProgression = [
            'Created' => ['received'],
            'Received' => ['delivered'],
            'Delivered' => ['collected']
        ];
        
        // Check if current status allows this transition
        if (!isset($validProgression[$currentStatus]) || !in_array($imageType, $validProgression[$currentStatus])) {
            throw new Exception("Cannot update from '$currentStatus' to '$newStatus'. Invalid status progression.");
        }
        
        $imageColumn = $imageColumnMap[$imageType];
        
        // Build update query - only update what exists in your table
        $updateFields = ["item_status = ?", "$imageColumn = ?"];
        $updateValues = [$newStatus, $imagePath];
        
        // Add user update if provided and column exists
        if ($userUpdate !== null) {
            $updateFields[] = "Item_User_Update = ?";
            $updateValues[] = $userUpdate;
        }
        
        // Add item ID for WHERE clause
        $updateValues[] = $itemId;
        
        // Use correct table name from your schema: Dn_items (with capital D)
        $sql = "UPDATE Dn_items SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute($updateValues);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => "Item status updated from '$currentStatus' to '$newStatus'",
                'data' => [
                    'item_id' => (int)$itemId,
                    'dn_no' => $dnNo,
                    'item_code' => $itemCode,
                    'old_status' => $currentStatus,
                    'new_status' => $newStatus,
                    'image_type' => $imageType,
                    'image_path' => $imagePath
                ]
            ]);
        } else {
            throw new Exception('No changes made. Item may not exist or data is identical.');
        }
        
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'error' => 'Database error occurred',
            'details' => $e->getMessage()
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid request method. Only POST allowed.'
    ]);
}
?>