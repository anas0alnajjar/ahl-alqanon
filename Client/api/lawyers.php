<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    include_once "../../DB_connection.php";

    try {


        include "../get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);


        if (!empty($OfficeId)) {
            

            // جلب المحامين المرتبطين بمكاتب الآدمن
            $sql = "SELECT lawyer_id, lawyer_name FROM lawyer WHERE office_id IN ($OfficeId)";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // جلب جميع النتائج
            $lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($lawyers)) {
                echo json_encode(["error" => "No lawyers found"]);
            } else {
                echo json_encode($lawyers);
            }
        } else {
            echo json_encode(["error" => "No offices found for this admin"]);
        }

    } catch(PDOException $e) {
        echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    }

} else {
    header("Location: ../../logout.php");
    exit;
}
?>
