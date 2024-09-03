<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    // Pagination
    include "../../DB_connection.php";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $court_name = $_POST['type'];
        $office_id = $_POST['office_id'];
        $public = isset($_POST['public']) ? 1 : 0; // تحقق من حالة التشيك بوكس

        if (!empty($id) && !empty($court_name)) {
            $stmt = $conn->prepare("UPDATE courts SET `court_name` = :court_name, office_id = :office_id, `public` = :public WHERE id = :id");
            $stmt->bindParam(':court_name', $court_name);
            $stmt->bindParam(':office_id', $office_id);
            $stmt->bindParam(':public', $public);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: ../courts.php?success=تم تحديث المحكمة بنجاح");
            } else {
                header("Location: ../courts.php?error=حدث خطأ أثناء التحديث");
            }
        } else {
            header("Location: ../courts.php?error=جميع الحقول مطلوبة");
        }
    } else {
        header("Location: ../courts.php");
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
