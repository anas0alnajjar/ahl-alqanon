<?php
include "../../DB_connection.php";

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
