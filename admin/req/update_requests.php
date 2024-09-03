<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تضمين ملف الاتصال بقاعدة البيانات
include '../../DB_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من القيم المستلمة من AJAX
    $field = isset($_POST['field']) ? $_POST['field'] : null;
    $value = isset($_POST['value']) ? $_POST['value'] : null;

    if ($field && $value !== null) {
        try {
            // يجب عليك تحديد user_id المناسب هنا
            $user_id = 1; // تأكد من تغيير هذه القيمة حسب السياق

            // إعداد الاستعلام
            $stmt = $conn->prepare("UPDATE requests SET $field = :value WHERE id = :user_id");
            $stmt->bindParam(':value', $value, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            // تنفيذ الاستعلام
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'تم التحديث بنجاح']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'فشل في التحديث']);
            }
        } catch (PDOException $e) {
            error_log("Database Query Failed: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'بيانات غير صالحة']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح']);
}
?>
