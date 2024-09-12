<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Lawyer') {
        include '../../DB_connection.php';

        include '../permissions_script.php';
        if ($pages['documents']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }
        

        if (isset($_POST['title']) && isset($_POST['client_id']) && isset($_POST['lawyer_id']) && isset($_POST['content']) && isset($_POST['document_id'])) {
            
            $title = $_POST['title'];
            $client_id = $_POST['client_id'];
            $lawyer_id = $_POST['lawyer_id'];
            $content = $_POST['content'];
            $document_id = $_POST['document_id'];
            $office_id = $_POST['office_id'];
            $notes = $_POST['notes'];

            $data = 'document_id='.$document_id;

            if (empty($title) || empty($client_id) || empty($lawyer_id) || empty($content) || empty($document_id)) {
                
                $em  = "All fields are required";
                header("Location: ../document-edit.php?error=$em&$data");
                exit;
            } else {
                // تحديث العنوان، معرف الموكل، معرف المحامي، والمحتوى
                $sql = "UPDATE documents SET
                        title=?, client_id=?, lawyer_id=?, content=? , office_id = ?,notes=?
                        WHERE document_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$title, $client_id, $lawyer_id, $content, $office_id,$notes, $document_id]);

                // تحديث الملف المرفق إذا تم تحميل ملف جديد
                if ($_FILES['new_attachment']['error'] === UPLOAD_ERR_OK) {
                    // حذف الملف المرفق القديم إذا كان موجوداً
                    $sql_old_attachment = "SELECT attachments FROM documents WHERE document_id=?";
                    $stmt_old_attachment = $conn->prepare($sql_old_attachment);
                    $stmt_old_attachment->execute([$document_id]);
                    $old_attachment = $stmt_old_attachment->fetchColumn();

                    if ($old_attachment) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/pdf/' . $old_attachment);
                    }

                    // تحميل الملف المرفق الجديد
                    $new_attachment = $_FILES['new_attachment']['tmp_name'];
                    $current_date = date("Y-m-d");
                    $new_attachment_name = $client_id . "_" . $lawyer_id. $current_date . ".pdf";
                    $pathFolder = $_SERVER['DOCUMENT_ROOT'] . '/pdf/';
                    move_uploaded_file($new_attachment, $pathFolder . $new_attachment_name);

                    // تحديث اسم الملف في قاعدة البيانات
                    $sql_attachment = "UPDATE documents SET attachments=? WHERE document_id=?";
                    $stmt_attachment = $conn->prepare($sql_attachment);
                    $stmt_attachment->execute([$new_attachment_name, $document_id]);
                }

                $sm = "تم تحديث المستند بنجاح!";
                header("Location: ../document-edit.php?success=$sm&$data");
                exit;
            }
        } else {
            $em = "An error occurred";
            header("Location: ../documents.php?error=$em");
            exit;
        }
    } else {
        header("Location: ../../logout.php");
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
} 
?>
