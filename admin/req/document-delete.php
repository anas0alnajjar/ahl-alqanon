<?php 
session_start();
if (
    isset($_SESSION['admin_id']) && 
    isset($_SESSION['role']) && 
    isset($_GET['document_id']) &&
    isset($_SERVER['HTTP_REFERER']) // التحقق من وجود العنوان URL المرجعي
) {
    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";
        $case_id = $_GET['id'];
        // DELETE
        function removeDocument($id, $conn) {
            $sql = "DELETE FROM documents WHERE document_id=?";
            $stmt = $conn->prepare($sql);
            $re = $stmt->execute([$id]);
            if ($re) {
                return true;
            } else {
                return false;
            }
        }

        $id = $_GET['document_id'];
        if (removeDocument($id, $conn)) {
            $sm = "تم الحذف بنجاح!";
            header("Location: ../documents.php"); 
            exit;
        } else {
            $em = "حدث خطأ غير معروف، تواصل مع الدعم الفني";
            header("Location: ../documents.php"); 
            exit;
        }
    } else {
        header("Location: cases.php");
        exit;
    }
} else {
    header("Location: cases.php");
    exit;
}
?>