<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
   
    if ($_SESSION['role'] == 'Admin') {
        include '../../DB_connection.php';

        if (isset($_POST['language_name']) && isset($_POST['language_code']) && isset($_POST['is_default'])) {
            
            $language_name = $_POST['language_name'];
            $language_code = $_POST['language_code'];
            $language_id = $_POST['language_id'];
            $is_default = $_POST['is_default'];

            $data = 'language_id='.$language_id;
            
            //echo $language_id;
            //exit;

            if (empty($language_name) || empty($language_code)) {
                
                $em  = "كل الحقول مطلوبة";
                header("Location: ../language-edit.php?error=$em&$data");
                exit;
            } else {
                if($is_default == 1){
                    $sql = 'UPDATE languages SET is_default = ?';
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([0]);
                }
                
                $sql = "UPDATE languages SET
                        name=?, code=? ,is_default=? 
                        WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$language_name, $language_code,$is_default,$language_id]);

                $sm = " اللغة تم تحديثها";
                header("Location: ../language-edit.php?success=$sm&$data");
                exit;
            }
        } else {
            $em = "حدث خطأ ما";
            header("Location: ../languages.php?error=$em");
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
