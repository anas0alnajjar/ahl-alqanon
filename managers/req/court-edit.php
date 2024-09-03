<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Managers') {
    // Pagination
    include "../../DB_connection.php";
    include '../permissions_script.php';
    if ($pages['courts']['write'] == 0) {
        header("Location: ../home.php");
        exit();
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $court_name = $_POST['type'];
    $office_id = $_POST['office_id'];

    if (!empty($id) && !empty($court_name)) {
        $stmt = $conn->prepare("UPDATE courts SET `court_name` = :court_name, office_id= :office_id WHERE id = :id");
        $stmt->bindParam(':court_name', $court_name);
        $stmt->bindParam(':office_id', $office_id);
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
?>
<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>