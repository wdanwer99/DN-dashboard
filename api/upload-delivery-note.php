<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Debug: Log the incoming data
        error_log("Received data: " . print_r($data, true));
        
        if ($data === null) {
            throw new Exception("Invalid JSON data received");
        }
        
        // Prepare the comprehensive INSERT statement matching the exact database schema
        $stmt = $pdo->prepare("
            INSERT INTO delivery_notes (
                print_date,
                purpose_of_delivery,
                delivery_address,
                site_Code,
                contract_info,
                Customer,
                Customer_PO,
                Customer_Tel,
                Project_Name,
                Project_Code,
                Product_Category,
                Product_Manager,
                Special_Unloading_Req,
                Installation_Environment,
                Description_MR,
                From_Warehouse,
                Warehouse_Keeper,
                Warehouse_Keeper_tel,
                Description_DN,
                Including_dangerous_goods,
                pickup_address,
                Site_Address,
                dn_no,
                mr_no,
                receiver_name,
                receiver_tel,
                Receiver_Company_Name,
                request_arrived_date,
                request_shipment_date,
                logistics_specialist,
                logistics_specialist_Tel,
                received_location,
                received_Auto_location,
                Collected_location,
                Collected_Auto_location,
                DN_Status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?
            )
        ");
        
        // Execute with all fields from the database schema
        $result = $stmt->execute([
            // Basic delivery information
            isset($data['print_date']) ? $data['print_date'] : date('Y-m-d H:i:s'),
            isset($data['purpose_of_delivery']) ? $data['purpose_of_delivery'] : null,
            isset($data['delivery_address']) ? $data['delivery_address'] : null,
            isset($data['site_code']) ? $data['site_code'] : null,
            isset($data['contract_info']) ? $data['contract_info'] : null,
            
            // Customer information
            isset($data['customer']) ? $data['customer'] : null,
            isset($data['customer_po']) ? $data['customer_po'] : null,
            isset($data['customer_tel']) ? $data['customer_tel'] : null,
            
            // Project information
            isset($data['project_name']) ? $data['project_name'] : null,
            isset($data['project_code']) ? $data['project_code'] : null,
            
            // Product information
            isset($data['product_category']) ? $data['product_category'] : null,
            isset($data['product_manager']) ? $data['product_manager'] : null,
            
            // Special requirements and environment
            isset($data['special_unloading_req']) ? $data['special_unloading_req'] : null,
            isset($data['installation_environment']) ? $data['installation_environment'] : null,
            
            // Descriptions
            isset($data['description_mr']) ? $data['description_mr'] : null,
            
            // Warehouse information
            isset($data['from_warehouse']) ? $data['from_warehouse'] : null,
            isset($data['warehouse_keeper']) ? $data['warehouse_keeper'] : null,
            isset($data['warehouse_keeper_tel']) ? $data['warehouse_keeper_tel'] : null,
            
            // Additional descriptions and safety
            isset($data['description_dn']) ? $data['description_dn'] : null,
            isset($data['including_dangerous_goods']) ? $data['including_dangerous_goods'] : 'No',
            
            // Addresses
            isset($data['pickup_address']) ? $data['pickup_address'] : null,
            isset($data['site_address']) ? $data['site_address'] : null,
            
            // Document numbers
            isset($data['dn_no']) ? $data['dn_no'] : null,
            isset($data['mr_no']) ? $data['mr_no'] : null,
            
            // Receiver information
            isset($data['receiver_name']) ? $data['receiver_name'] : null,
            isset($data['receiver_tel']) ? $data['receiver_tel'] : null,
            isset($data['receiver_company_name']) ? $data['receiver_company_name'] : null,
            
            // Date information
            isset($data['request_arrived_date']) ? $data['request_arrived_date'] : null,
            isset($data['request_shipment_date']) ? $data['request_shipment_date'] : null,
            
            // Logistics information
            isset($data['logistics_specialist']) ? $data['logistics_specialist'] : null,
            isset($data['logistics_specialist_tel']) ? $data['logistics_specialist_tel'] : null,
            
            // Location tracking
            isset($data['received_location']) ? $data['received_location'] : null,
            isset($data['received_auto_location']) ? $data['received_auto_location'] : null,
            isset($data['collected_location']) ? $data['collected_location'] : null,
            isset($data['collected_auto_location']) ? $data['collected_auto_location'] : null,
            
            // Status
            isset($data['dn_status']) ? $data['dn_status'] : 'Created'
        ]);
        
        if ($result) {
            $insertedId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true, 
                'message' => 'Delivery note uploaded successfully',
                'delivery_note_id' => $insertedId,
                'dn_no' => isset($data['dn_no']) ? $data['dn_no'] : null
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload delivery note to database']);
        }
        
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    } catch(Exception $e) {
        error_log("General error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error processing delivery note: ' . $e->getMessage()]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve delivery notes with all fields
    try {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM delivery_notes");
        $countStmt->execute();
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get delivery notes with pagination
        $stmt = $pdo->prepare("
            SELECT 
                id,
                print_date,
                purpose_of_delivery,
                delivery_address,
                site_Code,
                contract_info,
                Customer,
                Customer_PO,
                Customer_Tel,
                Project_Name,
                Project_Code,
                Product_Category,
                Product_Manager,
                Special_Unloading_Req,
                Installation_Environment,
                Description_MR,
                From_Warehouse,
                Warehouse_Keeper,
                Warehouse_Keeper_tel,
                Description_DN,
                Including_dangerous_goods,
                pickup_address,
                Site_Address,
                dn_no,
                mr_no,
                receiver_name,
                receiver_tel,
                Receiver_Company_Name,
                request_arrived_date,
                request_shipment_date,
                logistics_specialist,
                logistics_specialist_Tel,
                received_location,
                received_Auto_location,
                Collected_location,
                Collected_Auto_location,
                DN_Status,
                created_at,
                Updated_at
            FROM delivery_notes 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$limit, $offset]);
        $delivery_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true, 
            'data' => $delivery_notes,
            'pagination' => [
                'current_page' => $page,
                'total_records' => $totalRecords,
                'total_pages' => ceil($totalRecords / $limit),
                'records_per_page' => $limit
            ]
        ]);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update delivery note
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = isset($data['id']) ? $data['id'] : null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Delivery note ID is required']);
            return;
        }
        
        $stmt = $pdo->prepare("
    INSERT INTO delivery_notes (
        print_date,
        purpose_of_delivery,
        delivery_address,
        site_Code,
        contract_info,
        Customer,
        Customer_PO,
        Customer_Tel,
        Project_Name,
        Project_Code,
        Product_Category,
        Product_Manager,
        Special_Unloading_Req,
        Installation_Environment,
        Description_MR,
        From_Warehouse,
        Warehouse_Keeper,
        Warehouse_Keeper_tel,
        Description_DN,
        Including_dangerous_goods,
        pickup_address,
        Site_Address,
        dn_no,
        mr_no,
        receiver_name,
        receiver_tel,
        Receiver_Company_Name,
        request_arrived_date,
        request_shipment_date,
        logistics_specialist,
        logistics_specialist_Tel,
        received_location,
        received_Auto_location,
        Collected_location,
        Collected_Auto_location,
        DN_Status,
        Company_code,
        created_at,
        Updated_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
");
$result = $stmt->execute([
    isset($data['print_date']) ? $data['print_date'] : date('Y-m-d H:i:s'),
    isset($data['purpose_of_delivery']) ? $data['purpose_of_delivery'] : null,
    isset($data['delivery_address']) ? $data['delivery_address'] : null,
    isset($data['site_Code']) ? $data['site_Code'] : null,
    isset($data['contract_info']) ? $data['contract_info'] : null,
    isset($data['Customer']) ? $data['Customer'] : null,
    isset($data['Customer_PO']) ? $data['Customer_PO'] : null,
    isset($data['Customer_Tel']) ? $data['Customer_Tel'] : null,
    isset($data['Project_Name']) ? $data['Project_Name'] : null,
    isset($data['Project_Code']) ? $data['Project_Code'] : null,
    isset($data['Product_Category']) ? $data['Product_Category'] : null,
    isset($data['Product_Manager']) ? $data['Product_Manager'] : null,
    isset($data['Special_Unloading_Req']) ? $data['Special_Unloading_Req'] : null,
    isset($data['Installation_Environment']) ? $data['Installation_Environment'] : null,
    isset($data['Description_MR']) ? $data['Description_MR'] : null,
    isset($data['From_Warehouse']) ? $data['From_Warehouse'] : null,
    isset($data['Warehouse_Keeper']) ? $data['Warehouse_Keeper'] : null,
    isset($data['Warehouse_Keeper_tel']) ? $data['Warehouse_Keeper_tel'] : null,
    isset($data['Description_DN']) ? $data['Description_DN'] : null,
    isset($data['Including_dangerous_goods']) ? $data['Including_dangerous_goods'] : 'No',
    isset($data['pickup_address']) ? $data['pickup_address'] : null,
    isset($data['Site_Address']) ? $data['Site_Address'] : null,
    isset($data['dn_no']) ? $data['dn_no'] : null,
    isset($data['mr_no']) ? $data['mr_no'] : null,
    isset($data['receiver_name']) ? $data['receiver_name'] : null,
    isset($data['receiver_tel']) ? $data['receiver_tel'] : null,
    isset($data['Receiver_Company_Name']) ? $data['Receiver_Company_Name'] : null,
    isset($data['request_arrived_date']) ? $data['request_arrived_date'] : null,
    isset($data['request_shipment_date']) ? $data['request_shipment_date'] : null,
    isset($data['logistics_specialist']) ? $data['logistics_specialist'] : null,
    isset($data['logistics_specialist_Tel']) ? $data['logistics_specialist_Tel'] : null,
    isset($data['received_location']) ? $data['received_location'] : null,
    isset($data['received_Auto_location']) ? $data['received_Auto_location'] : null,
    isset($data['Collected_location']) ? $data['Collected_location'] : null,
    isset($data['Collected_Auto_location']) ? $data['Collected_Auto_location'] : null,
    isset($data['DN_Status']) ? $data['DN_Status'] : 'Created',
    isset($data['Company_code']) ? $data['Company_code'] : null,
    date('Y-m-d H:i:s'),
    date('Y-m-d H:i:s')
]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Delivery note updated successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update delivery note']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete delivery note
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = isset($data['id']) ? $data['id'] : null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Delivery note ID is required']);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM delivery_notes WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Delivery note deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete delivery note']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method. Use POST, GET, PUT, or DELETE.']);
}
?>