<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once "../../DB_connection.php";

    $query = "SELECT case_id, case_title FROM cases";
    $statement = $conn->prepare($query);
    $statement->execute();
    $cases = $statement->fetchAll(PDO::FETCH_ASSOC);

    $response = array(
        "cases" => $cases
    );

    echo json_encode($response);

} else {
    header("Location: ../logout.php");
    exit;
}
?>