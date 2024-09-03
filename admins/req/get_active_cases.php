<?php

session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

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

            // استعلام SQL
            $query = "SELECT 
                      cases.case_title, 
                      cases.case_id, 
                      CONCAT(clients.first_name, ' ', clients.last_name) AS client_name,
                      CASE 
                          WHEN cases.agency = 'on' THEN 'وكالة'
                          ELSE 'قضية' 
                      END AS source
                      FROM 
                      cases 
                      INNER JOIN 
                      clients ON cases.client_id = clients.client_id
                      WHERE cases.office_id IN ($office_ids)
                      ORDER BY `case_id` DESC;";

            // تنفيذ الاستعلام
            $statement = $conn->prepare($query);
            $statement->execute();
            $response = $statement->fetchAll(PDO::FETCH_ASSOC);

            // تنسيق البيانات كـ JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // إذا لم يكن للآدمن أي مكاتب
            echo json_encode([]);
        }

    } else {
        header("Location: ../index.php");
        exit;
    } 
} else {
    header("Location: ../index.php");
    exit;
} 

?>
