<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Lawyer') {
        if (isset($_GET['id'])) {
        include "../DB_connection.php";
        include "logo.php";
        include 'permissions_script.php';

        if ($pages['cases']['read'] == 0) {
            header("Location: home.php");
            exit();
        }

        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);
        
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Case View</title>
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <link rel="stylesheet" href="../css/style.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
 
    
        
        

    <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    
    
    <script>
    $(document).ready(function() {
        // فتح المودال عند الضغط على الزر
        $('#editDocBtn').click(function() {
            $('#edit_doc_modal').modal('show');
        });

        // التحكم في إغلاق المودال بناءً على رسالة الخطأ أو النجاح
        <?php if (isset($_GET['docerror']) || isset($_GET['docsuccess'])) { ?>
            // إغلاق المودال بعد عرض رسالة الخطأ أو النجاح
            $('#edit_doc_modal').modal('show');
        <?php } ?>
    });
    </script>
    <script>
      
      $(document).ready(function() {
          // فتح المودال عند الضغط على الزر
          $('#editClientBtn').click(function() {
              $('#edit_client_modal').modal('show');
          });
  
      });
  </script>
<style>
    *{
        direction: rtl;
    }
    /* تغيير نمط الخط وتكبير حجم النص */
    .card-header h2 {
        font-family: Arial, sans-serif;
        font-size: 18px;
    }

    /* تغيير لون الزر وإضافة حدود */
    .btn-link {
        color: #007bff;
        text-decoration: none;
        border: none;
        background-color: transparent;
    }

    .btn-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    /* تخفيض حجم الصورة وإضافة حواف مستديرة */
    .card-body img {
        max-width: 200px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* تغيير لون خلفية البطاقة وإضافة حدود */
    .card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* تغيير لون الحدود الداخلية */
    .card-body {
        border-top: 1px solid #dee2e6;
    }

    /* تخفيض حجم الحقول النصية */
    .form-control {
        font-size: 16px;
    }
    input[type="checkbox"] {
        appearance: none;
        width: 20px;
        height: 20px;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 3px;
        cursor: pointer;
        vertical-align: middle;
    }

    input[type="checkbox"]:checked {
        background-color: #007bff;
    }

    #dynamic_table1 th{
        text-wrap: nowrap;
        padding: 5px;
        margin: 5px;
        width:auto;
    }
    button{
        font-family: 'Cairo';
    }
    .card-header h2 {
        font-family: 'Cairo';
        font-size: 18px;
    }
    .card {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        transition: transform 0.2s;
    }



    .btn-block {
        margin-top: 10px;
    }


    .btn-link:focus {
        outline: none;
        box-shadow: none;
    }

    .modal-body .form-group, .modal-body .form-check {
                margin-bottom: 1rem;
            }
    .modal-body .form-check-inline {
        margin-right: 1rem;
        flex-basis: 45%;
    }

    label{
        cursor: pointer;
    }

    #id_picture{
        cursor: pointer;
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
  /* تنسيق الزر */
  .reminder-button {
        background-color: #4CAF50; /* لون الخلفية */
        color: white; /* لون النص */
        padding: 15px 25px; /* حجم الزر */
        text-align: center; /* محاذاة النص */
        font-size: 16px; /* حجم الخط */
        cursor: pointer; /* المؤشر عند التحويم */
        border: none; /* إزالة الحدود */
        border-radius: 5px; /* التقوس */
        margin-left: 10px; /* المسافة من اليسار */
    }
    .reminder-button:hover {
        background-color: #45a049; /* تغيير لون الخلفية عند التحويم */
    }
    .ck-editor__editable[role="textbox"] {
    /* Editing area */
    min-height: 200px;
    text-align: right; /* Ensure text is aligned to the right */
    direction: rtl; /* Ensure text direction is right-to-left */
    }
    .ck-content .image {
        /* Block images */
        max-width: 80%;
        margin: 20px auto;
    }
    .ck.ck-toolbar.ck-toolbar_grouping>.ck-toolbar__items {
        flex-wrap: wrap;
    }
  
    button[data-cke-tooltip-text="Insert image or file"] {
            display: none !important;
        }
    .ck.ck-editor__editable_inline[dir=ltr] {
        text-align: right;
    }
    /* إضافة تنسيقات للقوائم لتظهر من اليمين إلى اليسار */
    .ck-content ol, .ck-content ul {
        text-align: right;
        direction: rtl;
        padding-right: 40px; /* Ensure padding on the right for RTL */
    }
    .ck-content ol {
        list-style-type: decimal;
    }
    .ck-content ul {
        list-style-type: disc;
    }
    .error {
            border-color: #dc3545 !important; /* Change border color for invalid fields */
        }
        .iti {
            position: relative;
            display: block;
        }

        .iti__country-list {
            left:0;
        }
                .select-container {
            width: 100%;
            max-width: 400px; /* تغيير العرض حسب احتياجاتك */
            margin: 10px auto; /* تخصيص التوسيط إذا لزم الأمر */
            height: 50px; /* تعيين الطول الثابت للحاوية */
        }
        .select-container select {
            width: 100%;
            height: 100%; /* تعيين الطول الثابت لعناصر <select> */
            visibility: hidden; /* إخفاء عنصر <select> أثناء التحميل */
        }
        .selectize-control {
            height: 100%; /* تأكد من أن Selectize يستخدم نفس الطول */
        }
        select {
                max-height: 35px !important;
        }
</style>
</head>
<body>
<?php include "inc/footer.php"; ?>    
<div id="Mybody">
    <?php include "inc/navbar.php"; ?>
    
