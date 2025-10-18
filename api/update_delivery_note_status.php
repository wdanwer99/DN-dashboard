<?php
// api/update_delivery_note_status.php
require_once '../config/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Method not allowed. Use POST request.', null, 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$errors = validateInput($input, ['delivery_note_id']);
if (!empty($errors)) {
    sendResponse(false, implode(', ', $errors), null, 400);
}

$delivery_note_id = $input['delivery_note_id'];

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if delivery note exists
    $stmt = $pdo->prepare("SELECT id, DN_Status, dn_no FROM Delivery_Notes WHERE id = ?");
    $stmt->execute([$delivery_note_id]);
    $deliveryNote = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$deliveryNote) {
        $pdo->rollBack();
        sendResponse(false, 'Delivery note not found', null, 404);
    }
    
    // Get all items for this delivery note
    $stmt = $pdo->prepare("
        SELECT id, item_status 
        FROM Dn_items 
        WHERE dn_no = ?
    ");
    $stmt->execute([$deliveryNote['dn_no']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        $pdo->rollBack();
        sendResponse(false, 'No items found for this delivery note', null, 404);
    }
    
    // Count items by status
    $statusCounts = [
        'received' => 0,
        'delivered' => 0,
        'collected' => 0,
        'pending' => 0
    ];
    
    $totalItems = count($items);
    
    foreach ($items as $item) {
        $itemStatus = strtolower(trim($item['item_status']));
        if (isset($statusCounts[$itemStatus])) {
            $statusCounts[$itemStatus]++;
        } else {
            $statusCounts['pending']++;
        }
    }
    
    // Determine the new delivery note status
    $newStatus = null;
    $statusMessage = '';
    
    if ($statusCounts['received'] === $totalItems) {
        $newStatus = 'received';
        $statusMessage = 'All items have been received';
    } elseif ($statusCounts['delivered'] === $totalItems) {
        $newStatus = 'delivered';
        $statusMessage = 'All items have been delivered';
    } elseif ($statusCounts['collected'] === $totalItems) {
        $newStatus = 'collected';
        $statusMessage = 'All items have been collected';
    } else {
        // Mixed statuses - calculate which status is dominant or keep as in-progress
        $statusMessage = sprintf(
            'Items status: %d received, %d delivered, %d collected, %d pending',
            $statusCounts['received'],
            $statusCounts['delivered'],
            $statusCounts['collected'],
            $statusCounts['pending']
        );
        
        // Optional: Set to 'in_progress' if there are mixed statuses
        if ($statusCounts['received'] > 0 || $statusCounts['delivered'] > 0 || $statusCounts['collected'] > 0) {
            $newStatus = 'in_progress';
        } else {
            $newStatus = 'pending';
        }
    }
    
    // Update delivery note status if changed
    $updated = false;
    if ($newStatus && $newStatus !== $deliveryNote['DN_Status']) {
        $stmt = $pdo->prepare("
            UPDATE delivery_notes 
            SET DN_Status = ?, Updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$newStatus, $delivery_note_id]);
        $updated = true;
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Prepare response data
    $responseData = [
        'delivery_note_id' => $delivery_note_id,
        'previous_status' => $deliveryNote['DN_Status'],
        'current_status' => $newStatus,
        'updated' => $updated,
        'total_items' => $totalItems,
        'status_breakdown' => $statusCounts,
        'message' => $statusMessage
    ];
    
    sendResponse(
        true, 
        $updated ? 'Delivery note status updated successfully' : 'No status change required',
        $responseData,
        200
    );
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    sendResponse(false, 'Database error: ' . $e->getMessage(), null, 500);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    sendResponse(false, 'Error: ' . $e->getMessage(), null,500);
}
?>