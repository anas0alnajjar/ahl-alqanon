<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include '../DB_connection.php';
include "../data/setting.php";
$setting = getSetting($conn);

require_once '../admin/PHPMailer/src/Exception.php';
require_once '../admin/PHPMailer/src/PHPMailer.php';
require_once '../admin/PHPMailer/src/SMTP.php';

if (
    isset($_POST['fname']) &&
    isset($_POST['lname']) &&
    isset($_POST['username']) &&
    isset($_POST['pass']) &&
    isset($_POST['address']) &&
    isset($_POST['gender']) &&
    isset($_POST['email_address']) &&
    isset($_POST['date_of_birth']) &&
    isset($_POST['city']) &&
    isset($_POST['phone']) &&
    isset($_POST['as_a']) &&
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
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $uname = $_POST['username'];
        $pass = $_POST['pass'];
        $address = $_POST['address'];
        $gender = $_POST['gender'];
        $email_address = $_POST['email_address'];
        $date_of_birth = $_POST['date_of_birth'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $as_a = $_POST['as_a'];

        function usernameIsUnique($uname, $conn) {
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
            empty($fname) ||
            empty($lname) ||
            empty($uname) ||
            empty($pass) ||
            empty($address) ||
            empty($gender) ||
            empty($email_address) ||
            empty($date_of_birth) ||
            empty($city) ||
            empty($phone)
        ) {
            $response = ['error' => 'جميع الحقول مطلوبة'];
            echo json_encode($response);
            exit;
        } elseif (!usernameIsUnique($uname, $conn)) {
            $response = ['error' => 'اسم المستخدم مأخوذ، اختر واحدًا آخر'];
            echo json_encode($response);
            exit;
        } else {
            if ($setting['allow_check'] == 1) {
                // إرسال بريد إلكتروني للتحقق
                $verification_code = rand(100000, 999999);
                $_SESSION['verification_code'] = $verification_code; // حفظ رمز التحقق في الجلسة
                $_SESSION['user_data'] = [
                    'fname' => $fname,
                    'lname' => $lname,
                    'username' => $uname,
                    'password' => password_hash($pass, PASSWORD_DEFAULT),
                    'address' => $address,
                    'gender' => $gender,
                    'email_address' => $email_address,
                    'date_of_birth' => $date_of_birth,
                    'city' => $city,
                    'phone' => $phone,
                    'as_a' => $as_a
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
                            <p style='direction:rtl;'>مرحباً $fname $lname,</p>
                            <p style='direction:rtl;'>نرحب بك في $company_name .</p>
                            <p style='direction:rtl;'>رمز التحقق الخاص بك هو:</p>
                            <p class='verification-code'>$verification_code</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                if (!sendEmail($email_address, $subject, $message, $setting)) {
                    $response = ['error' => 'فشل في إرسال البريد الإلكتروني. حاول مرة أخرى'];
                    echo json_encode($response);
                    exit;
                }

                $response = ['success' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.', 'email_sent' => true];
                echo json_encode($response);
                exit;
            } else {
                // التسجيل المباشر بدون التحقق
                // التحقق من وجود البريد الإلكتروني
                $email_check_sql = $as_a == 1 ? "SELECT email FROM clients WHERE email = ?" : "SELECT lawyer_email FROM lawyer WHERE lawyer_email = ?";
                $stmt_email_check = $conn->prepare($email_check_sql);
                $stmt_email_check->execute([$email_address]);

                if ($stmt_email_check->rowCount() > 0) {
                    $response = ['error' => 'البريد الإلكتروني مستخدم بالفعل'];
                    echo json_encode($response);
                    exit;
                }

                $sql_requests = "SELECT automatic_acceptance, days FROM requests WHERE id = 1 LIMIT 1";
                $stmt_requests = $conn->prepare($sql_requests);
                $stmt_requests->execute();
                $request_settings = $stmt_requests->fetch(PDO::FETCH_ASSOC);

                $auto_accept = $request_settings['automatic_acceptance'];
                $stop_date = date('Y-m-d', strtotime("+{$request_settings['days']} days"));

                if ($as_a == 1) {
                    $default_role_sql = "SELECT power_id FROM powers WHERE default_role_client = 1 LIMIT 1";
                    $default_office_sql = "SELECT office_id FROM offices WHERE default_office = 1 LIMIT 1";
                    $stmt_default_role = $conn->prepare($default_role_sql);
                    $stmt_default_office = $conn->prepare($default_office_sql);
                    $stmt_default_role->execute();
                    $stmt_default_office->execute();

                    $default_role = $stmt_default_role->fetch(PDO::FETCH_ASSOC);
                    $default_office = $stmt_default_office->fetch(PDO::FETCH_ASSOC);

                    $sql_insert_client = "INSERT INTO clients (first_name, last_name, email, phone, username, `password`, `address`, gender, date_of_birth, city, stop, stop_date, role_id, office_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)";
                    $stmt_insert_client = $conn->prepare($sql_insert_client);
                    $stmt_insert_client->execute([$fname, $lname, $email_address, $phone, $uname, password_hash($pass, PASSWORD_DEFAULT), $address, $gender, $date_of_birth, $city, $stop_date, $default_role['power_id'] ?? NULL, $default_office['office_id'] ?? NULL]);
                    
                    $_SESSION['user_id'] = $conn->lastInsertId();
                    $_SESSION['role'] = 'Client';
                    $user_type = 'client';
                } elseif ($as_a == 2) {
                    $default_role_sql = "SELECT power_id FROM powers WHERE default_role_lawyer = 1 LIMIT 1";
                    $default_office_sql = "SELECT office_id FROM offices WHERE default_office = 1 LIMIT 1";
                    $stmt_default_role = $conn->prepare($default_role_sql);
                    $stmt_default_office = $conn->prepare($default_office_sql);
                    $stmt_default_role->execute();
                    $stmt_default_office->execute();

                    $default_role = $stmt_default_role->fetch(PDO::FETCH_ASSOC);
                    $default_office = $stmt_default_office->fetch(PDO::FETCH_ASSOC);

                    $sql_insert_lawyer = "INSERT INTO lawyer (lawyer_name, date_of_birth, lawyer_email, lawyer_phone, username, lawyer_password, lawyer_address, lawyer_gender, lawyer_city, stop, stop_date, role_id, office_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)";
                    $stmt_insert_lawyer = $conn->prepare($sql_insert_lawyer);
                    $stmt_insert_lawyer->execute(["$fname $lname", $date_of_birth, $email_address, $phone, $uname, password_hash($pass, PASSWORD_DEFAULT), $address, $gender, $city, $stop_date, $default_role['power_id'] ?? NULL, $default_office['office_id'] ?? NULL]);

                    $_SESSION['user_id'] = $conn->lastInsertId();
                    $_SESSION['role'] = 'Lawyer';
                    $user_type = 'lawyer';
                }

                $response = ['success' => 'تم التسجيل بنجاح. تستطيع تسجيل الدخول والمباشرة في العمل.', 'auto_accept' => true, 'user_type' => $user_type];
                echo json_encode($response);
                exit;
            }
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