<div class="container-fluid mt-5" style="max-width: 90%;">
    <div class="btn-group" style="direction:ltr;">
        <a href="home.php" class="btn btn-light">الرئيسية</a>
        <a href="cases.php" class="btn btn-dark">الرجوع</a>
    </div>
    <form class="" method="POST" action="" enctype="multipart/form-data" id="formEdit">
        <hr>
        <?php
            $query = "SELECT
                        tc.case_id,
                        tc.client_id,
                        tc.case_title
                    FROM
                        cases tc
                    WHERE
                        tc.case_id = :id
                    ";
                $id = $_GET['id'];

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                // Fetch data as associative array
                $hisName = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="case-info-tab" data-bs-toggle="tab" data-bs-target="#case-info" type="button" role="tab" aria-controls="case-info" aria-selected="true"><?=$hisName['case_title']?></button>
                </li>
                <?php if ($pages['sessions']['read'])  : ?>
                <li class="nav-item sessions" role="presentation">
                    <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions-info" type="button" role="tab" aria-controls="sessionsInfo" aria-selected="true">الجلسات</button>
                </li>
                <?php endif; ?>
                <?php if ($pages['payments']['read'] || $pages['expenses_sessions']['read'])  : ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expay-info-tab" data-bs-toggle="tab" data-bs-target="#expay-info" type="button" role="tab" aria-controls="" aria-selected="true">المصاريف / المدفوعات</button>
                </li>
                <?php endif; ?>
                <?php if ($pages['attachments']['read'])  : ?>
                <li class="nav-item attachments">
                <button class="nav-link" id="attchments-info-tab" data-bs-toggle="tab" data-bs-target="#attchment-info" type="button" role="tab" aria-controls="" aria-selected="true">المرفقات/ الملاحظات</button>
              </li>
              <?php endif; ?>


            </ul>

            <div class="tab-content mt-1" id="myTabsContent">
                <div class="tab-pane fade show active" id="case-info" role="tabpanel" aria-labelledby="case-info-tab">
                    <?php include "data/case-info-read.php"; ?>
                    
                    <div class="card" id="actionInfo">
                        <div class="card-header" id="heading9">
                            <h2 class="mb-0">
                                <button style="text-decoration:none;" class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
                                    المزيد              
                                </button>
                            </h2>
                        </div>

                        <div id="collapse9" class="collapse show" aria-labelledby="heading9" data-parent="#accordionMain" style="">
                            <div class="card-body" style="display:flex;">
                                <div class="column" style="min-width:25%">
                                    <div class="row">
                                        <?php if ($pages['clients']['read']) : ?>
                                            <button style="text-align: right;" id="editClientBtn" type="button" class="btn btn-link clients">معلومات الموكل</button>
                                        <?php endif; ?>
                                        <?php if ($pages['assistants']['add']) : ?>
                                            <button type="button" style="text-align: right;" class="btn btn-link assistants-add" id="addHelper">إضافة إداري</button>
                                        <?php endif; ?>
                                        <?php if ($pages['documents']['read'])  : ?>
                                            <button id="editDocBtn" style="text-align: right;" type="button" class="btn btn-link documents">العقود</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="column" style="min-width:25%">
                                    <div class="row">
                                        <button style="text-align: right;" id="printCon" type="button" class="btn btn-link">طباعة تقرير التكاليف والمصروفات</button>
                                        <button type="button" style="text-align: right;" class="btn btn-link" id="printInfo">طباعة معلومات القضية</button>
                                        <button id="printClient" style="text-align: right;" type="button" class="btn btn-link">طباعة معلومات الموكل</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="tab-content mt-1 attachments" id="attachTab">
                <div class="tab-pane fade show" id="attchment-info" role="tabpanel" aria-labelledby="attchments-info-tab">
                    <div class="card" id="attachmentsInfo">
                        <div id="heading400" class="card-header">
                            <h2 class="mb-0">
                                <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse400">   
                                المرفقات
                                </button>
                            </h2>
                        </div>
                        <div id="collapse400" class="collapse show" data-parent="#accordionMain">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="" class="mb-3" style="max-width: 100%; overflow-x: auto; scrollbar-width: thin; max-height: 400px;">
                                            <div id="dynamic_cards_files" class="row">
                                                <?php
                                                    $sql_files = "SELECT * FROM files WHERE case_id = :case_id";
                                                    $stmt_files = $conn->prepare($sql_files);
                                                    $stmt_files->bindParam(':case_id', $_GET['id']);
                                                    $stmt_files->execute();

                                                    $files = $stmt_files->fetchAll();
                                                    foreach ($files as $index => $file) {
                                                        $collapseIdFile = "collapseFiles" . $index;
                                                        $headerColorFile = "#5927e5"; // لون العنوان

                                                        echo '
                                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                            <div class="card h-100 shadow-sm">
                                                                <div class="card-header" style="background-color: ' . $headerColorFile . '; color: white;">
                                                                    <h2 class="mb-0">
                                                                        <button class="btn btn-link" style="color: white; text-decoration: none; overflow: hidden; font-size: small; outline: none !important; box-shadow: none !important;text-wrap: wrap;" type="button" data-toggle="collapse" data-target="#' . $collapseIdFile . '">
                                                                            الملف: ' . htmlspecialchars($file["file_name"]) . ' - التاريخ: ' . htmlspecialchars($file["created_date"]) . '
                                                                        </button>
                                                                    </h2>
                                                                </div>
                                                                <div id="' . $collapseIdFile . '" class="collapse" data-parent="#dynamic_cards_files">
                                                                    <div class="card-body">
                                                                        <p> تاريخ الرفع : ' . htmlspecialchars($file["created_date"]) . '</p>
                                                                        <a href="#" onclick="downloadFile(\'' . htmlspecialchars($file['file_path']) . '\')">تحميل الملف</a>
                                                                        <input type="hidden" class="fileId" value="' . htmlspecialchars($file["id"]) . '">
                                                                        <div class="text-center">
                                                                            <button type="button" style="min-width:100%;" class="btn btn-danger btn-sm btn-block deleteFileBtn attachments-delete" onclick="deleteFile(this)">حذف</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                    }
                                                ?>
                                            </div>
                                            <div class="text-left">
                                            </div>
                                        </div>
                                        <?php if ($pages['cases']['write']) : ?>
                                            <button style="width: 25%; float:left;" type="button" id="addFiles1" class="btn btn-dark btn-sm m-2 attachments-add">إضافة مرفقات</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card" id="descriptionInfo">
                        <div id="heading4" class="card-header">
                            <h2 class="mb-0">
                                <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse4">
                                    الملاحظات/وصف القضية
                                </button>
                            </h2>
                        </div>
                        <div id="collapse4" class="collapse show" data-parent="#accordionMain">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">ملاحظات</label>
                                            <textarea class="form-control" name="notes" rows="3"><?=$caseData['notes']?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">وصف القضية</label>
                                            <textarea id="editor" class="form-control" rows="3" name="case_description"><?=$caseData['case_description']?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        


            <div class="tab-content mt-1 sessions" id="sessionsTab">
                <div class="tab-pane fade show" id="sessions-info" role="tabpanel" aria-labelledby="session-info-tab">
                    <div class="card" id="sessionsInfo">

                        <div id="heading3" class="card-header">
                        <div style="">
                            <button style="min-width: 15%; float: left;" type="button" id="addRowBtn" class="btn btn-dark btn-sm addRowBtn sessions-add">إضافة جلسة</button>
                            </div>
                            <h2 class="mb-0">
                                <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse3">
                                    الجلسات
                                </button>
                            </h2>
                            
                        </div>
                        
                        
                        <div id="collapse3" class="collapse show" data-parent="#accordionMain">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3" style="max-width: 100%; overflow-x: auto; scrollbar-width: thin; max-height: 600px; min-height: 400px;">
                                        <div id="dynamic_cards" class="row">
                                            <?php

                                            if (!empty($OfficeId)) {

                                                // استعلام SQL لجلب المواعيد المرتبطة بالقضية
                                                $sql_sessions = "SELECT * FROM sessions WHERE case_id = :case_id ORDER BY sessions_id DESC";
                                                $stmt_sessions = $conn->prepare($sql_sessions);
                                                $stmt_sessions->bindParam(':case_id', $_GET['id']); // تعويض المعرف الذي تم تمريره عبر الرابط أو النموذج
                                                $stmt_sessions->execute();
                                            
                                                // جلب نتائج الاستعلام
                                                $sessions = $stmt_sessions->fetchAll();
                                            
                                                // جلب بيانات المحامين من قاعدة البيانات
                                                $sql_lawyers = "SELECT lawyer_id, lawyer_name FROM lawyer WHERE office_id IN ($OfficeId)";
                                                $stmt_lawyers = $conn->prepare($sql_lawyers);
                                                $stmt_lawyers->execute();
                                                $lawyers = $stmt_lawyers->fetchAll(PDO::FETCH_ASSOC);
                                            
                                                foreach ($sessions as $session) {
                                                    echo '
                                                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                        <div class="card h-100">
                                                            <div class="card-body">
                                                                <div class="form-group">
                                                                    <label class="mb-2">رقم الجلسة</label>
                                                                    <input type="text" class="form-control form-control-sm" name="session_number[]" value="' . htmlspecialchars($session["session_number"]) . '" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">المحامي المساعد</label>
                                                                    <select class="form-control form-control-sm" name="assistant_lawyer[]" disabled>
                                                                        <option value="">غير محدد</option>';
                                                                        foreach ($lawyers as $lawyer) {
                                                                            $selected = ($session["assistant_lawyer"] == $lawyer["lawyer_id"]) ? "selected" : "";
                                                                            echo '<option value="' . htmlspecialchars($lawyer["lawyer_id"]) . '" ' . $selected . '>' . htmlspecialchars($lawyer["lawyer_name"]) . '</option>';
                                                                        }
                                                                    echo '</select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">تاريخ الجلسة ميلادي</label>
                                                                    <input type="date" class="form-control form-control-sm geo-data-input" name="session_date[]" value="' . htmlspecialchars($session["session_date"]) . '" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">تاريخ الجلسة هجري</label>
                                                                    <input type="text" class="form-control form-control-sm hijri-date-input" name="session_date_hjri[]" value="' . htmlspecialchars($session["session_date_hjri"]) . '" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">ساعة الجلسة</label>
                                                                    <input type="time" class="form-control form-control-sm" name="session_hour[]" value="' . htmlspecialchars($session["session_hour"]) . '" required>
                                                                    <input type="hidden" class="form-control form-control-sm" name="sessions_id[]" value="' . htmlspecialchars($session["sessions_id"]) . '">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">الملاحظات</label>
                                                                    <textarea class="form-control form-control-sm" name="session_notes[]" rows="2">' . htmlspecialchars($session["notes"]) . '</textarea>
                                                                </div>
                                                                <button type="button" style="min-width: 100%;" class="btn btn-danger btn-sm btn-block sessions-delete deleteCardBtn" data-session-id="' . htmlspecialchars($session["sessions_id"]) . '">حذف</button>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                }
                                            } else {
                                                echo '<div class="col-12"><p>لا توجد مكاتب مرتبطة بك.</p></div>';
                                            }
                                            ?>
                                        </div>


                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($pages['payments']['read'] || $pages['expenses_sessions']['read'])  : ?>
            <div class="tab-content mt-1" id="expayTab">
                <div class="tab-pane fade show" id="expay-info" role="tabpanel" aria-labelledby="expay-info-tab">
                    <div class="card expenses_sessions" id="expensesInfo">
                        <div id="heading3" class="card-header expenses_sessions">
                            <h2 class="mb-0 ">
                                <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse96">
                                    مصاريف الجلسات <?= $expData["total_amount"] > 0 ? '<span style="color:green;">/ ' . htmlspecialchars(number_format($expData["total_amount"])) .  ' إلى الآن</span>' : '' ?>
                                </button>
                            </h2>
                        </div>
                        <div id="collapse96" class="collapse show expenses_sessions" data-parent="#accordionMain">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="expensesContainer" class="mb-3" style="max-width: 100%; overflow-x: auto; scrollbar-width: thin; max-height: 500px;">
                                            <div id="dynamic_cards_expenses" class="row">
                                            <?php
                                            // جلب أرقام الجلسات المتعلقة بالقضية
                                            $sql_sessions_numbers = "SELECT sessions_id, session_number FROM sessions WHERE case_id = :case_id ORDER BY session_number";
                                            $stmt_sessions_numbers = $conn->prepare($sql_sessions_numbers);
                                            $stmt_sessions_numbers->bindParam(':case_id', $_GET['id']);
                                            $stmt_sessions_numbers->execute();
                                            $sessions_numbers = $stmt_sessions_numbers->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            $sessions_numbers_json = json_encode($sessions_numbers);
                                            // جلب أنواع النفقات من جدول costs_type
                                            $sql_costs_types = "SELECT id, type FROM costs_type ORDER BY id DESC";
                                            $stmt_costs_types = $conn->prepare($sql_costs_types);
                                            $stmt_costs_types->execute();
                                            $costs_types = $stmt_costs_types->fetchAll();
                                        
                                            // جلب النفقات المرتبطة بالقضية
                                            $sql_expenses = "SELECT * FROM expenses WHERE case_id = :case_id ORDER BY id DESC";
                                            $stmt_expenses = $conn->prepare($sql_expenses);
                                            $stmt_expenses->bindParam(':case_id', $_GET['id']); 
                                            $stmt_expenses->execute();
                                            $expenses = $stmt_expenses->fetchAll();
                                        
                                            foreach ($expenses as $expense) {
                                                $collapseId = "collapseExpense" . $expense["id"];
                                                $headerColor = "#007bff"; // لون العنوان
                                        
                                                echo '
                                                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-header" style="background-color: ' . $headerColor . '; color: white;">
                                                            <h2 class="mb-0">
                                                                <button class="btn btn-link" style="color: white; text-decoration: none; overflow: hidden; font-size: small; outline: none !important; box-shadow: none !important;" type="button" data-toggle="collapse" data-target="#' . $collapseId . '">
                                                                    المبلغ: ' . htmlspecialchars($expense["amount"]) . ' - التاريخ: ' . htmlspecialchars($expense["pay_date"]) . '
                                                                </button>
                                                            </h2>
                                                        </div>
                                                        <div id="' . $collapseId . '" class="collapse" data-parent="#dynamic_cards_expenses">
                                                            <div class="card-body">
                                                                <div class="form-group">
                                                                    <label class="mb-2">رقم الجلسة</label>
                                                                    <select class="form-control form-control-sm" name="exp_session_id[]">
                                                                        <option value="">اختر رقم الجلسة</option>';
                                                                        foreach ($sessions_numbers as $session) {
                                                                            $selected = ($expense["session_id"] == $session["sessions_id"]) ? "selected" : "";
                                                                            echo '<option value="' . htmlspecialchars($session["sessions_id"]) . '" ' . $selected . '>' . htmlspecialchars($session["session_number"]) . '</option>';
                                                                        }
                                                                    echo '</select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">التاريخ ميلادي</label>
                                                                    <input type="date" class="form-control form-control-sm geo-data-input" name="pay_date[]" value="' . htmlspecialchars($expense["pay_date"]) . '" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">التاريخ هجري</label>
                                                                    <input type="text" class="form-control form-control-sm hijri-date-input" name="pay_date_hijri[]" value="' . htmlspecialchars($expense["pay_date_hijri"]) . '" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">المبلغ</label>
                                                                    <input type="number" class="form-control form-control-sm" name="amount[]" value="' . htmlspecialchars($expense["amount"]) . '" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="mb-2">الملاحظات</label>
                                                                    <textarea class="form-control form-control-sm" name="notes_expenses[]" rows="2">' . htmlspecialchars($expense["notes_expenses"]) . '</textarea>
                                                                </div>
                                                                <input type="hidden" name="expenses_id[]" value="' . htmlspecialchars($expense["id"]) . '">
                                                                <button style="min-width:100%;" type="button" class="btn btn-danger btn-sm btn-block deleteExpenses expenses_sessions-delete" data-expenses-id="' . htmlspecialchars($expense["id"]) . '">حذف</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                            ?>
                                        </div>

                                        </div>
                                        <button style="min-width: 15%; float: left;" type="button" id="addExpenses" class="btn btn-dark btn-sm m-2 expenses_sessions-add">إضافة بند</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card payments" id="paymentInfo">
                        <div id="heading300" class="card-header">
                            <h2 class="mb-0">
                                <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse300">
                                    المدفوعات
                                    <i class="fa fa-money"></i>

                                </button>
                            </h2>
                        </div>
                        <div id="collapse300" class="collapse show" data-parent="#accordionMain">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="paymentContainer" class="mb-3" style="max-width: 100%; overflow-x: auto; scrollbar-width: thin; max-height: 600px;">
                                            <div id="dynamic_cards_payment" class="row">
                                                <?php
                                                // استعلام SQL لجلب المدفوعات المرتبطة بالقضية
                                                $sql_payment = "SELECT * FROM payments WHERE case_id = :case_id ORDER BY id DESC";
                                                $stmt_payment = $conn->prepare($sql_payment);
                                                $stmt_payment->bindParam(':case_id', $_GET['id']); 
                                                $stmt_payment->execute();

                                                // جلب نتائج الاستعلام
                                                $payments = $stmt_payment->fetchAll();
                                                foreach ($payments as $index => $payment) {
                                                    $collapseIdPay = "collapsePayment" . $index;
                                                    $headerColorPay = "#17152f"; // لون العنوان
                                                    
                                                    echo '
                                                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                            <div class="card h-100 shadow-sm">
                                                                <div class="card-header" style="background-color: ' . $headerColorPay . '; color: white;">
                                                                    <h2 class="mb-0">
                                                                        <button class="btn btn-link" style="color: white; text-decoration: none; overflow: hidden; font-size: small; outline: none !important; box-shadow: none !important;" type="button" data-toggle="collapse" data-target="#' . $collapseIdPay . '">
                                                                            المبلغ: ' . htmlspecialchars($payment["amount_paid"]) . ' - التاريخ: ' . htmlspecialchars($payment["payment_date"]) . '
                                                                        </button>
                                                                    </h2>
                                                                </div>
                                                                <div id="' . $collapseIdPay . '" class="collapse" data-parent="#dynamic_cards_payment">
                                                                    <div class="card-body">
                                                                        <div class="form-group">
                                                                            <label>طريقة الدفع</label>
                                                                            <select class="form-control form-control-sm" name="payment_method[]" required>
                                                                                <option value="" disabled' . (empty($payment["payment_method"]) ? ' selected' : '') . '>اختر النوع</option>
                                                                                <option value="كاش"' . ($payment["payment_method"] == "كاش" ? ' selected' : '') . '>كاش</option>
                                                                                <option value="تحويل نقدي"' . ($payment["payment_method"] == "تحويل نقدي" ? ' selected' : '') . '>تحويل نقدي</option>
                                                                                <option value="أخرى"' . ($payment["payment_method"] == "أخرى" ? ' selected' : '') . '>أخرى</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>التاريخ ميلادي</label>
                                                                            <input type="date" class="form-control form-control-sm geo-data-input" name="payment_date[]" value="' . htmlspecialchars($payment["payment_date"]) . '" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>التاريخ هجري</label>
                                                                            <input type="text" class="form-control form-control-sm hijri-date-input" name="payment_date_hiri[]" value="' . $payment["payment_date_hiri"] . '" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>المبلغ</label>
                                                                            <input type="number" class="form-control form-control-sm" name="amount_paid[]" value="' . htmlspecialchars($payment["amount_paid"]) . '" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>الملاحظات</label>
                                                                            <textarea class="form-control form-control-sm" name="payment_notes[]" rows="2">' . htmlspecialchars($payment["payment_notes"]) . '</textarea>
                                                                        </div>
                                                                        <div class="form-group form-check mt-2">
                                                                            <input type="checkbox" class="form-check-input" id="received' . htmlspecialchars($payment["id"]) . '" name="received[]" value="1" ' . ($payment['received'] == 1 ? 'checked' : '') . '>
                                                                            <label for="received' . htmlspecialchars($payment["id"]) . '" class="form-check-label">مستلمة</label>
                                                                        </div>
                                                                        <input type="hidden" name="payment_id[]" value="' . htmlspecialchars($payment["id"]) . '">
                                                                        <div class="text-center" style="justify-content: space-between;display: flex;margin-top: 1rem;">';
                                                    
                                                    if ($payment['received'] !== 1) {
                                                        echo '<button style="min-width:49%" type="button" class="btn btn-warning btn-sm sendMessage" onclick="sendReminder(\'' . htmlspecialchars($payment['amount_paid']) . '\', \'' . htmlspecialchars($caseData['phone']) . '\', \'' . htmlspecialchars($caseData['client_first_name']. ' ' . $caseData['client_last_name'] )  . '\', \'' . htmlspecialchars($caseData['case_title']) .  '\'  );">إرسال تذكير</button>';
                                                        echo '<button style="min-width:49%" type="button" class="btn btn-danger btn-sm deletePayment payments-delete" data-payment-id="' . htmlspecialchars($payment["id"]) . '">حذف</button>';
                                                    } else {
                                                        echo '<button style="min-width:100%" type="button" class="btn btn-danger btn-sm btn-block deletePayment payments-delete" data-payment-id="' . htmlspecialchars($payment["id"]) . '">حذف</button>';
                                                    }
                                                
                                                    echo '
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                }
                                                
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button style="min-width: 15%; float: left;" type="button" id="addPayment" class="btn btn-dark btn-sm m-2 payments-add">إضافة دفعة</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

     

            
            <button type="button" class="btn btn-primary mb-3 cases-write" id="editCase"> حفظ التعديلات </button>
            <button type="button" class="btn btn-danger mb-3 cases-delete" id="deleteCase">حذف</button>
    </form>


    <input type="hidden" id="idForPrint"  value="<?php echo $_GET['id']; ?>" name="id">
</div>          
            


    <!--Start client Modal -->
    <?php 
     $client_id = $caseData['client_id'];
     $sql_client = "SELECT * FROM clients WHERE client_id=?";
     $stmt_client = $conn->prepare($sql_client);
     $stmt_client->execute([$client_id]);
     $client_info = $stmt_client->fetch();
    ?>
    <div class="modal fade" id="edit_client_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="modalLabel">Edit Client</h5> -->
                    <button type="button" class="close close-modal2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 65vh;overflow:auto;">
                <!-- Form for adding a client -->
                <form method="post" class="shadow p-3 mt-4" id="clientEdit" action="">
                    <!-- Form fields -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fname" class="form-label">الاسم الأول</label>
                            <input type="text" class="form-control" value="<?=$client_info['first_name']?>" name="first_name" id="fname">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lname" class="form-label">العائلة</label>
                            <input type="text" class="form-control" value="<?=$client_info['last_name']?>" name="last_name" id="lname">
                        </div>
                        <input type="hidden" value="<?=$OfficeId?>" name="office_idClient" id="office_idModal">
                        <div class="col-md-6 mb-3">
                            <label for="father_name" class="form-label">اسم الأب</label>
                            <input type="text" class="form-control" value="<?=$client_info['father_name']?>" name="father_name" id="father_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="grandfather_name" class="form-label">اسم الجد</label>
                            <input type="text" class="form-control" value="<?=$client_info['grandfather_name']?>" name="grandfather_name" id="grandfather_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="cursor: pointer;" for="city">المدينة</label>
                            <input class="form-control" type="text" name="city" id="city" value="<?=$client_info['city']?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <input type="text" class="form-control" value="<?=$client_info['address']?>" name="address" id="address">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_address" class="form-label">الإيميل</label>
                            <input type="email" class="form-control" value="<?=$client_info['email']?>" name="email" id="email_address" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">سنة الولادة</label>
                            <input type="date" class="form-control" value="<?=$client_info['date_of_birth']?>" name="date_of_birth" id="date_of_birth">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الجنس</label><br>
                            <input type="radio" value="Male" <?php if(isset($client_info['gender']) && $client_info['gender'] == 'Male'){ echo 'checked';}  ?> name="gender"> ذكر
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="Female" <?php if(isset($client_info['gender']) && $client_info['gender'] == 'Female') echo 'checked'; ?> name="gender"> أنثى
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="national_num" class="form-label">الرقم القومي</label>
                            <input type="text" class="form-control" value="<?=$client_info['national_num']?>" name="national_num" id="national_num">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="client_passport" class="form-label">رقم جواز السفر</label>
                            <input type="text" class="form-control" name="client_passport" id="client_passport" value="<?=$client_info['client_passport']?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">الهاتف</label>
                            <div style="min-width:100%;">
                                <input style="direction:ltr;" type="tel" id="phone" class="form-control" value="<?=$client_info['phone']?>" name="phone">
                            </div>
                            <input id="ClientID" type="hidden" class="form-control" value="<?=$client_id?>" name="client_id" id="client_id">
                        </div>
                    </div>

                    <div id="case-info" class="tab-pane fade show active">
    <div id="accordionMain">
        <div class="card">
            <div id="heading1" class="card-header">
                <h2 class="mb-0">
                    <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse1"> تفاصيل أخرى </button>
                </h2>
            </div>
            <div id="collapse1" class="collapse show" data-parent="#accordionMain">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="cursor: pointer;" for="alhi">الحي</label>
                            <input class="form-control" type="text" name="alhi" id="alhi" value="<?=$client_info['alhi']?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="cursor: pointer;" for="street_name">اسم الشارع</label>
                            <input class="form-control" type="text" name="street_name" id="street_name" value="<?=$client_info['street_name']?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="cursor: pointer;" for="num_build">رقم المبنى</label>
                            <input class="form-control" type="text" name="num_build" id="num_build" value="<?=$client_info['num_build']?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="cursor: pointer;" for="num_unit">رقم الوحدة</label>
                            <input class="form-control" type="text" name="num_unit" id="num_unit" value="<?=$client_info['num_unit']?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="cursor: pointer;" for="zip_code">الرمز البريدي</label>
                            <input class="form-control" type="text" name="zip_code" id="zip_code" value="<?=$client_info['zip_code']?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="cursor: pointer;" for="subnumber">الرقم الفرعي</label>
                            <input class="form-control" type="text" name="subnumber" id="subnumber" value="<?=$client_info['subnumber']?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal2" data-dismiss="modal">إغلاق</button>
                    <button id="editClient" type="submit" class="btn btn-success clients-write">تحديث</button>  
                </div>
                </form>
            </div>
        </div>
    </div>
    <!--End Modal -->

    <!-- Helpers Modal -->
    <div class="modal fade" id="helperModal" tabindex="-1" aria-labelledby="helperModalLabel" aria-hidden="true" style="text-align:right; direction:rtl;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="helperForm" method="POST">    
                        <div class="row">
                            <input type="hidden" name="lawyer_id555" value="<?=$caseData['lawyer_id']?>">
                            <input type="hidden" name="office_id" value="<?=$caseData['office_id']?>">
                            <div class="form-group col-md-6">
                                <label class="mb-2" for="">الاسم</label>
                                <input type="text" class="form-control" id="helper_nameModal" name="helper_name" required>
                            </div>
                            <div class="form-group col-md-6">
                            <label class="form-label">الدور</label>
                                <select id="role_idHelper" class="" name="role_id" required>
                                <option value=" " selected>اختر الدور</option>
                                <?php
                                if (!empty($user_id)) {
                                    // إعداد الاستعلام باستخدام الاستعلام المحضر
                                    $sql_roles = "SELECT power_id, role FROM powers WHERE FIND_IN_SET(:user_id, lawyer_id)";
                                    $stmt_roles = $conn->prepare($sql_roles);
                                    // ربط قيمة user_id بالاستعلام
                                    $stmt_roles->bindParam(':user_id', $user_id, PDO::PARAM_STR);
                                    $stmt_roles->execute();
                                    $result2 = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($result2) > 0) {
                                        foreach ($result2 as $row2) {
                                            $id = $row2["power_id"];
                                            $role = $row2["role"];
                                            echo "<option value='$id'>$role</option>\n";
                                        }
                                    } else {
                                        // echo "<option value=''>لا توجد أدوار مرتبطة بك</option>\n";
                                    }
                                } else {
                                    // echo "<option value=''>لا توجد مكاتب مرتبطة بك</option>\n";
                                }
                                ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                            <label class="mb-2" for="phone">الهاتف</label>
                                <input type="text" class="form-control" id="phoneHelper" name="phone" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="mb-2" for="usernameHelper">اسم المستخدم</label>
                                <input type="text" class="form-control" id="usernameHelper" name="username" required>
                            </div>
                            <div class="form-group col-md-6">   
                                <label class="mb-2" for="pass">كلمة السر</label>
                                <input type="password" class="form-control" id="pass" name="pass" required>
                            </div>
                            <div class="form-group col-md-6">   
                                <label class="mb-2" for="national_helper">الرقم القومي</label>
                                <input type="text" class="form-control" id="national_helper" name="national_helper" required>
                            </div>
                            <div class="form-group col-md-12">   
                                <label class="mb-2" for="passport_helper">رقم جواز السفر</label>
                                <input type="text" class="form-control" id="passport_helper" name="passport_helper" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close" id="colse555" data-dismiss="modal">إغلاق</button>
                    <button id="saveHelper" type="button" class="btn btn-primary assistants-add">حفظ</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Start Document Modal-->
    <div class="modal fade" id="edit_doc_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal1" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
    <?php
    $sql_documents = "SELECT * FROM documents WHERE case_id=?";
    $stmt_documents = $conn->prepare($sql_documents);
    $stmt_documents->execute([$_GET['id']]);
    $result_documents = $stmt_documents->fetchAll();
    ?>            
    <!-- بداية قسم الوثائق -->
    <table class="table table-bordered mt-3 n-table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">العنوان</th>
                <th scope="col">العملية</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($result_documents as $row_document): ?>
            <tr>
                <th scope="row"><?=$i?></th>
                <td><a href="document-view.php?document_id=<?=$row_document['document_id']?>"><?=$row_document['title']?></a></td>
                <td>
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#documentModal" data-id="<?=$row_document['document_id']?>">عرض</button>    
                    <?php if ($pages['documents']['write']) : ?>
                        <a href="document-edit.php?document_id=<?=$row_document['document_id']?>" class="btn btn-success">تعديل</a>
                    <?php endif; ?>
                    <?php if ($pages['documents']['delete']) : ?>
                        <button id="" type='button' class='btn btn-danger delete-doc' data-document_id='<?= $row_document['document_id'] ?>'>حذف</button>
                    <?php endif; ?>

                </td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <!-- نهاية قسم الوثائق -->


                </div>
                <div class="modal-footer">
                    <!-- أزرار الإغلاق -->
                    <button  type="button" class="btn btn-secondary close-modal1" data-dismiss="modal">إغلاق</button>
                <?php if ($pages['documents']['add']) : ?>
                    <button id="addDocBtn" type="button" class="btn btn-primary document-add" data-dismiss="modal">إضافة</button>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End -->


    <!-- Modal Document Add -->
    <div class="modal fade" id="add_doc_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal1" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 65vh;overflow:auto;">
                    <form method="post" class="shadow p-3" action="" enctype="multipart/form-data" id="hide-some-things">
                        <h3>إضافة عقد</h3>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="text" class="form-control" value="" name="document_title" id="document_title" required>
                        </div>
                        <div class="mb-3">
                            <?php
                            if (isset($_GET['id'])) {
                                $id_case_forDoc = $_GET['id'];
                                $sqlDoc = "SELECT cases.client_id, cases.office_id, clients.client_id, lawyer.lawyer_id,
                                                cases.lawyer_id, CONCAT(clients.first_name, ' ', clients.last_name) AS client_name, lawyer.lawyer_name
                                        FROM cases
                                        LEFT JOIN clients ON cases.client_id = clients.client_id
                                        LEFT JOIN lawyer ON cases.lawyer_id = lawyer.lawyer_id
                                        WHERE cases.case_id = '$id_case_forDoc'";
                                $resultDoc = $conn->query($sqlDoc);
                                $rowDoc = $resultDoc->fetch(PDO::FETCH_ASSOC);
                            }
                            ?>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="client_id_doc" id="client_id_doc" value="<?php echo $rowDoc['client_id'] ?> " readonly hidden>
                            <input type="text" class="form-control" name="lawyer_id_doc" id="lawyer_id_doc" value="<?php echo $rowDoc['lawyer_id'] ?> " readonly hidden>
                            <input type="text" class="form-control" name="office_id_doc" id="office_id_doc" value="<?php echo $rowDoc['office_id'] ?> " readonly hidden>
                            <input type="text" class="form-control" name="case_id_doc" id="case_id_doc"  value="<?php echo $_GET['id'] ?>" readonly hidden>
                            
                            <div class="mb-3">
                                <label class="form-label">تحميل ملف PDF</label>
                                <input type="file" class="form-control" id="attachments" name="attachments" accept="application/pdf">
                            </div>

                            <div id="editor" class="mt-3 mb-3">
                                <label class="form-label">الوثيقة</label>
                                <textarea name="content" id="edit" class="form-control" placeholder="هذا الحقل مطلوب"></textarea>
                            </div>

                            <textarea id="hiddenTextarea" style="display:none;"></textarea>
                        </div>   

                    </form>
                </div>
                <div class="modal-footer">
                        <button  type="button" class="btn btn-secondary closeDocumentation" data-dismiss="modal">إغلاق</button>
                        <a class="btn btn-dark" id="fileButton" style="color:white;">تحميل ملف ورد</a>
                        <button id="addReport" type="submit" class="btn btn-primary document-add">إضافة</button>
                </div>
                        
            </div>
        </div>
    </div>
    <!-- End -->


 <!-- Modal -->
 <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="documentModalBody" style="overflow: auto;max-height: 400px;">
            <!-- سيتم تحميل المحتوى هنا باستخدام JavaScript -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
          </div>
        </div>
      </div>
