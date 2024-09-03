<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../DB_connection.php'; // الاتصال بقاعدة البيانات

    $id = $_POST['id'];
    $type = $_POST['type']; // الحصول على نوع الحدث

    if ($type === 'session') {
        $sql = "DELETE FROM sessions WHERE sessions_id = ?";
    } else if ($type === 'event') {
        $sql = "DELETE FROM events WHERE event_id = ?";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event type']);
        exit();
    }

    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$id])) {
        echo json_encode(['status' => 'success']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete event', 'errorInfo' => $errorInfo]);
    }
}
?>
