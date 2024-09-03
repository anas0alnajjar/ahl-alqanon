<?php

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {

        include "../../DB_connection.php";
        include "../get_office.php";
        
        $user_id = $_SESSION['user_id'];
        $office_id = getOfficeId($conn, $user_id);

        if ($office_id !== null) {
            $query_for_clients = "
                SELECT 
                    c.client_id, 
                    c.first_name, 
                    c.last_name,
                    COALESCE(us.upcoming_sessions_count, 0) AS upcoming_sessions_count, 
                    dp.payment_date, 
                    dp.amount_paid
                FROM 
                    clients c
                LEFT JOIN (
                    SELECT 
                        ca.client_id, 
                        COUNT(s.sessions_id) AS upcoming_sessions_count
                    FROM 
                        cases ca
                    JOIN 
                        sessions s ON ca.case_id = s.case_id
                    WHERE 
                        s.session_date > CURRENT_DATE 
                        AND FIND_IN_SET(:helper_id, ca.helper_name)
                    GROUP BY 
                        ca.client_id
                ) us ON c.client_id = us.client_id
                LEFT JOIN (
                    SELECT 
                        ca.client_id, 
                        p.payment_date, 
                        p.amount_paid
                    FROM 
                        cases ca
                    JOIN 
                        payments p ON ca.case_id = p.case_id
                    WHERE 
                        p.received IS NULL 
                        AND FIND_IN_SET(:helper_id, ca.helper_name)
                ) dp ON c.client_id = dp.client_id
                WHERE 
                    c.client_id IN (
                        SELECT 
                            DISTINCT ca.client_id
                        FROM 
                            cases ca
                        WHERE 
                            FIND_IN_SET(:helper_id, ca.helper_name)
                    )
                ORDER BY 
                    dp.payment_date ASC;
            ";

            $statementClients = $conn->prepare($query_for_clients);
            $statementClients->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statementClients->execute();
            $response = $statementClients->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // إذا لم يتم العثور على OFFICE_ID للمساعد
            echo json_encode([]);
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
