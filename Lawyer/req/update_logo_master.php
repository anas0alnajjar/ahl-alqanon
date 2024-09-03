<?php
session_start();
include "../../DB_connection.php";

$response = array('success' => false);

try {
    if (!isset($_POST['admin_id'])) {
        throw new Exception("Admin ID is required.");
    }

    $admin_id = $_POST['admin_id']; // استلام admin_id من POST

    // التحقق من صلاحيات الكتابة
    include '../permissions_script.php';
    if ($pages['logo_contact']['read'] == 0) {
        throw new Exception("No read permissions.");
    }

    if (!isset($pages['logo_contact']) || $pages['logo_contact']['write'] == 0) {
        throw new Exception("No write permissions.");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/';

        // التحقق من تحميل الملفات
        if (empty($_FILES['logo']['tmp_name']) || !is_uploaded_file($_FILES['logo']['tmp_name'])) {
            throw new Exception("No file uploaded.");
        }

        // استعلام للحصول على مسار الصورة القديمة إن وجدت
        $oldPictureQuery = $conn->prepare("SELECT logo FROM setting WHERE admin_id = ?");
        $oldPictureQuery->execute([$admin_id]);
        $oldPicture = $oldPictureQuery->fetchColumn();

        // معالجة الملف المرفوع
        $pictureTmpName = $_FILES['logo']['tmp_name'];
        $pictureExtension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION)); // الحصول على الامتداد
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif']; // الامتدادات المسموح بها

        if (!in_array($pictureExtension, $validExtensions)) {
            throw new Exception("Invalid file extension.");
        }

        $pictureName = uniqid('logo', true) . '_' . bin2hex(random_bytes(8)) . '.' . $pictureExtension; // توليد اسم ملف فريد
        $picturePath = $target_dir . $pictureName;

        if (!move_uploaded_file($pictureTmpName, $picturePath)) {
            throw new Exception("Failed to move uploaded file.");
        }

        // تحديث قاعدة البيانات بالصورة الجديدة
        $updateQuery = $conn->prepare("UPDATE setting SET logo = ? WHERE admin_id = ?");
        $updateQuery->execute([$pictureName, $admin_id]);

        $response['success'] = true;
        $response['new_logo'] = $pictureName;

        // حذف الصورة القديمة إن وجدت
        if ($oldPicture && file_exists($target_dir . $oldPicture)) {
            unlink($target_dir . $oldPicture);
        }
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// تعيين نوع المحتوى إلى JSON
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
