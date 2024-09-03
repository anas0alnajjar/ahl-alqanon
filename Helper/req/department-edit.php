<?php
// department-edit.php

// ابدأ الجلسة إذا كنت تستخدم جلسات في مشروعك
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {

// قم بتضمين ملف الاتصال بقاعدة البيانات
require_once '../../DB_connection.php';
include '../permissions_script.php';
     if ($pages['judicial_circuits']['write'] == 0) {
         header("Location: ../home.php");
         exit();
     }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // الحصول على البيانات المرسلة من النموذج
    $id = $_POST['id'];
    $type = $_POST['type'];
    $office_id = $_POST['office_id'];
    $office_id_current = $_POST['office_id_current'];

    // تحقق من وجود جميع البيانات الضرورية
    if (!empty($id) && !empty($type) && !empty($office_id)) {
        try {
            // تحديث البيانات في قاعدة البيانات
            $sql = "UPDATE departments SET type = :type, office_id = :office_id WHERE id = :id";
            $stmt = $conn->prepare($sql);

            // تمرير القيم إلى الاستعلام
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':office_id', $office_id);
            $stmt->bindParam(':id', $id);

            // تنفيذ الاستعلام
            if ($stmt->execute()) {
                header("Location: ../departments_types.php?success=تم تحديث الدائرة بنجاح");
            } else {
                header("Location: ../departments_types.php?success=حدث خطأ أثناء تحديث الدائرة");
            }
        } catch (PDOException $e) {
            // التعامل مع الأخطاء
            header("Location: ../departments_types.php?error=خطأ في قاعدة البيانات: " . urlencode($e->getMessage()));
        }
    } else {
        header("Location: ../departments_types.php?error=الرجاء تعبئة جميع الحقول");
    }
} else {
    header("Location: ../departments_types.php?error=طلب غير صالح");
}
} else {
    header("Location: ../../login.php");
    exit;
}
// إنهاء التنفيذ
exit();
?>
