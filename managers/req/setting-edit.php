<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Managers') {
        if (isset($_POST['host_whatsapp']) &&
            isset($_POST['token_whatsapp']) &&
            isset($_POST['host_email']) && 
            isset($_POST['password_email']) &&
            isset($_POST['port_email']) &&
            isset($_POST['username_email'])) {

            include '../../DB_connection.php';
            include '../permissions_script.php';
            if ($pages['logo_contact']['read'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            $host_whatsapp = $_POST['host_whatsapp'];
            $token_whatsapp = $_POST['token_whatsapp'];
            $host_email = $_POST['host_email'];
            $password_email = $_POST['password_email'];
            $port_email = $_POST['port_email'];
            $username_email = $_POST['username_email'];

            $admin_id = $_POST['admin_id'];

            $sql = "UPDATE setting 
                    SET 
                        host_whatsapp=?,
                        token_whatsapp=?,
                        host_email=?,
                        username_email=?,
                        password_email=?,
                        port_email=?
                    WHERE admin_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$host_whatsapp, $token_whatsapp, $host_email, $username_email, $password_email, $port_email, $admin_id]);
            $sm = "تم تحديث الاعدادات بنجاح";
            header("Location: ../settings.php?success=$sm");
            exit;
        } else {
            $em = "An error occurred";
            header("Location: ../section.php?error=$em");
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
