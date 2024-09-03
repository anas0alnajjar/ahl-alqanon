<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    require '../../DB_connection.php';

    $lawyer_id = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : null;

    $events = array();

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
                        clients cl ON c.client_id = cl.client_id";

    // إضافة الشروط بناءً على الفلاتر
    if ($lawyer_id && $lawyer_id !== 'all') {
        $sql_sessions .= " WHERE lw.lawyer_id = :lawyer_id";
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
        $row['type'] = 'session'; // إضافة نوع الحدث
        $events[] = $row;
    }

    $sql_events = "SELECT event_id, lawyer_id, event_name, event_start_date, event_end_date 
                   FROM events";

    if ($lawyer_id && $lawyer_id !== 'all') {
        $sql_events .= " WHERE lawyer_id = :lawyer_id";
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
        $row['type'] = 'event'; // إضافة نوع الحدث
        $events[] = $row;
    }

    $response = array(
        'status' => true,
        'data' => $events
    );

    echo json_encode($response);
} else {
    header("Location: ../login.php");
    exit;
}
?>
