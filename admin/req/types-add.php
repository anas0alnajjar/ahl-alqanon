<?php 
session_start();

// التحقق من أن المستخدم مسجل الدخول ولديه دور "Admin"
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    // التحقق من وجود البيانات المرسلة وتعبئتها بشكل صحيح
    if (isset($_POST['types']) && !empty($_POST['types'])) {
        include '../../DB_connection.php';

        $types = $_POST['types'];
        $office_id = $_POST['office_id'];
        $public = isset($_POST['public']) ? 1 : 0; // تحقق من حالة التش

        // استعداد الاستعلام
        $sql  = "INSERT INTO costs_type (`type`, office_id, public) VALUES(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // التنفيذ
        if ($stmt->execute([$types, $office_id, $public])) {
            // في حال نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة نجاح
            $sm = "تم تسجيل نوع جديد بنجاح";
            header("Location: ../types.php?popsuccess=$sm");
            exit;
        } else {
            // في حالة عدم نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة خطأ
            $em = "حدث خطأ أثناء الحفظ";
            header("Location: ../types.php?poperror=$em");
            exit;
        }
    } else {
        // إذا كانت البيانات المرسلة غير متاحة أو فارغة، قم بإعادة التوجيه مع رسالة خطأ
        $em = "جميع الحقول مطلوبة";
        header("Location: ../types.php?poperror=$em");
        exit;
    }
} else {
    // إذا لم يكن المستخدم مسجل الدخول كمسؤول، قم بإعادة التوجيه إلى صفحة تسجيل الخروج
    header("Location: ../../logout.php");
    exit;
}
?>
