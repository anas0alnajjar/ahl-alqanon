<?php 
session_start();

// تأكد من أن المستخدم مُسجل الدخول
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

include "../DB_connection.php";

// تأكد من وجود id في عنوان URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // قم بإزالة السجل من جدول adversaries
    $sql = "DELETE FROM adversaries WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // تم حذف السجل بنجاح
        // redirect to the page you want
        header("Location: adversaries.php");
        exit();
    } else {
        // حدث خطأ أثناء حذف السجل
        echo "حدث خطأ أثناء حذف السجل.";
    }
} else {
    // لم يتم العثور على id في عنوان URL
    echo "لم يتم العثور على id في عنوان URL.";
}
?>