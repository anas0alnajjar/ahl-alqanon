<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include_once "../../DB_connection.php";

    $user_id = $_SESSION['user_id'];

    // تعديل الاستعلام لجلب المكاتب المرتبطة بالآدمن فقط
    $query = "SELECT office_id, office_name FROM offices WHERE admin_id = :admin_id";
    $statement = $conn->prepare($query);
    $statement->bindParam(':admin_id', $user_id, PDO::PARAM_INT);
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
