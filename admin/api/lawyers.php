<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once "../../DB_connection.php";

    try {


        $sql = "SELECT lawyer_id, lawyer_name FROM lawyer";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // جلب جميع النتائج
        $lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($lawyers)) {
            echo json_encode(["error" => "No lawyers found"]);
        } else {
            echo json_encode($lawyers);
        }

    } catch(PDOException $e) {
        echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    }

} else {
    header("Location: ../../logout.php");
    exit;
}
?>
