<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = file_get_contents('php://input');
        error_log("Raw input: " . $input);
        
        $requestData = json_decode($input, true);
        error_log("Decoded data: " . print_r($requestData, true));
        
        if (!$requestData) {
            throw new Exception('Invalid JSON data received');
        }
        
        // Handle both old format (direct data) and new format (deliveryNote + items)
        $data = isset($requestData['deliveryNote']) ? $requestData['deliveryNote'] : $requestData;
        $items = isset($requestData['items']) ? $requestData['items'] : [];
        
        // Check for duplicate DN number
        if (!empty($data['dn_no'])) {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Delivery_Notes WHERE dn_no = ?");
            $checkStmt->execute([$data['dn_no']]);
            $count = $checkStmt->fetchColumn();
            
            if ($count > 0) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Duplicate delivery note: DN number ' . $data['dn_no'] . ' already exists'
                ]);
                exit;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO Delivery_Notes (
            print_date, purpose_of_delivery, delivery_address, site_Code, contract_info,
            Customer, Customer_PO, Customer_Tel, Project_Name, Project_Code,
            Product_Category, Product_Manager, Special_Unloading_Req, Installation_Environment,
            Description_MR, From_Warehouse, Warehouse_Keeper, Warehouse_Keeper_tel,
            Description_DN, Including_dangerous_goods, pickup_address, Site_Address,
            dn_no, mr_no, receiver_name, receiver_tel, Receiver_Company_Name,
            request_arrived_date, request_shipment_date, logistics_specialist,
            logistics_specialist_Tel, DN_Status, Company_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Created', ?)");
        
        // Helper function to handle empty strings as null
        $getValue = function($key) use ($data) {
            $value = $data[$key] ?? null;
            return ($value === '' || $value === null) ? null : $value;
        };
        
        $params = [
            $getValue('print_date'),
            $getValue('purpose_of_delivery'),
            $getValue('delivery_address'),
            $getValue('site_Code'),
            $getValue('contract_info'),
            $getValue('Customer'),
            $getValue('Customer_PO'),
            $getValue('Customer_Tel'),
            $getValue('Project_Name'),
            $getValue('Project_Code'),
            $getValue('Product_Category'),
            $getValue('Product_Manager'),
            $getValue('Special_Unloading_Req'),
            $getValue('Installation_Environment'),
            $getValue('Description_MR'),
            $getValue('From_Warehouse'),
            $getValue('Warehouse_Keeper'),
            $getValue('Warehouse_Keeper_tel'),
            $getValue('Description_DN'),
            $getValue('Including_dangerous_goods'),
            $getValue('pickup_address'),
            $getValue('Site_Address'),
            $getValue('dn_no'),
            $getValue('mr_no'),
            $getValue('receiver_name'),
            $getValue('receiver_tel'),
            $getValue('Receiver_Company_Name'),
            $getValue('request_arrived_date'),
            $getValue('request_shipment_date'),
            $getValue('logistics_specialist'),
            $getValue('logistics_specialist_Tel'),
            // $getValue('received_location'),
            // $getValue('received_Auto_location'),
            // $getValue('Collected_location'),
            // $getValue('Collected_Auto_location'),
            $getValue('Company_code') ?: '1000'
        ];
        
        error_log("SQL params: " . print_r($params, true));
        
        $result = $stmt->execute($params);
        
        if ($result) {
            $insertId = $pdo->lastInsertId();
            $itemsInserted = 0;
            
            // Insert items if provided
            if (!empty($items)) {
                $itemStmt = $pdo->prepare("INSERT INTO Dn_items (
                    dn_no, site_code, item_code, qty, item_description, Company_code
                ) VALUES (?, ?, ?, ?, ?, ?)");
                
                foreach ($items as $item) {
                    // Handle null values properly
                    $itemCode = isset($item['item_code']) && $item['item_code'] !== '' ? trim($item['item_code']) : null;
                    $qty = isset($item['qty']) && $item['qty'] !== '' ? floatval($item['qty']) : null;
                    $description = isset($item['item_description']) && $item['item_description'] !== '' ? trim($item['item_description']) : null;
                    
                    // Validate item code length (max 15 characters)
                    if ($itemCode && strlen($itemCode) > 15) {
                        throw new Exception("Item code '{$itemCode}' exceeds 15 characters limit");
                    }
                    
                    // Skip completely empty items
                    if ($itemCode === null && $qty === null && $description === null) {
                        continue;
                    }
                    
                    $itemResult = $itemStmt->execute([
                        $item['dn_no'] ?? $data['dn_no'],
                        $item['site_code'] ?? $data['site_Code'],
                        $itemCode,
                        $qty,
                        $description,
                        $item['Company_code'] ?? $data['Company_code'] ?? '1000'
                    ]);
                    
                    if ($itemResult) {
                        $itemsInserted++;
                    }
                }
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Delivery note uploaded successfully',
                'id' => $insertId,
                'dn_no' => $data['dn_no'],
                'items_inserted' => $itemsInserted,
                'total_items' => count($items)
            ]);
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("SQL Error: " . print_r($errorInfo, true));
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $errorInfo[2]]);
        }
        
    } catch(Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>