</div>

<!-- Modal for uploading files  -->

<div class="modal fade" id="upload_files_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-modal2" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="close-modal">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="direction:rtl;">
                <div class="container mt-5">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="multiupload">اختر الملفات:</label>
                            <input type="file" id="multiupload" name="upload_image[]" multiple class="form-control" required>
                        </div>
                        <button type="button" id="upcvr" class="btn btn-primary">بدء الرفع</button>
                    </form>
                    <div id="uploadsts" class="mt-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- أزرار الإغلاق -->
                <button type="button" class="btn btn-secondary close-modal2" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
    

<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>   

<script>
$(document).ready(function () {
            // تطبيق Selectize على جميع عناصر <select>
            $('select').selectize({
                sortField: 'text',
                onInitialize: function() {
                    // إظهار عنصر <select> بعد التهيئة
                    this.$wrapper.find('select').css('visibility', 'visible');
                }
            });
        }); 
</script>



<script>
      $(document).ready(function() {
          // فتح المودال عند الضغط على الزر
          $('#editClientBtn').click(function() {
              $('#edit_client_modal').modal('show');
          });
      });
</script>



        
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
        <script src="../js/bootstrap-hijri-datetimepicker.js?v2"></script>
        
        <script src="../documents/js/mammoth.browser.min.js"></script>
        <script src="../documents/js/script.js"></script>

