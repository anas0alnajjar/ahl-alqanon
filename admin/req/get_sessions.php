<?php

session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {

        include "../../DB_connection.php";
        
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

                            ORDER BY `s`.`session_date` DESC;";


        $statementSession = $conn->prepare($query_for_session);
        $statementSession->execute();
        $response = $statementSession->fetchAll(PDO::FETCH_ASSOC);

        // تنسيق البيانات كـ JSON
        header('Content-Type: application/json');
        echo json_encode($response);

    } else {
        header("Location: ../../index.php");
        exit;
    } 
} else {
	header("Location: ../../index.php");
	exit;
} 