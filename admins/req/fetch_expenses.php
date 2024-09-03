<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include "../../DB_connection.php";

    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $office_id = isset($_POST['office_id']) ? $_POST['office_id'] : null;

    // جلب المكاتب المرتبطة بالآدمن
    $admin_id = $_SESSION['user_id'];
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (empty($offices)) {
        echo json_encode([]);
        exit;
    }

    $office_ids = implode(',', $offices);

    $query = "SELECT 
        e.id AS expense_id,
        e.case_id,
        e.pay_date AS pay_date,
        ca.office_id AS office_id,
        of.office_name,
        e.amount AS amount,
        'مصاريف جلسات' AS type,
        e.notes_expenses AS notes,
        e.pay_date_hijri AS pay_date_hijri,
        'sessions' AS source
    FROM 
        expenses e
    LEFT JOIN 
        cases ca ON e.case_id = ca.case_id
    LEFT JOIN 
        offices of ON ca.office_id = of.office_id
    WHERE 
        ca.office_id IN ($office_ids)";

    if ($office_id) {
        $query .= " AND ca.office_id = :office_id";
    }
    if ($start_date) {
        $query .= " AND e.pay_date >= :start_date";
    }
    if ($end_date) {
        $query .= " AND e.pay_date <= :end_date";
    }

    $query .= " UNION ALL SELECT 
        o.id AS expense_id,
        NULL AS case_id,
        o.pay_date AS pay_date,
        o.office_id AS office_id,
        of.office_name,
        o.amount AS amount,
        c.type AS type,
        o.notes_expenses AS notes,
        o.pay_date_hijri AS pay_date_hijri,
        'genral' AS source
    FROM 
        overhead_costs o
    LEFT JOIN 
        offices of ON o.office_id = of.office_id
    LEFT JOIN 
        costs_type c ON o.type_id = c.id
    WHERE 
        o.office_id IN ($office_ids)";

    if ($office_id) {
        $query .= " AND o.office_id = :office_id";
    }
    if ($start_date) {
        $query .= " AND o.pay_date >= :start_date";
    }
    if ($end_date) {
        $query .= " AND o.pay_date <= :end_date";
    }

    $stmt = $conn->prepare($query);
    
    if ($office_id) {
        $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
    }
    if ($start_date) {
        $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    }
    if ($end_date) {
        $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
    
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
