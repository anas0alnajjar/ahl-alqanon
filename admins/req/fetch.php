<?php
// fetch.php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include "../../DB_connection.php";

    $admin_id = $_SESSION['user_id'];

    // جلب مكاتب الآدمن
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $admin_id);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        $stmt = $conn->prepare("
            SELECT 
                cases.case_id,
                cases.case_title,
                cases.case_number,
                cases.case_description,
                GROUP_CONCAT(DISTINCT CONCAT(client_plaintiff.first_name, ' ', client_plaintiff.last_name) SEPARATOR ' ➖ ') AS plaintiff_names,
                GROUP_CONCAT(DISTINCT CONCAT(adversaries.fname, ' ', adversaries.lname) SEPARATOR ' ➖ ') AS defendant_names,
                lawyer.lawyer_name AS lawyer_name,
                offices.office_name AS office_name,
                types_of_cases.type_case AS type_case,
                courts.court_name AS court_name1,
                departments.type AS department_names,
                CONCAT(clients.first_name, ' ', clients.last_name) AS client_name,
                IFNULL(
                    GROUP_CONCAT(
                        DISTINCT CONCAT('رقم الجلسة: ', sessions.session_number, ', تاريخ: ', sessions.session_date, ', ساعة: ', DATE_FORMAT(sessions.session_hour, '%h:%i %p'))
                        ORDER BY sessions.session_date ASC SEPARATOR '<br>'
                    ), 
                    'لا توجد جلسات قادمة'
                ) AS session_details
            FROM 
                cases
            LEFT JOIN 
                (SELECT 
                    cases.case_id,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(cases.plaintiff, ',', numbers.n), ',', -1) AS client_id_split
                 FROM 
                    cases
                 CROSS JOIN 
                    (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4) numbers
                 WHERE 
                    CHAR_LENGTH(cases.plaintiff) - CHAR_LENGTH(REPLACE(cases.plaintiff, ',', '')) >= numbers.n - 1
                ) AS plaintiff_split ON cases.case_id = plaintiff_split.case_id
            LEFT JOIN 
                clients AS client_plaintiff ON plaintiff_split.client_id_split = client_plaintiff.client_id
            LEFT JOIN 
                offices ON cases.office_id = offices.office_id
            LEFT JOIN 
                types_of_cases ON cases.case_type = types_of_cases.id
            LEFT JOIN 
                courts ON cases.court_name = courts.id
            LEFT JOIN 
                lawyer ON cases.lawyer_id = lawyer.lawyer_id
            LEFT JOIN 
                departments ON cases.department = departments.id
            LEFT JOIN 
                adversaries ON FIND_IN_SET(adversaries.id, cases.defendant)
            LEFT JOIN 
                clients ON cases.client_id = clients.client_id
            LEFT JOIN 
                sessions ON cases.case_id = sessions.case_id AND sessions.session_date >= CURDATE()
            WHERE 
                cases.office_id IN ($office_ids)
            GROUP BY 
                cases.case_id
        ");
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
