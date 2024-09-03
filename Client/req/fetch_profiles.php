<?php
session_start();

// التحقق من أن المستخدم لديه صلاحيات مناسبة
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Client') {
    include "../../DB_connection.php";
    include '../permissions_script.php';

    // التحقق من صلاحيات القراءة
    if ($pages['profiles']['read'] == 0) {
        header("Location: ../home.php");
        exit();
    }

    // استدعاء دالة للحصول على معرف المكتب
    include "../get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);

    if (!empty($OfficeId)) {
        // استخدام معاملات مكانية لتجنب حقن SQL
        $stmt = $conn->prepare("SELECT profiles.*, offices.office_name
                                FROM profiles 
                                LEFT JOIN offices ON profiles.office_id = offices.office_id 
                                WHERE profiles.office_id = :office_id
                                ORDER BY profiles.id DESC");
        $stmt->bindParam(':office_id', $OfficeId, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // تعيين نوع المحتوى إلى JSON لتجنب مشاكل الترميز
        header('Content-Type: application/json');
        echo json_encode($results);
    } else {
        header('Content-Type: application/json');
        echo json_encode([]);
    }
} else {
    // إعادة توجيه المستخدم إذا لم يكن مصرحًا له
    header("Location: ../../logout.php");
    exit();
}
?>
