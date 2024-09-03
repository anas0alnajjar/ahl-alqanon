<?php
use PHPMailer\PHPMailer\PHPMailer;

session_start();
include '../DB_connection.php';
include "../data/setting.php";
$setting = getSetting($conn);

require_once '../admin/PHPMailer/src/Exception.php';
require_once '../admin/PHPMailer/src/PHPMailer.php';
require_once '../admin/PHPMailer/src/SMTP.php';

if (
    isset($_POST['manager_name']) &&
    isset($_POST['manager_address']) &&
    isset($_POST['manager_email']) &&
    isset($_POST['manager_gender']) &&
    isset($_POST['username']) &&
    isset($_POST['manager_password']) &&
    isset($_POST['manager_city']) &&
    isset($_POST['manager_phone']) &&
    isset($_POST['office_name']) &&
    isset($_POST['g-recaptcha-response'])
) {
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
        $manager_name = $_POST['manager_name'];
        $manager_address = $_POST['manager_address'];
        $mangaer_email = $_POST['manager_email'];
        $manager_gender = $_POST['manager_gender'];
        $username = $_POST['username'];
        $manager_password = $_POST['manager_password'];
        $manager_city = $_POST['manager_city'];
        $manager_phone = $_POST['manager_phone'];
        $office_name = $_POST['office_name'];

        function emailIsUnique($email, $conn)
        {
            $sql = "SELECT manager_email FROM managers_office WHERE manager_email=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->rowCount() == 0;
        }
        function usernameIsUnique($uname, $conn)
        {
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

            return $stmt->rowCount() == 0;
        }

        if (
            empty($manager_name) ||
            empty($manager_address) ||
            empty($mangaer_email) ||
            empty($manager_gender) ||
            empty($username) ||
            empty($manager_password) ||
            empty($manager_city) ||
            empty($manager_phone) ||
            empty($office_name)
        ) {
            $response = ['error' => 'جميع الحقول مطلوبة'];
            echo json_encode($response);
            exit;
        } elseif (!filter_var($mangaer_email, FILTER_VALIDATE_EMAIL)) {
            $response = ['error' => 'بريد إلكتروني غير صالح'];
            echo json_encode($response);
            exit;
        } elseif (!emailIsUnique($mangaer_email, $conn)) {
            $response = ['error' => 'البريد الإلكتروني مستخدم بالفعل'];
            echo json_encode($response);
            exit;
        } elseif (!usernameIsUnique($username, $conn)) {
            $response = ['error' => 'اسم المستخدم مأخوذ، اختر واحدًا آخر'];
            echo json_encode($response);
            exit;
        } else {
            // إرسال بريد إلكتروني للتحقق
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_code'] = $verification_code; // حفظ رمز التحقق في الجلسة
            $_SESSION['manager_data'] = [
                'manager_name' => $manager_name,
                'manager_address' => $manager_address,
                'manager_email' => $mangaer_email,
                'manager_gender' => $manager_gender,
                'username' => $username,
                'manager_password' => $manager_password,
                'manager_city' => $manager_city,
                'manager_phone' => $manager_phone,
                'office_name' => $office_name
            ];

            $subject = "رمز التحقق الخاص بك";
            $company_name = $setting['company_name'];
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
                    .verification-code {
                        font-size: 20px;
                        font-weight: bold;
                        color: #4CAF50;
                        margin: 20px 0;
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
                        <h2>$company_name</h2>
                    </div>
                    <div class='content'>
                        <p style='direction:rtl;'>مرحباً $manager_name ,</p>
                        <p style='direction:rtl;'>نرحب بك في $company_name .</p>
                        <p style='direction:rtl;'>رمز التحقق الخاص بك هو:</p>
                        <p class='verification-code'>$verification_code</p>
                    </div>
                    <div class='footer'>
                        <p>شكراً لاستخدامك خدمتنا.</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            if (!sendEmail($mangaer_email, $subject, $message, $setting)) {
                $response = ['error' => 'فشل في إرسال البريد الإلكتروني. حاول مرة أخرى'];
                echo json_encode($response);
                exit;
            }

            $response = ['success' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.'];
            echo json_encode($response);
            exit;
        }
    } else {
        $response = ['error' => 'فشل في التحقق من reCAPTCHA. حاول مرة أخرى'];
        echo json_encode($response);
        exit;
    }
} else {
    $response = ['error' => 'حدث خطأ'];
    echo json_encode($response);
    exit;
}

function sendEmail($recipient, $subject, $message, $settings)
{
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
?>