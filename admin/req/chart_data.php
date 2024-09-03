<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once "../../DB_connection.php";

    $officeId = $_POST['office_id'];

                    $query = "SELECT 
                        c.case_title, 
                        of.office_name,
                        COALESCE(SUM(DISTINCT e.amount), 0) AS total_exp,
                        COALESCE(SUM(DISTINCT oc.amount), 0) AS total_overhead,
                        COALESCE(SUM(p.amount_paid), 0) AS total_paid
                    FROM 
                        offices of
                    LEFT JOIN cases c ON of.office_id = c.office_id
                    LEFT JOIN expenses e ON c.case_id = e.case_id
                    LEFT JOIN overhead_costs oc ON of.office_id = oc.office_id
                    LEFT JOIN payments p ON c.case_id = p.case_id
                    WHERE 
                        of.office_id = :office_id
                GROUP BY 
                    c.office_id";

    $statement = $conn->prepare($query);
    $statement->bindParam(':office_id', $officeId);
    $statement->execute();
    $officeData = $statement->fetch(PDO::FETCH_ASSOC);

    $response = array(
        "office_name" => $officeData['office_name'],
        "total_paid" => $officeData['total_paid'],
        "total_exp" => $officeData['total_exp'] + $officeData['total_overhead']
    );

    echo json_encode($response);

} else {
    header("Location: ../logout.php");
    exit;
}
?>