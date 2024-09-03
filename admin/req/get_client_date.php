<?php

session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {

        include "../../DB_connection.php";
        

  
        
        

        $query_for_clients = "WITH upcoming_sessions AS (
                                SELECT c.client_id, COUNT(s.sessions_id) AS upcoming_sessions_count
                                FROM clients c
                                JOIN cases ca ON c.client_id = ca.client_id
                                JOIN sessions s ON ca.case_id = s.case_id
                                WHERE s.session_date > CURRENT_DATE
                                GROUP BY c.client_id
                            ),
                            total_expenses AS (
                                SELECT c.client_id, SUM(e.amount) AS total_expenses
                                FROM clients c
                                JOIN cases ca ON c.client_id = ca.client_id
                                JOIN expenses e ON ca.case_id = e.case_id
                                GROUP BY c.client_id
                            ),
                            total_payments AS (
                                SELECT c.client_id, SUM(p.amount_paid) AS total_payments
                                FROM clients c
                                JOIN cases ca ON c.client_id = ca.client_id
                                JOIN payments p ON ca.case_id = p.case_id
                                GROUP BY c.client_id
                            ),
                            due_payments AS (
                                SELECT c.client_id, p.payment_date, p.amount_paid
                                FROM clients c
                                JOIN cases ca ON c.client_id = ca.client_id
                                JOIN payments p ON ca.case_id = p.case_id
                                WHERE p.received != 1 OR p.received IS NOT NULL
                            )
                            SELECT c.client_id, c.first_name, c.last_name,
                                COALESCE(us.upcoming_sessions_count, 0) AS upcoming_sessions_count, 
                                COALESCE(te.total_expenses, 0) AS total_expenses, 
                                COALESCE(tp.total_payments, 0) AS total_payments, 
                                (COALESCE(te.total_expenses, 0) - COALESCE(tp.total_payments, 0)) AS outstanding_cost,
                                dp.payment_date, dp.amount_paid
                            FROM clients c
                            LEFT JOIN upcoming_sessions us ON c.client_id = us.client_id
                            LEFT JOIN total_expenses te ON c.client_id = te.client_id
                            LEFT JOIN total_payments tp ON c.client_id = tp.client_id
                            LEFT JOIN due_payments dp ON c.client_id = dp.client_id
                            WHERE c.client_id IN (
                                SELECT DISTINCT c.client_id
                                FROM clients c
                                JOIN cases ca ON c.client_id = ca.client_id
                            )
                            ORDER BY dp.payment_date ASC;
                            ";

$statementClients = $conn->prepare($query_for_clients);
$statementClients->execute([]);
$response = $statementClients->fetchAll(PDO::FETCH_ASSOC);



header('Content-Type: application/json');
echo json_encode($response);
?>  

<?php 

  }else {
    header("Location: ../../index.php");
    exit;
  } 
}else {
	header("Location: ../../index.php");
	exit;
} 

?>