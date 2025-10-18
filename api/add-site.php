<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO Site_Details (site_Code, Site_Name, Tel_Operator, Address, GPS_Latitude, GPS_Longitude, Site_Type, Access_Instructions, Company_code, Project_code_User, Batch_no_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['site_Code'] ?? null,
        $_POST['Site_Name'] ?? null,
        $_POST['Tel_Operator'] ?? null,
        $_POST['Address'] ?? null,
        $_POST['GPS_Latitude'] ?? null,
        $_POST['GPS_Longitude'] ?? null,
        $_POST['Site_Type'] ?? null,
        $_POST['Access_Instructions'] ?? null,
        $_POST['Company_code'] ?? null,
        $_POST['Project_code_User'] ?? null,
        $_POST['Batch_no_user'] ?? null
    ]);

    echo json_encode(['success' => true, 'message' => 'Site added successfully']);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>