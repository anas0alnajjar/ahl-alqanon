<?php 
session_start();

// التحقق من أن المستخدم مسجل الدخول ولديه دور "Admin"
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    // التحقق من وجود البيانات المرسلة وتعبئتها بشكل صحيح
    if (isset($_POST['types']) && !empty($_POST['types'])) {
        include '../../DB_connection.php';

        include '../permissions_script.php';
        if ($pages['courts']['add'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        $court_name = $_POST['types'];
        $office_id = $_POST['office_id'];


        // استعداد الاستعلام
        $sql  = "INSERT INTO courts (`court_name`, office_id) VALUES(?, ?)";
        $stmt = $conn->prepare($sql);
        
        // التنفيذ
        if ($stmt->execute([$court_name, $office_id])) {
            // في حال نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة نجاح
            $sm = "تم تسجيل نوع جديد بنجاح";
            header("Location: ../courts.php?popsuccess=$sm");
            exit;
        } else {
            // في حالة عدم نجاح التنفيذ، قم بإعادة توجيه المستخدم مع رسالة خطأ
            $em = "حدث خطأ أثناء الحفظ";
            header("Location: ../courts.php?poperror=$em");
            exit;
        }
    } else {
        // إذا كانت البيانات المرسلة غير متاحة أو فارغة، قم بإعادة التوجيه مع رسالة خطأ
        $em = "جميع الحقول مطلوبة";
        header("Location: ../courts.php?poperror=$em");
        exit;
    }
} else {
    // إذا لم يكن المستخدم مسجل الدخول كمسؤول، قم بإعادة التوجيه إلى صفحة تسجيل الخروج
    header("Location: ../../logout.php");
    exit;
}
?>
