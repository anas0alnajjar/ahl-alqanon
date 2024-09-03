<?php
include "../../DB_connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $session_id = (int)$_POST['id'];

        try {
            $stmt = $conn->prepare("DELETE FROM sessions WHERE sessions_id = :session_id");
            $stmt->bindParam(':session_id', $session_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'لم يتم العثور على الجلسة.']);
            }
        } catch (PDOException $e) {
            error_log("Error deleting session: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'خطأ في الخادم.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'معرف الجلسة غير صالح.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'طلب غير صالح.']);
}
?>