<script>
     $(document).ready(function() {
        
        $('.close-modal1').click(function() {
            $('#edit_doc_modal').modal('hide');
        }); });
        $('.close-modal2').click(function() {
            $('#edit_client_modal').modal('hide');
        }); 
        $('.close-modal2').click(function() {
            $('#money_modal').modal('hide');
        }); 
        $('.close-modal3').click(function() {
            $('#money_modal').modal('hide');
        }); 
</script>

<script>
$(document).ready(function(){
    $('#deleteCase').click(function(){
        var caseId = $('#caseId').val(); // الحصول على قيمة الـ id من الحقل المخفي

        // عرض نافذة تأكيد قبل عملية الحذف
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا، وسيتم حذف كل من الجلسات والمدفوعات والعقود والملفات المرتبطة بالقضية!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفها!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إذا تم تأكيد الحذف، إرسال طلب الحذف
                $.ajax({
                    type: 'POST',
                    url: 'req/delete_case.php', // اسم ملف PHP الذي سيتم تشغيله لعملية الحذف
                    data: {id: caseId}, // إرسال الـ id كبيانات
                    success: function(response){
                        // console.log(response);
                        Swal.fire({
                            icon: 'success',
                            title: 'تم !',
                            text: 'تم حذف الحالة بنجاح من قاعدة البيانات.',
                            showConfirmButton: false,
                            timer: 2000, // يختفي الإشعار بعد 2000 مللي ثانية (2 ثانية)
                            willClose: function() {
                                // إعادة التوجيه إلى cases.php بعد نجاح عملية الحذف
                                window.location.href = 'cases.php';
                            }
                        });
                    },
                    error: function(xhr, status, error){
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء محاولة حذف القضية!',
                        });
                        console.error(error);
                    }
                });
            }
        });
    });
});

