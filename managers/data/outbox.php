<?php
function getAllNotifications($conn, $office_id) {
    $sql = "
        SELECT sn.recipient_email, sn.recipient_phone, sn.case_id, sn.session_id, sn.sent_date
        FROM sent_notifications_sessions sn
        INNER JOIN cases c ON sn.case_id = c.case_id
        WHERE c.office_id = :office_id
        ORDER BY sn.id DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllReminders($conn, $office_id) {
    $sql = "
        SELECT r.message_date, r.client_id, r.case_id, r.message, r.phone_used, r.type_notifcation
        FROM reminder_due r
        INNER JOIN cases c ON r.case_id = c.case_id
        WHERE c.office_id = :office_id
        ORDER BY r.message_date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCases($conn, $office_id) {
    $sql = "
        SELECT case_id, case_title
        FROM cases
        WHERE office_id = :office_id
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
    $stmt->execute();
    $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $caseMap = [];
    foreach ($cases as $case) {
        $caseMap[$case['case_id']] = $case['case_title'];
    }
    return $caseMap;
}

function getAllTasks($conn, $office_id) {
    $sql = "
        SELECT t.title, t.date_time, l.lawyer_name AS lawyer, h.helper_name AS helper
        FROM todos t
        LEFT JOIN lawyer l ON t.lawyer_id = l.lawyer_id
        LEFT JOIN helpers h ON t.helper_id = h.id
        WHERE t.lawyer_id IN (SELECT lawyer_id FROM lawyer WHERE office_id = :office_id)
        OR t.helper_id IN (SELECT id FROM helpers WHERE office_id = :office_id)
        OR t.client_id IN (SELECT client_id FROM clients WHERE office_id = :office_id)
        ORDER BY t.date_time DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHelpers($conn, $office_id) {
    $sql = "
        SELECT id, helper_name
        FROM helpers
        WHERE office_id = :office_id
        ORDER BY id
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// استخدام الدوال مع معرف المكتب

?>
