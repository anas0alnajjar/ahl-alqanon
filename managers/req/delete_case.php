<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {
        include "../../DB_connection.php";

        include '../permissions_script.php';

        if ($pages['cases']['delete'] == 0) {
            exit();
        }

        // التحقق مما إذا تم استقبال قيمة id
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];

            // الحصول على ملف هوية الموكل
            $stmt = $conn->prepare("SELECT id_picture FROM cases WHERE case_id = ?");
            $stmt->execute([$id]);
            $client_picture = $stmt->fetch(PDO::FETCH_ASSOC);


            // الحصول على أسماء الملفات المرتبطة بالقضية
            $stmt = $conn->prepare("SELECT file_path FROM files WHERE case_id = ?");
            $stmt->execute([$id]);
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // حذف الملفات من النظام
            foreach ($files as $file) {
                $filePath = '../../Lawyer/files/' . $file['file_path']; 
                if (file_exists($filePath)) {
                    unlink($filePath);
                } else {
                    error_log("File not found: " . $filePath); // سجل خطأ في حالة عدم وجود الملف
                }
       
                // الحصول على أسماء الملفات المرتبطة بالعقود
            $stmt = $conn->prepare("SELECT attachments FROM documents WHERE case_id = ?");
            $stmt->execute([$id]);
            $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // حذف الملفات من النظام
            foreach ($documents as $docu) {
                $filePathDoc = '../../pdf/' . $docu['attachments']; 
                if (file_exists($filePathDoc)) {
                    unlink($filePathDoc);
                } else {
                    error_log("File not found: " . $filePathDoc); // سجل خطأ في حالة عدم وجود الملف
                }
            }
        }

            // حذف ملف الهوية من النظام
            if ($client_picture) {
                $picturePath = '../../uploads/' . $client_picture['id_picture'];
                if (file_exists($picturePath)) {
                    unlink($picturePath);
                } else {
                    error_log("Picture not found: " . $picturePath); // سجل خطأ في حالة عدم وجود ملف الهوية
                }
            }

            // إجراء عملية الحذف في قاعدة البيانات
            $stmt = $conn->prepare("DELETE FROM cases WHERE case_id = ?");
            $stmt->execute([$id]);
            $rowCountCases = $stmt->rowCount();

            $stmt = $conn->prepare("DELETE FROM `sessions` WHERE case_id = ?");
            $stmt->execute([$id]);
            $rowCountSessions = $stmt->rowCount();

            $stmt = $conn->prepare("DELETE FROM expenses WHERE case_id = ?");
            $stmt->execute([$id]);
            $rowCountExpenses = $stmt->rowCount();

            $stmt = $conn->prepare("DELETE FROM payments WHERE case_id = ?");
            $stmt->execute([$id]);
            $rowCountPayment = $stmt->rowCount();

            $stmt = $conn->prepare("DELETE FROM documents WHERE case_id = ?");
            $stmt->execute([$id]);
            $rowCountDocuments = $stmt->rowCount();
            
            $stmt = $conn->prepare("DELETE FROM files WHERE case_id = ?");
            $stmt->execute([$id]);
            $rowCountFiles = $stmt->rowCount();

            // التحقق مما إذا تمت عملية الحذف بنجاح أم لا
            if ($rowCountCases > 0) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    } else {
        header("location: cases.php");
    }
} else {
    header("Location: ../../login.php");
    exit;
}
?>