</script>

<!-- للوكالة -->
<script>
        document.getElementById('flexSwitchCheckChecked').addEventListener('change', function() {
            var label = document.getElementById('legal-number');
            if (this.checked) {
                label.textContent = 'رقم الوكالة';
            } else {
                label.textContent = 'رقم القضية';
            }
        });

        // لضبط النص عند تحميل الصفحة بناءً على حالة الـ checkbox
        window.addEventListener('load', function() {
            var checkbox = document.getElementById('flexSwitchCheckChecked');
            var label = document.getElementById('legal-number');
            if (checkbox.checked) {
                label.textContent = 'رقم الوكالة';
            } else {
                label.textContent = 'رقم القضية';
            }
        });
</script>

<!-- للجلسات -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
    var cardsContainer = document.getElementById('dynamic_cards');
    var rowIndex = 0;
    var lawyers = [];

    // دالة لجلب بيانات المحامين باستخدام AJAX
    function fetchLawyers() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'api/lawyers.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                lawyers = JSON.parse(xhr.responseText);
            } else {
                console.error('فشل في جلب بيانات المحامين');
            }
        };
        xhr.onerror = function() {
            console.error('حدث خطأ أثناء جلب بيانات المحامين');
        };
        xhr.send();
    }

    // استدعاء دالة جلب بيانات المحامين عند تحميل الصفحة
    fetchLawyers();

    // Function to convert Gregorian date to Hijri
    function convertToHijri(gregorianDate) {
        if (gregorianDate) {
            return moment(gregorianDate, 'YYYY-MM-DD').format('iYYYY-iMM-iDD');
        }
        return '';
    }

    // Function to convert Hijri date to Gregorian
    function convertToGregorian(hijriDate) {
        if (hijriDate) {
            return moment(hijriDate, 'iYYYY-iMM-iDD').format('YYYY-MM-DD');
        }
        return '';
    }

    // Watch for changes in date fields
    function attachDateChangeEvents(card) {
        var gregorianInput = card.querySelector('.geo-data-input');
        var hijriInput = card.querySelector('.hijri-date-input');

        gregorianInput.addEventListener('input', function() {
            hijriInput.value = convertToHijri(gregorianInput.value);
        });

        hijriInput.addEventListener('change', function() {
            gregorianInput.value = convertToGregorian(hijriInput.value);
        });

        $(hijriInput).hijriDatePicker({
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
            gregorianInput.value = convertToGregorian(e.date.format('iYYYY-iMM-iDD'));
        });
    }

    // Apply events to existing elements
    var existingCards = document.querySelectorAll('#dynamic_cards .col-sm-12.col-md-6.col-lg-4');
    existingCards.forEach(function(card) {
        attachDateChangeEvents(card);
    });

    // Function to add a new card
    function addNewCard() {
        rowIndex++;

        var newCard = document.createElement('div');
        newCard.className = 'col-sm-12 col-md-6 col-lg-4 mb-3';

        newCard.innerHTML = `
            <div class="card h-100">
                <div class="card-body">
                    <div class="form-group">
                        <label class="mb-2">رقم الجلسة</label>
                        <input type="text" class="form-control form-control-sm" name="new_session_number[]" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">تاريخ الجلسة ميلادي</label>
                        <input type="date" class="form-control form-control-sm geo-data-input" name="new_session_date[]" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">تاريخ الجلسة هجري</label>
                        <input type="text" class="form-control form-control-sm hijri-date-input" name="new_session_date_hjri[]" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">ساعة الجلسة</label>
                        <input type="time" class="form-control form-control-sm" name="new_session_hour[]" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">الملاحظات</label>
                        <textarea class="form-control form-control-sm" name="notes_sessions[]" rows="2"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="button" style="min-width:49%" class="btn btn-warning btn-sm btn-block save-sessions">حفظ</button>
                        <button type="button" style="min-width:49%" class="btn btn-danger btn-sm btn-block deleteCardBtn">حذف</button>
                        <button style="" type="button"  class="addRowBtn btn btn-dark btn-sm w-100 mt-5">إضافة جلسة أخرى</button>
                    </div>
                </div>
            </div>
        `;
        newCard.dataset.rowIndex = rowIndex;
        cardsContainer.appendChild(newCard);
        attachDateChangeEvents(newCard);

        // Ensure the collapsible section is open before scrolling
        var collapsible = document.getElementById('collapse3');
        if (!collapsible.classList.contains('show')) {
            $(collapsible).collapse('show');
        }

        // Scroll to the new card smoothly
        newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Add event listener for the button outside the cards
    var outsideButton = document.getElementById('addRowBtn');
    if (outsideButton) {
        outsideButton.addEventListener('click', addNewCard);
    }

    // Add new element on button click (inside the cards)
    cardsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('addRowBtn')) {
            addNewCard();
        }
    });

    // Remove element on delete button click
    cardsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('deleteCardBtn')) {
            var card = event.target.closest('.col-sm-12.col-md-6.col-lg-4');
            var rowIndex = card.dataset.rowIndex;
            card.remove();
        }
    });
});
</script>



