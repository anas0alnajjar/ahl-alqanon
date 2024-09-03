<?php 
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    if (isset($_POST['id']) && isset($_POST['office_name']) && !empty($_POST['office_name'])) {
        include '../../DB_connection.php';

        $id = $_POST['id'];
        $office_name = $_POST['office_name'];
        $admin_id = $_POST['admin_id'];
        $stop = isset($_POST['stop']) ? 1 : 0;
        $default_office = isset($_POST['default_office']) ? 1 : 0;
        $stop_date = isset($_POST['stop_date']) ? $_POST['stop_date'] : NULL;
        $footer_text = isset($_POST['footer_text']) ? $_POST['footer_text'] : NULL;

        // التحقق من وجود مكتب افتراضي آخر
        if ($default_office == 1) {
            $check_sql = "SELECT office_name FROM offices WHERE default_office = 1 AND office_id != ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute([$id]);
            $existing_default_office = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_default_office) {
                $em = "يوجد بالفعل مكتب افتراضي باسم: " . $existing_default_office['office_name'];
                header("Location: ../offices.php?error=" . urlencode($em));
                exit;
            }
        }

        // رفع صورة الهيدر إذا تم تحميلها
        $header_image = NULL;
        if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] == UPLOAD_ERR_OK) {
            $header_image_extension = pathinfo($_FILES['header_image']['name'], PATHINFO_EXTENSION);
            $header_image = 'header_' . uniqid() . '.' . $header_image_extension;
            if (move_uploaded_file($_FILES['header_image']['tmp_name'], '../../uploads/' . $header_image)) {
                // حذف الصورة القديمة إذا تم تحميل صورة جديدة
                $sql = "SELECT header_image FROM offices WHERE office_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id]);
                $old_image = $stmt->fetchColumn();
                if ($old_image && file_exists('../../uploads/' . $old_image)) {
                    unlink('../../uploads/' . $old_image);
                }
            } else {
                $header_image = NULL; // عدم عرض خطأ في حالة فشل رفع الصورة الجديدة
            }
        }

        // إعداد الاستعلام
        $sql = "UPDATE offices SET office_name = ?, admin_id = ?, `stop` = ?, `stop_date` = ?, `footer_text` = ?, default_office = ?";
        $params = [$office_name, $admin_id, $stop, $stop_date, $footer_text, $default_office];

        if ($header_image) {
            $sql .= ", header_image = ?";
            $params[] = $header_image;
        }

        $sql .= " WHERE office_id = ?";
        $params[] = $id;

        $stmt = $conn->prepare($sql);

        // التنفيذ
        if ($stmt->execute($params)) {
            $sm = "تم تحديث بيانات المكتب بنجاح";
            header("Location: ../offices.php?success=" . urlencode($sm));
            exit;
        } else {
            $em = "حدث خطأ أثناء التحديث";
            header("Location: ../offices.php?error=" . urlencode($em));
            exit;
        }
    } else {
        $em = "جميع الحقول مطلوبة";
        header("Location: ../offices.php?error=" . urlencode($em));
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
