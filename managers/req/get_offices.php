<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Managers') {
    include_once "../../DB_connection.php";
    include "../get_office.php";
    
    $user_id = $_SESSION['user_id'];
    $office_id = getOfficeId($conn, $user_id);

    if ($office_id !== null) {
        // تعديل الاستعلام لجلب المكتب المرتبط بالمدير فقط
        $query = "SELECT office_id, office_name FROM offices WHERE office_id = :office_id";
        $statement = $conn->prepare($query);
        $statement->bindParam(':office_id', $office_id, PDO::PARAM_INT);
        $statement->execute();
        $offices = $statement->fetchAll(PDO::FETCH_ASSOC);

        $response = array(
            "offices" => $offices
        );

        echo json_encode($response);
    } else {
        // إذا لم يتم العثور على OFFICE_ID للمدير
        echo json_encode(array("offices" => []));
    }
} else {
    header("Location: ../logout.php");
    exit;
}
?>
