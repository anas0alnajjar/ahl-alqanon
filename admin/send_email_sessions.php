<?php
use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(403);
    echo "Access forbidden!";
    exit;
}

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'DB_connection.php';

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

function isSessionAddedToTodos($conn, $sessionId) {
    $checkSql = "SELECT COUNT(*) AS count FROM todos WHERE session_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$sessionId]);
    $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

function getAdminSettings($conn, $admin_id) {
    try {
        // جلب إعدادات الآدمن المعطى
        $sql = "SELECT `host_email`, `username_email`, `password_email`, `port_email` FROM `setting` WHERE `admin_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$admin_id]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        // التحقق مما إذا كانت إعدادات الآدمن فارغة
        if (empty($settings['host_email']) || empty($settings['username_email']) || empty($settings['password_email']) || empty($settings['port_email'])) {
            // echo "Admin $admin_id settings are empty, using main admin settings\n";
            // جلب إعدادات الآدمن الرئيسي
            $sql = "SELECT `host_email`, `username_email`, `password_email`, `port_email` FROM `setting` WHERE `admin_id` = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $settings;
    } catch (Exception $e) {
        error_log("Failed to fetch admin settings: " . $e->getMessage());
        // echo "Failed to fetch admin settings for admin $admin_id, using main admin settings\n";
        // في حالة حدوث خطأ، جلب إعدادات الآدمن الرئيسي
        $sql = "SELECT `host_email`, `username_email`, `password_email`, `port_email` FROM `setting` WHERE `admin_id` = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function getAdminStatus($conn, $admin_id) {
    $sql = "SELECT `stop`, `stop_date` FROM `admin` WHERE `admin_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$admin_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getOfficeStatus($conn, $office_id) {
    $sql = "SELECT `stop`, `stop_date` FROM `offices` WHERE `office_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$office_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function sendAndLogNotificationForSession($conn, $recipient, $subject, $message, $sessionId, $caseId, $settings) {
    if (!empty($recipient) && !empty($settings['host_email']) && !empty($settings['username_email']) && !empty($settings['password_email']) && !empty($settings['port_email'])) {
        if (sendEmail($recipient, $subject, $message, $settings)) {
            try {
                $insertSentSql = "INSERT INTO sent_notifications_sessions (session_id, case_id, recipient_email) VALUES (?, ?, ?)";
                $insertStmt = $conn->prepare($insertSentSql);
                $insertStmt->execute([$sessionId, $caseId, $recipient]);
                // echo "Logged notification for session $sessionId, case $caseId, recipient $recipient\n";
            } catch (PDOException $e) {
                error_log("Failed to insert sent notification: " . $e->getMessage());
                // echo "Failed to log notification for session $sessionId, case $caseId, recipient $recipient\n";
            }
        } else {
            error_log("Failed to send email to {$recipient}");
            // echo "Failed to send email to {$recipient}\n";
        }
    }
}

function executeNotificationsSessionFunction($conn) {
    $sql = "SELECT 
                c.case_id,
                c.office_id,
                s.sessions_id AS session_id,
                s.session_hour,
                s.assistant_lawyer,
                lw.lawyer_email AS lawyerEmail,
                lw.lawyer_id,
                lw.lawyer_name,
                cl.client_id,
                cl.receive_emails,
                c.case_title, 
                cl.first_name AS client_first_name, 
                cl.last_name AS client_last_name,
                cl.email AS clientEmail,
                DATEDIFF(COALESCE(MAX(s.session_date), CURDATE()), CURDATE()) AS days_remaining,
                COALESCE(MAX(s.session_date), CURDATE()) AS next_session_date,
                al.lawyer_email AS assistantLawyerEmail,
                al.lawyer_name AS assistantLawyerName
            FROM 
                sessions s
            LEFT JOIN 
                cases c ON c.case_id = s.case_id
            LEFT JOIN 
                clients cl ON c.client_id = cl.client_id
            LEFT JOIN
                lawyer lw ON lw.lawyer_id = c.lawyer_id
            LEFT JOIN
                lawyer al ON al.lawyer_id = s.assistant_lawyer
            WHERE 
                s.session_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 7 DAY
            GROUP BY 
                c.case_id, c.case_title, cl.first_name, cl.last_name, s.sessions_id  
            ORDER BY 
                `next_session_date` DESC;";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $sessions = $stmt->fetchAll();

    $sentNotifications = [];
    $sentSql = "SELECT session_id, recipient_email FROM sent_notifications_sessions WHERE recipient_email !='';";
    $sentStmt = $conn->query($sentSql);
    while ($row = $sentStmt->fetch(PDO::FETCH_ASSOC)) {
        $sentNotifications[$row['session_id']][$row['recipient_email']] = true;
    }

    foreach ($sessions as $session) {
        $office_id = $session['office_id'];  

        // جلب حالة المكتب
        $officeStatus = getOfficeStatus($conn, $office_id);
        if ($officeStatus['stop'] == 1 && (is_null($officeStatus['stop_date']) || $officeStatus['stop_date'] <= date('Y-m-d'))) {
            // echo "Skipping office $office_id as it is stopped or stop date is in the past\n";
            continue; // تخطي المكاتب المتوقفة
        }

        // جلب معلومات الآدمن المرتبط بالمكتب
        $admin_id = $conn->query("SELECT admin_id FROM offices WHERE office_id = $office_id")->fetchColumn();
        $adminStatus = getAdminStatus($conn, $admin_id);

        if ($adminStatus['stop'] == 1 && (is_null($adminStatus['stop_date']) || $adminStatus['stop_date'] <= date('Y-m-d'))) {
            // echo "Skipping admin $admin_id as they are stopped or stop date is in the past\n";
            continue; // تخطي الآدمن المتوقف
        }

        // جلب إعدادات الآدمن
        $adminSettings = getAdminSettings($conn, $admin_id);

        $defaultMessage = "عزيزي/عزيزتي,
        نود تذكيرك بأن لديك جلسة قادمة لـ القضية '{$session['case_title']}' بتاريخ {$session['next_session_date']} في الساعة {$session['session_hour']}.        
        مع أطيب التحيات،
        فريق العمل";

        // جلب رسالة الموكل
        try {
            $stmt = $conn->prepare("SELECT message_text FROM templates WHERE office_id = ? AND for_whom = 1 AND type_template = 1");
            $stmt->bindParam(1, $office_id, PDO::PARAM_INT);
            $stmt->execute();
            $messageTemplate = $stmt->fetch(PDO::FETCH_ASSOC)['message_text'] ?? $defaultMessage;
        } catch (Exception $e) {
            $messageTemplate = $defaultMessage; // استخدام الرسالة الافتراضية في حالة حدوث خطأ
        }

        $dueDate = $session['next_session_date'];
        $dueHour = $session['session_hour'];

        $alreadySentToClient = isset($sentNotifications[$session['session_id']][$session['clientEmail']]);
        $alreadySentToLawyer = isset($sentNotifications[$session['session_id']][$session['lawyerEmail']]);
        $alreadySentToAssistantLawyer = isset($sentNotifications[$session['session_id']][$session['assistantLawyerEmail']]);

        $clientNotificationTextOld = str_replace(
            ['{$client_first_name}', '{$case_title}', '{$dueDate}', '{$dueHour}', '{$client_last_name}', '{$lawyer_name}'],
            [$session['client_first_name'], $session['case_title'], $dueDate, $dueHour, $session['client_last_name'], $session['lawyer_name']],
            $messageTemplate
        );
        
        // تعريف $clientNotificationText بشكل يتضمن تنسيق HTML
        $clientNotificationText = "<div style='direction: rtl; text-align: right; font-family: Arial, sans-serif;'>\n";
        $clientNotificationText .= "    <h2 style='color: #4CAF50;'>تذكير بموعد الجلسة</h2>\n";
        $clientNotificationText .= "    $clientNotificationTextOld\n";
        $clientNotificationText .= "</div>\n";

        if (!$alreadySentToClient && $session['receive_emails'] == 1) {
            if (!empty($session['clientEmail'])) {
                sendAndLogNotificationForSession($conn, $session['clientEmail'], 'تذكير بموعد الجلسة', $clientNotificationText, $session['session_id'], $session['case_id'], $adminSettings);
            }
        }

        // جلب رسالة المحامي
        try {
            $stmt = $conn->prepare("SELECT message_text FROM templates WHERE office_id = ? AND for_whom = 2 AND type_template = 1");
            $stmt->bindParam(1, $office_id, PDO::PARAM_INT);
            $stmt->execute();
            $lawyerMessageTemplate = $stmt->fetch(PDO::FETCH_ASSOC)['message_text'] ?? $defaultMessage;
        } catch (Exception $e) {
            $lawyerMessageTemplate = $defaultMessage; // استخدام الرسالة الافتراضية في حالة حدوث خطأ
        }

        $lawyerNotificationText = str_replace(
            ['{$client_first_name}', '{$case_title}', '{$dueDate}', '{$dueHour}', '{$client_last_name}', '{$lawyer_name}'],
            [$session['client_first_name'], $session['case_title'], $dueDate, $dueHour, $session['client_last_name'], $session['lawyer_name']],
            $lawyerMessageTemplate
        );

        // تعريف $lawyerNotificationText بشكل يتضمن تنسيق HTML
        $lawyerNotificationTextHtml = "<div style='direction: rtl; text-align: right; font-family: Arial, sans-serif;'>\n";
        $lawyerNotificationTextHtml .= "    <h2 style='color: #4CAF50;'>تذكير بموعد الجلسة</h2>\n";
        $lawyerNotificationTextHtml .= "    $lawyerNotificationText\n";
        $lawyerNotificationTextHtml .= "</div>\n";

        $lawyerNotificationTextForTodo = "لديك جلسة للقضية '{$session['case_title']}' بتاريخ {$dueDate} في الساعة {$dueHour}.";

        if (!$alreadySentToLawyer) {
            if (!empty($session['lawyerEmail'])) {
                sendAndLogNotificationForSession($conn, $session['lawyerEmail'], 'تذكير بموعد الجلسة', $lawyerNotificationTextHtml, $session['session_id'], $session['case_id'], $adminSettings);
            }
        }

        if (!$alreadySentToAssistantLawyer) {
            if (!empty($session['assistantLawyerEmail'])) {
                sendAndLogNotificationForSession($conn, $session['assistantLawyerEmail'], 'تذكير بموعد الجلسة', $lawyerNotificationTextHtml, $session['session_id'], $session['case_id'], $adminSettings);
            }
        }

        if (!isSessionAddedToTodos($conn, $session['session_id'])) {
            try {
                $insertTodoSql = "INSERT INTO todos (title, lawyer_id, client_id, case_id, session_id) VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertTodoSql);
                $insertStmt->execute([$lawyerNotificationTextForTodo, $session['lawyer_id'], $session['client_id'], $session['case_id'], $session['session_id']]);
                // echo "Added todo for session {$session['session_id']}\n";
            } catch (PDOException $e) {
                error_log("Failed to insert into todos: " . $e->getMessage());
                // echo "Failed to add todo for session {$session['session_id']}\n";
            }
        }

        $emails = [];
        if (!$alreadySentToClient && $session['receive_emails'] == 1) {
            $emails[] = $session['clientEmail'];
        }
        if (!$alreadySentToLawyer) {
            $emails[] = $session['lawyerEmail'];
        }
        if (!$alreadySentToAssistantLawyer) {
            $emails[] = $session['assistantLawyerEmail'];
        }
        foreach ($emails as $email) {
            if (!empty($email)) {
                $checkNotificationSql = "SELECT 1 FROM sent_notifications_sessions WHERE session_id = ? AND recipient_email = ?";
                $checkStmt = $conn->prepare($checkNotificationSql);
                $checkStmt->execute([$session['session_id'], $email]);
                $notificationExists = $checkStmt->fetchColumn();

                if (!$notificationExists) {
                    try {
                        $insertSentSql = "INSERT INTO sent_notifications_sessions (case_id, session_id, recipient_email) VALUES (?, ?, ?)";
                        $insertStmt = $conn->prepare($insertSentSql);
                        $insertStmt->execute([$session['case_id'], $session['session_id'], $email]);
                        // echo "Logged notification for session {$session['session_id']}, case {$session['case_id']}, recipient $email\n";
                    } catch (PDOException $e) {
                        error_log("Failed to insert sent notification: " . $e->getMessage());
                        // echo "Failed to log notification for session {$session['session_id']}, case {$session['case_id']}, recipient $email\n";
                    }
                }
            }
        }
    }
}

executeNotificationsSessionFunction($conn);

?>

