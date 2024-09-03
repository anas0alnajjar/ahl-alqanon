<?php

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {

        include "../../DB_connection.php";
        include "../get_office.php";

        $user_id = $_SESSION['user_id'];
        $office_id = getOfficeId($conn, $user_id);

        if ($office_id !== null) {

            $query_for_session = "SELECT 
                                    c.case_id, 
                                    c.case_title, 
                                    cl.first_name AS client_first_name, 
                                    cl.last_name AS client_last_name,
                                    cl.client_id,
                                    cl.email AS client_email,
                                    s.session_date,
                                    s.session_hour
                                FROM 
                                    sessions s
                                LEFT JOIN 
                                    cases c ON c.case_id = s.case_id
                                LEFT JOIN 
                                    clients cl ON c.client_id = cl.client_id
                                WHERE 
                                    FIND_IN_SET(:helper_id, c.helper_name) OR s.assistant_lawyer = :assistant_lawyer
                                ORDER BY 
                                    s.session_date DESC";

            $statementSession = $conn->prepare($query_for_session);
            $statementSession->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statementSession->bindParam(':assistant_lawyer', $user_id, PDO::PARAM_INT);
            $statementSession->execute();
            $response = $statementSession->fetchAll(PDO::FETCH_ASSOC);

            // تنسيق البيانات كـ JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // إذا لم يتم العثور على OFFICE_ID للمساعد
            $response = array(
                'status' => false,
                'message' => 'لم يتم العثور على مكتب مرتبط بهذا المساعد.'
            );
            echo json_encode($response);
        }

    } else {
        header("Location: ../../index.php");
        exit;
    } 
} else {
    header("Location: ../../index.php");
    exit;
} 
?>