<!-- نهاية التوليد للجلسات -->






<!-- للمصاريف -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    var addButton = document.getElementById('addExpenses');
    var cardsContainer = document.getElementById('dynamic_cards_expenses');
    var rowIndex = 0;
    var sessionsNumbers = <?php echo $sessions_numbers_json; ?>; // جلب بيانات الجلسات من PHP

    // دالة لتحويل التاريخ الميلادي إلى هجري
    function convertToHijri(gregorianDate) {
        if (gregorianDate) {
            return moment(gregorianDate, 'YYYY-MM-DD').format('iYYYY-iMM-iDD');
        }
        return '';
    }

    // دالة لتحويل التاريخ الهجري إلى ميلادي
    function convertToGregorian(hijriDate) {
        if (hijriDate) {
            return moment(hijriDate, 'iYYYY-iMM-iDD').format('YYYY-MM-DD');
        }
        return '';
    }

    // مراقبة التغيرات في حقول التواريخ
    function attachDateChangeEvents(card) {
        var gregorianInput = card.querySelector('.geo-data-input');
        var hijriInput = card.querySelector('.hijri-date-input');

        gregorianInput.addEventListener('input', function() {
            hijriInput.value = convertToHijri(gregorianInput.value);
        });

        hijriInput.addEventListener('change', function() {
            gregorianInput.value = convertToGregorian(hijriInput.value);
        });

        $(hijriInput).hijriDatePicker({
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
            gregorianInput.value = convertToGregorian(e.date.format('iYYYY-iMM-iDD'));
        });
    }

    // تطبيق الأحداث للعناصر الموجودة بالفعل
    var existingCards = document.querySelectorAll('#dynamic_cards_expenses .col-sm-12.col-md-6.col-lg-4');
    existingCards.forEach(function(card) {
        attachDateChangeEvents(card);
    });

    // إنشاء خيارات الجلسات
    function createSessionOptions() {
        var options = '<option value="">اختر رقم الجلسة</option>';
        sessionsNumbers.forEach(function(session) {
            options += `<option value="${session.sessions_id}">${session.session_number}</option>`;
        });
        return options;
    }

    // إضافة عنصر جديد عند الضغط على زر الإضافة
    addButton.addEventListener('click', function() {
        rowIndex++;

        var sessionOptions = createSessionOptions();

        var newCard = document.createElement('div');
        newCard.className = 'col-sm-12 col-md-6 col-lg-4 mb-3';
        newCard.innerHTML = `
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <label class="mb-2">رقم الجلسة</label>
                        <select class="form-control form-control-sm" name="exp_session_id_new[]">
                            ${sessionOptions}
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">التاريخ ميلادي</label>
                        <input type="date" class="form-control form-control-sm geo-data-input geogra-input" name="newPay[]" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">التاريخ هجري</label>
                        <input type="text" class="form-control form-control-sm hijri-date-input hejri-input" name="newPayHijri[]" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">المبلغ</label>
                        <input type="number" class="form-control form-control-sm" name="newAmount[]" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2">الملاحظات</label>
                        <textarea class="form-control form-control-sm" name="NewNotes[]" rows="2"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="button" style="min-width:49%" class="btn btn-warning btn-sm btn-block save-sessions">حفظ</button>
                        <button type="button" style="min-width:49%" class="btn btn-danger btn-sm btn-block deleteExpenses">حذف</button>
                    </div>
                </div>
            </div>
        `;
        newCard.dataset.rowIndex = rowIndex;
        cardsContainer.appendChild(newCard);
        attachDateChangeEvents(newCard);

        // التركيز على البطاقة الجديدة وجعلها مرئية بالكامل للمستخدم
        newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // حذف عنصر عند الضغط على زر الحذف
    cardsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('deleteExpenses')) {
            var card = event.target.closest('.col-sm-12.col-md-6.col-lg-4');
            var rowIndex = card.dataset.rowIndex;
            card.remove();
        }
    });
});
</script>

