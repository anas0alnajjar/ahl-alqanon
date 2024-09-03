<?php 
session_start();

// التحقق من أن المستخدم مسجل الدخول ولديه دور "Admin"
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    // التحقق من وجود البيانات المرسلة وتعبئتها بشكل صحيح
    if (isset($_POST['types']) && !empty($_POST['types'])) {
        include '../../DB_connection.php';

        $types = $_POST['types'];
        $office_id = $_POST['office_id'];

        include '../permissions_script.php';
            if ($pages['judicial_circuits']['add'] == 0) {
                header("Location: ../home.php");
                exit();
            }


        // استعداد الاستعلام
        $sql  = "INSERT INTO departments (`type`, office_id) VALUES(?, ?)";
        $stmt = $conn->prepare($sql);
        
        // التنفيذ
        if ($stmt->execute([$types, $office_id])) {
            // في حال نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة نجاح
            $sm = "تم تسجيل نوع جديد بنجاح";
            header("Location: ../departments_types.php?popsuccess=$sm");
            exit;
        } else {
            // في حالة عدم نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة خطأ
            $em = "حدث خطأ أثناء الحفظ";
            header("Location: ../departments_types.php?poperror=$em");
            exit;
        }
    } else {
        // إذا كانت البيانات المرسلة غير متاحة أو فارغة، قم بإعادة التوجيه مع رسالة خطأ
        $em = "جميع الحقول مطلوبة";
        header("Location: ../departments_types.php?poperror=$em");
        exit;
    }
} else {
    // إذا لم يكن المستخدم مسجل الدخول كمسؤول، قم بإعادة التوجيه إلى صفحة تسجيل الخروج
    header("Location: ../../logout.php");
    exit;
}
?>
