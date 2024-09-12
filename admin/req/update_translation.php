<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
   
    if ($_SESSION['role'] == 'Admin') {
        include '../../DB_connection.php';

        if (isset($_POST['translation_id']) && isset($_POST['translated_text_name']) ) {
            
           // $tranlation_key_name = $_POST['tranlation_key_name'];
            $translated_text = $_POST['translated_text_name'];
            $translation_id = $_POST['translation_id'];
            $data = 'translation_id='.$translation_id;
            
            //echo $language_id;
            //exit;

            if (empty($translation_id)) {
                
                $em  = "حدث خطأ ما";
                header("Location: ../translation-edit.php?error=$em&$data");
                exit;
            } else {
                // تحديث العنوان، معرف الموكل، معرف المحامي، والمحتوى
                $sql = "UPDATE translations SET
                        translated_text=? 
                        WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$translated_text,$translation_id]);
               
                   
    
                     

                $sm = " اللغة تم تحديثها";
                header("Location: ../translation-edit.php?success=$sm&$data");
                exit;
            }
        } else {
            $em = "حدث خطأ ما";
            header("Location: ../translation-edit.php?error=$em&$data");
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
