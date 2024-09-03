<?php
session_start();

include "../DB_connection.php";
include "logo.php";
include 'permissions_script.php';

if ($pages['sessions']['read'] == 0) {
    header("Location: home.php");
    exit();
}

include "get_office.php";
$user_id = $_SESSION['user_id'];
$office_id = getOfficeId($conn, $user_id);

function getClients($conn, $helper_id) {
    $sql = "SELECT DISTINCT cl.client_id, cl.first_name, cl.last_name 
            FROM clients cl
            JOIN cases ca ON cl.client_id = ca.client_id
            WHERE FIND_IN_SET(:helper_id, ca.helper_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':helper_id', $helper_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$clients = getClients($conn, $user_id);

function getCases($conn, $helper_id) {
    $sql = "SELECT DISTINCT c.case_id, c.case_title 
            FROM cases c
            LEFT JOIN sessions s ON c.case_id = s.case_id
            WHERE FIND_IN_SET(:helper_id, c.helper_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':helper_id', $helper_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
    $page_number = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
    $total_records_per_page = 6;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $cases = getCases($conn, $user_id);
    $dateFilter = isset($_GET['dateFilter']) ? $_GET['dateFilter'] : 'all';
    $clientFilter = isset($_GET['clientFilter']) ? $_GET['clientFilter'] : '';


    $sql = "SELECT 
            c.case_id, 
            c.case_title, 
            cl.first_name AS client_first_name, 
            cl.last_name AS client_last_name,
            cl.email AS client_email,
            s.session_hour,
            s.session_date,
            s.session_date_hjri,
            s.sessions_id,
            s.notes,
            l.lawyer_name,
            al.lawyer_name AS assistant_lawyer_name
        FROM 
            sessions s
        LEFT JOIN 
            cases c ON c.case_id = s.case_id
        LEFT JOIN 
            lawyer l ON l.lawyer_id = c.lawyer_id
        LEFT JOIN 
            lawyer al ON al.lawyer_id = s.assistant_lawyer
        LEFT JOIN 
            clients cl ON c.client_id = cl.client_id
        WHERE FIND_IN_SET(:helper_id, c.helper_name)";

if (!empty($search)) {
    $sql .= " AND (
                c.case_title LIKE :search  
                OR cl.first_name LIKE :search 
                OR cl.last_name LIKE :search 
                OR l.lawyer_name LIKE :search 
                OR al.lawyer_name LIKE :search 
                OR CONCAT(cl.first_name, ' ', cl.last_name) LIKE :search
            )";
}

if ($dateFilter == 'today') {
    $sql .= " AND DATE(s.session_date) = CURDATE()";
} elseif ($dateFilter == 'week') {
    $sql .= " AND s.session_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($dateFilter == 'month') {
    $sql .= " AND MONTH(s.session_date) = MONTH(CURDATE()) AND YEAR(s.session_date) = YEAR(CURDATE())";
}

if (!empty($clientFilter)) {
    $sql .= " AND cl.client_id = :clientFilter";
}

$sql .= " ORDER BY 
            CASE
                WHEN s.session_date >= CURRENT_DATE() THEN 0 
                ELSE 1 
            END,
            s.session_date ASC
            LIMIT :offset, :total_records_per_page";

$stmt = $conn->prepare($sql); 
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT); 
$stmt->bindValue(':total_records_per_page', (int)$total_records_per_page, PDO::PARAM_INT); 
$stmt->bindValue(':helper_id', $user_id, PDO::PARAM_INT); 

if (!empty($search)) { 
    $searchParam = "%$search%"; 
    $stmt->bindValue(':search', $searchParam); 
}

if (!empty($clientFilter)) {
    $stmt->bindValue(':clientFilter', $clientFilter, PDO::PARAM_INT);
}

$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_records_sql = "SELECT 
COUNT(c.case_id)
FROM 
sessions s
LEFT JOIN 
cases c ON c.case_id = s.case_id
LEFT JOIN 
lawyer l ON l.lawyer_id = c.lawyer_id
LEFT JOIN 
lawyer al ON al.lawyer_id = s.assistant_lawyer
LEFT JOIN 
clients cl ON c.client_id = cl.client_id 
WHERE FIND_IN_SET(:helper_id, c.helper_name)";

if (!empty($search)) {
$total_records_sql .= " AND (
    c.case_title LIKE :search  
    OR cl.first_name LIKE :search
    OR cl.last_name LIKE :search 
    OR l.lawyer_name LIKE :search 
    OR al.lawyer_name LIKE :search 
    OR CONCAT(cl.first_name, ' ', cl.last_name) LIKE :search
)";
}

if ($dateFilter == 'today') {
$total_records_sql .= " AND DATE(s.session_date) = CURDATE()";
} elseif ($dateFilter == 'week') {
$total_records_sql .= " AND s.session_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($dateFilter == 'month') {
$total_records_sql .= " AND MONTH(s.session_date) = MONTH(CURDATE()) AND YEAR(s.session_date) = YEAR(CURDATE())";
}

if (!empty($clientFilter)) {
$total_records_sql .= " AND cl.client_id = :clientFilter";
}

$total_records_stmt = $conn->prepare($total_records_sql);
$total_records_stmt->bindValue(':helper_id', $user_id, PDO::PARAM_INT);

if (!empty($search)) { 
$total_records_stmt->bindValue(':search', $searchParam); 
}

if (!empty($clientFilter)) {
$total_records_stmt->bindValue(':clientFilter', $clientFilter, PDO::PARAM_INT);
}

$total_records_stmt->execute();
$total_records = $total_records_stmt->fetchColumn();
$total_pages = ceil($total_records / $total_records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions</title>
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    

    <!-- تضمين ملفات SweetAlert2 JavaScript -->
    
    <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
    <style>
    #showDates.rotated {
        transform: rotate(180deg);
    }
    *{
        direction: rtl;
    }
    .card {
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.card-text {
    font-size: 16px;
    margin-bottom: 5px;
}

.card-link {
    color: blue;
    text-decoration: none;
}

.card-link:hover {
    text-decoration: underline;
}
button:focus{
    outline: none;
    box-shadow: none !important;
    color:red;
}

    .bootstrap-datetimepicker-widget{
        top: 0 !important;
        bottom: auto !important;
        right: auto !important;
        left: 0 !important;
    }
    .data-switch-button{
        display:none !important;
    }
            .modal-body .form-row {
            margin-bottom: 15px;
        }
        .modal-body .form-group label {
            margin-bottom: 5px;
        }
        .modal-body .form-group {
            margin-bottom: 0;
        }
        .progress {
    height: 20px;
    background-color: #f5f5f5;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
}

.progress-bar {
    height: 100%;
    transition: width 0.4s ease;
    border-radius: 10px;
}
.d-none {
    display: none;
}

    </style>

</head>
<body>
      <!-- Modal -->
 <div class="modal fade" id="sessionModal" tabindex="-1" role="dialog" aria-labelledby="sessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionModalLabel">إضافة جلسة جديدة</h5>
                <button style="position: absolute;left: 15px;" type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sessionForm">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>رقم الجلسة</label>
                            <input type="text" class="form-control" name="session_number" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>لأي قضية</label>
                            <select class="form-control" name="case_id" required>
                                <option selected value="" disabled>اختر قضية</option>
                                <?php if (!empty($cases)): ?>
                                    <?php foreach ($cases as $case): ?>
                                        <option value="<?= htmlspecialchars($case['case_id']) ?>"><?= htmlspecialchars($case['case_title']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">لا توجد قضايا متاحة</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>تاريخ الجلسة ميلادي</label>
                            <input type="date" class="form-control geo-date-input gregorian" name="session_date_gregorian" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>تاريخ الجلسة هجري</label>
                            <input type="text" class="form-control hijri-date-input hegira" name="session_date_hijri" required autocomplete="off">
                        </div>
                    </div>
                    <?php if ($pages['add_old_session']['add'] == 0) : ?>
                    <div class="row" id="error">
                        <p style="color:red;font-size:smaller">غير مسموح لك إضافة جلسة بتاريخ قديم!</p>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>ساعة الجلسة</label>
                            <input type="time" class="form-control" name="session_hour" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>الملاحظات</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>

                    
                    <button type="submit" class="btn btn-primary btn-block">حفظ</button>
                    <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">إغلاق</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editSessionModal" tabindex="-1" role="dialog" aria-labelledby="editSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSessionModalLabel">تعديل جلسة</h5>
                <button style="position: absolute;left: 15px;" type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSessionForm">
                    <input type="hidden" name="session_id" id="editSessionId">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>رقم الجلسة</label>
                            <input type="text" class="form-control" name="session_number" id="editSessionNumber" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>لأي قضية</label>
                            <select class="form-control" name="case_id" id="editCaseId" required>
                                <option selected value="" disabled>اختر قضية</option>
                                <?php if (!empty($cases)): ?>
                                    <?php foreach ($cases as $case): ?>
                                        <option value="<?= htmlspecialchars($case['case_id']) ?>"><?= htmlspecialchars($case['case_title']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">لا توجد قضايا متاحة</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>تاريخ الجلسة ميلادي</label>
                            <input type="date" class="form-control geo-date-input" name="session_date_gregorian" id="editSessionDateGregorian" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>تاريخ الجلسة هجري</label>
                            <input type="text" class="form-control hijri-date-input" name="session_date_hijri" id="editSessionDateHijri" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>ساعة الجلسة</label>
                            <input type="time" class="form-control" name="session_hour" id="editSessionHour" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>الملاحظات</label>
                            <textarea class="form-control" name="notes" id="editNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">حفظ</button>
                    <button type="button" class="btn btn-secondary" id="closeEditModal" data-dismiss="modal">إغلاق</button>
                </form>
            </div>
        </div>
    </div>
</div>
    

    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr;">
            <a href="home.php" class="btn btn-light">الرئيسية</a>
            <?php if ($pages['sessions']['add']) : ?>
            <button id="addSessionBtn" class="btn btn-dark" data-toggle="modal" data-target="#sessionModal">إضافة جلسة جديدة</button>
            <?php endif; ?>
        </div>
        <form action="sessions.php" class="mt-3" method="GET" id="searchForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <select id="dateFilter" class="form-control">
                        <option value="all">كل الجلسات</option>
                        <option value="today" <?= ($dateFilter == 'today') ? 'selected' : '' ?>>جلسات اليوم</option>
                        <option value="week" <?= ($dateFilter == 'week') ? 'selected' : '' ?>>جلسات الأسبوع</option>
                        <option value="month" <?= ($dateFilter == 'month') ? 'selected' : '' ?>>جلسات الشهر</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="clientFilter" class="form-control">
                        <option value="">كل الموكلين</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= htmlspecialchars($client['client_id']) ?>" <?= ($clientFilter == $client['client_id']) ? 'selected' : '' ?>><?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="input-group mb-3" style="direction: ltr;">
            
                <input type="text" class="form-control" name="search" placeholder="ابحث هنا..." value="<?php echo htmlentities($search); ?>">
                
                <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                
            </div>
             
            
        </form>
        

        <?php if (empty($sessions)): ?>
            <div class="alert alert-info mt-3 n-table" role="alert">لا يوجد جلسات!</div>
        <?php endif; ?>


        
        <div class="row" style="justify-content: space-around;">
    <?php $i = 0; ?>
    <?php
    $current_date = new DateTime();
    $total_days = 30; // القيمة الثابتة للأيام المتبقية
    $i = 0;
    ?>

    <?php foreach ($sessions as $session): ?>
        <?php if ($i % 2 == 0): ?>
        <div class="row" style="padding: 0;">
        <?php endif; ?>
        <div class="col-md-6 d-flex align-items-stretch">
            <div class="card mb-4 flex-fill">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                        <a href="case-view.php?id=<?= $session['case_id'] ?>&tab=sessions" style="text-decoration: none;"><?= $session['case_title'] ?></a>
                    </h5>
                    <p class="card-text"><strong>اسم الموكل:</strong> <?= $session['client_first_name'] . ' ' . $session['client_last_name'] ?></p>
                    <p class="card-text"><strong>اسم المحامي:</strong> <?= $session['lawyer_name'] ?></p>
                    
                    <?php if (!empty($session['assistant_lawyer_name'])): ?>
                        <p class="card-text"><strong>المحامي المساعد:</strong> <?= $session['assistant_lawyer_name'] ?></p>
                    <?php endif; ?>
                    
                    <?php
                        $current_date = new DateTime(); // تأكد من تعريف التاريخ الحالي

                        $target_date = new DateTime($session['session_date']);
                        $interval = $current_date->diff($target_date);
                        $days_remaining = (int)$interval->format('%r%a');

                        if ($days_remaining > 30) {
                            $percentage_remaining = 0; // نسبة مميزة لحالة الأيام المتبقية أكبر من 30 يومًا
                            $progress_color = 'rgba(0, 123, 255, 0.7)'; // لون مختلف
                            $is_long_wait = true;
                        } else if ($days_remaining >= 0) {
                            $percentage_remaining = (1 - ($days_remaining / $total_days)) * 100;
                            $progress_color = 'rgba(40, 167, 69, 0.7)'; // أخضر
                            if ($percentage_remaining <= 25) {
                                $progress_color = 'rgba(220, 53, 69, 0.7)'; // أحمر
                            } elseif ($percentage_remaining <= 50) {
                                $progress_color = 'rgba(255, 193, 7, 0.7)'; // أصفر
                            }
                            $is_long_wait = false;
                        } else {
                            $days_since_end = abs($days_remaining);
                            $percentage_remaining = 100;
                            $progress_color = 'rgba(26, 132, 211, 0.7)'; //
                            $is_long_wait = false;
                        }

                        if ($days_remaining >= 0) {
                            $days_remaining_text = ($days_remaining == 0) ? "اليوم" : $days_remaining . ' أيام';
                            echo '<p class="card-text"><strong>الأيام المتبقية:</strong> ' . $days_remaining_text . '</p>';
                        } else {
                            echo '<p class="card-text" style="color: green;"><strong>انتهت منذ:</strong> ' . $days_since_end . ' أيام</p>';
                        }
                        ?>
                        <button style="text-decoration:none;" type="button" class="btn btn-link mt-auto" data-bs-toggle="modal" data-bs-target="#sessionDetailsModal<?= $session['sessions_id'] ?>">
                            <i class="fa fa-info-circle"></i> تفاصيل الجلسة
                        </button>
                    
                    <canvas id="progressChart<?= $session['sessions_id'] ?>" width="400" height="50"
                            data-percentage="<?= $percentage_remaining ?>"
                            data-color="<?= $progress_color ?>"
                            data-is-long-wait="<?= $is_long_wait ?>"></canvas>
    

                        
    
                    <div class="modal fade details" id="sessionDetailsModal<?= $session['sessions_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="sessionDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="sessionDetailsModalLabel">تفاصيل الجلسة</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p class="card-text"><strong>التاريخ ميلادي:</strong> <?= $session['session_date'] ?></p>
                                    <p class="card-text"><strong>التاريخ هجري:</strong> <?= $session['session_date_hjri'] ?></p>
                                    <p class="card-text"><strong>الساعة:</strong> <?= $session['session_hour'] ?></p>
                                    <?php if (!empty($session['notes'])): ?>
                                        <p class="card-text"><strong>الملاحظات:</strong> <?= $session['notes'] ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close" data-dismiss="modal">إغلاق</button>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="d-flex justify-content-end mt-2">
                    <?php if ($pages['sessions']['write']) : ?>
                        <button type="button" class="btn btn-warning btn-sm edit-button mx-2" data-id="<?= $session['sessions_id'] ?>">تعديل</button>
                    <?php endif; ?>
                    <?php if ($pages['sessions']['delete']) : ?>
                        <button type="button" class="btn btn-danger btn-sm delete-button" data-id="<?= $session['sessions_id'] ?>">حذف</button>
                    <?php endif; ?>
                    </div>
    
                    <input type="hidden" value="<?= $session['sessions_id'] ?>">
                </div>
            </div>
        </div>
        <?php
        $i++;
        if ($i % 2 == 0) echo '</div>';
        ?>
    <?php endforeach; ?>
    <?php if ($i % 2 != 0) echo '</div>'; ?>
</div>
<!-- Pagination -->
<nav aria-label="Page navigation example">
    <ul class="pagination" style="direction: ltr;float:right;">
        <!-- Previous Page -->
        <li class="page-item <?php echo ($page_number <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_number=<?php echo ($page_number - 1); ?>&search=<?= htmlentities($search) ?>&dateFilter=<?= htmlentities($dateFilter) ?>&clientFilter=<?= htmlentities($clientFilter) ?>">Previous</a>
        </li>

        <!-- First Page -->
        <?php if ($page_number > 3): ?>
            <li class="page-item">
                <a class="page-link" href="?page_number=1&search=<?= htmlentities($search) ?>&dateFilter=<?= htmlentities($dateFilter) ?>&clientFilter=<?= htmlentities($clientFilter) ?>">1</a>
            </li>
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = max(1, $page_number - 2); $i <= min($total_pages, $page_number + 2); $i++): ?>
            <li class="page-item <?php echo ($page_number == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?page_number=<?php echo $i; ?>&search=<?= htmlentities($search) ?>&dateFilter=<?= htmlentities($dateFilter) ?>&clientFilter=<?= htmlentities($clientFilter) ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Last Page -->
        <?php if ($page_number < $total_pages - 2): ?>
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page_number=<?php echo $total_pages; ?>&search=<?= htmlentities($search) ?>&dateFilter=<?= htmlentities($dateFilter) ?>&clientFilter=<?= htmlentities($clientFilter) ?>"><?php echo $total_pages; ?></a>
            </li>
        <?php endif; ?>

        <!-- Next Page -->
        <li class="page-item <?php echo ($page_number >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_number=<?php echo ($page_number + 1); ?>&search=<?= htmlentities($search) ?>&dateFilter=<?= htmlentities($dateFilter) ?>&clientFilter=<?= htmlentities($clientFilter) ?>">Next</a>
        </li>
    </ul>
</nav>
        </div>


     


        
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
<script src="../js/bootstrap-hijri-datetimepicker.js?v2"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // تغيير عرض العناصر ذات الكلاس due_date عند النقر على الزر #showDates
        $("#showDates").click(function() {
            $(".due_date").toggle(); // تبديل عرض العناصر
            $(this).toggleClass('rotated'); // تبديل الكلاس rotated
        });
    });
</script>
    
<script>
$(document).ready(function() {
    $('.delete-button').click(function() {
        var sessionId = $(this).data('id');

        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'req/delete_session_v2.php',
                    type: 'POST',
                    data: { id: sessionId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'تم الحذف!',
                                'تم حذف الجلسة بنجاح.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'خطأ!',
                                response.error || 'حدث خطأ أثناء حذف الجلسة.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage;
                        try {
                            errorMessage = JSON.parse(xhr.responseText).error;
                        } catch (e) {
                            errorMessage = 'حدث خطأ في الاتصال بالخادم.';
                        }
                        Swal.fire(
                            'خطأ!',
                            errorMessage + ': ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>

<script>
     $('#addSessionBtn').click(function() {
        $('#sessionModal').modal('show');
    });
     $('#closeEditModal,.btn-close,#closeModal,.close-details, .close').click(function() {
        $('#editSessionModal,#sessionModal,.details').modal('hide');
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function convertToHijri(gregorianDate) {
        if (gregorianDate) {
            return moment(gregorianDate, 'YYYY-MM-DD').format('iYYYY-iMM-iDD');
        }
        return '';
    }

    function convertToGregorian(hijriDate) {
        if (hijriDate) {
            return moment(hijriDate, 'iYYYY-iMM-iDD').format('YYYY-MM-DD');
        }
        return '';
    }

    function attachDateChangeEvents() {
        var gregorianInputs = document.querySelectorAll('.geo-date-input');
        var hijriInputs = document.querySelectorAll('.hijri-date-input');

        gregorianInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                var hijriField = input.closest('form').querySelector('.hijri-date-input');
                hijriField.value = convertToHijri(input.value);
            });
        });

        hijriInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                var gregorianField = input.closest('form').querySelector('.geo-date-input');
                gregorianField.value = convertToGregorian(input.value);
            });

            $(input).hijriDatePicker({
                locale: "ar-sa",
                format: "DD-MM-YYYY",
                hijriFormat: "iYYYY-iMM-iDD",
                dayViewHeaderFormat: "MMMM YYYY",
                hijriDayViewHeaderFormat: "iMMMM iYYYY",
                showSwitcher: true,
                allowInputToggle: true,
                useCurrent: false,
                isRTL: true,
                viewMode: 'days',
                keepOpen: false,
                hijri: true,
                debug: false,
                showClear: true,
                showClose: true
            }).on('dp.change', function(e) {
                var gregorianField = input.closest('form').querySelector('.geo-date-input');
                gregorianField.value = convertToGregorian(e.date.format('iYYYY-iMM-iDD'));
            });
        });
    }

    $('#sessionModal, #editSessionModal').on('shown.bs.modal', function () {
        attachDateChangeEvents();
    });

    // فتح مودل التعديل عند النقر على زر التعديل
    document.querySelectorAll('.edit-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var sessionId = this.getAttribute('data-id');
            $.ajax({
                url: 'req/get_session.php',
                type: 'GET',
                data: { id: sessionId },
                success: function(response) {
                    if (response.success) {
                        var session = response.session;
                        $('#editSessionId').val(session.sessions_id);
                        $('#editSessionNumber').val(session.session_number);
                        $('#editCaseId').val(session.case_id);
                        $('#editSessionDateGregorian').val(session.session_date);
                        $('#editSessionDateHijri').val(session.session_date_hjri);
                        $('#editSessionHour').val(session.session_hour);
                        $('#editNotes').val(session.notes);
                        
                        $('#editSessionModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: response.message
                        });
                    }
                }
            });
        });
    });

    // التحقق من أن جميع الحقول ممتلئة
    function validateForm() {
        var isValid = true;
        $('#editSessionForm').find('input[required], select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return isValid;
    }

    $('#editSessionForm').submit(function(e) {
        e.preventDefault();
        if (!validateForm()) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'يرجى تعبئة جميع الحقول المطلوبة'
            });
            return;
        }

        var formData = $(this).serialize();
        
        $.ajax({
            url: 'req/update_session.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم تحديث البيانات بنجاح .',
                        showConfirmButton: false,
                        timer: 3000,
                        willClose: function() {
                            window.location.reload(); 
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
                });
            }
        });
    });

    document.getElementById("sessionForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let formData = new FormData(this);

        fetch('req/save_session.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (error) {
                throw new Error('Invalid JSON: ' + text);
            }
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم حفظ البيانات بنجاح.',
                    showConfirmButton: false,
                    timer: 2000,
                    willClose: function() {
                        window.location.reload();
                    }
                });
                document.getElementById("sessionForm").reset();
                $('#sessionModal').modal('hide');
            } else {
                Swal.fire({
                    title: 'خطأ!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'خطأ!',
                text: 'حدث خطأ غير متوقع: ' + error.message,
                icon: 'error',
                confirmButtonText: 'موافق'
            });
            console.error('Error:', error);
        });
    });
});
</script>
<script>
$(document).ready(function() {
    // Function to get filter values and update the URL
    function updateFilters() {
        var dateFilter = $('#dateFilter').val();
        var clientFilter = $('#clientFilter').val();
        var search = $('input[name="search"]').val();

        var url = new URL(window.location.href);
        url.searchParams.set('dateFilter', dateFilter);
        url.searchParams.set('clientFilter', clientFilter);
        url.searchParams.set('search', search);
        window.location.href = url.toString();
    }

    // Attach change event to the filters
    $('#dateFilter').change(function() {
        updateFilters();
    });

    $('#clientFilter').change(function() {
        updateFilters();
    });

    // Handle search form submission
    $('#searchForm').submit(function(event) {
        event.preventDefault(); // Prevent form from submitting traditionally
        updateFilters();
    });
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('canvas[id^="progressChart"]').forEach(function(canvas) {
        var ctx = canvas.getContext('2d');
        var percentage = canvas.getAttribute('data-percentage');
        var color = canvas.getAttribute('data-color');
        var isLongWait = canvas.getAttribute('data-is-long-wait') === 'true';
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [''],
                datasets: [{
                    label: 'نسبة التقدم',
                    data: [percentage],
                    backgroundColor: isLongWait ? 'rgba(0, 123, 255, 0.7)' : color,
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    barThickness: isLongWait ? 50 : 20,
                    borderDash: isLongWait ? [5, 5] : []
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        display: false
                    },
                    y: {
                        display: false
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });
    });
});
</script>

