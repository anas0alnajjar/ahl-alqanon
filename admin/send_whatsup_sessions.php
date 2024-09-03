<?php

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(403);
    echo "Access forbidden!";
    exit;
}

class WhatsAppNotification
{
    private $conn;
    private $settings;

    public function __construct($conn, $settings)
    {
        $this->conn = $conn;
        $this->settings = $settings;
    }

    private function sendWhatsAppMessage($recipient, $message, $settings)
    {
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

    private function logNotification($sessionId, $caseId, $recipient)
    {
        try {
            $insertSentSql = "INSERT INTO sent_notifications_sessions (session_id, case_id, recipient_phone) VALUES (?, ?, ?)";
            $insertStmt = $this->conn->prepare($insertSentSql);
            $insertStmt->execute([$sessionId, $caseId, $recipient]);
            // echo "Logged notification for session $sessionId, case $caseId, recipient $recipient\n";
        } catch (PDOException $e) {
            error_log("Failed to insert sent notification: " . $e->getMessage());
            // echo "Failed to log notification for session $sessionId, case $caseId, recipient $recipient\n";
        }
    }

    private function sendAndLogNotification($recipient, $message, $sessionId, $caseId, $settings, $admin_id)
    {
        // echo "Sending WhatsApp message to $recipient for session $sessionId, case $caseId using admin $admin_id\n";
        if ($this->sendWhatsAppMessage($recipient, $message, $settings)) {
            // echo "Successfully sent WhatsApp message to $recipient using admin $admin_id\n";
            $this->logNotification($sessionId, $caseId, $recipient);
        } else {
            error_log("Failed to send WhatsApp message to $recipient using admin $admin_id");
            // echo "Failed to send WhatsApp message to $recipient using admin $admin_id\n";
        }
    }

    private function fetchSessions()
    {
        $sql = "SELECT 
                c.case_id,
                c.office_id,
                s.sessions_id AS session_id,
                s.session_hour,
                lw.lawyer_email AS lawyerEmail,
                lw.lawyer_id,
                lw.lawyer_phone AS lawyerPhone,
                lw.lawyer_name,
                cl.client_id,
                cl.receive_whatsupp,
                cl.phone AS clientPhone,
                c.case_title, 
                cl.first_name AS client_first_name, 
                cl.last_name AS client_last_name,
                cl.email AS clientEmail,
                DATEDIFF(COALESCE(MAX(s.session_date), CURDATE()), CURDATE()) AS days_remaining,
                COALESCE(MAX(s.session_date), CURDATE()) AS next_session_date,
                al.lawyer_phone AS assistantLawyerPhone,
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
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function fetchSentNotifications()
    {
        $sentNotifications = [];
        $sentSql = "SELECT session_id, recipient_phone FROM sent_notifications_sessions WHERE recipient_phone IS NOT NULL";
        $sentStmt = $this->conn->query($sentSql);
        while ($row = $sentStmt->fetch(PDO::FETCH_ASSOC)) {
            $sentNotifications[$row['session_id']][$row['recipient_phone']] = true;
        }
        return $sentNotifications;
    }

    private function getOfficeStatus($office_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT stop, stop_date FROM offices WHERE office_id = ?");
            $stmt->bindParam(1, $office_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch office status: " . $e->getMessage());
            return false;
        }
    }

    private function getMessageTemplate($office_id, $for_whom)
    {
        try {
            $stmt = $this->conn->prepare("SELECT message_text FROM templates WHERE office_id = ? AND for_whom = ? AND type_template = 2");
            $stmt->bindParam(1, $office_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $for_whom, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['message_text'] ?? '';
        } catch (Exception $e) {
            error_log("Failed to fetch message template: " . $e->getMessage());
            return ''; // استخدام الرسالة الافتراضية في حالة حدوث خطأ
        }
    }

    private function prepareNotificationText($template, $session)
    {
        return str_replace(
            ['{$client_first_name}', '{$case_title}', '{$dueDate}', '{$dueHour}', '{$client_last_name}', '{$lawyer_name}'],
            [$session['client_first_name'], $session['case_title'], $session['next_session_date'], $session['session_hour'], $session['client_last_name'], $session['lawyer_name']],
            $template
        );
    }

    private function getAdminSettingsWhatsapp($admin_id)
    {
        try {
            // جلب إعدادات الآدمن المعطى
            $sql = "SELECT `host_whatsapp`, `token_whatsapp` FROM `setting` WHERE `admin_id` = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$admin_id]);
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);

            // إذا كانت الإعدادات فارغة، جلب إعدادات الآدمن الرئيسي
            if (!$settings['host_whatsapp'] || !$settings['token_whatsapp']) {
                // echo "Admin $admin_id settings are empty, using main admin settings\n";
                $sql = "SELECT `host_whatsapp`, `token_whatsapp` FROM `setting` WHERE `admin_id` = 1";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return $settings;
        } catch (Exception $e) {
            error_log("Failed to fetch admin settings: " . $e->getMessage());
            // echo "Failed to fetch admin settings for admin $admin_id, using main admin settings\n";
            // في حالة حدوث خطأ، جلب إعدادات الآدمن الرئيسي
            $sql = "SELECT `host_whatsapp`, `token_whatsapp` FROM `setting` WHERE `admin_id` = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    private function getAdminStatus($admin_id)
    {
        try {
            $sql = "SELECT `stop`, `stop_date` FROM `admin` WHERE `admin_id` = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$admin_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch admin status: " . $e->getMessage());
            return false;
        }
    }

    public function executeNotifications()
    {
        $sessions = $this->fetchSessions();
        $sentNotifications = $this->fetchSentNotifications();

        foreach ($sessions as $session) {
            $office_id = $session['office_id'];
            $officeStatus = $this->getOfficeStatus($office_id);

            if ($officeStatus === false || ($officeStatus['stop'] == 1 && (is_null($officeStatus['stop_date']) || $officeStatus['stop_date'] <= date('Y-m-d')))) {
                // echo "Skipping office $office_id as it is stopped or stop date is in the past\n";
                continue; // تخطي المكاتب المتوقفة
            }

            $admin_id = $this->conn->query("SELECT admin_id FROM offices WHERE office_id = $office_id")->fetchColumn();
            $adminStatus = $this->getAdminStatus($admin_id);

            if ($adminStatus === false || ($adminStatus['stop'] == 1 && (is_null($adminStatus['stop_date']) || $adminStatus['stop_date'] <= date('Y-m-d')))) {
                // echo "Skipping admin $admin_id as they are stopped or stop date is in the past\n";
                continue; // تخطي الآدمن المتوقف
            }

            $adminSettings = $this->getAdminSettingsWhatsapp($admin_id);

            $defaultMessage = "عزيزي/عزيزتي,
    
            نود تذكيرك بأن لديك جلسة قادمة لـ القضية '{$session['case_title']}' بتاريخ {$session['next_session_date']} في الساعة {$session['session_hour']}.
            
            مع أطيب التحيات،
            فريق العمل";

            $clientMessageTemplate = $this->getMessageTemplate($office_id, 1) ?: $defaultMessage;
            $lawyerMessageTemplate = $this->getMessageTemplate($office_id, 2) ?: $defaultMessage;

            $clientNotificationText = $this->prepareNotificationText($clientMessageTemplate, $session);
            $lawyerNotificationText = $this->prepareNotificationText($lawyerMessageTemplate, $session);

            $alreadySentToClient = isset($sentNotifications[$session['session_id']][$session['clientPhone']]);
            $alreadySentToLawyer = isset($sentNotifications[$session['session_id']][$session['lawyerPhone']]);
            $alreadySentToAssistantLawyer = isset($sentNotifications[$session['session_id']][$session['assistantLawyerPhone']]);

            if (!$alreadySentToClient && $session['receive_whatsupp'] == 1 && !empty($session['clientPhone'])) {
                $this->sendAndLogNotification($session['clientPhone'], $clientNotificationText, $session['session_id'], $session['case_id'], $adminSettings, $admin_id);
            }

            if (!$alreadySentToLawyer && !empty($session['lawyerPhone'])) {
                $this->sendAndLogNotification($session['lawyerPhone'], $lawyerNotificationText, $session['session_id'], $session['case_id'], $adminSettings, $admin_id);
            }

            if (!$alreadySentToAssistantLawyer && !empty($session['assistantLawyerPhone'])) {
                $this->sendAndLogNotification($session['assistantLawyerPhone'], $lawyerNotificationText, $session['session_id'], $session['case_id'], $adminSettings, $admin_id);
            }
        }
    }
}

require_once 'DB_connection.php';
require_once "req/whatsupp_setting.php"; // يحتوي على تعريف الدالة sendWhatsAppMessage

$notification = new WhatsAppNotification($conn, $settings);
$notification->executeNotifications();

?>
