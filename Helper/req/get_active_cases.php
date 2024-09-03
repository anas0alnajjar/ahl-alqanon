<?php

session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {

        include "../../DB_connection.php";
        include "../get_office.php";
        
        $user_id = $_SESSION['user_id'];
        $office_id = getOfficeId($conn, $user_id);

        if ($office_id !== null) {
            // استعلام SQL لجلب القضايا والعملاء المرتبطين بالمساعد
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
                      WHERE FIND_IN_SET(:helper_id, cases.helper_name)
                      ORDER BY `case_id` DESC;";

            // تنفيذ الاستعلام
            $statement = $conn->prepare($query);
            $statement->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement->execute();
            $response = $statement->fetchAll(PDO::FETCH_ASSOC);

            // تنسيق البيانات كـ JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // إذا لم يتم العثور على OFFICE_ID للمساعد
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
