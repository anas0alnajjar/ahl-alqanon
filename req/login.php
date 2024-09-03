<?php
session_start();
include "../data/setting.php";


// توليد توكن CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit;
    }

    if (isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['g-recaptcha-response'])) {
        include "../DB_connection.php";
        
        $setting = getSetting($conn);

        // تحقق من reCAPTCHA
        
        $recaptcha_secret = $setting['secret_key'];
        $recaptcha_response = $_POST['g-recaptcha-response'];
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

        $recaptcha_data = [
            'secret' => $recaptcha_secret,
            'response' => $recaptcha_response
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($recaptcha_data)
            ]
        ];

        $context  = stream_context_create($options);
        $recaptcha_verify = file_get_contents($recaptcha_url, false, $context);
        $recaptcha_success = json_decode($recaptcha_verify);

        if ($recaptcha_success->success && $recaptcha_success->score >= 0.5) {
            // إعداد المحاولات الفاشلة وفترة الحظر
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }
            if (!isset($_SESSION['lockout_time'])) {
                $_SESSION['lockout_time'] = null;
            }

            $uname = trim($_POST['uname']);
            $pass = trim($_POST['pass']);

            // التحقق من فترة الحظر
            if ($_SESSION['login_attempts'] >= 3) {
                if (time() - $_SESSION['lockout_time'] < 300) {
                    echo json_encode(['status' => 'error', 'message' => 'تم إيقاف الحساب لمدة 5 دقائق. حاول لاحقاً.']);
                    exit;
                } else {
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['lockout_time'] = null;
                }
            }

            if (empty($uname)) {
                echo json_encode(['status' => 'error', 'message' => 'اسم المستخدم مطلوب']);
                exit;
            } else if (empty($pass)) {
                echo json_encode(['status' => 'error', 'message' => 'كلمة السر مطلوبة']);
                exit;
            } else {
                // التحقق من المستخدم في جميع الجداول الممكنة
                $sql_admin = "SELECT admin_id AS id, username, password, 'Admin' AS role, stop, stop_date FROM admin WHERE username = ? AND admin_id = 1";
                $sql_admins = "SELECT admin_id AS id, username, password, 'Admins' AS role, stop, stop_date FROM admin WHERE username = ? AND admin_id != 1";
                $sql_client = "SELECT client_id AS id, username, password, 'Client' AS role, stop, stop_date, office_id FROM clients WHERE username = ?";
                $sql_lawyer = "SELECT lawyer_id AS id, username, lawyer_password AS password, 'Lawyer' AS role, stop, stop_date, office_id FROM lawyer WHERE username = ?";
                $sql_helpers = "SELECT id, username, pass AS password, 'Helper' AS role, stop, stop_date, office_id FROM helpers WHERE username = ?";
                $sql_managers = "SELECT id, username, manager_password AS password, 'Managers' AS role, stop, stop_date, office_id FROM managers_office WHERE username = ?";

                $queries = [$sql_admin, $sql_admins, $sql_managers, $sql_client, $sql_lawyer, $sql_helpers];
                $user = null;

                foreach ($queries as $sql) {
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$uname]);
                    if ($stmt->rowCount() == 1) {
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        break;
                    }
                }

                if ($user) {
                    // تحقق من حالة الحساب
                    if ($user['stop'] == 1 && empty($user['stop_date'])) {
                        echo json_encode(['status' => 'error', 'message' => 'الحساب متوقف.']);
                        exit;
                    }

                    if (!empty($user['stop_date']) && strtotime($user['stop_date']) <= time()) {
                        echo json_encode(['status' => 'error', 'message' => 'الحساب متوقف.']);
                        exit;
                    }

                    // تحقق من حالة المكتب المرتبط، باستثناء الآدمن بمعرف 1
                    if ($user['role'] != 'Admin' || $user['id'] != 1) {
                        if (isset($user['office_id'])) {
                            $stmt = $conn->prepare("SELECT `stop`, stop_date, admin_id FROM offices WHERE office_id = ?");
                            $stmt->execute([$user['office_id']]);
                            $office = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($office['stop'] == 1 && empty($office['stop_date'])) {
                                echo json_encode(['status' => 'error', 'message' => 'المكتب المرتبط بالحساب متوقف.']);
                                exit;
                            }

                            if (!empty($office['stop_date']) && strtotime($office['stop_date']) <= time()) {
                                echo json_encode(['status' => 'error', 'message' => 'المكتب المرتبط بالحساب متوقف.']);
                                exit;
                            }

                            // تحقق من حالة الآدمن المرتبط بالمكتب
                            $stmt = $conn->prepare("SELECT stop, stop_date FROM `admin` WHERE admin_id = ?");
                            $stmt->execute([$office['admin_id']]);
                            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($admin['stop'] == 1 && empty($admin['stop_date'])) {
                                echo json_encode(['status' => 'error', 'message' => 'الآدمن المرتبط بالمكتب متوقف.']);
                                exit;
                            }

                            if (!empty($admin['stop_date']) && strtotime($admin['stop_date']) <= time()) {
                                echo json_encode(['status' => 'error', 'message' => 'الآدمن المرتبط بالمكتب متوقف.']);
                                exit;
                            }
                        } elseif ($user['role'] == 'Admins') {
                            $stmt = $conn->prepare("SELECT * FROM offices WHERE admin_id = ?");
                            $stmt->execute([$user['id']]);
                            if ($stmt->rowCount() == 0) {
                                echo json_encode(['status' => 'error', 'message' => 'يبدو أنك لا تنتمي لأي مكتب.']);
                                exit;
                            }
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'يبدو أنك لا تنتمي لأي مكتب.']);
                            exit;
                        }
                    }

                    if (password_verify($pass, $user['password'])) {
                        session_regenerate_id(true); // منع جلسة ثابتة
                        $_SESSION['role'] = $user['role'];
                        $id = $user['id'];
                        $_SESSION['login_attempts'] = 0; // إعادة تعيين المحاولات عند تسجيل الدخول الناجح
                        switch ($user['role']) {
                            case 'Admin':
                                $_SESSION['admin_id'] = $id;
                                echo json_encode(['status' => 'success', 'redirect' => '../admin/index.php']);
                                break;
                            case 'Admins':
                                $_SESSION['user_id'] = $id;
                                echo json_encode(['status' => 'success', 'redirect' => '../admins/index.php']);
                                break;
                            case 'Managers':
                                $_SESSION['user_id'] = $id;
                                echo json_encode(['status' => 'success', 'redirect' => '../managers/index.php']);
                                break;
                            case 'Client':
                                $_SESSION['user_id'] = $id;
                                echo json_encode(['status' => 'success', 'redirect' => '../Client/index.php']);
                                break;
                            case 'Lawyer':
                                $_SESSION['user_id'] = $id;
                                echo json_encode(['status' => 'success', 'redirect' => '../Lawyer/index.php']);
                                break;
                            case 'Helper':
                                $_SESSION['user_id'] = $id;
                                echo json_encode(['status' => 'success', 'redirect' => '../Helper/index.php']);
                                break;
                            default:
                                echo json_encode(['status' => 'error', 'message' => 'خطأ في اسم المستخدم أو كلمة المرور']);
                        }
                        exit;
                    } else {
                        $_SESSION['login_attempts'] += 1;
                        if ($_SESSION['login_attempts'] >= 3) {
                            $_SESSION['lockout_time'] = time();
                        }
                        echo json_encode(['status' => 'error', 'message' => 'خطأ في اسم المستخدم أو كلمة المرور']);
                        exit;
                    }
                } else {
                    $_SESSION['login_attempts'] += 1;
                    if ($_SESSION['login_attempts'] >= 3) {
                        $_SESSION['lockout_time'] = time();
                    }
                    echo json_encode(['status' => 'error', 'message' => 'خطأ في اسم المستخدم أو كلمة المرور']);
                    exit;
                }
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'فشل التحقق من reCAPTCHA. يرجى المحاولة مرة أخرى.']);
        }
    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
