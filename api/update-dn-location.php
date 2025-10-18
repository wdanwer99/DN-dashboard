<?php
// api/update-dn-location.php
require_once '../config/database.php';

// Get POST data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Log received data for debugging
error_log("Received location update request: " . print_r($data, true));

// Validate required fields
$required = ['dn_no', 'status_type'];
$errors = validateInput($data, $required);

if (!empty($errors)) {
    sendResponse(false, implode(', ', $errors), null, 400);
}

$dn_no = trim($data['dn_no']);
$status_type = strtolower(trim($data['status_type']));
$current_location_link = isset($data['current_location_link']) ? trim($data['current_location_link']) : null;
$picked_location_link = isset($data['picked_location_link']) ? trim($data['picked_location_link']) : null;

// Validate status type
$valid_statuses = ['received', 'delivered', 'collected'];
if (!in_array($status_type, $valid_statuses)) {
    sendResponse(false, "Invalid status_type. Must be one of: " . implode(', ', $valid_statuses), null, 400);
}

// Validate that at least one location link is provided
if (empty($current_location_link) && empty($picked_location_link)) {
    sendResponse(false, "At least one location link (current or picked) must be provided", null, 400);
}

try {
    // Verify DN exists
    $checkStmt = $pdo->prepare("SELECT id FROM Delivery_Notes WHERE dn_no = ?");
    $checkStmt->execute([$dn_no]);
    $dn = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$dn) {
        sendResponse(false, "Delivery Note not found with dn_no: $dn_no", null, 404);
    }

    // Build update query - try multiple column name variations
    $updates = [];
    $params = [];
    
    // Get actual column names from database
    $columnsQuery = $pdo->query("SHOW COLUMNS FROM Delivery_Notes");
    $existingColumns = $columnsQuery->fetchAll(PDO::FETCH_COLUMN);
    error_log("Available columns: " . implode(', ', $existingColumns));
    
    // Map column names (case-insensitive check)
    $columnLookup = array_combine(
        array_map('strtolower', $existingColumns),
        $existingColumns
    );
    
    switch ($status_type) {
        case 'received':
            $pickedKey = 'received_location';
            $autoKey = 'received_auto_location';
            
            if ($picked_location_link !== null && isset($columnLookup[strtolower($pickedKey)])) {
                $realCol = $columnLookup[strtolower($pickedKey)];
                $updates[] = "$realCol = ?";
                $params[] = $picked_location_link;
            }
            if ($current_location_link !== null && isset($columnLookup[strtolower($autoKey)])) {
                $realCol = $columnLookup[strtolower($autoKey)];
                $updates[] = "$realCol = ?";
                $params[] = $current_location_link;
            }
            break;
            
        case 'delivered':
            $pickedKey = 'delivered_location';
            $autoKey = 'delivered_auto_location';
            
            if ($picked_location_link !== null && isset($columnLookup[strtolower($pickedKey)])) {
                $realCol = $columnLookup[strtolower($pickedKey)];
                $updates[] = "$realCol = ?";
                $params[] = $picked_location_link;
            }
            if ($current_location_link !== null && isset($columnLookup[strtolower($autoKey)])) {
                $realCol = $columnLookup[strtolower($autoKey)];
                $updates[] = "$realCol = ?";
                $params[] = $current_location_link;
            }
            break;
            
        case 'collected':
            $pickedKey = 'collected_location';
            $autoKey = 'collected_auto_location';
            
            if ($picked_location_link !== null && isset($columnLookup[strtolower($pickedKey)])) {
                $realCol = $columnLookup[strtolower($pickedKey)];
                $updates[] = "$realCol = ?";
                $params[] = $picked_location_link;
            }
            if ($current_location_link !== null && isset($columnLookup[strtolower($autoKey)])) {
                $realCol = $columnLookup[strtolower($autoKey)];
                $updates[] = "$realCol = ?";
                $params[] = $current_location_link;
            }
            break;
    }
    
    if (empty($updates)) {
        sendResponse(false, "Could not find matching location columns in database for status: $status_type", null, 400);
    }
    
    // Build and execute update query
    $sql = "UPDATE Delivery_Notes SET " . implode(', ', $updates) . ", Updated_at = NOW() WHERE dn_no = ?";
    $params[] = $dn_no;
    
    error_log("Executing SQL: $sql");
    error_log("Parameters: " . print_r($params, true));
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    $rowsAffected = $stmt->rowCount();
    
    error_log("Rows affected: $rowsAffected");
    
    sendResponse(
        true, 
        "Location links updated successfully for DN: $dn_no ($rowsAffected row(s) updated)",
        [
            'dn_no' => $dn_no,
            'status_type' => $status_type,
            'rows_affected' => $rowsAffected,
            'updated_fields' => [
                'current_location' => $current_location_link !== null,
                'picked_location' => $picked_location_link !== null
            ]
        ],
        200
    );
    
} catch(PDOException $e) {
    error_log("Database error in update-dn-location.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    sendResponse(false, "Database error: " . $e->getMessage(), null, 500);
} catch(Exception $e) {
    error_log("Error in update-dn-location.php: " . $e->getMessage());
    sendResponse(false, "Error: " . $e->getMessage(), null,500);
}
?>