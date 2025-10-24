<?php
// api/login_api.php - Updated to use App_user field
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database credentials
$host = 'mysql5027.site4now.net'; 
$dbname = 'db_9b86be_arpusof'; 
$username = '9b86be_arpusof'; 
$password = 'arpusoft@123';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}


$app_user = isset($data['app_user']) ? trim($data['app_user']) : '';
$user_password = isset($data['password']) ? $data['password'] : '';

if (empty($app_user) || empty($user_password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find employee by App_user
    $stmt = $pdo->prepare("
        SELECT employee_id, first_name, last_name, email, phone_number, 
               app_Password, App_User_Status, job_title, department, App_user
        FROM employees 
        WHERE App_user = ? AND App_User_Status = 'Active'
    ");
    $stmt->execute([$app_user]);
    
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password, or account not active']);
        exit;
    }
    
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify password - plain text comparison
    // IMPORTANT: Consider using password_hash() and password_verify() for better security
    if ($user_password !== $employee['app_Password']) {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        exit;
    }
    
    // Generate token
    $token = bin2hex(random_bytes(32));
    
    // Return employee data
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'id' => $employee['employee_id'],
            'email' => $employee['email'],
            'phone' => $employee['phone_number'],
            'app_user' => $employee['App_user'],
            'name' => trim($employee['first_name'] . ' ' . $employee['last_name']),
            'first_name' => $employee['first_name'],
            'last_name' => $employee['last_name'],
            'job_title' => $employee['job_title'],
            'department' => $employee['department'],
            'status' => $employee['App_User_Status'],
            'token' => $token
        ]
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'General error: ' . $e->getMessage()
    ]);
}
?>