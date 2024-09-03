<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    require '../../DB_connection.php';

    if (isset($_POST['event_id'])) {
        $event_id = $_POST['event_id'];

        $delete_query = "DELETE FROM events WHERE event_id = :event_id";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response = array(
                'status' => true,
                'msg' => 'تم حذف الحدث بنجاح!'
            );
        } else {
            $response = array(
                'status' => false,
                'msg' => 'خطأ في حذف الحدث!'
            );
        }
    } else {
        $response = array(
            'status' => false,
            'msg' => 'البيانات غير مكتملة!'
        );
    }

    echo json_encode($response);
} else {
    header("Location: ../login.php");
    exit;
}
?>
