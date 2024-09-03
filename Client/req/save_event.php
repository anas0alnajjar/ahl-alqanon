<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    require '../../DB_connection.php';

    if (isset($_POST['event_name']) && isset($_POST['event_start_date']) && isset($_POST['event_end_date']) && isset($_POST['lawer_name']) && isset($_POST['client_name'])) {
        $event_name = $_POST['event_name'];
        $event_start_date = $_POST['event_start_date'];
        $event_end_date = $_POST['event_end_date'];
        $lawer_name = $_POST['lawer_name'];
        $client_name = $_POST['client_name'];

        try {
            $insert_query = "INSERT INTO events (event_name, event_start_date, event_end_date, lawyer_id, client_id) VALUES (:event_name, :event_start_date, :event_end_date, :lawer_name, :client_name)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bindParam(':event_name', $event_name, PDO::PARAM_STR);
            $stmt->bindParam(':event_start_date', $event_start_date, PDO::PARAM_STR);
            $stmt->bindParam(':event_end_date', $event_end_date, PDO::PARAM_STR);
            $stmt->bindParam(':lawer_name', $lawer_name, PDO::PARAM_INT);
            $stmt->bindParam(':client_name', $client_name, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $event_id = $conn->lastInsertId(); // الحصول على معرف الحدث الجديد
                $response = array(
                    'status' => true,
                    'msg' => 'تم حفظ الحدث بنجاح!',
                    'event_id' => $event_id // إضافة معرف الحدث الجديد في الرد
                );
            } else {
                $response = array(
                    'status' => false,
                    'msg' => 'خطأ في حفظ الحدث!'
                );
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => false,
                'msg' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()
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
