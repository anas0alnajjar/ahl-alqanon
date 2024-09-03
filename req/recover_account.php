<?php
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include '../DB_connection.php';
include "../data/setting.php";
$setting = getSetting($conn);

require_once '../admin/PHPMailer/src/Exception.php';
require_once '../admin/PHPMailer/src/PHPMailer.php';
require_once '../admin/PHPMailer/src/SMTP.php';

try {
    if (isset($_POST['uname']) && isset($_POST['email']) && isset($_POST['g-recaptcha-response'])) {
        $uname = $_POST['uname'];
        $email = $_POST['email'];

        // التحقق من reCAPTCHA
        $recaptcha_secret = $setting['secret_key'];
        $recaptcha_response = $_POST['g-recaptcha-response'];
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

        $recaptcha_data = [
            'secret' => $recaptcha_secret,
            'response' => $recaptcha_response
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($recaptcha_data)
            ]
        ];

        $context = stream_context_create($options);
        $recaptcha_verify = file_get_contents($recaptcha_url, false, $context);
        $recaptcha_success = json_decode($recaptcha_verify);

        if ($recaptcha_success->success && $recaptcha_success->score >= 0.5) {
            // التحقق من وجود المستخدم عبر جميع الجداول المحتملة
            $stmt = $conn->prepare("
                SELECT username, email FROM admin WHERE username = ? AND email = ?
                UNION
                SELECT username, lawyer_email AS email FROM lawyer WHERE username = ? AND lawyer_email = ?
                UNION
                SELECT username, email FROM helpers WHERE username = ? AND email = ?
                UNION
                SELECT username, email FROM clients WHERE username = ? AND email = ?
                UNION
                SELECT username, manager_email AS email FROM managers_office WHERE username = ? AND manager_email = ?
            ");
            $stmt->execute([$uname, $email, $uname, $email, $uname, $email, $uname, $email, $uname, $email]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                $token = bin2hex(random_bytes(50)); // إنشاء رمز مميز
                $expires = date('U') + 1800; // صلاحية الرمز 30 دقيقة

                // حفظ الرمز في قاعدة البيانات
                $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$email, $token, $expires]);

                $reset_link = "https://app.ahl-alqanon.com/reset_password.php?token=" . $token;

                $subject = "رابط استرداد الحساب";
                $message = "
                <html lang='ar'>
                <head>
                    <meta charset='UTF-8'>
                    <title>$subject</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0;
                            background-color: #f4f4f4;
                            direction: rtl;
                            text-align: right;
                        }
                        .container {
                            width: 100%;
                            padding: 20px;
                            background-color: #ffffff;
                            border-radius: 10px;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            margin: 50px auto;
                            max-width: 600px;
                        }
                        .header {
                            text-align: center;
                            padding-bottom: 20px;
                            border-bottom: 1px solid #dddddd;
                        }
                        .content {
                            margin-top: 20px;
                        }
                        .content p {
                            font-size: 16px;
                            color: #333333;
                            line-height: 1.6;
                        }
                        .reset-link {
                            display: block;
                            width: fit-content;
                            margin: 20px auto;
                            padding: 10px 20px;
                            background-color: #4CAF50;
                            color: #ffffff;
                            text-decoration: none;
                            border-radius: 5px;
                            text-align: center;
                        }
                        .footer {
                            text-align: center;
                            padding-top: 20px;
                            border-top: 1px solid #dddddd;
                            margin-top: 20px;
                            color: #777777;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>{$setting['company_name']}</h2>
                        </div>
                        <div class='content'>
                            <p>مرحباً،</p>
                            <p>لتغيير كلمة المرور الخاصة بك، يرجى النقر على الرابط أدناه:</p>
                            <a class='reset-link' href='$reset_link'>استرداد الحساب</a>
                        </div>
                    </div>
                </body>
                </html>
                ";

                if (sendEmail($email, $subject, $message, $setting)) {
                    $response = ['status' => 'success', 'message' => 'تم إرسال رابط استرداد الحساب إلى بريدك الإلكتروني.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'فشل في إرسال البريد الإلكتروني. حاول مرة أخرى'];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'اسم المستخدم أو البريد الإلكتروني غير صحيح'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'فشل في التحقق من reCAPTCHA. حاول مرة أخرى'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'جميع الحقول مطلوبة'];
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()]);
}

function sendEmail($recipient, $subject, $message, $settings)
{
    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $settings['host_email'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['username_email'];
        $mail->Password = $settings['password_email'];
        $mail->SMTPSecure = 'ssl';
        $mail->Port = $settings['port_email'];

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
?>
