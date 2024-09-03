<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once "../../DB_connection.php";

    $query = "SELECT lawyer_id, lawyer_name FROM lawyer;";
    $statement = $conn->prepare($query);
    $statement->execute();
    $lawyers = $statement->fetchAll(PDO::FETCH_ASSOC);

    $response = array(
        "lawyers" => $lawyers
    );

    echo json_encode($response);

} else {
    header("Location: ../logout.php");
    exit;
}
?>