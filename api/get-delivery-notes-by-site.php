<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($pdo)) {
            throw new Exception('Database connection failed');
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $siteCode = $data['site_code'] ?? '';
        $page = (int)($data['page'] ?? 1);
        $limit = (int)($data['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        if (empty($siteCode)) {
            throw new Exception('Site code is required');
        }

    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM Delivery_Notes WHERE site_Code = ?");
    $countStmt->execute([$siteCode]);
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get paginated results
    $stmt = $pdo->prepare("SELECT * FROM Delivery_Notes WHERE site_Code = ? ORDER BY created_at DESC LIMIT $offset, $limit");
    $stmt->execute([$siteCode]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true, 
        'data' => $notes,
        'total' => $totalCount,
        'page' => $page,
        'limit' => $limit,
        'totalPages' => ceil($totalCount / $limit)
    ]);

    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>