<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    include_once "../../DB_connection.php";

    $user_id = $_SESSION['user_id'];

    $query = "SELECT case_id, case_title 
              FROM cases 
              WHERE client_id = :client_id OR FIND_IN_SET(:client_id, plaintiff)";
              
    $statement = $conn->prepare($query);
    $statement->bindParam(':client_id', $user_id, PDO::PARAM_INT);
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
