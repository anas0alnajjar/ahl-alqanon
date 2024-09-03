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

function fetchEmailSettings($conn, $admin_id) {
    try {
        $sql = "SELECT `host_email`, `username_email`, `password_email`, `port_email` FROM `setting` WHERE `admin_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$admin_id]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($settings['host_email']) || empty($settings['username_email']) || empty($settings['password_email']) || empty($settings['port_email'])) {
            $sql = "SELECT `host_email`, `username_email`, `password_email`, `port_email` FROM `setting` WHERE `admin_id` = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $settings;
    } catch (Exception $e) {
        error_log("Failed to fetch admin settings: " . $e->getMessage());
        $sql = "SELECT `host_email`, `username_email`, `password_email`, `port_email` FROM `setting` WHERE `admin_id` = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function isOfficeActive($conn, $office_id) {
    $sql = "SELECT `stop` FROM offices WHERE office_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$office_id]);
    $officeStatus = $stmt->fetch(PDO::FETCH_ASSOC)['stop'];
    return $officeStatus == 0;
}

function isAdminActive($conn, $admin_id) {
    $sql = "SELECT `stop` FROM `admin` WHERE `admin_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$admin_id]);
    $adminStatus = $stmt->fetch(PDO::FETCH_ASSOC)['stop'];
    return $adminStatus == 0;
}

function sendMail($recipient, $subject, $message, $settings) {
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

function fetchCases($conn) {
    $sql = "SELECT 
                cases.case_id,
                cases.case_title,
                cases.office_id,
                clients.client_id,
                clients.first_name,
                clients.last_name,
                clients.phone,
                clients.email,
                clients.receive_emails, 
                payments.id AS payment_id,
                payments.amount_paid,
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
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMessageTemplate($conn, $office_id) {
    try {
        $sql = "SELECT message_text FROM templates WHERE office_id = ? AND for_whom = 1 AND type_template = 3";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$office_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['message_text'] ?? null;
    } catch (Exception $e) {
        error_log("Error fetching message template for office_id $office_id: " . $e->getMessage());
        return null;
    }
}

function sendPaymentReminder($conn, $caseReminder, $settings) {
    $clientName = $caseReminder['first_name'] . ' ' . $caseReminder['last_name'];
    $caseTitle = $caseReminder['case_title'];
    $amountMoney = $caseReminder['amount_paid'];
    $payment_date = $caseReminder['payment_date'];
    $payment_date_hiri = $caseReminder['payment_date_hiri'];

    if (empty($clientName) || empty($caseTitle) || $amountMoney <= 0) {
        return;
    }

    $messageTemplate = getMessageTemplate($conn, $caseReminder['office_id']);

    if (!$messageTemplate) {
        $messageTemplate = "
        <div style='direction: rtl; text-align: right; font-family: Arial, sans-serif;'>
            <p>عزيزي {$clientName}،</p>
            <p>لديك مستحقات بقيمة " . number_format((float)$amountMoney, 2) . " للقضية {$caseTitle}.</p>
            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>
            <p>شكرًا.</p>
        </div>";
    }

    $clientNotificationText = str_replace(
        ['{$client_first_name}', '{$case_title}', '{$amountMoney}', '{$payment_date}', '{$client_last_name}', '{$payment_date_hiri}'],
        [$caseReminder['first_name'], $caseTitle, number_format((float)$amountMoney, 2), $payment_date, $caseReminder['last_name'], $payment_date_hiri],
        $messageTemplate
    );

    if ($caseReminder['receive_emails'] == 1) {
        $recipientEmail = $caseReminder['email'];
        $emailSubject = "تذكير بالمستحقات - $caseTitle";

        $emailMessage = "<div style='direction: rtl; text-align: right; font-family: Arial, sans-serif;'>\n";
        $emailMessage .= "    <h2 style='color: #4CAF50;'>تذكير بالمستحقات المالية</h2>\n";
        $emailMessage .= "    $clientNotificationText\n";
        $emailMessage .= "</div>\n";

        $emailSent = sendMail($recipientEmail, $emailSubject, $emailMessage, $settings);

        if ($emailSent) {
            $currentDate = new DateTime();
            $sql = "INSERT INTO reminder_due (client_id, case_id, payment_id, `message`, message_date, phone_used, type_notifcation) 
                    VALUES (:client_id, :case_id, :payment_id, :message, :message_date, :phone_used, 'Email')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':client_id' => $caseReminder['client_id'],
                ':case_id' => $caseReminder['case_id'],
                ':payment_id' => $caseReminder['payment_id'],
                ':message' => $emailMessage,
                ':message_date' => $currentDate->format('Y-m-d'),
                ':phone_used' => $caseReminder['email']
            ]);
        }
    }
}

$casesReminders = fetchCases($conn);

foreach ($casesReminders as $caseReminder) {
    $office_id = $caseReminder['office_id'];

    if (!isOfficeActive($conn, $office_id)) {
        continue;
    }

    $admin_id = $conn->query("SELECT admin_id FROM offices WHERE office_id = $office_id")->fetchColumn();

    if (!isAdminActive($conn, $admin_id)) {
        continue;
    }

    $settings = fetchEmailSettings($conn, $admin_id);

    if ($caseReminder['amount_paid'] > 0 && $caseReminder['received'] != 1) {
        $sql = "SELECT message_date FROM reminder_due WHERE client_id = :client_id AND case_id = :case_id AND payment_id = :payment_id AND type_notifcation = 'Email' ORDER BY message_date DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':client_id' => $caseReminder['client_id'], ':case_id' => $caseReminder['case_id'], ':payment_id' => $caseReminder['payment_id']]);
        $lastReminder = $stmt->fetch(PDO::FETCH_ASSOC);

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
            sendPaymentReminder($conn, $caseReminder, $settings);
        }
    }
}
?>
