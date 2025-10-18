<?php
// api/add-project-columns.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if columns exist and add them if they don't
    $columns = [
        'Project_code_User' => 'VARCHAR(50)',
        'Batch_no_user' => 'VARCHAR(50)', 
        'Receive_Rep' => 'VARCHAR(255)',
        'Delivery_Rep' => 'VARCHAR(255)',
        'Collect_Rep' => 'VARCHAR(255)'
    ];
    
    foreach ($columns as $columnName => $columnType) {
        // Check if column exists
        $checkStmt = $pdo->prepare("SHOW COLUMNS FROM Delivery_Notes LIKE ?");
        $checkStmt->execute([$columnName]);
        
        if ($checkStmt->rowCount() == 0) {
            // Column doesn't exist, add it
            $alterSQL = "ALTER TABLE Delivery_Notes ADD COLUMN $columnName $columnType";
            $pdo->exec($alterSQL);
            echo "Added column: $columnName\n";
        } else {
            echo "Column already exists: $columnName\n";
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Database columns updated successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>