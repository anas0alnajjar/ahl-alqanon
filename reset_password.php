<?php
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include 'DB_connection.php';
include "data/setting.php";
$setting = getSetting($conn);

// تحقق من وجود الرمز المميز في الرابط
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("الرابط غير صالح أو انتهت صلاحيته.");
}

$token = $_GET['token'];

// التحقق من الرمز المميز في قاعدة البيانات
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires >= ?");
$stmt->execute([$token, date('U')]);
$resetData = $stmt->fetch();

if (!$resetData) {
    die("الرابط غير صالح أو انتهت صلاحيته.");
}

// إذا تم إرسال النموذج لتغيير كلمة المرور
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // التحقق من مطابقة كلمتي المرور
    if ($new_password !== $confirm_password) {
        echo "<div class='alert alert-danger'>كلمتا المرور غير متطابقتين.</div>";
    } else {
        // تحديث كلمة المرور في قاعدة البيانات المناسبة
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // التحقق من البريد الإلكتروني لأي جدول ينتمي
        $email = $resetData['email'];

        // تحديث كلمة المرور في الجداول المتاحة
        $tables = [
            'admin' => ['password', 'email'],
            'lawyer' => ['lawyer_password', 'lawyer_email'],
            'helpers' => ['password', 'email'],
            'clients' => ['password', 'email'],
            'managers_office' => ['password', 'email'],
            'ask_join' => ['password', 'email']
        ];

        $updated = false;
        foreach ($tables as $table => $columns) {
            $stmt = $conn->prepare("UPDATE $table SET {$columns[0]} = ? WHERE {$columns[1]} = ?");
            if ($stmt->execute([$hashed_password, $email])) {
                if ($stmt->rowCount() > 0) {
                    $updated = true;
                    break;
                }
            }
        }

        if ($updated) {
            // حذف الرمز المميز بعد الاستخدام
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);

            // تسجيل الدخول تلقائياً بعد إعادة تعيين كلمة المرور
            $_SESSION['user_email'] = $email; // استخدام البريد الإلكتروني كمرجع للجلسة
            echo "<script>
                    alert('تم تغيير كلمة المرور بنجاح.');
                    window.location.href = 'login.php';
                  </script>";
            exit;
        } else {
            echo "<div class='alert alert-danger'>حدث خطأ أثناء تحديث كلمة المرور. حاول مرة أخرى.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة المرور - أهل القانون</title>
    <link rel="stylesheet" href="css/bootstrap5-2.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/yshstyle.css">
    <style>
        body {
            direction: rtl;
            font-family: 'Cairo', sans-serif;
            background-color: #cfccc0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .reset-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        .reset-container h2 {
            margin-bottom: 20px;
            color: #272c3f;
            font-weight: bold;
        }

        .reset-container .form-control {
            border-radius: 50px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }

        .reset-container .btn-primary {
            border-radius: 50px;
            background: #272c3f;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .reset-container .btn-primary:hover {
            background: #cfccc0;
            color: #272c3f;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>تغيير كلمة المرور</h2>
        <form method="POST" action="">
            <input type="password" name="new_password" class="form-control" placeholder="كلمة المرور الجديدة" required>
            <input type="password" name="confirm_password" class="form-control" placeholder="تأكيد كلمة المرور" required>
            <button type="submit" class="btn btn-primary w-100">تحديث كلمة المرور</button>
        </form>
    </div>
</body>
</html>
