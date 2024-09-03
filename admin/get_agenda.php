<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    require '../DB_connection.php';

    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;

    if ($startDate && $endDate) {
        try {
            $sessions = [];
            $events = [];

            // جلب الجلسات
            $sql_sessions = "SELECT 
                                c.case_id, 
                                c.case_title AS title, 
                                cl.first_name AS client_first_name, 
                                cl.last_name AS client_last_name,
                                cl.client_id,
                                cl.email AS client_email,
                                s.session_date AS start,
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
                            WHERE s.session_date BETWEEN :start_date AND :end_date
                            ORDER BY start DESC";

            $stmt_sessions = $conn->prepare($sql_sessions);
            $stmt_sessions->bindParam(':start_date', $startDate);
            $stmt_sessions->bindParam(':end_date', $endDate);
            $stmt_sessions->execute();
            $sessions = $stmt_sessions->fetchAll(PDO::FETCH_ASSOC);

            // جلب الأحداث
            $sql_events = "SELECT 
                            event_id AS case_id,
                            event_name AS title,
                            '' AS client_first_name,
                            '' AS client_last_name,
                            '' AS client_id,
                            '' AS client_email,
                            event_start_date AS start,
                            '' AS session_hour,
                            lawyer_id,
                            '' AS sessions_id
                        FROM events
                        WHERE event_start_date BETWEEN :start_date AND :end_date
                        ORDER BY start DESC";

            $stmt_events = $conn->prepare($sql_events);
            $stmt_events->bindParam(':start_date', $startDate);
            $stmt_events->bindParam(':end_date', $endDate);
            $stmt_events->execute();
            $events = $stmt_events->fetchAll(PDO::FETCH_ASSOC);

            $response = array(
                'status' => true,
                'data' => array(
                    'sessions' => $sessions,
                    'events' => $events
                )
            );
            echo json_encode($response);
        } catch (Exception $e) {
            $response = array('status' => false, 'message' => $e->getMessage());
            echo json_encode($response);
        }
    } else {
        $response = array('status' => false, 'message' => 'Invalid date range.');
        echo json_encode($response);
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