<?php if ($pages['add_old_session']['add'] == 0) : ?>
<script>
    function validateDate(input) {
        var today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        var hijriInput = input.closest('.form-group').parentElement.querySelector('.hegira');
        var errorDiv = document.getElementById('error');

        if (input.value < today) {
            input.value = ''; // تفريغ الحقل الميلادي
            if (hijriInput) {
                hijriInput.value = ''; // تفريغ الحقل الهجري
                hijriInput.classList.add('is-invalid');
                hijriInput.classList.remove('is-valid');
            }
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');

            if (errorDiv) {
                errorDiv.classList.remove('d-none'); // إظهار الرسالة
            }
        } else {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
            if (hijriInput) {
                hijriInput.classList.add('is-valid');
                hijriInput.classList.remove('is-invalid');
            }

            if (errorDiv) {
                errorDiv.classList.add('d-none'); // إخفاء الرسالة
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        function attachEventListeners() {
            // التحقق من تواريخ الجلسات عند تغيير التاريخ الميلادي
            document.querySelectorAll('.gregorian').forEach(function(input) {
                input.addEventListener('change', function() {
                    validateDate(input);
                });
            });
        }

        attachEventListeners();

        // مراقبة التغييرات في قيم الحقول بشكل دوري
        var prevGeoValues = new Map();

        setInterval(function() {
            document.querySelectorAll('.gregorian').forEach(function(geoInput) {
                var currentValue = geoInput.value;
                var prevValue = prevGeoValues.get(geoInput) || '';
                if (currentValue !== prevValue) {
                    prevGeoValues.set(geoInput, currentValue);
                    validateDate(geoInput);
                }
            });
        }, 1000); // تحقق كل ثانية
    });
</script>
<?php endif; ?>




</body>

</html>

    <?php
} else {
    header("Location: ../login.php");
    exit;
}
?>
