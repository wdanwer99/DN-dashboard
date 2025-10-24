<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT * FROM truck_assignments ORDER BY assigned_date DESC");
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true, 
            'data' => $assignments,
            'message' => 'Assignments loaded successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'message' => 'Failed to load assignments'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid request method',
        'message' => 'Only POST method allowed'
    ]);
}
?>