<?php 
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    if (isset($_POST['id']) && isset($_POST['office_name']) && !empty($_POST['office_name'])) {
        include '../../DB_connection.php';

        include '../permissions_script.php';
        if ($pages['offices']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        $id = $_POST['id'];
        $office_name = $_POST['office_name'];
        $admin_id = $_POST['admin_id'];
        $stop = isset($_POST['stop']) ? 1 : 0;
        $stop_date = isset($_POST['stop_date']) ? $_POST['stop_date'] : NULL;
        $footer_text = isset($_POST['footer_text']) ? $_POST['footer_text'] : NULL;

        // رفع صورة الهيدر إذا تم تحميلها
        $header_image = NULL;
        if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] == UPLOAD_ERR_OK) {
            $header_image_extension = pathinfo($_FILES['header_image']['name'], PATHINFO_EXTENSION);
            $header_image = 'header_' . uniqid() . '.' . $header_image_extension;
            move_uploaded_file($_FILES['header_image']['tmp_name'], '../../uploads/' . $header_image);

            // حذف الصورة القديمة إذا تم تحميل صورة جديدة
            $sql = "SELECT header_image FROM offices WHERE office_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $old_image = $stmt->fetchColumn();
            if ($old_image) {
                unlink('../../uploads/' . $old_image);
            }
        }

        // إعداد الاستعلام
        $sql = "UPDATE offices SET office_name = ?, admin_id = ?, `stop` = ?, `stop_date` = ?, `footer_text` = ?";
        if ($header_image) {
            $sql .= ", header_image = ?";
            $params = [$office_name, $admin_id, $stop, $stop_date, $footer_text, $header_image, $id];
        } else {
            $params = [$office_name, $admin_id, $stop, $stop_date, $footer_text, $id];
        }
        $sql .= " WHERE office_id = ?";
        $stmt = $conn->prepare($sql);

        // التنفيذ
        if ($stmt->execute($params)) {
            $sm = "تم تحديث بيانات المكتب بنجاح";
            header("Location: ../offices.php?popsuccess=$sm");
            exit;
        } else {
            $em = "حدث خطأ أثناء التحديث";
            header("Location: ../offices.php?poperror=$em");
            exit;
        }
    } else {
        $em = "جميع الحقول مطلوبة";
        header("Location: ../offices.php?poperror=$em");
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
