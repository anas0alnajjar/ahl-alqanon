<?php
include "../../DB_connection.php";

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$condition = isset($_POST['condition']) ? $_POST['condition'] : 'equals';
$search_text = isset($_POST['search_text']) ? $_POST['search_text'] : '';
$filter_type = isset($_POST['filter_type']) ? $_POST['filter_type'] : 'all';
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$records_per_page = isset($_POST['records_per_page']) ? intval($_POST['records_per_page']) : 10;
$offset = ($page - 1) * $records_per_page;

// جلب التاريخ الحالي
$current_date = date('Y-m-d');

// إعداد استعلامات SQL الأساسية
$sessions_query = "SELECT 
                    s.*, 
                    l1.lawyer_name AS lawyer_name, 
                    l2.lawyer_name AS assistant_lawyer_name,
                    c.case_title, 
                    'session' AS type, 
                    s.session_date AS date, 
                    s.session_hour AS time, 
                    cl.first_name AS client_first_name, 
                    cl.last_name AS client_last_name
                    FROM 
                    sessions s 
                    LEFT JOIN 
                    cases c ON s.case_id = c.case_id
                    LEFT JOIN 
                    lawyer l1 ON c.lawyer_id = l1.lawyer_id
                    LEFT JOIN 
                    lawyer l2 ON s.assistant_lawyer = l2.lawyer_id
                    LEFT JOIN 
                    clients cl ON c.client_id = cl.client_id";

$events_query = "SELECT e.*, l.lawyer_name, c.first_name AS client_first_name, c.last_name AS client_last_name, h.helper_name, 'event' as type, e.event_start_date as date, e.event_start_date as time
                 FROM events e 
                 LEFT JOIN lawyer l ON e.lawyer_id = l.lawyer_id 
                 LEFT JOIN clients c ON e.client_id = c.client_id 
                 LEFT JOIN helpers h ON e.helper_id = h.id";

$search_conditions_sessions = [];
$search_conditions_events = [];

// إذا كان هناك نص للبحث
if ($search_text) {
    $search_text = "%$search_text%";
    $search_conditions_sessions[] = "(s.notes LIKE '$search_text' 
                                      OR l1.lawyer_name LIKE '$search_text' 
                                      OR l2.lawyer_name LIKE '$search_text' 
                                      OR c.case_title LIKE '$search_text' 
                                      OR s.session_number LIKE '$search_text' 
                                      OR cl.first_name LIKE '$search_text' 
                                      OR cl.last_name LIKE '$search_text' 
                                      OR CONCAT(cl.first_name, ' ', cl.last_name) LIKE '$search_text')";
    
    $search_conditions_events[] = "(e.event_name LIKE '$search_text' 
                                    OR l.lawyer_name LIKE '$search_text' 
                                    OR c.first_name LIKE '$search_text' 
                                    OR c.last_name LIKE '$search_text' 
                                    OR CONCAT(c.first_name, ' ', c.last_name) LIKE '$search_text' 
                                    OR h.helper_name LIKE '$search_text')";
} else if ($start_date) {
    if ($condition == 'equals') {
        $search_conditions_sessions[] = "DATE(s.session_date) = '$start_date'";
        $search_conditions_events[] = "DATE(e.event_start_date) = '$start_date'";
    } elseif ($condition == 'greater') {
        $search_conditions_sessions[] = "DATE(s.session_date) > '$start_date'";
        $search_conditions_events[] = "DATE(e.event_start_date) > '$start_date'";
    } elseif ($condition == 'less') {
        $search_conditions_sessions[] = "DATE(s.session_date) < '$start_date'";
        $search_conditions_events[] = "DATE(e.event_start_date) < '$start_date'";
    }
} else {
    $search_conditions_sessions[] = "DATE(s.session_date) >= '$current_date'";
    $search_conditions_events[] = "DATE(e.event_start_date) >= '$current_date'";
}

// دمج الشروط في الاستعلامات
if (!empty($search_conditions_sessions)) {
    $sessions_query .= " WHERE " . implode(' AND ', $search_conditions_sessions);
}

if (!empty($search_conditions_events)) {
    $events_query .= " WHERE " . implode(' AND ', $search_conditions_events);
}

// إضافة تقسيم الصفحات
$sessions_query .= " ORDER BY s.session_date ASC, s.session_hour ASC LIMIT $records_per_page OFFSET $offset";
$events_query .= " ORDER BY e.event_start_date ASC, e.event_start_date ASC LIMIT $records_per_page OFFSET $offset";

// جلب البيانات
$sessions = [];
$events = [];

if ($filter_type == 'all' || $filter_type == 'sessions') {
    $sessions_stmt = $conn->query($sessions_query);
    while ($session = $sessions_stmt->fetch(PDO::FETCH_ASSOC)) {
        $sessions[] = $session;
    }
}

if ($filter_type == 'all' || $filter_type == 'events') {
    $events_stmt = $conn->query($events_query);
    while ($event = $events_stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = $event;
    }
}

// حساب العدد الإجمالي للسجلات
$total_sessions = $conn->query("SELECT COUNT(*) FROM sessions")->fetchColumn();
$total_events = $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();

$total_pages_sessions = ceil($total_sessions / $records_per_page);
$total_pages_events = ceil($total_events / $records_per_page);

// تحضير مخرجات HTML
$output_sessions = '';
$output_events = '';

