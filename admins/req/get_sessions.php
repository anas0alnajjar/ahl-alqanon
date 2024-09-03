<?php

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admins') {

        include "../../DB_connection.php";

        $user_id = $_SESSION['user_id'];

        // جلب مكاتب الآدمن
        $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
        $stmt_offices = $conn->prepare($sql_offices);
        $stmt_offices->bindParam(':admin_id', $user_id);
        $stmt_offices->execute();
        $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($offices)) {
            $office_ids = implode(',', $offices);

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
                                    c.office_id IN ($office_ids)
                                ORDER BY 
                                    s.session_date DESC";

            $statementSession = $conn->prepare($query_for_session);
            $statementSession->execute();
            $response = $statementSession->fetchAll(PDO::FETCH_ASSOC);

            // تنسيق البيانات كـ JSON
            header('Content-Type: application/json');
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
        header("Location: ../../index.php");
        exit;
    } 
} else {
    header("Location: ../../index.php");
    exit;
} 
?>
