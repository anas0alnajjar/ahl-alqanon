<?php
include "../DB_connection.php";

$dateFilter = isset($_GET['dateFilter']) ? $_GET['dateFilter'] : 'all';
$clientFilter = isset($_GET['clientFilter']) ? $_GET['clientFilter'] : '';
$lawyerFilter = isset($_GET['lawyerFilter']) ? $_GET['lawyerFilter'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$dateCondition = '1=1'; // Default to all sessions
switch ($dateFilter) {
    case 'today':
        $dateCondition = "s.session_date = CURDATE()";
        break;
    case 'week':
        $dateCondition = "YEARWEEK(s.session_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $dateCondition = "MONTH(s.session_date) = MONTH(CURDATE()) AND YEAR(s.session_date) = YEAR(CURDATE())";
        break;
}

$sql = "SELECT 
            c.case_id, 
            c.case_title, 
            cl.first_name AS client_first_name, 
            cl.last_name AS client_last_name,
            cl.email AS client_email,
            s.session_hour,
            s.session_date,
            s.sessions_id,
            l.lawyer_name
        FROM 
            sessions s
        LEFT JOIN 
            cases c ON c.case_id = s.case_id
        LEFT JOIN 
            lawyer l ON l.lawyer_id = c.lawyer_id
        LEFT JOIN 
            clients cl ON c.client_id = cl.client_id
        WHERE $dateCondition";

if (!empty($clientFilter)) {
    $sql .= " AND c.client_id = :clientFilter";
}

if (!empty($lawyerFilter)) {
    $sql .= " AND c.lawyer_id = :lawyerFilter";
}

if (!empty($search)) {
    $sql .= " AND (
                c.case_title LIKE :search  
                OR cl.first_name LIKE :search 
                OR cl.last_name LIKE :search 
                OR l.lawyer_name LIKE :search 
                OR CONCAT(cl.first_name, ' ', cl.last_name) LIKE :search
            )";
}

$sql .= " ORDER BY 
            CASE
                WHEN s.session_date >= CURRENT_DATE() THEN 0
                ELSE 1
            END,
            s.session_date ASC";

$stmt = $conn->prepare($sql);

if (!empty($clientFilter)) {
    $stmt->bindValue(':clientFilter', (int)$clientFilter, PDO::PARAM_INT);
}

if (!empty($lawyerFilter)) {
    $stmt->bindValue(':lawyerFilter', (int)$lawyerFilter, PDO::PARAM_INT);
}

if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
}

$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($sessions)) {
    echo "<div class='alert alert-info mt-3' role='alert'>لا توجد جلسات مطابقة للبحث!</div>";
} else {
    $i = 0;
    echo '<div class="row" style="justify-content: space-around;">';
    foreach ($sessions as $session) {
        if ($i % 2 == 0) echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<div class="card mb-4">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title"><a href="case-view.php?id=' . $session['case_id'] . '&tab=sessions" style="text-decoration: none;">' . $session['case_title'] . '</a></h5>';
        echo '<p class="card-text">اسم الموكل: ' . $session['client_first_name'] . ' ' . $session['client_last_name'] . '</p>';
        echo '<p class="card-text">اسم المحامي: ' . $session['lawyer_name'] . '</p>';

        $target_date = new DateTime($session['session_date']);
        $current_date = new DateTime();
        $interval = $current_date->diff($target_date);
        $days_remaining = (int)$interval->format('%r%a');

        if ($days_remaining >= 0) {
            $days_remaining_text = ($days_remaining == 1) ? "1 يوم" : $days_remaining . ' أيام';
            $days_remaining_text = ($days_remaining == 0) ? "اليوم" : $days_remaining . ' أيام';
            echo "<p class='card-text'>الأيام المتبقية: " . $days_remaining_text . "</p>";
        } else {
            echo "<p style='color:green;'>انتهت منذ حوالي " . abs($days_remaining) . " يوم</p>";
        }

        if ($days_remaining >= 0) {
            echo '<button type="button" class="btn btn-link" data-toggle="modal" data-target="#sessionDetailsModal' . $session['sessions_id'] . '"><i class="fa fa-spinner fa-spin"></i></button>';
        }

        echo '<div class="modal fade" id="sessionDetailsModal' . $session['sessions_id'] . '" tabindex="-1" role="dialog" aria-labelledby="sessionDetailsModalLabel" aria-hidden="true">';
        echo '<div class="modal-dialog modal-sm" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h5 class="modal-title" id="sessionDetailsModalLabel">تفاصيل الجلسة</h5>';
        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button>';
        echo '</div>';
        echo '<div class="modal-body">';
        echo '<p><strong>تاريخ الجلسة:</strong> ' . $session['session_date'] . '</p>';
        echo '<p><strong>الساعة:</strong> ' . $session['session_hour'] . '</p>';
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<button style="float: left;" type="button" class="btn btn-danger btn-sm delete-button" data-id="' . $session['sessions_id'] . '">حذف</button>';

        echo '<input type="hidden" value="' . $session['sessions_id'] . '">';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        $i++;
        if ($i % 2 == 0) echo '</div>';
    }
    if ($i % 2 != 0) echo '</div>';
    echo '</div>';
}
?>
