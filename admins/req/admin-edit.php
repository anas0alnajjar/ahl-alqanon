<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admins') {

        if (isset($_POST['fname']) && isset($_POST['admin_id']) && isset($_POST['lname'])) {
            
            include '../../DB_connection.php';

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $admin_id = $_POST['admin_id'];
            $data = 'admin_id='.$admin_id;

            if (empty($fname)) {
                $em  = "الاسم مطلوب";
                header("Location: ../admin-profile.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em  = "اسم العائلة مطلوب";
                header("Location: ../admin-profile.php?error=$em&$data");
                exit;
            } else {
                try {
                    $conn->beginTransaction();

                    // تحديث معلومات الآدمن
                    $sql = "UPDATE `admin` SET fname=?, lname=? WHERE admin_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$fname, $lname, $admin_id]);

                    $conn->commit();
                    $sm = "تم التحديث بنجاح!";
                    header("Location: ../admin-profile.php?success=$sm&$data");
                    exit;

                } catch (Exception $e) {
                    $conn->rollBack();
                    $em = "حدث خطأ أثناء التحديث: " . $e->getMessage();
                    header("Location: ../admin-profile.php?error=$em&$data");
                    exit;
                }
            }
        } else {
            $em = "An error occurred";
            header("Location: ../admin-profile.php?error=$em&$data");
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
