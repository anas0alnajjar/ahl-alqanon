<?php

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {

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
                                    c.client_id = :client_id OR FIND_IN_SET(:client_id, c.plaintiff)
                                ORDER BY 
                                    s.session_date DESC";

            $statementSession = $conn->prepare($query_for_session);
            $statementSession->bindParam(':client_id', $user_id, PDO::PARAM_INT);
            $statementSession->execute();
            $response = $statementSession->fetchAll(PDO::FETCH_ASSOC);

            // تنسيق البيانات كـ JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // إذا لم يتم العثور على OFFICE_ID للعميل
            $response = array(
                'status' => false,
                'message' => 'لم يتم العثور على مكتب مرتبط بهذا العميل.'
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