$months = [
    1 => 'يناير',
    2 => 'فبراير',
    3 => 'مارس',
    4 => 'أبريل',
    5 => 'مايو',
    6 => 'يونيو',
    7 => 'يوليو',
    8 => 'أغسطس',
    9 => 'سبتمبر',
    10 => 'أكتوبر',
    11 => 'نوفمبر',
    12 => 'ديسمبر'
];

if (empty($sessions)) {
    $output_sessions .= '<div class="alert alert-info text-center m-auto w-50" role="alert">
                    <i class="bi bi-info-circle-fill"></i> لا يوجد أية جلسات 
                </div>';
} else {
    $current_date = '';
    foreach ($sessions as $item) {
        $session_date = strtotime($item['date']);
        $item_date = date("d", $session_date) . ' ' . $months[date("n", $session_date)] . ', ' . date("Y", $session_date);
        if ($item_date != $current_date) {
            $current_date = $item_date;
            $output_sessions .= '<div class="time-label">' . $current_date . '</div>';
        }

        $output_sessions .= '
        <div>
            <i class="timeline-icon bi bi-people text-bg-primary"></i>
            <div class="timeline-item">
                <span class="time"><i class="bi bi-clock-fill"></i> ' . date("h:i A", strtotime($item['time'])) . '</span>
                <h3 class="timeline-header highlight-text">جلسة رقم ' . $item['session_number'] . '</h3>
                <div class="timeline-body">';

        if (!empty($item['case_title'])) {
            $output_sessions .= 'قضية: ' . $item['case_title'] . '<br>';
        }

        if (!empty($item['lawyer_name'])) {
            $output_sessions .= 'المحامي: ' . $item['lawyer_name'] . '<br>';
        }
        if (!empty($item['assistant_lawyer_name'])) {
            $output_sessions .= 'محامي مساعد: ' . $item['assistant_lawyer_name'] . '<br>';
        }

        if (!empty($item['client_first_name']) && trim($item['client_first_name']) !== '') {
            $output_sessions .= 'عميل: ' . $item['client_first_name'] . ' ' . $item['client_last_name'] . '<br>';
        }
        if (!empty($item['notes'])) {
        $output_sessions .= '<div class="session-note">';
        $output_sessions .= '<i class="fas fa-sticky-note"></i>';
        $output_sessions .= '<p>' . nl2br(htmlspecialchars($item['notes'])) . '</p>';
        $output_sessions .= '</div>';
        }

        $output_sessions .= '</div>
                <div class="timeline-footer">
                    <a class="btn btn-primary btn-sm edit-button" data-id="' . $item['sessions_id'] . '">تعديل</a>
                    <a class="btn btn-danger btn-sm delete-button" data-id="' . $item['sessions_id'] . '">حذف</a>
                </div>
            </div>
        </div>';
    }
}

if (empty($events)) {
    $output_events .= '<div class="alert alert-info text-center m-auto w-50" role="alert">
                    <i class="bi bi-info-circle-fill"></i> لا يوجد أية أحداث 
                </div>';
} else {
    $current_date = '';
    foreach ($events as $item) {
        $event_date = strtotime($item['date']);
        $item_date = date("d", $event_date) . ' ' . $months[date("n", $event_date)] . ', ' . date("Y", $event_date);
        if ($item_date != $current_date) {
            $current_date = $item_date;
            $output_events .= '<div class="time-label">' . $current_date . '</div>';
        }

        $output_events .= '
        <div>
            <i class="timeline-icon bi bi-calendar-event text-bg-success"></i>
            <div class="timeline-item">
                <span class="time"><i class="bi bi-clock-fill"></i> ' . date("h:i A", strtotime($item['time'])) . '</span>
                <h3 class="timeline-header highlight-text">' . $item['event_name'] . '</h3>
                <div class="timeline-body">';

        $event_start_date = strtotime($item['event_start_date']);
        $event_end_date = strtotime($item['event_end_date']);

        $output_events .= 'من: ' . date("d", $event_start_date) . ' ' . $months[date("n", $event_start_date)] . ' ' . date("Y", $event_start_date) . ' , ' . date("h:i A", $event_start_date) .
            ' إلى: ' . date("d", $event_end_date) . ' ' . $months[date("n", $event_end_date)] . ' ' . date("Y", $event_end_date) . ' , ' . date("h:i A", $event_end_date) . '<br>';

        if (!empty($item['lawyer_name'])) {
            $output_events .= 'محامي: ' . $item['lawyer_name'] . '<br>';
        }

        if (!empty($item['client_first_name']) && trim($item['client_first_name']) !== '') {
            $output_events .= 'عميل: ' . $item['client_first_name'] . ' ' . $item['client_last_name'] . '<br>';
        }

        if (!empty($item['helper_name'])) {
            $output_events .= 'مساعد: ' . $item['helper_name'] . '<br>';
        }

        $output_events .= '</div>
                <div class="timeline-footer">
                    <a class="btn btn-primary btn-sm edit-event-btn" data-id="' . $item['event_id'] . '">تعديل</a>
                    <a class="btn btn-danger btn-sm delete-event-btn" data-id="' . $item['event_id'] . '">حذف</a>
                </div>
            </div>
        </div>';
    }
}

// إعادة البيانات كـ JSON
$response = [
    'sessions' => $output_sessions,
    'events' => $output_events,
    'total_pages_sessions' => $total_pages_sessions,
    'total_pages_events' => $total_pages_events,
    'current_page' => $page
];

echo json_encode($response);
?>
