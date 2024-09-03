<?php
session_start();
if (isset($_SESSION['admin_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";

    $stmt = $conn->prepare("SELECT
                            id,
                                CASE 
                                    WHEN t.type_template = 1 THEN 'تذكير بالجلسات (إيميل)'
                                    WHEN t.type_template = 2 THEN 'تذكير بالجلسات (واتساب)'
                                    WHEN t.type_template = 3 THEN 'تذكير بالمستحقات (إيميل)'
                                    WHEN t.type_template = 4 THEN 'تذكير بالمستحقات (واتساب)'
                                    WHEN t.type_template = 5 THEN 'إخطار بالمهام (واتساب)'
                                END AS type_template,
                                o.office_name,
                                CASE 
                                    WHEN t.for_whom = 1 THEN 'عميل'
                                    WHEN t.for_whom = 2 THEN 'محامي'
                                    WHEN t.for_whom = 3 THEN 'مساعد'
                                END AS for_whom_translated
                            FROM templates t
                            JOIN offices o ON t.office_id = o.office_id;");
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
