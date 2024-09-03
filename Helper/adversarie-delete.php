<?php 
session_start();

// تأكد من أن المستخدم مُسجل الدخول
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'Helper') {
    header("Location: login.php");
    exit();
}

include "../DB_connection.php";
include 'permissions_script.php';
    if ($pages['adversaries']['delete'] == 0) {
        header("Location: home.php");
        exit();
    }

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