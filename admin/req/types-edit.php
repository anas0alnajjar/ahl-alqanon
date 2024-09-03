<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    // Pagination
    include "../../DB_connection.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $type = $_POST['type'];
    $office_id = $_POST['office_id'];
    $public = isset($_POST['public']) ? 1 : 0; // تحقق من حالة التشيك
    
    if (!empty($id) && !empty($type)) {
        $stmt = $conn->prepare("UPDATE costs_type SET `type` = :type, office_id= :office_id, public = :public WHERE id = :id");
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':office_id', $office_id);
        $stmt->bindParam(':public', $public);
        $stmt->bindParam(':id', $id);
        

        if ($stmt->execute()) {
            header("Location: ../types.php?success=تم تحديث النوع بنجاح");
        } else {
            header("Location: ../types.php?error=حدث خطأ أثناء التحديث");
        }
    } else {
        header("Location: ../types.php?error=جميع الحقول مطلوبة");
    }
} else {
    header("Location: ../types.php");
}
?>
<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>