<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $reportType = $input['report_type'] ?? '';

        $data = [];

        switch ($reportType) {
            case 'dn-status':
            case 'dn-details':
            case 'dn-sites':
                $statusStmt = $pdo->query("SELECT DISTINCT DN_Status FROM Delivery_Notes WHERE DN_Status IS NOT NULL ORDER BY DN_Status");
                $siteStmt = $pdo->query("SELECT DISTINCT site_Code FROM Delivery_Notes WHERE site_Code IS NOT NULL ORDER BY site_Code");
                
                $data['statuses'] = $statusStmt->fetchAll(PDO::FETCH_COLUMN);
                $data['sites'] = $siteStmt->fetchAll(PDO::FETCH_COLUMN);
                break;

            case 'trips-cars':
                $statusStmt = $pdo->query("SELECT DISTINCT status FROM truck_assignments WHERE status IS NOT NULL ORDER BY status");
                $truckStmt = $pdo->query("SELECT DISTINCT truck_no FROM trucks_info WHERE truck_no IS NOT NULL ORDER BY truck_no");
                
                $data['statuses'] = $statusStmt->fetchAll(PDO::FETCH_COLUMN);
                $data['trucks'] = $truckStmt->fetchAll(PDO::FETCH_COLUMN);
                break;

            case 'payments':
                $statusStmt = $pdo->query("SELECT DISTINCT payment_status FROM truck_assignment_accounts WHERE payment_status IS NOT NULL ORDER BY payment_status");
                $data['statuses'] = $statusStmt->fetchAll(PDO::FETCH_COLUMN);
                break;
        }

        echo json_encode(['success' => true, 'data' => $data]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>