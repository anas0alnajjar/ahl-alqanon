<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
    include_once "../../DB_connection.php";

    $query = "SELECT 
                c.case_id,
                c.case_title,
                SUM(p.amount_paid) AS total_paid,
                SUM(e.amount) AS total_exp
            FROM 
                cases c
            JOIN 
                clients cl ON c.client_id = cl.client_id
            LEFT JOIN 
                payments p ON c.case_id = p.case_id
            LEFT JOIN 
                expenses e ON c.case_id = e.case_id
            GROUP BY 
                c.case_id  
            ORDER BY `total_paid` ASC";

    $statement = $conn->prepare($query);
    $statement->execute();
    $cases = $statement->fetchAll(PDO::FETCH_ASSOC);

    // حساب الفرق بين المدفوعات والمصاريف
    foreach ($cases as &$case) {
        $case['difference'] = $case['total_paid'] - $case['total_exp'];
    }

    $response = array(
        "cases" => $cases
    );

    echo json_encode($response);

} else {
    header("Location: ../logout.php");
    exit;
}
?>