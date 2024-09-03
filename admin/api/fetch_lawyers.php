<?php
include "../../DB_connection.php"; // تضمين ملف الاتصال بقاعدة البيانات

if (isset($_GET['office_id'])) {
    $office_id = $_GET['office_id'];
    $sql = "SELECT `lawyer_id`, `lawyer_name` FROM lawyer WHERE `office_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$office_id]);
    $lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($lawyers);
}
?>