<!-- نهاية معالجة المصروفات -->

    <script>
    
    function uploadajax(ttl, cl, id) {
    var fileList = $('#multiupload').prop("files");

    if (fileList.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'تحذير',
            text: 'الرجاء اختيار ملفات للرفع قبل النقر على زر التحميل.',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
    
    var form_data = new FormData();
    form_data.append("upload_image", fileList[cl]);
    form_data.append("id", id);

    $.ajax({
        url: "req/uploadFiles.php",
        cache: false,
        contentType: false,
        processData: false,
        async: true,
        data: form_data,
        type: 'POST',
        xhr: function() {
            var xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', function(event) {
                    var percent = 0;
                    if (event.lengthComputable) {
                        percent = Math.ceil(event.loaded / event.total * 100);
                    }
                    $('#prog' + cl).css('width', percent + '%').text(percent + '%');
                }, false);
            }
            return xhr;
        },
        success: function(res, status) {
            if (status == 'success') {
                $('#prog' + cl).css('width', '100%').text('100%');
                if (cl < ttl) {
                    uploadajax(ttl, cl + 1, id);
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم رفع جميع الملفات بنجاح.',
                        showConfirmButton: false,
                        timer: 2000,
                        willClose: function() {
                            $('#upload_files_modal').modal('hide');
                            location.reload();
                        }
                    });
                }
            }
            
        },
        fail: function(res) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'فشل الرفع، حاول مرة أخرى أو تواصل مع الدعم الفني',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
    });
}

