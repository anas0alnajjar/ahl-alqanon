<?php 
session_start();
if (
    isset($_SESSION['admin_id']) && 
    isset($_SESSION['role']) && 
    isset($_GET['language_id']) &&
    isset($_SERVER['HTTP_REFERER']) // التحقق من وجود العنوان URL المرجعي
) {
    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";
        $language_id = $_GET['language_id'];
        // DELETE
        function removelanguage($id, $conn) {
            $sql = "DELETE FROM languages WHERE id=?";
            $stmt = $conn->prepare($sql);
            $re = $stmt->execute([$id]);
            if ($re) {
                return true;
            } else {
                return false;
            }
        }

        $id = $_GET['language_id'];
        if (removeLanguage($id, $conn)) {
            $sm = "تم الحذف بنجاح!";
            header("Location: ../languages.php?success=".$sm); 
            exit;
        } else {
            $em = "حدث خطأ غير معروف، تواصل مع الدعم الفني";
            header("Location: ../languages.php?error=".$em); 
            exit;
        }
    } else {
        header("Location: languages.php");
        exit;
    }
} else {
    header("Location: languages.php");
    exit;
}
?>