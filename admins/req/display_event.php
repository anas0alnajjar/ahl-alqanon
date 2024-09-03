<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    require '../../DB_connection.php';

    $lawyer_id = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : null;

    $events = array();

    // جلب مكاتب الآدمن
    $user_id = $_SESSION['user_id'];
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $user_id);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        // جلب المحامين المرتبطين بالمكاتب
        $sql_lawyers = "SELECT lawyer_id FROM lawyer WHERE office_id IN ($office_ids)";
        $stmt_lawyers = $conn->prepare($sql_lawyers);
        $stmt_lawyers->execute();
        $lawyers = $stmt_lawyers->fetchAll(PDO::FETCH_COLUMN);

        // جلب الموكلين المرتبطين بالمكاتب
        $sql_clients = "SELECT client_id FROM clients WHERE office_id IN ($office_ids)";
        $stmt_clients = $conn->prepare($sql_clients);
        $stmt_clients->execute();
        $clients = $stmt_clients->fetchAll(PDO::FETCH_COLUMN);

        // بناء جملة الاستعلام بناءً على الفلاتر
        $sql_sessions = "SELECT 
                            c.case_id, 
                            c.case_title, 
                            cl.first_name AS client_first_name, 
                            cl.last_name AS client_last_name,
                            cl.client_id,
                            cl.email AS client_email,
                            s.session_date,
                            s.session_hour,
                            lw.lawyer_id,
                            s.sessions_id
                        FROM 
                            sessions s
                        LEFT JOIN 
                            cases c ON c.case_id = s.case_id
                        LEFT JOIN 
                            lawyer lw ON c.lawyer_id = lw.lawyer_id
                        LEFT JOIN 
                            clients cl ON c.client_id = cl.client_id
                        WHERE c.office_id IN ($office_ids)";

        // إضافة الشروط بناءً على الفلاتر
        if ($lawyer_id && $lawyer_id !== 'all') {
            $sql_sessions .= " AND lw.lawyer_id = :lawyer_id";
        }

        $sql_sessions .= " ORDER BY s.session_date DESC;";

        $stmt_sessions = $conn->prepare($sql_sessions);
        if ($lawyer_id && $lawyer_id !== 'all') {
            $stmt_sessions->bindParam(':lawyer_id', $lawyer_id);
        }
        $stmt_sessions->execute();
        while ($row = $stmt_sessions->fetch(PDO::FETCH_ASSOC)) {
            $row['id'] = 'S' . $row['sessions_id'];
            $row['title'] = $row['case_title'];
            $row['start'] = $row['session_date'];
            $row['end'] = null;
            $row['session_hour'] = $row['session_hour'];
            $events[] = $row;
        }

        // جلب الأحداث المرتبطة بالمحامين أو الموكلين المرتبطين بالمكاتب
        $sql_events = "SELECT event_id, lawyer_id, client_id, event_name, event_start_date, event_end_date 
                       FROM events";

        // التحقق من أن القوائم ليست فارغة قبل إضافة الشرط
        if (!empty($lawyers) || !empty($clients)) {
            $conditions = [];
            if (!empty($lawyers)) {
                $lawyer_ids = implode(',', $lawyers);
                $conditions[] = "lawyer_id IN ($lawyer_ids)";
            }


            $sql_events .= " WHERE " . implode(" OR ", $conditions);
        }

        if ($lawyer_id && $lawyer_id !== 'all') {
            $sql_events .= (strpos($sql_events, 'WHERE') !== false ? " AND" : " WHERE") . " lawyer_id = :lawyer_id";
        }

        $stmt_events = $conn->prepare($sql_events);
        if ($lawyer_id && $lawyer_id !== 'all') {
            $stmt_events->bindParam(':lawyer_id', $lawyer_id);
        }
        $stmt_events->execute();
        while ($row = $stmt_events->fetch(PDO::FETCH_ASSOC)) {
            $row['id'] = 'E' . $row['event_id'];
            $row['title'] = $row['event_name'];
            $row['start'] = $row['event_start_date'];
            $row['end'] = $row['event_end_date'];
            $events[] = $row;
        }

        $response = array(
            'status' => true,
            'data' => $events
        );

        echo json_encode($response);
    } else {
        // إذا لم يكن للآدمن أي مكاتب
        $response = array(
            'status' => false,
            'message' => 'لا توجد مكاتب مرتبطة بهذا المسؤول.'
        );
        echo json_encode($response);
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
