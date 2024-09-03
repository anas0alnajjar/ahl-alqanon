<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    include_once "../../DB_connection.php";

    $user_id = $_SESSION['user_id'];

    // جلب العملاء المرتبطين بالمساعد
    $query = "
        SELECT DISTINCT cl.client_id, cl.first_name, cl.last_name
        FROM clients cl
        JOIN cases ca ON cl.client_id = ca.client_id
        WHERE FIND_IN_SET(:helper_id, ca.helper_name)
    ";
    $statement = $conn->prepare($query);
    $statement->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
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
