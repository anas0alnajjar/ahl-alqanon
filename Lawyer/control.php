<?php
session_start();
if (
    isset($_SESSION['user_id']) &&
    isset($_SESSION['role'])
) {
    if ($_SESSION['role'] == 'Lawyer') {
        include "../DB_connection.php";
        include "information.php";
        include "logo.php";
        include 'permissions_script.php';

        if ($pages['control']['read'] == 0) {
            header("Location: home.php");
            exit();
        }

        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $office_id = getOfficeId($conn, $user_id);

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

        $cases = getCases($conn, $user_id);


        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>

            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <!-- <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet"> -->
            <link rel="stylesheet" href="../css/style-dash.css">
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>المحامي - الصفحة الرئيسية</title>

            <link rel="stylesheet" href="../css/style.css">
            <!-- <link rel="stylesheet" href="../css/yshstyle.css"> -->
            <link rel="icon" href="../img/<?= $setting['logo'] ?>">


            <!-- CSS for full calendar -->
            <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" /> -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">

            <!-- Bootstrap CSS and JS -->



            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@3/dark.css">




            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
                crossorigin="anonymous" />



            <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

            <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
                integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
            <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
                integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
            <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
                integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
            <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
            <link rel="stylesheet" href="../css/adminlte.css"><!--end::Required Plugin(AdminLTE)-->


            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
                @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');

                * {
                    direction: rtl;
                }

                .text-left {
                    text-align: right;
                }

                .overflow-x-auto {
                    scrollbar-width: thin;
                    max-height: 600px;
                    scroll-behavior: smooth;
                }

                td a {
                    text-decoration: none;
                }

                .card {
                    transition: transform 0.3s ease-in-out;
                }



                .bg-gradient {
                    background: linear-gradient(90deg, rgba(58, 123, 213, 1) 0%, rgba(58, 213, 158, 1) 100%);
                }

                .card {
                    max-height: 600px !important;
                }

                #calendar {
                    transition: transform 0.3s ease-in-out;

                }

                .close {
                    position: absolute;
                    left: 5px;
                    font-size: x-large;
                }
                
                .fc-scroller.fc-day-grid-container {
                    scrollbar-width: thin !important;
                    height: unset !important;
                    min-height: max-content !important;
                }

                .fc-content {
                    text-wrap: wrap !important;
                }

                .custom-card {
                    border-radius: 0.5rem;
                    border: 1px solid #e5e7eb;
                    padding: 1rem;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    margin-bottom: 1rem;
                    position: relative;
                    overflow: hidden;
                    transition: transform 0.3s ease-in-out;
                }

                .custom-card:hover {
                    transform: translateY(-10px);
                }

                .pulse {
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0% {
                        transform: scale(1);
                    }

                    50% {
                        transform: scale(1.1);
                    }

                    100% {
                        transform: scale(1);
                    }
                }

                .fc-title {
                    text-wrap: wrap;
                }

                @media only screen and (max-width: 600px) {
                    #calendar {
                        font-size: smaller;
                    }

                    .fc-title {
                        white-space: normal;
                        /* هذا لتفعيل التفاف النص */
                        font-size: xx-small;
                    }

                    h2 {
                        font-size: medium;
                        margin-top: 3px;
                    }

                    .fc-scroller.fc-day-grid-container {
                        scrollbar-width: thin !important;
                        min-height: 188.5px !important;
                    }

                    .fc-toolbar {
                        flex-direction: column;
                        align-items: flex-start;
                    }

                    .fc-toolbar .fc-left,
                    .fc-toolbar .fc-right {
                        margin-bottom: 10px;
                    }

                    .fc-view-container {
                        overflow-x: scroll;
                    }
                }

                #chart-container {
                    width: 80%;
                    margin: 0 auto;
                }

                @media (max-width: 768px) {
                    #chart-container {
                        width: 100%;
                        margin: 0 auto;
                    }
                }
                .event-session {
                    margin: 10px 0;
                    padding: 10px;
                    border-radius: 5px;
                }

                .event {
                    background-color: #f8d7da;
                    border-left: 5px solid #721c24;
                }

                .session {
                    background-color: #d4edda;
                    border-left: 5px solid #155724;
                }

                .date-header {
                    background-color: #e2e3e5;
                    padding: 5px;
                    margin: 10px 0;
                    border-radius: 5px;
                    text-align: center;
                }

                .modal-body {
                    max-height: 400px;
                    overflow-y: auto;
                }

                #agendaContent {
                    line-height: normal;
                }

                /* تنسيق العناوين */
                h2 {
                    font-size: 24px;
                    margin-bottom: 10px;
                }

                h3 {
                    font-size: 20px;
                    margin-top: 20px;
                    margin-bottom: 10px;
                }

                /* تنسيق الفقرات */
                p {
                    font-size: 16px;
                    line-height: 1.5;
                }

                /* تنسيق القوائم */
                ul {
                    list-style-type: none;
                    padding: 0;
                }

                li.event-session {
                    font-size: 16px;
                    margin-bottom: 10px;
                }

                /* تنسيق النصوص الأساسية */
                strong {
                    font-weight: bold;
                }

                .text-center {
                    text-align: center;
                }

                .mt-2 {
                    margin-top: 20px;
                }

                hr {
                    border: 0;
                    border-top: 1px solid #ccc;
                    margin: 20px 0;
                }

            </style>
        </head>

        <body class="bg-gray-50 font-inter text-gray-800">
        <?php if ($pages['calendar']['add'] == 1) { ?>
                <!-- Start popup dialog box -->
                <div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
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
                                    <input type="hidden" value="<?= $user_id ?>" name="lawyer_name" id="editLawyerName">
                                    <div class="form-group col-md-6 mb-2">
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
                            <?php if ($pages['calendar']['write'] == 1) { ?>
                                <button type="button" class="btn btn-primary" id="saveEditEventButton">حفظ التعديلات</button>
                            <?php } ?>
                            <?php if ($pages['calendar']['delete'] == 1) { ?>
                                <a class="btn btn-danger delete-event-btn">حذف</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End popup dialog box -->

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
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-12">
                                        <label>الملاحظات</label>
                                        <textarea class="form-control" name="notes" id="editNotes" rows="2"></textarea>
                                    </div>
                                </div>
                                <?php if ($pages['sessions']['write'] == 1) { ?>
                                    <button type="submit" class="btn btn-primary btn-block">حفظ</button>
                                <?php } ?>
                                <?php if ($pages['sessions']['delete'] == 1) { ?>
                                    <a class="btn btn-danger delete-button">حذف</a>
                                <?php } ?>

                                <button type="button" class="btn btn-secondary" id="closeEditModal"
                                    data-dismiss="modal">إغلاق</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Modal لاختيار المدة الزمنية -->
            <div id="dateRangeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dateRangeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="dateRangeModalLabel">اختيار المدة الزمنية</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="dateRangeForm">
                                <div class="form-group">
                                    <label for="startDate">من:</label>
                                    <input type="date" id="startDate" name="startDate" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="endDate">إلى:</label>
                                    <input type="date" id="endDate" name="endDate" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary mt-5">توليد الأجندة</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal لعرض الأجندة -->
            <div id="agendaModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="agendaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="agendaModalLabel">الأجندة</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="agendaContent">
                                <!-- سيتم تعبئة الأجندة هنا -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="download-pdf" class="btn btn-primary">تنزيل الأجندة كـ PDF</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "inc/navbar.php"; ?>

            <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen transition-all main">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row gap-6 mb-6">
                        <!-- Cards section -->
                        <div class="lg:flex-1 grid grid-cols-1 md:grid-cols-2 gap-6" style="">
                            <!-- Card 1 -->
                            <div class="custom-card card-1" data-count="<?php echo $open_sessions_result['sessions_coming']; ?>"
                                data-percentage="<?php echo round($percentage); ?>" data-text="عدد الجلسات القادمة">
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <div class="text-xl font-semibold mb-1 text-count"></div>
                                        <div class="text-sm font-medium text-description"></div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <a href="sessions.php"><button style="color: white;" type="button" class="btn btn-link"><i
                                                class="fa fa-spinner fa-spin"></i></button></a>
                                    <div class="w-full bg-gray-100 rounded-full h-4 relative overflow-hidden">
                                        <div
                                            class="progress-bar h-full absolute right-0 top-0 bg-gradient-to-l from-blue-500 to-green-500 rounded-full p-1">
                                        </div>
                                        <div class="w-2 h-2 rounded-full bg-white ml-auto"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 ml-4 text-percentage"></span>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="custom-card card-3" data-count="<?php echo $clients_count_result['clients_count']; ?>"
                                data-text="عدد الموكلين">
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <a href="clients.php"><i style="position: absolute;left: 10px;"
                                                class="fas fa-users text-gray-400 text-3xl pulse"></i></a>
                                        <div class="text-xl font-semibold mb-1 text-count"></div>
                                        <div class="text-sm font-medium text-description"></div>
                                    </div>
                                </div>
                                <a href="client-add.php" class="text-white font-medium text-sm hover:text-gray-200">أضف عميل</a>
                            </div>

                            <div class="custom-card card-5" data-count="<?php echo $helpers_count_result['helpers_count']; ?>"
                                data-text="عدد الإداريين">
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <i style="position: absolute;left: 10px;"
                                            class="fas fa-plus-circle text-gray-400 text-3xl pulse"></i>
                                        <div class="text-xl font-semibold mb-1 text-count"></div>
                                        <div class="text-sm font-medium text-description"></div>
                                    </div>
                                </div>
                                <a href="client-add.php" class="text-white font-medium text-sm hover:text-gray-200">أضف
                                    إداري</a>
                            </div>

                            <!-- Card 3 -->
                            <div class="custom-card card-4" data-count="<?php echo $total_cases_result['total_cases_count'] ?>"
                                data-text="عدد القضايا">
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <a href="cases.php"><i style="position: absolute;left: 10px;"
                                                class="fas fa-gavel text-gray-400 text-3xl pulse"></i></a>
                                        <div class="text-xl font-semibold mb-1 text-count"></div>
                                        <div class="text-sm font-medium text-description"></div>
                                    </div>
                                </div>
                                <a href="add_case.php" class="text-white font-medium text-sm hover:text-gray-200">أضف قضية</a>
                            </div>

                            <!-- Card 4 -->
                            <div class="custom-card card-6" data-count="<?php echo $monthly_costs_result['monthly_costs']; ?>"
                                data-text="مصاريف الجلسات للشهر الحالي">
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <i style="position: absolute;left: 10px;"
                                            class="fas fa-money-bill-alt text-gray-400 text-3xl pulse"></i>
                                        <div class="text-xl font-semibold mb-1 text-count"></div>
                                        <div class="text-sm font-medium text-description">

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Card 5 -->
                            <div class="custom-card card-5" data-count="<?php if (isset($monthly_payment_result['monthly_paid']) && $monthly_payment_result['monthly_paid'] > 0) {
                                echo $monthly_payment_result['monthly_paid'];
                            } ?>" data-text="الدفعات الغير مستلمة لهذا الشهر">

                                <div class="flex justify-between mb-4">
                                    <div>
                                        <i style="position: absolute;left: 10px;"
                                            class="fas fa-hand-holding-usd text-gray-400 text-3xl pulse"></i>
                                        <div class="text-xl font-semibold mb-1 text-count">

                                        </div>
                                        <div class="text-sm font-medium text-description"></div>
                                    </div>
                                </div>
                                <p class="text-white font-medium text-sm hover:text-gray-200">
                                    <?php if (empty($monthly_payment_result['monthly_paid']) || $monthly_payment_result['monthly_paid'] === 0) {
                                        echo "لا يوجد أي دفعات مستحقة لهذا الشهر";
                                    } ?>
                                </p>
                            </div>


                        </div>
                        <div class="lg:flex-1 card card-primary shadow-lg rounded-md overflow-hidden h-full flex flex-col">
                            <div class="lg:flex-1" id="calendar-container"
                                style="overflow: auto;scrollbar-width: none !important;">

                                <div class="card-header flex justify-between items-center bg-blue-600 text-white px-4 py-2">
                                    <h3 class="card-title">الأجندة</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool text-white" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body bg-white p-4 flex-grow">
                                    <div class="flex flex-col lg:flex-row gap-4 h-full">
                                        <!-- Calendar section -->
                                        <!-- Calendar section -->
                                        <div class="lg:flex-1" id="calendar-container"
                                            style="overflow: auto;scrollbar-width: none !important;">
                                            <div class="bg-white border border-gray-100 shadow-md p-6 rounded-md h-full">
                                                <div class="w-full h-full">
                                                    <div class="mb-2">
                                                        <button id="openDateRangePicker" class="btn btn-primary btn-sm">طباعة
                                                            الأجندة</button>
                                                    </div>
                                                    <div id="calendar" style="">
                                                    </div> <!-- Adjust width and height as needed -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- /.card-body -->
                            </div> <!-- /.card -->
                        </div>
                    </div>




                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md card">
                            <div class="flex justify-between mb-4 items-start">
                                <div class="font-medium">إدارة القضايا</div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[540px]" data-tab-for="order" data-page="active" id="active-cases">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                                اسم القضية</th>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                                الموكل</th>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">
                                                وكالة/قضية</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md card">
                            <div class="flex justify-between mb-4 items-start">
                                <div class="font-medium">إدارة الجلسات</div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[540px]" id="sessions-cases">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                                اسم القضية</th>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                                تستحق في</th>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                                الموكل</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div id="contaner-chart"
                            class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md lg:col-span-2 card">
                            <div class="flex justify-between mb-4 items-start">
                                <div class="font-medium">راقب الجلسات</div>
                            </div>
                            <div id="chart-container">
                                <select id="client-select">
                                    <option value="">اختر عميل</option>
                                    <!-- سيتم إضافة الخيارات هنا بواسطة JavaScript -->
                                </select>

                                <select id="case-filter">
                                    <option value="">اختر قضية</option>
                                    <!-- سيتم إضافة الخيارات هنا بواسطة JavaScript -->
                                </select>

                                <!-- <canvas id="case-chart" width="400" height="200"></canvas> -->

                                <canvas id="case-chart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md card">
                            <div class="flex justify-between mb-4 items-start">
                                <div class="font-medium">الموكلين والمستحقات المالية</div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[460px]" id="clientDate">
                                    <thead>
                                        <tr style='text-wrap:nowrap;'>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                                اسم الموكل</th>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                                عدد الجلسات القادمة</th>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                                مبلغ الدفعة القادمة</th>
                                            <th
                                                class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">
                                                تاريخ الاستحقاق</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </main>


            <script src="https://unpkg.com/@popperjs/core@2"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="../js/scriptDashboard.js"></script>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="../js/bootstrap-hijri-datetimepicker.js?v2"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-hijri/2.1.1/moment-hijri.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>


            <script>
                $(document).ready(function () {
                    $("#navLinks li:nth-child(2) a").addClass('active');
                });
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>


            <!-- <script src="../js/get_data_for_users.js"></script> -->
            <script src="../js/get_data_users_v2.js"></script>
            <!-- To Concert Calendar System -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/ar.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
            <script>
                $(document).ready(function () {
                    $('.close').click(function () {
                        $('#event_entry_modal').modal('hide');
                    });
                });
            </script>

            <script>
                function randomColor() {
                    const r = Math.floor(Math.random() * 256);
                    const g = Math.floor(Math.random() * 256);
                    const b = Math.floor(Math.random() * 256);
                    return { r, g, b, rgba: `rgba(${r}, ${g}, ${b}, 0.9)` };
                }

                function isLight({ r, g, b }) {
                    // Calculate luminance
                    const luminance = 0.299 * r + 0.587 * g + 0.114 * b;
                    return luminance > 186;
                }

                function applyRandomColor(card, index) {




                    const countElement = card.querySelector('.text-count');
                    const descriptionElement = card.querySelector('.text-description');
                    const percentageElement = card.querySelector('.text-percentage');
                    const progressBarElement = card.querySelector('.progress-bar');

                    countElement.textContent = card.getAttribute('data-count');
                    descriptionElement.textContent = card.getAttribute('data-text');
                    if (percentageElement && progressBarElement) {
                        const percentage = card.getAttribute('data-percentage');
                        percentageElement.textContent = `${percentage}%`;
                        progressBarElement.style.width = `${percentage}%`;
                    }

                    card.classList.add(`unique-animation-${index}`);

                }

                document.querySelectorAll('.custom-card').forEach((card, index) => applyRandomColor(card, index));
            </script>

            <?php if ($pages['add_old_session']['add'] == 0): ?>
                <script>
                    function validateDate(input) {
                        var today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
                        var hijriInput = input.closest('.form-group').parentElement.querySelector('.start-event');
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

                    document.addEventListener("DOMContentLoaded", function () {
                        function attachEventListeners() {
                            // التحقق من تواريخ الجلسات عند تغيير التاريخ الميلادي
                            document.querySelectorAll('.start-event').forEach(function (input) {
                                input.addEventListener('change', function () {
                                    validateDate(input);
                                });
                            });
                        }

                        attachEventListeners();

                        // مراقبة التغييرات في قيم الحقول بشكل دوري
                        var prevGeoValues = new Map();

                        setInterval(function () {
                            document.querySelectorAll('.start-event').forEach(function (geoInput) {
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

            <script src="../js/adminlite.js"></script>
        </body>

        </html>
        <?php

    } else {
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}

?>