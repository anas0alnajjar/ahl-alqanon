<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {

        if (isset($_POST['fname']) && isset($_POST['admin_id']) && isset($_POST['lname']) && isset($_POST['role_id'])) {
            
            include '../../DB_connection.php';

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $role_id = $_POST['role_id'];
            $admin_id = $_POST['admin_id'];
            $stop_date = $_POST['stop_date'];
            $stop_account = isset($_POST['stop']) ? 1 : 0;

            $data = 'admin_id='.$admin_id;

            if (empty($fname)) {
                $em  = "الاسم مطلوب";
                header("Location: ../admin-edit.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em  = "اسم العائلة مطلوب";
                header("Location: ../admin-edit.php?error=$em&$data");
                exit;
            } else if (empty($role_id)) {
                $em  = "الدور مطلوب";
                header("Location: ../admin-edit.php?error=$em&$data");
                exit;
            } else {
                try {
                    $conn->beginTransaction();

                    // تحديث معلومات الآدمن
                    $sql = "UPDATE `admin` SET fname=?, lname=?, role_id=?, stop=?, stop_date=? WHERE admin_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$fname, $lname, $role_id, $stop_account, $stop_date, $admin_id]);

                    // تحقق من وجود سجل في جدول setting
                    $sql = "SELECT COUNT(*) FROM `setting` WHERE admin_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$admin_id]);
                    $exists = $stmt->fetchColumn() > 0;

                    // التعامل مع رفع اللوغو
                    if (isset($_FILES['admin_logo']) && $_FILES['admin_logo']['error'] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['admin_logo']['tmp_name'];
                        $fileName = $_FILES['admin_logo']['name'];
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $uploadFileDir = '../../img/';
                        $dest_path = $uploadFileDir . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            if ($exists) {
                                $sql = "SELECT logo FROM `setting` WHERE admin_id=?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$admin_id]);
                                $oldLogo = $stmt->fetchColumn();

                                if ($oldLogo && file_exists($uploadFileDir . $oldLogo)) {
                                    unlink($uploadFileDir . $oldLogo);
                                }

                                $sql = "UPDATE `setting` SET logo=? WHERE admin_id=?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$newFileName, $admin_id]);
                            } else {
                                $sql = "INSERT INTO `setting` (admin_id, logo) VALUES (?, ?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$admin_id, $newFileName]);
                                $exists = true; // تحديث قيمة $exists بعد الإدراج
                            }
                        } else {
                            throw new Exception('Failed to move the uploaded file.');
                        }
                    }

                    // تحديث أو إدراج إعدادات الواتساب والإيميل
                    if ($admin_id != 1) {
                        $host_whatsapp = $_POST['host_whatsapp'];
                        $token_whatsapp = $_POST['token_whatsapp'];
                        $host_email = $_POST['host_email'];
                        $username_email = $_POST['username_email'];
                        $password_email = $_POST['password_email'];
                        $port_email = $_POST['port_email'];

                        if ($exists) {
                            $sql = "UPDATE `setting` SET host_whatsapp=?, token_whatsapp=?, host_email=?, username_email=?, password_email=?, port_email=? WHERE admin_id=?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$host_whatsapp, $token_whatsapp, $host_email, $username_email, $password_email, $port_email, $admin_id]);
                        } else {
                            $sql = "INSERT INTO `setting` (admin_id, host_whatsapp, token_whatsapp, host_email, username_email, password_email, port_email) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$admin_id, $host_whatsapp, $token_whatsapp, $host_email, $username_email, $password_email, $port_email]);
                        }
                    }

                    $conn->commit();
                    $sm = "تم التحديث بنجاح!";
                    header("Location: ../admin-edit.php?success=$sm&$data");
                    exit;

                } catch (Exception $e) {
                    $conn->rollBack();
                    $em = "حدث خطأ أثناء التحديث: " . $e->getMessage();
                    header("Location: ../admin-edit.php?error=$em&$data");
                    exit;
                }
            }
        } else {
            $em = "An error occurred";
            header("Location: ../users.php?error=$em");
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
