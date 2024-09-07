<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {
        if (isset($_POST['content']) &&
            isset($_POST['lawer_name']) &&
            isset($_POST['client_name']) &&
            isset($_POST['document_title'])) {
                
            include '../../DB_connection.php';

                
            include '../permissions_script.php';
            if ($pages['documents']['add'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            $content = $_POST['content'];
            $lawer_name = $_POST['lawer_name'];
            $client_name = $_POST['client_name'];
            $document_title = $_POST['document_title'];
            $office_id = $_POST['office_id'];
            $notes = $_POST['notes'];
            $data = 'content='.$content.'&document_title='.$document_title.'&lawer_name='.$lawer_name.'&client_name='.$client_name.'&office_id='.$office_id;
            
            if (empty($content) || empty($lawer_name) || empty($client_name) || empty($document_title)) {
                $em = "جميع الحقول مطلوبة، ما عدا الملف، رجاءً قم بملئها";
                header("Location: ../document-add.php?error=$em&$data");
                exit;
            }
            
            // التحقق مما إذا كان الملف مرفقًا
            $fileUploaded = isset($_FILES['attachments']) && $_FILES['attachments']['error'] === UPLOAD_ERR_OK;
            
            // إذا كان الملف مرفقًا، قم بعمليات الرفع
            if ($fileUploaded) {
                $attachments = $_FILES['attachments']['tmp_name'];

                // تعيين اسم المرفق
                $unique_id = uniqid();
                $current_date = date("Y-m-d");
                $attachments_name = $unique_id . "_" . $client_name . "_" . $current_date . ".pdf";
                $pathFolder = $_SERVER['DOCUMENT_ROOT'] . '/pdf/';
                move_uploaded_file($attachments,$pathFolder.$attachments_name);
            } else {
                // إذا لم يكن الملف مرفقًا، قم بتعيين قيمة فارغة لاسم المرفق
                $attachments_name = "";
            }

            // استعداد الاستعلام
            $sql  = "INSERT INTO documents(title, content, client_id, lawyer_id, attachments, office_id, notes) VALUES(?,?,?,?,?,?, ?)";
            $stmt = $conn->prepare($sql);

            // تنفيذ الاستعلام
            $stmt->execute([$document_title, $content, $client_name, $lawer_name, $attachments_name, $office_id, $notes]);

            $sm = "تم حفظ المستند بنجاح";
            header("Location: ../document-add.php?success=$sm");
            exit;
        } else {
            $em = "An error occurred";
            header("Location: ../document-add.php?error=$em");
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
