<?php
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

$response = [];


try {
    // Check if user is logged in as admin
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {

        // Include database connection file
        include '../../DB_connection.php';

        include "../get_office.php";
        $user_id = $_SESSION['user_id'];
        $office_id = getOfficeId($conn, $user_id);


        // Ensure $conn is PDO instance
        if (!$conn instanceof PDO) {
            throw new Exception("Invalid database connection");
        }

        $session_id = $_GET['id'];

        if (!empty($office_id)) {
            // Query to fetch session details ensuring it belongs to admin's offices
            $sql = "
                SELECT sessions.*
                FROM sessions
                LEFT JOIN cases ON sessions.case_id = cases.case_id
                WHERE sessions.sessions_id = :session_id
                AND cases.office_id IN ($office_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':session_id', $session_id);

            if ($stmt->execute()) {
                $session = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($session) {
                    $response = ['success' => true, 'session' => $session];
                } else {
                    $response = ['success' => false, 'message' => 'الجلسة غير موجودة.'];
                }
            } else {
                $errorInfo = $stmt->errorInfo();
                $response = ['success' => false, 'message' => 'حدث خطأ أثناء جلب بيانات الجلسة: ' . $errorInfo[2]];
            }
        } else {
            $response = ['success' => false, 'message' => 'لم يتم العثور على مكاتب مرتبطة بهذا المسؤول.'];
        }

    } else {
        $response = ['success' => false, 'message' => 'غير مصرح لك بالوصول.'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()];
}

echo json_encode($response);
?>
