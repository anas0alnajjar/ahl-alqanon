<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    require_once 'PHPMailer/src/Exception.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    include "../DB_connection.php";

    // جلب إعدادات البريد الإلكتروني من قاعدة البيانات
    $sqlSetting = "SELECT `host_email`, `username_email`, `password_email`, `port_email` FROM `setting` WHERE admin_id = 1";
    $stmtSetting = $conn->prepare($sqlSetting);
    $stmtSetting->execute();
    $settings = $stmtSetting->fetch(PDO::FETCH_ASSOC);

    // Function to send email (using PHPMailer)
    if (!function_exists('sendEmail')) {
        function sendEmail($recipient, $subject, $message, $settings) {
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                return false; // Return false indicating email is invalid
            }

            $mail = new PHPMailer(true);

            try {
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host = $settings['host_email'];
                $mail->SMTPAuth = true;
                $mail->Username = $settings['username_email'];
                $mail->Password = $settings['password_email'];
                $mail->SMTPSecure = 'ssl';
                $mail->Port = $settings['port_email'];

                // Email Content
                $mail->setFrom($settings['username_email']);
                $mail->addAddress($recipient);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = $subject;
                $mail->Body = $message;

                $mail->send();
                return true; 
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                return false; 
            }
        }
    }

    if (isset($_POST['message_id']) && isset($_POST['reply_message'])) {
        $message_id = $_POST['message_id'];
        $reply_message = $_POST['reply_message'];

        // جلب البريد الإلكتروني للمرسل من قاعدة البيانات
        $sqlMessage = "SELECT sender_email FROM `message` WHERE message_id=?";
        $stmtMessage = $conn->prepare($sqlMessage);
        $stmtMessage->execute([$message_id]);
        $message = $stmtMessage->fetch(PDO::FETCH_ASSOC);

        if ($message) {
            $recipient = $message['sender_email'];
            $subject = "رد على رسالتك";
            $sendStatus = sendEmail($recipient, $subject, $reply_message, $settings);

            if ($sendStatus) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
        } else {
            echo json_encode(['status' => 'error']);
        }
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'unauthorized']);
}
?>
