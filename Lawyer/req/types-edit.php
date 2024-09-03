<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    // Pagination
    include "../../DB_connection.php";
    include '../permissions_script.php';
    if ($pages['expense_types']['write'] == 0) {
        header("Location: ../home.php");
        exit();
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $type = $_POST['type'];
    $office_id = $_POST['office_id'];

    if (!empty($id) && !empty($type)) {
        $stmt = $conn->prepare("UPDATE costs_type SET `type` = :type, office_id= :office_id WHERE id = :id");
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':office_id', $office_id);
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