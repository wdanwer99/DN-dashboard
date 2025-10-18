<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "UPDATE employees SET first_name = ?, last_name = ?, gender = ?, date_of_birth = ?, email = ?, phone_number = ?, hire_date = ?, job_title = ?, department = ?, salary = ?, status = ?, App_user = ?, app_Password = ?, App_User_Status = ? WHERE employee_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['first_name'] ?? null,
        $_POST['last_name'] ?? null,
        $_POST['gender'] ?? null,
        $_POST['date_of_birth'] ?? null,
        $_POST['email'] ?? null,
        $_POST['phone_number'] ?? null,
        $_POST['hire_date'] ?? null,
        $_POST['job_title'] ?? null,
        $_POST['department'] ?? null,
        $_POST['salary'] ?? null,
        $_POST['status'] ?? 'Active',
        $_POST['App_user'] ?? null,
        $_POST['app_Password'] ?? null,
        $_POST['App_User_Status'] ?? 'Active',
        $_POST['employee_id'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>