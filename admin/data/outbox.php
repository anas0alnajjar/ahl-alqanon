<?php
function getAllNotifications($conn) {
    $sql = "SELECT recipient_email, recipient_phone, case_id, session_id, sent_date FROM sent_notifications_sessions ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllReminders($conn) {
    $sql = "SELECT message_date, client_id, case_id, message, phone_used, `type_notifcation` FROM reminder_due ORDER BY message_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllCases($conn) {
    $sql = "SELECT case_id, case_title FROM cases";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $caseMap = [];
    foreach ($cases as $case) {
        $caseMap[$case['case_id']] = $case['case_title'];
    }
    return $caseMap;
}

function getAllTasks($conn) {
    $sql = "SELECT t.title, t.date_time, l.lawyer_name AS lawyer, h.helper_name AS helper
                FROM todos t
                LEFT JOIN lawyer l ON t.lawyer_id = l.lawyer_id
                LEFT JOIN helpers h ON t.helper_id = h.id
                WHERE t.helper_id IS NOT NULL;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

