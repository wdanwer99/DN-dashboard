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
                $stmt = $pdo->query("SELECT `Project_Name`,`Project_Code`,count(*) as count,`DN_Status` FROM `delivery_notes` group by `Project_Name`,`Project_Code`,`DN_Status`");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'dn-details':
                $stmt = $pdo->query("SELECT d.`dn_no`, d.`Project_Code`, d.`Project_Name`, d.`Customer`, d.`Customer_PO`, d.`Product_Manager`, COUNT(*) AS item_count, d.`DN_Status` FROM `delivery_notes` d JOIN `dn_items` I ON d.dn_no = I.dn_no GROUP BY d.`dn_no`, d.`Customer`, d.`Customer_PO`, d.`Project_Name`, d.`Project_Code`, d.`Product_Manager`, d.`DN_Status`");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'dn-sites':
                $stmt = $pdo->query("SELECT S.`site_Code`, S.`Site_Name`, S.`Tel_Operator`, S.`Site_Type`, S.`Address`, COUNT(*) AS DN_Counts, d.`DN_Status` FROM `site_details` S JOIN `delivery_notes` d ON S.site_Code = d.site_Code GROUP BY S.`site_Code`, S.`Site_Name`, S.`Tel_Operator`, S.`Site_Type`, S.`Address`, d.`DN_Status`");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'trips-cars':
                $stmt = $pdo->query("SELECT A.`site_Code`, T.`truck_id`, T.`truck_no` As Plate_No, T.`driver_name`, T.`driver_phone`, COUNT(*) AS Trip_Counts, A.`status` FROM `truck_assignments` A JOIN `trucks_info` T ON T.`truck_id` = A.`truck_id` GROUP BY A.`site_Code`, A.`status`, T.`truck_id`, T.`truck_no`, T.`driver_name`, T.`driver_phone`");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            case 'payments':
                $stmt = $pdo->query("SELECT COUNT(*) AS row_count, C.assignment_id, A.site_Code, D.`Customer`, D.`Customer_PO`, D.`Project_Name`, D.`Project_Code`, C.cost, C.balance, C.payment_status FROM `truck_assignment_accounts` C JOIN `truck_assignments` A ON C.assignment_id = A.assignment_id JOIN `delivery_notes` D ON A.site_Code = D.site_Code GROUP BY C.assignment_id, C.cost, C.balance, C.payment_status, A.site_Code, D.`Customer`, D.`Customer_PO`, D.`Project_Name`, D.`Project_Code`");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            default:
                throw new Exception('Invalid report type');
        }

        echo json_encode(['success' => true, 'data' => $data]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>