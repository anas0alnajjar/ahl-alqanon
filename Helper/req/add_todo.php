<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {
        include '../../DB_connection.php';
        include '../permissions_script.php';

        if ($pages['notifications']['add'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        // Function to send WhatsApp message
        if (!function_exists('sendWhatsAppMessage')) {
            function sendWhatsAppMessage($recipient, $message, $settings) {
                $params = array(
                    'token' => $settings['token_whatsapp'],
                    'to' => $recipient,
                    'body' => $message
                );

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $settings['host_whatsapp'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => http_build_query($params),
                    CURLOPT_HTTPHEADER => array(
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    error_log("Curl Error: " . $err);
                    return false;
                } else {
                    return true;
                }
            }
        }


        include "../get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);

        // جلب إعدادات واتس آب من قاعدة البيانات
        function getWhatsAppSettings($conn, $office_id) {
            $sqlSetting = "SELECT s.host_whatsapp, s.token_whatsapp 
                           FROM setting s 
                           JOIN offices o ON s.admin_id = o.admin_id 
                           WHERE o.office_id = ?";
            $stmtSetting = $conn->prepare($sqlSetting);
            $stmtSetting->execute([$office_id]);
            return $stmtSetting->fetch(PDO::FETCH_ASSOC);
        }
        

        // استخدام معرف الآدمن الحالي أو المعرف واحد كبديل
        $settings = getWhatsAppSettings($conn, $OfficeId);
        if (!$settings) {
            $settings = getWhatsAppSettings($conn, 1);
        }

        if (isset($_POST['title']) && isset($_POST['lawyer_id']) && !empty($_POST['lawyer_id'])) {
            $title = $_POST['title'];
            $lawyer_id = $_POST['lawyer_id'];
            $client_id = $_POST['client_id'] ?? '';
            $helper_id = $_POST['helper_id'] ?? '';
            $priority = $_POST['priority'] ?? 'طبيعية';
            $task_title = $_POST['task_title'];
            $task_attach = '';

            // التعامل مع المرفقات
            if (isset($_FILES['task_attach']) && $_FILES['task_attach']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = '../../uploads/';
                $fileName = time() . '_' . basename($_FILES['task_attach']['name']); // إضافة توقيع زمني
                $uploadFilePath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['task_attach']['tmp_name'], $uploadFilePath)) {
                    $task_attach = $fileName;
                } else {
                    echo 'error';
                    exit();
                }
            }

            if (empty($title)) {
                echo 'error';
            } else {
                $stmt = $conn->prepare("INSERT INTO todos (title, lawyer_id, client_id, helper_id, priority, task_title, task_attach) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $res = $stmt->execute([$title, $lawyer_id, $client_id, $helper_id, $priority, $task_title, $task_attach]);

                if ($res) {
                    // إرسال رسالة للعميل إذا كان معرفًا ويستقبل رسائل واتساب
                    if (!empty($client_id)) {
                        $client_stmt = $conn->prepare("SELECT phone, first_name, receive_whatsupp FROM clients WHERE client_id = ?");
                        $client_stmt->execute([$client_id]);
                        $client = $client_stmt->fetch(PDO::FETCH_ASSOC);
                        if ($client && $client['receive_whatsupp'] == 1) {
                            $client_message = "عزيزي " . $client['first_name'] . "،\n\nلديك رسالة من المحامي الخاص بك: $title.\n\nأتمنى أن تكون بخير.\n\n";
                            sendWhatsAppMessage($client['phone'], $client_message, $settings);
                        }
                    }

                    echo 'success';
                } else {
                    echo 'error';
                }
                exit();
            }
        } else {
            echo 'error';
        }
    } else {
        header("Location: ../../login.php");
        exit();
    }
} else {
    header("Location: ../../logout.php");
    exit();
}
?>
