<?php
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

$response = [];

try {
    // Check if user is logged in as lawyer
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {

        // Include database connection file
        include '../../DB_connection.php';
        include '../permissions_script.php';
        if ($pages['sessions']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        $lawyer_id = $_SESSION['user_id'];

        // تأكد من أن $conn هو PDO وليس MySQLi
        if (!$conn instanceof PDO) {
            throw new Exception("Invalid database connection");
        }

        $session_id = $_POST['session_id'];
        $case_id = $_POST['case_id'];
        $session_number = $_POST['session_number'];
        $session_date_gregorian = $_POST['session_date_gregorian'];
        $session_hour = $_POST['session_hour'];
        $session_date_hijri = $_POST['session_date_hijri'];
        $notes = isset($_POST['notes']) && !empty($_POST['notes']) ? $_POST['notes'] : null;

        // Validate input
        if (empty($session_id) || empty($case_id) || empty($session_number) || empty($session_date_gregorian) || empty($session_hour) || empty($session_date_hijri)) {
            throw new Exception("All fields are required");
        }


        $sql = "UPDATE sessions 
                SET case_id = :case_id, 
                    session_number = :session_number, 
                    session_date = :session_date_gregorian, 
                    session_hour = :session_hour, 
                    session_date_hjri = :session_date_hijri, 
                    notes = :notes
                WHERE sessions_id = :session_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':case_id', $case_id);
        $stmt->bindParam(':session_number', $session_number);
        $stmt->bindParam(':session_date_gregorian', $session_date_gregorian);
        $stmt->bindParam(':session_hour', $session_hour);
        $stmt->bindParam(':session_date_hijri', $session_date_hijri);
        $stmt->bindParam(':notes', $notes, is_null($notes) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':session_id', $session_id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'تم تحديث الجلسة بنجاح.'];
        } else {
            $errorInfo = $stmt->errorInfo();
            $response = ['success' => false, 'message' => 'حدث خطأ أثناء تحديث الجلسة: ' . $errorInfo[2]];
        }

    } else {
        $response = ['success' => false, 'message' => 'غير مصرح لك بالوصول.'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()];
}

echo json_encode($response);
?>
