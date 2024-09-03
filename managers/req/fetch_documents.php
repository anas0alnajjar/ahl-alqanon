<?php
session_start();

// التحقق من الجلسة وصلاحيات المستخدم
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] === 'Managers') {
    include "../../DB_connection.php";
    include "../get_office.php";

    // الحصول على معرف المستخدم من الجلسة
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);

    if (!empty($OfficeId)) {
        // استخدام استعلام مُحَضّر لتجنب هجمات SQL Injection
        $stmt = $conn->prepare("SELECT documents.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name 
                                FROM documents 
                                LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                                LEFT JOIN clients ON documents.client_id = clients.client_id 
                                WHERE lawyer.office_id = :officeId OR clients.office_id = :officeId
                                ORDER BY documents.document_id DESC");
        $stmt->bindParam(':officeId', $OfficeId, PDO::PARAM_INT);
        $stmt->execute();

        // جلب النتائج كصفوف مرتبطة
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // تحويل النتائج إلى JSON
        echo json_encode($results);
    } else {
        // إذا لم يتم العثور على OfficeId، إرجاع مصفوفة فارغة
        echo json_encode([]);
    }
} else {
    // إعادة توجيه المستخدم إلى صفحة تسجيل الخروج إذا لم يكن لديه الصلاحيات
    header("Location: ../../logout.php");
    exit;
}
?>
