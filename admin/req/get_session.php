<?php
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

$response = [];

try {
    // Check if user is logged in as admin
    if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {

        // Include database connection file
        include '../../DB_connection.php';

        // تأكد من أن $conn هو PDO وليس MySQLi
        if (!$conn instanceof PDO) {
            throw new Exception("Invalid database connection");
        }

        $session_id = $_GET['id'];

        $sql = "SELECT * FROM sessions WHERE sessions_id = :session_id";
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
        $response = ['success' => false, 'message' => 'غير مصرح لك بالوصول.'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()];
}

echo json_encode($response);
?>