$('#upcvr').click(function() {
    var fileList = $('#multiupload').prop("files");
    var id = '<?= $_GET["id"] ?>';
    $('#uploadsts').html('');

    if (fileList.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'تحذير',
            text: 'الرجاء اختيار ملفات للرفع قبل النقر على زر التحميل.',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
    
    for (var i = 0; i < fileList.length; i++) {
        $('#uploadsts').append(
            '<div class="mb-3">' +
                '<p>' + fileList[i].name + '</p>' +
                '<div class="progress">' +
                    '<div class="progress-bar" id="prog' + i + '" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>' +
                '</div>' +
            '</div>'
        );
        if (i == fileList.length - 1) {
            uploadajax(fileList.length - 1, 0, id);
        }
    }
});

    </script>
<script src="../js/view_cases.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#phone");
            if (input) {
                var iti = window.intlTelInput(input, {
                    initialCountry: "auto",
                    geoIpLookup: function(callback) {
                        fetch('https://ipinfo.io/json', { headers: { 'Accept': 'application/json' }})
                            .then(response => response.json())
                            .then(data => callback(data.country))
                            .catch(() => callback("us"));
                    },
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
                });

                input.addEventListener('blur', function() {
                    var phoneNumber = input.value;
                    var regionCode = iti.getSelectedCountryData().iso2;
                    try {
                        var parsedNumber = libphonenumber.parsePhoneNumberFromString(phoneNumber, regionCode.toUpperCase());
                        if (parsedNumber && parsedNumber.isValid()) {
                            input.value = parsedNumber.formatInternational();
                        } else {
                            alert('الرجاء إدخال رقم هاتف صحيح');
                        }
                    } catch (error) {
                        alert('حدث خطأ أثناء معالجة الرقم، الرجاء المحاولة مرة أخرى');
                    }
                });
            }
        });
    </script>
       <?php if ($pages['add_old_session']['add'] == 0) : ?>
<script>
    function validateDate(input) {
        var today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        var hijriInput = input.closest('.form-group').parentElement.querySelector('.hejri-input');

        if (input.value < today) {
            input.value = ''; // تفريغ الحقل الميلادي
            if (hijriInput) {
                hijriInput.value = ''; // تفريغ الحقل الهجري
                hijriInput.classList.add('is-invalid');
                hijriInput.classList.remove('is-valid');
            }
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
            if (hijriInput) {
                hijriInput.classList.add('is-valid');
                hijriInput.classList.remove('is-invalid');
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        function attachEventListeners() {
            // التحقق من تواريخ الجلسات عند تغيير التاريخ الميلادي
            document.querySelectorAll('.geogra-input').forEach(function(input) {
                input.addEventListener('change', function() {
                    validateDate(input);
                });
            });
        }

        attachEventListeners();

        // التأكد من تطبيق المستمعين للأحداث عند إضافة بطاقات جديدة
        var addButton = document.getElementById('addRowBtn');
        addButton.addEventListener('click', function() {
            setTimeout(attachEventListeners, 500); // انتظر نصف ثانية لتطبيق المستمعين
        });

        // مراقبة التغييرات في قيم الحقول بشكل دوري
        var prevGeoValues = new Map();

        setInterval(function() {
            document.querySelectorAll('.geogra-input').forEach(function(geoInput) {
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
        }else{
            header("location: cases.php");
        }

    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
} 
?>
