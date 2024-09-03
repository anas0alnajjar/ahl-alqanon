<?php


if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(403);
    echo "Access forbidden!";
    exit;
}

require_once '/home/u789524392/domains/support.anas0alnajjar.com/public_html/DB_connection.php';
require_once "req/whatsupp_setting.php";

function getAdminSettingsWhatsapp($conn, $admin_id) {
    try {
        $sql = "SELECT `host_whatsapp`, `token_whatsapp` FROM `setting` WHERE `admin_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$admin_id]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($settings['host_whatsapp']) || empty($settings['token_whatsapp'])) {
            // echo "Admin $admin_id settings are empty, using main admin settings\n";
            $sql = "SELECT `host_whatsapp`, `token_whatsapp` FROM `setting` WHERE `admin_id` = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $settings;
    } catch (Exception $e) {
        error_log("Failed to fetch admin settings: " . $e->getMessage());
        // echo "Failed to fetch admin settings for admin $admin_id, using main admin settings\n";
        $sql = "SELECT `host_whatsapp`, `token_whatsapp` FROM `setting` WHERE `admin_id` = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function getAdminStatusWhatsapp($conn, $admin_id) {
    $sql = "SELECT `stop`, `stop_date` FROM `admin` WHERE `admin_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$admin_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getOfficeStatusWhatsapp($conn, $office_id) {
    $sql = "SELECT `stop`, `stop_date` FROM `offices` WHERE `office_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$office_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$sqlReminderDues = "
    SELECT 
        cases.case_id,
        cases.case_title,
        cases.office_id,
        clients.client_id,
        clients.first_name,
        clients.last_name,
        clients.phone,
        clients.email,
        clients.receive_emails, 
        payments.amount_paid,
        payments.id AS payment_id,
        payments.payment_date,
        payments.payment_method,
        payments.received,
        payments.payment_date_hiri
    FROM 
        cases
    LEFT JOIN 
        payments ON payments.case_id = cases.case_id
    LEFT JOIN 
        clients ON clients.client_id = cases.client_id
    ORDER BY 
        cases.case_id;";

$stmtReminderDues = $conn->prepare($sqlReminderDues);
$stmtReminderDues->execute();
$casesReminders = $stmtReminderDues->fetchAll(PDO::FETCH_ASSOC);

foreach ($casesReminders as $caseReminder) {
    $office_id = $caseReminder['office_id'];

    $officeStatus = getOfficeStatusWhatsapp($conn, $office_id);
    if ($officeStatus['stop'] == 1 && (is_null($officeStatus['stop_date']) || $officeStatus['stop_date'] <= date('Y-m-d'))) {
        // echo "Skipping office $office_id as it is stopped\n";
        continue;
    }

    $admin_id = $conn->query("SELECT admin_id FROM offices WHERE office_id = $office_id")->fetchColumn();
    $adminStatus = getAdminStatusWhatsapp($conn, $admin_id);

    if ($adminStatus['stop'] == 1 && (is_null($adminStatus['stop_date']) || $adminStatus['stop_date'] <= date('Y-m-d'))) {
        // echo "Skipping admin $admin_id as they are stopped\n";
        continue;
    }

    $adminSettings = getAdminSettingsWhatsapp($conn, $admin_id);

    if (empty($adminSettings['host_whatsapp']) || empty($adminSettings['token_whatsapp'])) {
        // echo "Admin settings are incomplete for admin $admin_id, skipping notification\n";
        continue;
    }

    $case_id = $caseReminder['case_id'];
    $payment_id = $caseReminder['payment_id'];
    $client_id = $caseReminder['client_id'];
    $clientName = $caseReminder['first_name'] . ' ' . $caseReminder['last_name'];
    $caseTitle = $caseReminder['case_title'];
    $amountMoney = $caseReminder['amount_paid'];
    $received = $caseReminder['received'];
    $payment_date = $caseReminder['payment_date'];
    $payment_date_hiri = $caseReminder['payment_date_hiri'];

    if ($amountMoney == 0) {
        continue;
    }

    $defaultMessage = "
        عزيزي/عزيزتي {$clientName}،

        لديك مستحقات بقيمة " . number_format($amountMoney) . " للقضية {$caseTitle}.

        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.

        شكرًا.
    ";

    try {
        $stmt = $conn->prepare("SELECT message_text FROM templates WHERE office_id = :office_id AND for_whom = 1 AND type_template = 4");
        $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
        $stmt->execute();
        $messageTemplate = $stmt->fetch(PDO::FETCH_ASSOC)['message_text'] ?? $defaultMessage;
    } catch (Exception $e) {
        error_log("Error fetching message template for office_id $office_id: " . $e->getMessage());
        $messageTemplate = $defaultMessage;
    }

    $clientNotificationTextOld = str_replace(
        ['{$client_first_name}', '{$case_title}', '{$amountMoney}', '{$payment_date}', '{$client_last_name}', '{$payment_date_hiri}'],
        [$caseReminder['first_name'], $caseTitle, $amountMoney, $payment_date, $caseReminder['last_name'], $payment_date_hiri],
        $messageTemplate
    );

    if ($received != 1) {
        $sqlReminder = "SELECT message_date FROM reminder_due WHERE client_id = ? AND case_id = ? AND payment_id = ? AND type_notifcation = 'Whatsupp' ORDER BY message_date DESC LIMIT 1";
        $stmtReminder = $conn->prepare($sqlReminder);
        $stmtReminder->execute([$client_id, $case_id, $payment_id]);
        $lastReminder = $stmtReminder->fetch(PDO::FETCH_ASSOC);

        $currentDate = new DateTime();
        $reminderNeeded = true;

        if ($lastReminder) {
            $lastReminderDate = new DateTime($lastReminder['message_date']);
            $interval = $currentDate->diff($lastReminderDate);
            if ($interval->days < 5) {
                $reminderNeeded = false;
            }
        }

        if ($reminderNeeded) {
            $recipient = $caseReminder['phone'];
            $message = $clientNotificationTextOld;

            // echo "Sending WhatsApp message to $recipient for payment $payment_id using admin $admin_id\n";

            $messageSent = sendWhatsAppMessage($recipient, $message, $adminSettings);

            if ($messageSent) {
                // echo "Successfully sent WhatsApp message to $recipient using admin $admin_id\n";
                $sqlInsertReminder = "INSERT INTO reminder_due (client_id, case_id, payment_id, `message`, message_date, phone_used, type_notifcation) VALUES (?, ?, ?, ?, ?, ?, 'Whatsupp')";
                $stmtInsertReminder = $conn->prepare($sqlInsertReminder);
                $stmtInsertReminder->execute([$client_id, $case_id, $payment_id, $message, $currentDate->format('Y-m-d'), $caseReminder['phone']]);
                // echo "Logged reminder for client $client_id, case $case_id, payment $payment_id\n";
            } else {
                // echo "Failed to send WhatsApp message to $recipient using admin $admin_id\n";
            }
        }
    }
}

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

?>
