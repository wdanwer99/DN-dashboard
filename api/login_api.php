<?php
// api/login.php - Following your exact working pattern

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database credentials directly (like your working code)
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

// Get and validate inputs
$email = isset($data['email']) ? trim(strtolower($data['email'])) : '';
$user_password = isset($data['password']) ? $data['password'] : '';

if (empty($email) || empty($user_password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

try {
    // Create PDO connection exactly like your working code
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find user by email
    $stmt = $pdo->prepare("SELECT id, email, phone_number, password FROM user WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify password
    if (!password_verify($user_password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Generate token
    $token = bin2hex(random_bytes(32));
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'phone' => $user['phone_number'],
            'token' => $token
        ]
    ]);
    
} catch(PDOException $e) {
    // Show actual error for debugging
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