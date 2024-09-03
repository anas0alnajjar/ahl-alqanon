<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
include "../../DB_connection.php";
include '../permissions_script.php';
if ($pages['roles']['delete'] == 0) {
    header("Location: ../home.php");
    exit();
}

if (isset($_GET['id'])) {
    $roleId = (int)$_GET['id'];

    // حذف الرول
    $sqlPer = "DELETE FROM page_permissions WHERE role_id = ?";
    $stmtPer = $conn->prepare($sqlPer);
    $stmtPer->execute([$roleId]);

    $sql = "DELETE FROM powers WHERE power_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$roleId])) {
        // إعادة التوجيه بعد النجاح
        header("Location: ../powers.php?success=تم حذف الدور بنجاح");
    } else {
        // إعادة التوجيه بعد الفشل
        header("Location: ../powers.php?error=role_not_deleted");
    }
} else {
    header("Location: ../powers.php");
}
?>
<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>