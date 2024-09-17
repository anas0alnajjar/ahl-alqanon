<?php
session_start();
include "DB_connection.php"; // تأكد من تضمين الاتصال بقاعدة البيانات

// تحقق من وجود اللغة في الطلب والجلسة
if (isset($_POST['language']) && isset($_SESSION['role']) && (isset($_SESSION['user_id']) || isset($_SESSION['admin_id']))) {
    $language = $_POST['language'];
    $role = $_SESSION['role'];
    $userId = $_SESSION['user_id'] ?? $_SESSION['admin_id'];

    // تحديد الجدول بناءً على الدور
    switch ($role) {
        case 'Admin':
        case 'Admins':
            $sql = "UPDATE admin SET language_id = ? WHERE admin_id = ?";
            break;
        case 'Client':
            $sql = "UPDATE clients SET language_id = ? WHERE client_id = ?";
            break;
        case 'Helper':
            $sql = "UPDATE helpers SET language_id = ? WHERE id = ?";
            break;
        case 'Lawyer':
            $sql = "UPDATE lawyer SET language_id = ? WHERE lawyer_id = ?";
            break;
        case 'Managers':
            $sql = "UPDATE managers_office SET language_id = ? WHERE id = ?";
            break;
        default:
            echo 'دور غير معروف.';
            exit;
    }

    // تحديث اللغة في قاعدة البيانات
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$language, $userId])) {
        unset($_SESSION['lang_id']);
        echo 'تم تحديث اللغة بنجاح';
    } else {
        echo 'فشل في تحديث اللغة';
    }
} else {
    echo 'بيانات غير صحيحة';
}
?>
