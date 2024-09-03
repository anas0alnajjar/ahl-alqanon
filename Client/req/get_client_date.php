<?php

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Client') {

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
                        AND (ca.client_id = :client_id OR FIND_IN_SET(:client_id, ca.plaintiff))
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
                        p.received IS NULL OR p.received != 1
                        AND (ca.client_id = :client_id OR FIND_IN_SET(:client_id, ca.plaintiff))
                ) dp ON c.client_id = dp.client_id
                WHERE 
                    c.client_id IN (
                        SELECT 
                            DISTINCT ca.client_id
                        FROM 
                            cases ca
                        WHERE 
                            ca.client_id = :client_id OR FIND_IN_SET(:client_id, ca.plaintiff)
                    )
                ORDER BY 
                    dp.payment_date ASC;
            ";

            $statementClients = $conn->prepare($query_for_clients);
            $statementClients->bindParam(':client_id', $user_id, PDO::PARAM_INT);
            $statementClients->execute();
            $response = $statementClients->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // إذا لم يتم العثور على OFFICE_ID للعميل
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

