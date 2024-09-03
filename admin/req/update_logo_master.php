<?php
include "../../DB_connection.php";

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التأكد من وجود الجلسة ومعرف المسؤول
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../home.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/';

    // التحقق من تحميل الملفات
    if (!empty($_FILES['logo']['tmp_name']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
        // استعلام للحصول على مسار الصورة القديمة إن وجدت
        $oldPictureQuery = $conn->prepare("SELECT logo FROM setting WHERE admin_id = ?");
        $oldPictureQuery->execute([$admin_id]);
        $oldPicture = $oldPictureQuery->fetchColumn();

        // معالجة الملف المرفوع
        $pictureTmpName = $_FILES['logo']['tmp_name'];
        $pictureExtension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION)); // الحصول على الامتداد
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif']; // الامتدادات المسموح بها

        if (in_array($pictureExtension, $validExtensions)) {
            $pictureName = uniqid('logo', true) . '_' . bin2hex(random_bytes(8)) . '.' . $pictureExtension; // توليد اسم ملف فريد
            $picturePath = $target_dir . $pictureName;

            if (move_uploaded_file($pictureTmpName, $picturePath)) {
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
                $response['error'] = "Failed to move uploaded file.";
            }
        } else {
            $response['error'] = "Invalid file extension.";
        }
    } else {
        $response['error'] = "No file uploaded.";
    }

    echo json_encode($response);
    exit();
} else {
    header("Location: lawyers.php");
    exit();
}
?>
