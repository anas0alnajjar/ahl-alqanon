<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    include_once "../../DB_connection.php";
    include "../get_office.php";
    
    $user_id = $_SESSION['user_id'];
    $office_id = getOfficeId($conn, $user_id);

    if ($office_id !== null) {
        // تعديل الاستعلام لجلب المحامين المرتبطين بالمكتب الذي يديره الآدمن
        $query = "SELECT lawyer_id, lawyer_name 
                  FROM lawyer 
                  WHERE office_id = :office_id";
        $statement = $conn->prepare($query);
        $statement->bindParam(':office_id', $office_id, PDO::PARAM_INT);
        $statement->execute();
        $lawyers = $statement->fetchAll(PDO::FETCH_ASSOC);

        $response = array(
            "lawyers" => $lawyers
        );

        echo json_encode($response);
    } else {
        // إذا لم يتم العثور على OFFICE_ID للمدير
        $response = array(
            "lawyers" => []
        );
        echo json_encode($response);
    }
} else {
    header("Location: ../logout.php");
    exit;
}
?>
