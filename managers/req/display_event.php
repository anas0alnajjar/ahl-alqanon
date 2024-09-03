<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Managers') {
    require '../../DB_connection.php';
    include "../get_office.php";
    
    $user_id = $_SESSION['user_id'];
    $office_id = getOfficeId($conn, $user_id);

    $lawyer_id = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : null;

    $events = array();

    if ($office_id !== null) {
        // جلب المحامين المرتبطين بالمكتب
        $sql_lawyers = "SELECT lawyer_id FROM lawyer WHERE office_id = :office_id";
        $stmt_lawyers = $conn->prepare($sql_lawyers);
        $stmt_lawyers->bindParam(':office_id', $office_id, PDO::PARAM_INT);
        $stmt_lawyers->execute();
        $lawyers = $stmt_lawyers->fetchAll(PDO::FETCH_COLUMN);

        // جلب الموكلين المرتبطين بالمكتب
        $sql_clients = "SELECT client_id FROM clients WHERE office_id = :office_id";
        $stmt_clients = $conn->prepare($sql_clients);
        $stmt_clients->bindParam(':office_id', $office_id, PDO::PARAM_INT);
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
                        WHERE c.office_id = :office_id";

        // إضافة الشروط بناءً على الفلاتر
        if ($lawyer_id && $lawyer_id !== 'all') {
            $sql_sessions .= " AND lw.lawyer_id = :lawyer_id";
        }

        $sql_sessions .= " ORDER BY s.session_date DESC;";

        $stmt_sessions = $conn->prepare($sql_sessions);
        $stmt_sessions->bindParam(':office_id', $office_id, PDO::PARAM_INT);
        if ($lawyer_id && $lawyer_id !== 'all') {
            $stmt_sessions->bindParam(':lawyer_id', $lawyer_id, PDO::PARAM_INT);
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

        // جلب الأحداث المرتبطة بالمحامين أو الموكلين المرتبطين بالمكتب
        $sql_events = "SELECT event_id, lawyer_id, client_id, event_name, event_start_date, event_end_date 
                       FROM events
                       WHERE (lawyer_id IN (SELECT lawyer_id FROM lawyer WHERE office_id = :office_id)
                       OR client_id IN (SELECT client_id FROM clients WHERE office_id = :office_id))";

        if ($lawyer_id && $lawyer_id !== 'all') {
            $sql_events .= " AND lawyer_id = :lawyer_id";
        }

        $stmt_events = $conn->prepare($sql_events);
        $stmt_events->bindParam(':office_id', $office_id, PDO::PARAM_INT);
        if ($lawyer_id && $lawyer_id !== 'all') {
            $stmt_events->bindParam(':lawyer_id', $lawyer_id, PDO::PARAM_INT);
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
        // إذا لم يتم العثور على OFFICE_ID للمدير
        $response = array(
            'status' => false,
            'message' => 'لا يوجد مكتب مرتبط بهذا المسؤول.'
        );
        echo json_encode($response);
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
