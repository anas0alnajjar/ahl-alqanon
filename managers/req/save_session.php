<?php
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

$response = [];

try {
    // Check if user is logged in as admin
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Managers') {

        // Include database connection file
        include '../../DB_connection.php';
        include '../permissions_script.php';
        if ($pages['sessions']['add'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        // تأكد من أن $conn هو PDO وليس MySQLi
        if (!$conn instanceof PDO) {
            throw new Exception("Invalid database connection");
        }

        $case_id = $_POST['case_id'];
        $session_number = $_POST['session_number'];
        $session_date_gregorian = $_POST['session_date_gregorian'];
        $session_hour = $_POST['session_hour'];
        $session_date_hijri = $_POST['session_date_hijri'];
        $notes = isset($_POST['notes']) && !empty($_POST['notes']) ? $_POST['notes'] : null;
        $assistant_lawyer = isset($_POST['assistant_lawyer']) && !empty($_POST['assistant_lawyer']) ? $_POST['assistant_lawyer'] : null;

        $sql = "INSERT INTO sessions (case_id, session_number, session_date, session_hour, session_date_hjri, notes, assistant_lawyer) 
                VALUES (:case_id, :session_number, :session_date_gregorian, :session_hour, :session_date_hijri, :notes, :assistant_lawyer)";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':case_id', $case_id);
        $stmt->bindParam(':session_number', $session_number);
        $stmt->bindParam(':session_date_gregorian', $session_date_gregorian);
        $stmt->bindParam(':session_hour', $session_hour);
        $stmt->bindParam(':session_date_hijri', $session_date_hijri);
        $stmt->bindParam(':notes', $notes, is_null($notes) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':assistant_lawyer', $assistant_lawyer, is_null($assistant_lawyer) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'تم حفظ الجلسة بنجاح.'];
        } else {
            $errorInfo = $stmt->errorInfo();
            $response = ['success' => false, 'message' => 'حدث خطأ أثناء حفظ الجلسة: ' . $errorInfo[2]];
        }

    } else {
        $response = ['success' => false, 'message' => 'غير مصرح لك بالوصول.'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()];
}

echo json_encode($response);
?>
