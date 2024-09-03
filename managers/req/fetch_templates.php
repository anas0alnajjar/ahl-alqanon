<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Managers') {
    include "../../DB_connection.php";

    

    include "../get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);

    $stmt = $conn->prepare("SELECT
                                t.id,
                                CASE 
                                    WHEN t.type_template = 1 THEN 'تذكير بالجلسات (إيميل)'
                                    WHEN t.type_template = 2 THEN 'تذكير بالجلسات (واتساب)'
                                    WHEN t.type_template = 3 THEN 'تذكير بالمستحقات (إيميل)'
                                    WHEN t.type_template = 4 THEN 'تذكير بالمستحقات (واتساب)'
                                    WHEN t.type_template = 5 THEN 'إخطار بالمهام (واتساب)'
                                END AS type_template,
                                CASE 
                                    WHEN t.for_whom = 1 THEN 'عميل'
                                    WHEN t.for_whom = 2 THEN 'محامي'
                                    WHEN t.for_whom = 3 THEN 'مساعد'
                                END AS for_whom_translated
                            FROM templates t
                            WHERE t.office_id = :office_id");
    $stmt->bindParam(':office_id', $OfficeId, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
