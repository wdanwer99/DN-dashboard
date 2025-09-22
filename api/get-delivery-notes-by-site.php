<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $siteCode = $_GET['site_code'] ?? '';
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;

    if (empty($siteCode)) {
        throw new Exception('Site code is required');
    }

    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM Delivery_Notes WHERE site_Code = ?");
    $countStmt->execute([$siteCode]);
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get paginated results
    $stmt = $pdo->prepare("SELECT * FROM Delivery_Notes WHERE site_Code = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$siteCode, $limit, $offset]);
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
?>