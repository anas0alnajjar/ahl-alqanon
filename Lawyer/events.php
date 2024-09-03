<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {
        include "../DB_connection.php";
        include "logo.php";
        function getCases($conn, $user_id)
        {
            $sql = "SELECT case_id, case_title FROM cases WHERE lawyer_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() >= 1) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }

        $user_id = $_SESSION['user_id'];
        $cases = getCases($conn, $user_id);
        

        function getLawyers($conn)
        {
            $sql = "SELECT lawyer_id, lawyer_name FROM lawyer";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        
        $lawyers = getLawyers($conn);
        include 'permissions_script.php';
        if ($pages['events']['read'] == 0) {
            header("Location: home.php");
            exit();
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lawyer - Events</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
            <link rel="icon" href="../img/<?= $setting['logo'] ?>">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@3/dark.css">
            <link rel="stylesheet" href="../css/style.css">
            <link rel="stylesheet" href="../css/adminlte.css">

            <link rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css"
                integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


            <!-- تضمين ملفات SweetAlert2 JavaScript -->

            <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"
                integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>


            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <style>
                * {
                    direction: rtl;
                }

                .sticky-header {
                    position: -webkit-sticky;
                    position: sticky;
                    top: 0;
                    margin-bottom: 20px;
                    background: white;
                    z-index: 10;
                    padding: 0px 25px;
                    /* تباعد داخلي */
                    color: #333;
                    /* لون النص */
                    text-align: right;
                    /* محاذاة النص إلى الوسط */
                }

                .dropdown-menu {
                    min-width: 100px;
                }

                .dropdown-item i {
                    margin-right: 5px;
                }


                #recordsPerPageDropdown {

                    margin-left: 10px;
                }

                .filter-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: space-between;
                    align-items: center;
                }

                .dropdown-menu {
                    direction: rtl;
                    text-align: right;
                }

                .form-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    align-items: center;
                }

                /* Styling the select to look like btn btn-primary */
                .form-select.btn-primary {
                    color: #fff;
                    background-color: #007bff;
                    border-color: #007bff;
                    padding: .375rem .75rem;
                    font-size: 1rem;
                    line-height: 1.5;
                    border-radius: .25rem;
                    display: inline-block;
                    text-align: center;
                    vertical-align: middle;
                    user-select: none;
                    border: 1px solid transparent;
                    appearance: none;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    position: relative;
                    padding-right: 2.25rem;
                    /* space for the arrow */
                }

                .form-select.btn-primary:focus,
                .form-select.btn-primary:hover {
                    color: #fff;
                    background-color: #0056b3;
                    border-color: #004085;
                    outline: none;
                    box-shadow: none;
                }

                .form-select.btn-primary::after {
                    content: "\25BC";
                    /* arrow character */
                    position: absolute;
                    right: .75rem;
                    top: 50%;
                    transform: translateY(-50%);
                    pointer-events: none;
                    color: #fff;
                }

                .form-select.btn-primary option {
                    background-color: #fff;
                    color: #000;
                }

                .app-main .app-content-header {
                    padding: 1rem 25px;
                }
                .bootstrap-datetimepicker-widget{
                top: auto !important;
                bottom: 0 !important;
                right: auto !important;
                left: auto !important;
            }
    .data-switch-button{
        display:none !important;
    }
            </style>
        </head>

        <body>
            <?php include "inc/navbar.php"; ?>
            <div class="app-wrapper">
                <main class="app-main">
                    <div class="sticky-header">
                        <div class=" input-group mt-3 text-center p-2 w-auto"
                            style="max-width: 100%; min-width: 80%; direction: ltr;">
                            <input type="search" class="form-control" id="search_text" placeholder="ابحث هنا...">
                            <div class="input-group-append">
                                <button style="border-radius: 0;" class="btn btn-primary">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="app-content-header ">
                        <div class="container-fluid w-100 " style="direction:rtl;">

                            <div class="container mt-4 mx-0 text-end" id="filter">
                                <div class="filter-container">
                                    <div id="dropdown" class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            id="recordsPerPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            عرض <span id="recordsPerPage">5</span> سجلات
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="recordsPerPageDropdown">
                                            <li><a class="dropdown-item" href="#" onclick="setRecordsPerPage(5)">5</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="setRecordsPerPage(10)">10</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="setRecordsPerPage(20)">20</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="setRecordsPerPage(50)">50</a></li>
                                        </ul>
                                        <select id="filter_type" class="form-select btn-primary w-auto d-inline-block ml-2">
                                            <option value="all">الجلسات /الأحداث</option>
                                            <option value="sessions">الجلسات</option>
                                            <option value="events">الأحداث</option>
                                        </select>
                                    </div>
                                    <div class="text-start">
                                        <span class="filter-icon" data-bs-toggle="collapse" data-bs-target="#formContainer">
                                            <i class="fas fa-filter"></i> اضغط للفلترة
                                        </span>
                                    </div>
                                </div>
                                <div class="collapse mt-3" id="formContainer">
                                    <div class="form-container">
                                        <select name="" id="condition" class="form-select w-auto">
                                            <option value="equals" selected>يساوي</option>
                                            <option value="greater">أكبر من</option>
                                            <option value="less">أقل من</option>
                                        </select>
                                        <label for="start_date">التاريخ</label>
                                        <input type="date" id="start_date" class="form-control w-auto">

                                        <button id="filterBtn" class="btn btn-primary">تصفية</button>
                                        <button id="defaultBtn" class="btn btn-secondary">عرض الافتراضي</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>



                    <div class="floating-button-add" id="floatingButton">
                        <i class="fas fa-plus"></i>
                    </div>
                    <?php if ($pages['events']['add'] || $pages['sessions']['add']) : ?>
                    <div class="filter-options" id="filterOptions">
                       <?php  if ($pages['events']['add'] == 1) { ?> 
                        <div class="option" id="addEventOption">
                            <i class="fas fa-calendar-alt"></i> <span>اضافة حدث</span>
                        </div>
                        <?php }?>
                        <?php  if ($pages['sessions']['add'] == 1) { ?> 
                        <div class="option" id="addSessionOption">
                            <i class="fas fa-users"></i> <span>اضافة جلسة</span>
                        </div>
                        <?php }?>
                    </div>
                    <?php endif;?>
                    <div class="app-content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6" id="timeline-sessions-col">
                                    <h2>الجلسات</h2>
                                    <hr>
                                    <div class="timeline" id="timeline-sessions">
                                    </div>
                                </div>
                                <div class="col-md-6" id="timeline-events-col">
                                    <h2>الأحداث</h2>
                                    <hr>
                                    <div class="timeline" id="timeline-events">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </main>
            </div>

            <?php if ($pages['calendar']['add'] == 1) { ?>
                <!-- Start popup dialog box -->
                <div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">إضافة حدث جديد</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="event_form">
                                    <div class="alert alert-danger" role="alert" id="error-message" style="display: none;">
                                        <!-- سيتم عرض رسالة الخطأ هنا -->
                                    </div>
                                    <div class="form-group col-md-12 mb-2">
                                        <label for="event_name">اسم الحدث</label>
                                        <input type="text" name="event_name" id="event_name" class="form-control"
                                            placeholder="أدخل اسم الحدث">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12 mb-2">
                                            <label for="event_start_date">تاريخ بدء الحدث</label>
                                            <input type="datetime-local" name="event_start_date" id="event_start_date"
                                                class="form-control start-event" placeholder="تاريخ بدء الحدث" required>
                                        </div>
                                        <?php if ($pages['add_old_session']['add'] == 0): ?>
                                            <div class="row" id="error">
                                                <p style="color:red;font-size:smaller">غير مسموح لك إضافة حدث بتاريخ قديم!</p>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-group col-md-12 mb-2">
                                            <label for="event_end_date">تاريخ انتهاء الحدث</label>
                                            <input type="datetime-local" name="event_end_date" id="event_end_date"
                                                class="form-control" placeholder="تاريخ انتهاء الحدث" required>
                                            <input type="hidden" name="lawer_name" id="lawer_name"
                                                value="<?= $_SESSION['user_id'] ?>">
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="client_name">اختر الموكل</label>
                                            <select id="client_name" class="form-select" name="client_name">
                                                <option value="">اختر الموكل...</option>
                                                <?php
                                                if ($user_id !== null) {
                                                    // جلب العملاء المرتبطين بالمكتب
                                                    $sqlClients = "SELECT client_id, first_name, last_name FROM clients WHERE lawyer_id = ? ORDER BY client_id;";
                                                    $stmtClients = $conn->prepare($sqlClients);
                                                    $stmtClients->execute([$user_id]);
                                                    if ($stmtClients->rowCount() > 0) {
                                                        while ($row = $stmtClients->fetch(PDO::FETCH_ASSOC)) {
                                                            $client_id = $row["client_id"];
                                                            $client_name = $row["first_name"] . " " . $row["last_name"];
                                                            echo "<option value='$client_id'>$client_name</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No clients found</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" onclick="save_event()">حفظ الحدث</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End popup dialog box -->
            <?php } ?>

            <!-- Modal Session-->
            <div class="modal fade" id="sessionModal" tabindex="-1" role="dialog" aria-labelledby="sessionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="sessionModalLabel">إضافة جلسة جديدة</h5>
                            <button style="position: absolute;left: 15px;" type="button" class="btn-close" data-dismiss="modal"
                                aria-label="Close"></button>
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
                                                    <option value="<?= htmlspecialchars($case['case_id']) ?>">
                                                        <?= htmlspecialchars($case['case_title']) ?>
                                                    </option>
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
                                        <input type="date" class="form-control geo-date-input" name="session_date_gregorian"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>تاريخ الجلسة هجري</label>
                                        <input type="text" class="form-control hijri-date-input" name="session_date_hijri"
                                            required autocomplete="off">
                                    </div>
                                </div>
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
                                <button type="button" class="btn btn-secondary" id="closeModal"
                                    data-dismiss="modal">إغلاق</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Session Modal -->
            <div class="modal fade" id="editSessionModal" tabindex="-1" role="dialog" aria-labelledby="editSessionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSessionModalLabel">تعديل جلسة</h5>
                            <button style="position: absolute; left: 15px;" type="button" class="btn-close" data-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editSessionForm">
                                <input type="hidden" name="session_id" id="editSessionId">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>رقم الجلسة</label>
                                        <input type="text" class="form-control" name="session_number" id="editSessionNumber"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>لأي قضية</label>
                                        <select class="form-control" name="case_id" id="editCaseId" required>
                                            <option selected value="" disabled>اختر قضية</option>
                                            <?php if (!empty($cases)): ?>
                                                <?php foreach ($cases as $case): ?>
                                                    <option value="<?= htmlspecialchars($case['case_id']) ?>">
                                                        <?= htmlspecialchars($case['case_title']) ?>
                                                    </option>
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
                                        <input type="date" class="form-control geo-date-input" name="session_date_gregorian"
                                            id="editSessionDateGregorian" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>تاريخ الجلسة هجري</label>
                                        <input type="text" class="form-control hijri-date-input" name="session_date_hijri"
                                            id="editSessionDateHijri" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>ساعة الجلسة</label>
                                        <input type="time" class="form-control" name="session_hour" id="editSessionHour"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>الملاحظات</label>
                                        <textarea class="form-control" name="notes" id="editNotes" rows="2"></textarea>
                                    </div>
                                </div>
                               <?php if ($pages['sessions']['write'] == 1) { ?>
                                <button type="submit" class="btn btn-primary btn-block">حفظ</button>
                                <?php } ?>
                                <button type="button" class="btn btn-secondary" id="closeEditModal"
                                    data-dismiss="modal">إغلاق</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Event Modal -->
            <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editEventModalLabel">تعديل الحدث</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editEventForm">
                                <input type="hidden" name="event_id" id="editEventId">
                                <div class="row">
                                    <div class="form-group col-md-12 mb-2">
                                        <label for="editEventName">اسم الحدث</label>
                                        <input type="text" name="event_name" id="editEventName" class="form-control"
                                            placeholder="أدخل اسم الحدث">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 mb-2">
                                        <label for="editEventStartDate">تاريخ بدء الحدث</label>
                                        <input type="datetime-local" name="event_start_date" id="editEventStartDate"
                                            class="form-control" placeholder="تاريخ بدء الحدث" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-2">
                                        <label for="editEventEndDate">تاريخ انتهاء الحدث</label>
                                        <input type="datetime-local" name="event_end_date" id="editEventEndDate"
                                            class="form-control" placeholder="تاريخ انتهاء الحدث" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12 mb-2">
                                        <label for="editClientName">اختر الموكل</label>
                                        <select id="editClientName" class="form-select" name="client_name">
                                            <option value="">اختر الموكل...</option>
                                                <?php
                                                if ($user_id !== null) {
                                                    // جلب العملاء المرتبطين بالمكتب
                                                    $sqlClients = "SELECT client_id, first_name, last_name FROM clients WHERE lawyer_id = ? ORDER BY client_id;";
                                                    $stmtClients = $conn->prepare($sqlClients);
                                                    $stmtClients->execute([$user_id]);
                                                    if ($stmtClients->rowCount() > 0) {
                                                        while ($row = $stmtClients->fetch(PDO::FETCH_ASSOC)) {
                                                            $client_id = $row["client_id"];
                                                            $client_name = $row["first_name"] . " " . $row["last_name"];
                                                            echo "<option value='$client_id'>$client_name</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No clients found</option>";
                                                    }
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="saveEditEventButton">حفظ التعديلات</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End popup dialog box -->

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>

            <script src="../js/bootstrap-hijri-datetimepicker.js?v2"></script>

            
            <script src="../js/events.js"></script>
        </body>

        </html>
        <?php
    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>