<?php
// api/register.php - Following your exact working pattern

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
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$user_password = isset($data['password']) ? $data['password'] : '';

if (empty($email) || empty($phone) || empty($user_password)) {
    echo json_encode(['success' => false, 'message' => 'Email, phone, and password are required']);
    exit;
}

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid phone number (10-15 digits)']);
    exit;
}

if (strlen($user_password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

try {
    // Create PDO connection exactly like your working code
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists. Please use a different email.']);
        exit;
    }
    
    // Check if phone already exists
    $stmt = $pdo->prepare("SELECT id FROM user WHERE phone_number = ?");
    $stmt->execute([$phone]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Phone number already exists. Please use a different phone number.']);
        exit;
    }
    
    // Hash password and insert user
    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO user (email, phone_number, password) VALUES (?, ?, ?)");
    $result = $stmt->execute([$email, $phone, $hashed_password]);
    
    if ($result) {
        $user_id = $pdo->lastInsertId();
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'data' => [
                'id' => $user_id,
                'email' => $email,
                'phone' => $phone,
                'token' => $token
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create account. Please try again.']);
    }
    
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