<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    require '../../DB_connection.php';
    include "../get_office.php";
    
    $user_id = $_SESSION['user_id'];

    $events = array();

    if ($user_id !== null) {

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
                        WHERE c.lawyer_id = :lawyer_id
                        ORDER BY s.session_date DESC;";

        $stmt_sessions = $conn->prepare($sql_sessions);
        $stmt_sessions->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
        $stmt_sessions->execute();

        while ($row = $stmt_sessions->fetch(PDO::FETCH_ASSOC)) {
            $row['id'] = 'S' . $row['sessions_id'];
            $row['title'] = $row['case_title'];
            $row['start'] = $row['session_date'];
            $row['end'] = null;
            $row['session_hour'] = $row['session_hour'];
            $row['type'] = 'session'; // إضافة نوع الحدث
            $events[] = $row;
        }

        // جلب الأحداث المرتبطة بالمحامين أو الموكلين المرتبطين بالمحامي
        $sql_events = "SELECT event_id, lawyer_id, client_id, event_name, event_start_date, event_end_date 
                       FROM events
                       WHERE lawyer_id = :lawyer_id";

        $stmt_events = $conn->prepare($sql_events);
        $stmt_events->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
        $stmt_events->execute();

        while ($row = $stmt_events->fetch(PDO::FETCH_ASSOC)) {
            $row['id'] = 'E' . $row['event_id'];
            $row['title'] = $row['event_name'];
            $row['start'] = $row['event_start_date'];
            $row['end'] = $row['event_end_date'];
            $row['type'] = 'event'; // إضافة نوع الحدث
            $events[] = $row;
        }

        $response = array(
            'status' => true,
            'data' => $events
        );

        echo json_encode($response);
    } else {
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
