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

        // Simple query - no user filtering
        $whereClause = "dn.site_Code = ?";
        $params = [$siteCode];

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM Delivery_Notes dn WHERE $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated results with items count
        $dataSql = "
            SELECT dn.*, 
                   COALESCE(COUNT(di.id), 0) as items_count
            FROM Delivery_Notes dn 
            LEFT JOIN dn_items di ON dn.dn_no = di.dn_no 
            WHERE $whereClause
            GROUP BY dn.dn_no 
            ORDER BY dn.created_at DESC 
            LIMIT $offset, $limit
        ";
        
        $stmt = $pdo->prepare($dataSql);
        $stmt->execute($params);
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
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid request method'
    ]);
}
?>