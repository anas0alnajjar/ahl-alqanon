<?php 
session_start();

// التحقق من أن المستخدم مسجل الدخول ولديه دور "Admin"
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    // التحقق من وجود البيانات المرسلة وتعبئتها بشكل صحيح
    if (isset($_POST['office_name']) && !empty($_POST['office_name'])) {
        include '../../DB_connection.php';

        $office_name = $_POST['office_name'];
        $admin_id = $_POST['admin_id'];
        $stop = isset($_POST['stop']) ? 1 : 0;
        $default_office = isset($_POST['default_office']) ? 1 : 0;
        $stop_date = isset($_POST['stop_date']) ? $_POST['stop_date'] : NULL;
        $footer_text = isset($_POST['footer_text']) ? $_POST['footer_text'] : NULL;

        // التحقق من وجود مكتب افتراضي
        if ($default_office == 1) {
            $check_sql = "SELECT office_name FROM offices WHERE default_office = 1";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute();
            $existing_default_office = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_default_office) {
                // إذا كان هناك مكتب افتراضي بالفعل، قم بإعادة التوجيه مع رسالة خطأ
                $em = "يوجد بالفعل مكتب افتراضي باسم: " . $existing_default_office['office_name'];
                header("Location: ../offices.php?poperror=$em");
                exit;
            }
        }

        // رفع صورة الهيدر إذا تم تحميلها
        $header_image = NULL;
        if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] == UPLOAD_ERR_OK) {
            // توليد اسم فريد باستخدام UUID
            $header_image_extension = pathinfo($_FILES['header_image']['name'], PATHINFO_EXTENSION);
            $header_image = 'header_' . uniqid() . '.' . $header_image_extension;
            move_uploaded_file($_FILES['header_image']['tmp_name'], '../../uploads/' . $header_image);
        }

        // استعداد الاستعلام
        $sql  = "INSERT INTO offices (`office_name`, admin_id, `stop`, `stop_date`, `header_image`, `footer_text`, default_office) VALUES(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // التنفيذ
        if ($stmt->execute([$office_name, $admin_id, $stop, $stop_date, $header_image, $footer_text, $default_office])) {
            // في حال نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة نجاح
            $sm = "تم تسجيل مكتب جديد بنجاح";
            header("Location: ../offices.php?success=$sm");
            exit;
        } else {
            // في حالة عدم نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة خطأ
            $em = "حدث خطأ أثناء الحفظ";
            header("Location: ../offices.php?poperror=$em");
            exit;
        }
    } else {
        // إذا كانت البيانات المرسلة غير متاحة أو فارغة، قم بإعادة التوجيه مع رسالة خطأ
        $em = "جميع الحقول مطلوبة";
        header("Location: ../offices.php?poperror=$em");
        exit;
    }
} else {
    // إذا لم يكن المستخدم مسجل الدخول كمسؤول، قم بإعادة التوجيه إلى صفحة تسجيل الخروج
    header("Location: ../../logout.php");
    exit;
}
?>
