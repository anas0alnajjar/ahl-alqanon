<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include '../../DB_connection.php';

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
                    require_once "whatsupp_setting.php";

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

                    // إرسال رسالة للمساعد إذا كان معرفًا
                    if (!empty($helper_id)) {
                        $helper_stmt = $conn->prepare("SELECT phone, helper_name FROM helpers WHERE id = ?");
                        $helper_stmt->execute([$helper_id]);
                        $helper = $helper_stmt->fetch(PDO::FETCH_ASSOC);
                        if ($helper) {
                            $helper_message = "عزيزي " . $helper['helper_name'] . "،\n\nتم تكليفك بمهمة جديدة : $title.\n\n على أن تنفذ $priority\n\nأتمنى أن تكون بخير.";
                            sendWhatsAppMessage($helper['phone'], $helper_message, $settings);
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
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
