-- Corrected Database Schema
CREATE TABLE Delivery_Notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    print_date DATETIME,
    purpose_of_delivery TEXT,
    delivery_address TEXT,
    site_Code VARCHAR(255),
    contract_info VARCHAR(255),
    Customer VARCHAR(255),
    Customer_PO VARCHAR(255),
    Customer_Tel VARCHAR(255),
    Project_Name VARCHAR(255),
    Project_Code VARCHAR(255),
    Product_Category VARCHAR(255),
    Product_Manager VARCHAR(255),
    Special_Unloading_Req VARCHAR(255),
    Installation_Environment VARCHAR(255),
    Description_MR VARCHAR(255),
    From_Warehouse VARCHAR(255),
    Warehouse_Keeper VARCHAR(255),
    Warehouse_Keeper_tel VARCHAR(50),
    Description_DN VARCHAR(255),
    Including_dangerous_goods VARCHAR(255),
    pickup_address TEXT,
    Site_Address VARCHAR(255),
    dn_no VARCHAR(50),
    mr_no VARCHAR(50),
    receiver_name VARCHAR(255),
    receiver_tel VARCHAR(50),
    Receiver_Company_Name VARCHAR(255),
    request_arrived_date DATETIME,
    request_shipment_date DATETIME,
    logistics_specialist VARCHAR(255),
    logistics_specialist_Tel VARCHAR(255),
    received_location VARCHAR(255),
    received_Auto_location VARCHAR(255),
    Collected_location VARCHAR(255),
    Collected_Auto_location VARCHAR(255),
    DN_Status ENUM('Created', 'Received', 'Delivered', 'Collected', 'Closed') DEFAULT 'Created',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Dn_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dn_no VARCHAR(50) NOT NULL,
    site_code VARCHAR(255),
    item_code VARCHAR(50) NOT NULL,
    qty DECIMAL(12,2) NOT NULL,
    item_description TEXT,
    Item_received_Image VARCHAR(255),
    Item_Delivered_Image VARCHAR(255),
    Item_Collected_Image VARCHAR(255),
    Item_User_Created VARCHAR(255),
    Item_User_Update VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    item_status ENUM('Created', 'Received', 'Delivered', 'Collected') DEFAULT 'Created'
);

CREATE TABLE items_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50),
    item_description TEXT,
    item_Status ENUM('Active', 'Not Active') DEFAULT 'Active'
);

CREATE TABLE Site_Details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_Code VARCHAR(20) UNIQUE,
    Site_Name VARCHAR(100) NOT NULL,
    Tel_Operator VARCHAR(50) NOT NULL,
    Address VARCHAR(255),
    GPS_Latitude DECIMAL(10, 6),
    GPS_Longitude DECIMAL(10, 6),
    Site_Type ENUM('Macro', 'Rooftop', 'Indoor', 'Small Cell') NOT NULL,
    Access_Instructions TEXT,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Updated_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE trucks_info (
    truck_id INT AUTO_INCREMENT PRIMARY KEY,
    truck_no VARCHAR(50) NOT NULL,
    driver_name VARCHAR(255) NOT NULL,
    driver_phone VARCHAR(50),
    capacity DECIMAL(10,2),
    status ENUM('Available', 'In Transit', 'Maintenance', 'Unavailable') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE truck_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    site_Code VARCHAR(20),
    dn_no VARCHAR(50) NOT NULL,
    truck_id INT NOT NULL,
    assigned_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Assigned', 'In Transit', 'Delivered', 'Cancelled') DEFAULT 'Assigned',
    FOREIGN KEY (truck_id) REFERENCES trucks_info(truck_id)
);

CREATE TABLE truck_assignment_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    cost DECIMAL(12,2) NOT NULL,
    advance_payment DECIMAL(12,2) DEFAULT 0,
    Gove_Fees DECIMAL(12,2) DEFAULT 0,
    balance DECIMAL(12,2) GENERATED ALWAYS AS (cost - advance_payment) STORED,
    payment_status ENUM('Unpaid', 'Partial', 'Paid') DEFAULT 'Unpaid',
    payment_date DATETIME NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES truck_assignments(assignment_id)
);

CREATE TABLE excel_sheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    sheet_data LONGTEXT NOT NULL,
    merged_cells TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);