<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once "../../DB_connection.php";

    $query = "SELECT office_id, office_name FROM offices";
    $statement = $conn->prepare($query);
    $statement->execute();
    $offices = $statement->fetchAll(PDO::FETCH_ASSOC);

    $response = array(
        "offices" => $offices
    );

    echo json_encode($response);

} else {
    header("Location: ../logout.php");
    exit;
}
?>