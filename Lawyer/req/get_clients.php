<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    include_once "../../DB_connection.php";

    
    $user_id = $_SESSION['user_id'];

        $query = "SELECT client_id, first_name, last_name FROM clients WHERE lawyer_id = :lawyer_id";
        $statement = $conn->prepare($query);
        $statement->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $clients = $statement->fetchAll(PDO::FETCH_ASSOC);

        $response = array(
            "clients" => $clients
        );

        echo json_encode($response);

} else {
    header("Location: ../logout.php");
    exit;
}
?>
