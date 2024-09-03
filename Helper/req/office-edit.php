<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
    if (isset($_POST['id']) && isset($_POST['office_name']) && !empty($_POST['office_name'])) {
        include '../../DB_connection.php';

        $id = $_POST['id'];
        $office_name = $_POST['office_name'];
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
        $sql = "UPDATE offices SET office_name = ?, footer_text = ?";
        if ($header_image) {
            $sql .= ", header_image = ?";
            $params = [$office_name, $footer_text, $header_image, $id];
        } else {
            $params = [$office_name, $footer_text, $id];
        }
        $sql .= " WHERE office_id = ?";
        $stmt = $conn->prepare($sql);

        // التنفيذ
        if ($stmt->execute($params)) {
            echo json_encode(['status' => 'success', 'message' => 'تم تحديث بيانات المكتب بنجاح']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء التحديث']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'جميع الحقول مطلوبة']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة']);
}
?>
