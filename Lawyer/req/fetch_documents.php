<?php
session_start();

// التحقق من الجلسة وصلاحيات المستخدم
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] === 'Lawyer') {
    include "../../DB_connection.php";
    include "../get_office.php";

    // الحصول على معرف المستخدم من الجلسة
    $user_id = $_SESSION['user_id'];

    if (!empty($user_id)) {
        // استخدام استعلام مُحَضّر لتجنب هجمات SQL Injection
        $stmt = $conn->prepare("SELECT documents.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name 
                                FROM documents 
                                LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                                LEFT JOIN clients ON documents.client_id = clients.client_id 
                                LEFT JOIN cases ON documents.case_id = lawyer.lawyer_id 
                                WHERE lawyer.lawyer_id = :lawyer_id OR clients.lawyer_id = :lawyer_id
                                ORDER BY documents.document_id DESC");
        $stmt->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
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
