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
        
        $stmt = $pdo->prepare("INSERT INTO Delivery_Notes (
            print_date, purpose_of_delivery, delivery_address, site_Code, contract_info,
            Customer, Customer_PO, Customer_Tel, Project_Name, Project_Code,
            Product_Category, Product_Manager, Special_Unloading_Req, Installation_Environment,
            Description_MR, From_Warehouse, Warehouse_Keeper, Warehouse_Keeper_tel,
            Description_DN, Including_dangerous_goods, pickup_address, Site_Address,
            dn_no, mr_no, receiver_name, receiver_tel, Receiver_Company_Name,
            request_arrived_date, request_shipment_date, logistics_specialist,
            logistics_specialist_Tel, received_location, received_Auto_location,
            Collected_location, Collected_Auto_location, DN_Status, Company_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Created', ?)");
        
        $params = [
            $data['print_date'] ?? null,
            $data['purpose_of_delivery'] ?? null,
            $data['delivery_address'] ?? null,
            $data['site_Code'] ?? null,
            $data['contract_info'] ?? null,
            $data['Customer'] ?? null,
            $data['Customer_PO'] ?? null,
            $data['Customer_Tel'] ?? null,
            $data['Project_Name'] ?? null,
            $data['Project_Code'] ?? null,
            $data['Product_Category'] ?? null,
            $data['Product_Manager'] ?? null,
            $data['Special_Unloading_Req'] ?? null,
            $data['Installation_Environment'] ?? null,
            $data['Description_MR'] ?? null,
            $data['From_Warehouse'] ?? null,
            $data['Warehouse_Keeper'] ?? null,
            $data['Warehouse_Keeper_tel'] ?? null,
            $data['Description_DN'] ?? null,
            $data['Including_dangerous_goods'] ?? null,
            $data['pickup_address'] ?? null,
            $data['Site_Address'] ?? null,
            $data['dn_no'] ?? null,
            $data['mr_no'] ?? null,
            $data['receiver_name'] ?? null,
            $data['receiver_tel'] ?? null,
            $data['Receiver_Company_Name'] ?? null,
            $data['request_arrived_date'] ?? null,
            $data['request_shipment_date'] ?? null,
            $data['logistics_specialist'] ?? null,
            $data['logistics_specialist_Tel'] ?? null,
            $data['received_location'] ?? null,
            $data['received_Auto_location'] ?? null,
            $data['Collected_location'] ?? null,
            $data['Collected_Auto_location'] ?? null,
            $data['Company_code'] ?? null
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
                    // Ensure required fields are not empty
                    $itemCode = trim($item['item_code'] ?? '');
                    $qty = floatval($item['qty'] ?? 0);
                    
                    if (empty($itemCode) && $qty == 0) {
                        continue; // Skip empty items
                    }
                    
                    $itemResult = $itemStmt->execute([
                        $item['dn_no'] ?? $data['dn_no'],
                        $item['site_code'] ?? $data['site_Code'],
                        $itemCode ?: 'N/A',
                        $qty,
                        $item['item_description'] ?? '',
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