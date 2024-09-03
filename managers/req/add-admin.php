<?php
include "../../DB_connection.php";

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role_id = $_POST['role_id'];

    $host_email = $_POST['host_email'];
    $username_email = $_POST['username_email'];
    $password_email = $_POST['password_email'];
    $port_email = $_POST['port_email'];
    $host_whatsapp = $_POST['host_whatsapp'];
    $token_whatsapp = $_POST['token_whatsapp'];

    function usernamelIsUnique($uname, $conn) {
        $sql = "SELECT username FROM `admin` WHERE username = ?
                UNION
                SELECT username FROM lawyer WHERE username = ?
                UNION
                SELECT username FROM helpers WHERE username = ?
                UNION
                SELECT username FROM clients WHERE username = ?
                UNION
                SELECT username FROM managers_office WHERE username = ?
                UNION
                SELECT username FROM ask_join WHERE username = ?";
  
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname, $uname, $uname, $uname, $uname, $uname]);
        
        return $stmt->rowCount() === 0;
    }

    if (usernamelIsUnique($username, $conn)) {
        try {
            $conn->beginTransaction();

            $sql = "INSERT INTO `admin` (username, password, fname, lname, role_id) VALUES (:username, :password, :first_name, :last_name, :role_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':role_id', $role_id);

            if ($stmt->execute()) {
                $admin_id = $conn->lastInsertId();

                // Handling logo upload
                if (isset($_FILES['admin_logo']) && $_FILES['admin_logo']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['admin_logo']['tmp_name'];
                    $fileName = $_FILES['admin_logo']['name'];
                    $fileSize = $_FILES['admin_logo']['size'];
                    $fileType = $_FILES['admin_logo']['type'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = '../../img/';
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $logo = $newFileName;
                    } else {
                        $response['message'] = 'حدث خطأ أثناء تحميل اللوغو.';
                        echo json_encode($response);
                        exit;
                    }
                }

                $sql = "INSERT INTO `setting` (host_email, username_email, password_email, port_email, host_whatsapp, token_whatsapp, logo, admin_id) VALUES (:host_email, :username_email, :password_email, :port_email, :host_whatsapp, :token_whatsapp, :logo, :admin_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':host_email', $host_email);
                $stmt->bindParam(':username_email', $username_email);
                $stmt->bindParam(':password_email', $password_email);
                $stmt->bindParam(':port_email', $port_email);
                $stmt->bindParam(':host_whatsapp', $host_whatsapp);
                $stmt->bindParam(':token_whatsapp', $token_whatsapp);
                $stmt->bindParam(':logo', $logo);
                $stmt->bindParam(':admin_id', $admin_id);

                if ($stmt->execute()) {
                    $conn->commit();
                    $response['success'] = true;
                    $response['message'] = 'تمت إضافة المستخدم بنجاح';
                } else {
                    $conn->rollBack();
                    $response['message'] = 'حدث خطأ أثناء حفظ الإعدادات.';
                }
            } else {
                $conn->rollBack();
                $response['message'] = 'حدث خطأ أثناء الإضافة';
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $response['message'] = 'فشل الإضافة: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'اسم المستخدم موجود بالفعل. يرجى اختيار اسم مستخدم آخر.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
