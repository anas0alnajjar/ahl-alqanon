<?php
session_start();
if (
    isset($_SESSION['admin_id']) &&
    isset($_SESSION['role'])
) {
    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "information.php";
        include "logo.php";
        function getCases($conn)
        {
            $sql = "SELECT case_id, case_title FROM cases";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() >= 1) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }


        function getLawyers($conn)
        {
            $sql = "SELECT lawyer_id, lawyer_name FROM lawyer";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $cases = getCases($conn);
        $lawyers = getLawyers($conn);
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


                #calendar {
                    transition: transform 0.3s ease-in-out;
                    width: 100%;
                    height: 100%;
                }

                .close {
                    position: absolute;
                    left: 5px;
                    font-size: x-large;
                }

                        .fc-scroller.fc-day-grid-container {
            scrollbar-width: none !important;
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

                /* إعدادات الأجندة للشاشة الكبيرة */
                #calendar-container {
                    max-height: 600px;
                    overflow-y: auto;
                    overflow-x: hidden;
                }

                /* إعدادات الأجندة للشاشة الصغيرة */
                @media (max-width: 768px) {
                    #calendar-container {
                        max-height: none;
                        height: auto;
                        width: 100%;
                        overflow-y: visible;
                    }

                    .fc-toolbar {
                        display: flex;
                        flex-wrap: wrap;
                    }

                    .fc-toolbar .fc-left,
                    .fc-toolbar .fc-right {
                        flex: 1 1 100%;
                        display: flex;
                        justify-content: center;
                    }

                    .fc-view-container {
                        overflow-x: auto;
                    }

                    .fc-day-header {
                        white-space: nowrap;
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
                                    <div class="form-group col-md-6 mb-2">
                                        <label for="editLawyerName">اختر المحامي</label>
                                        <select id="editLawyerName" class="form-select" name="lawer_name">
                                            <option value="" selected>اختر المحامي...</option>
                                            <?php
                                            include "../DB_connection.php";
                                            $sql = "SELECT DISTINCT lawyer_id, lawyer_name FROM lawyer ORDER BY lawyer_id;";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $lawyer_id = $row["lawyer_id"];
                                                    $lawyer_name = $row["lawyer_name"];
                                                    echo "<option value='$lawyer_id'>$lawyer_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 mb-2">
                                        <label for="editClientName">اختر الموكل</label>
                                        <select id="editClientName" class="form-select" name="client_name">
                                            <option value="" selected>اختر الموكل...</option>
                                            <?php
                                            include "../DB_connection.php";
                                            $sql = "SELECT DISTINCT client_id, first_name, last_name FROM clients ORDER BY client_id;";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $client_id = $row["client_id"];
                                                    $client_name = $row["first_name"] . " " . $row["last_name"];
                                                    echo "<option value='$client_id'>$client_name</option>";
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
                            <a class="btn btn-danger delete-event-btn">حذف</a>
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
                                    <div class="form-group col-md-6">
                                        <label>المحامي المساعد</label>
                                        <select class="form-control" name="assistant_lawyer" id="assistant_lawyer">
                                            <option selected value="" disabled>اختر محامي</option>
                                            <?php if (!empty($lawyers)): ?>
                                                <?php foreach ($lawyers as $lawyer): ?>
                                                    <option value="<?= htmlspecialchars($lawyer['lawyer_id']) ?>">
                                                        <?= htmlspecialchars($lawyer['lawyer_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">لا يوجد محامين متاحين</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col-md-12">
                                        <label>الملاحظات</label>
                                        <textarea class="form-control" name="notes" id="editNotes" rows="2"></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">حفظ</button>
                                <a class="btn btn-danger delete-button">حذف</a>
                                <button type="button" class="btn btn-secondary" id="closeEditModal"
                                    data-dismiss="modal">إغلاق</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
                                            class="form-control" placeholder="تاريخ بدء الحدث" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-2">
                                        <label for="event_end_date">تاريخ انتهاء الحدث</label>
                                        <input type="datetime-local" name="event_end_date" id="event_end_date"
                                            class="form-control" placeholder="تاريخ انتهاء الحدث" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-2">
                                        <label for="lawer_name">اختر المحامي</label>
                                        <select id="lawer_name" class="form-select" name="lawer_name">
                                            <option value="" selected>اختر المحامي...</option>
                                            <?php
                                            include "../DB_connection.php";
                                            $sql = "SELECT DISTINCT lawyer_id, lawyer_name FROM lawyer ORDER BY lawyer_id;";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $lawyer_id = $row["lawyer_id"];
                                                    $lawyer_name = $row["lawyer_name"];
                                                    echo "<option value='$lawyer_id'>$lawyer_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="client_name">اختر الموكل</label>
                                        <select id="client_name" class="form-select" name="client_name">
                                            <option value="" selected>اختر الموكل...</option>
                                            <?php
                                            include "../DB_connection.php";
                                            $sql = "SELECT DISTINCT client_id, first_name, last_name FROM clients ORDER BY client_id;";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $client_id = $row["client_id"];
                                                    $client_name = $row["first_name"] . " " . $row["last_name"];
                                                    echo "<option value='$client_id'>$client_name</option>";
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

            <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen flex flex-col">
                <div class="col-md-12 flex-grow p-2">
                    <div class="card card-primary shadow-lg rounded-md overflow-hidden h-full flex flex-col">
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
                                <div class="lg:flex-1" id="calendar-container"
                                    style="overflow: auto; scrollbar-width: none !important;">
                                    <div class="bg-gray-50 border border-gray-200 shadow-md p-4 rounded-md h-full">
                                        <div class="w-full h-full flex flex-col">
                                            <select class="mb-2  border rounded-md w-full" name="" id="lawyers-events">
                                                <option value="all" selected>كل المحامين</option>
                                            </select>
                                            <button id="openDateRangePicker" class="btn btn-primary btn-sm mb-2 w-full">طباعة
                                                الأجندة</button>
                                            <div id="calendar" class="w-full flex-grow border-t border-gray-200 mt-2 pt-2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /.card-body -->
                    </div> <!-- /.card -->
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
            <script src="../js/get_date_v2.js"></script>
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
                    const { r, g, b, rgba } = randomColor();
                    card.style.background = `linear-gradient(136deg, ${rgba} 1%, ${rgba} 5%)`;
                    const textColor = isLight({ r, g, b }) ? 'black' : 'white';
                    card.style.color = textColor;

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
            <?php include "inc/footer.php"; ?>


            <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
            <script src="../js/adminlite.js"></script>
            <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->

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