<?php 
session_start();
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {
        include_once '../DB_connection.php';



        include 'permissions_script.php';
        if ($pages['import']['read'] == 0) {
            header("Location: home.php");
            exit();
        }
        
        include "logo.php";

        if (isset($_POST['clear_data'])) {
            unset($_SESSION['imported_data']);
        }

        // استعلام SQL لاسترجاع أسماء الجداول في قاعدة البيانات
        $sqlTables = "SHOW TABLES";
        $stmtTables = $conn->prepare($sqlTables);
        $stmtTables->execute();
        $tables = $stmtTables->fetchAll(PDO::FETCH_COLUMN);

        // مصفوفة لتحويل أسماء الأعمدة إلى العربية
        $columnTranslations = [
            'id' => 'معرف',
            'case_id' => 'معرف القضية',
            'admin_id' => 'معرف المسؤول',
            'case_number' => 'رقم القضية',
            'case_date' => 'تاريخ القضية',
            'username' => 'اسم المستخدم',
            'password' => 'كلمة السر',
            'fname' => 'الاسم الأول',
            'lname' => 'اسم العائلة',
            'role_id' => 'معرف الدور',
            'stop' => 'إيقاف مؤقت',
            'stop_date' => 'تاريخ الإيقاف',
            'address' => 'العنوان',
            'email_address' => 'الإيميل',
            'date_of_birth' => 'تاريخ الولادة',
            'city' => 'المدينة',
            'phone' => 'الهاتف',
            'gender' => 'الجنس',
            'office_id' => 'معرف المكتب',
            'first_name' => 'الاسم الأول',
            'last_name' => 'اسم العائلة',
            'email' => 'الإيميل',
            'as_a' => 'كعميل/ كمحامي',
            'case_title' => 'عنوان القضية',
            'case_type' => 'نوع القضية',
            'case_description' => 'وصف القضية',
            'client_id' => 'معرف الموكل',
            'lawyer_id' => 'معرف المحامي',
            'agency' => 'وكالة',
            'plaintiff' => 'المدعي',
            'defendant' => 'المدعى عليه',
            'court_name' => 'المحكمة',
            'department' => 'الدائرة',
            'notes' => 'ملاحظات',
            'id_picture' => 'صورة هوية الموكل',
            'judge_name' => 'اسم القاضي',
            'helper_name' => 'اسم الإداري',
            'father_name' => 'اسم الأب',
            'grandfather_name' => 'اسم الجد',
            'national_num' => 'الرقم القومي',
            'alhi' => 'الحي',
            'street_name' => 'اسم الشارع',
            'num_build' => 'رقم المبنى',
            'num_unit' => 'رقم الوحدة',
            'zip_code' => 'الرمز البريدي',
            'subnumber' => 'الرقم الفرعي',
            'receive_emails' => 'استقبال الإيميلات',
            'receive_whatsupp' => 'استقبال واتساب',
            'client_passport' => 'رقم جواز السفر',
            'type' => 'النوع',
            'title' => 'العنوان',
            'content' => 'محتوى الوثيقة',
            'attachments' => 'المرفق',
            'event_name' => 'اسم الحدث',
            'event_start_date' => 'تاريخ البداية للحدث',
            'event_end_date' => 'تاريخ النهاية للحدث',
            'helper_id' => 'معرف الإداري',
            'pay_date' => 'تاريخ الدفع',
            'amount' => 'المبلغ',
            'notes_expenses' => 'ملاحظات الدفعة',
            'pay_date_hijri' => 'تاريخ الدفع هجري',
            'file_name' => 'اسم الملف',
            'file_path' => 'اسم الملف على الخادم',
            'created_date' => 'تاريخ الرفع',
            'pass' => 'كلمة السر',
            'national_helper' => 'الرقم الوطني',
            'passport_helper' => 'جواز السفر',
            'lawyer_name' => 'اسم المحامي',
            'lawyer_email' => 'الايميل',
            'lawyer_phone' => 'الهاتف',
            'lawyer_password' => 'كلمة السر',
            'lawyer_address' => 'العنوان',
            'lawyer_gender' => 'الجنس',
            'lawyer_city' => 'المدينة',
            'lawyer_logo' => 'اللوغو',
            'preferred_date' => 'التاريخ المفضل',
            'lawyer_passport' => 'جواز السفر',
            'lawyer_national' => 'الرقم الوطني',
            'manager_name' => 'اسم مدير المكتب',
            'manager_email' => 'ايميل مدير المكتب',
            'manager_phone' => 'هاتف مدير المكتب',
            'manager_password' => 'كلمة السر',
            'manager_address' => 'عنوان مدير المكتب',
            'manager_gender' => 'الجنس',
            'manager_city' => 'المدينة',
            'manager_national' => 'الرقم القومي',
            'manager_passport' => 'رقم جواز السفر',
            'sender_full_name' => 'اسم المرسل',
            'sender_email' => 'ايميل المرسل',
            'message' => 'الرسالة',
            'date_time' => 'تاريخ الإرسال',
            'office_name' => 'اسم المكتب',
            'type_id' => 'معرف النوع',
            'page_name' => 'اسم الصفحة',
            'can_read' => 'قراءة',
            'can_write' => 'كتابة',
            'can_add' => 'إضافة',
            'can_delete' => 'حذف',
            'amount_paid' => 'مبلغ الدفعة',
            'payment_date' => 'تاريخ الدفعة',
            'payment_method' => 'طريقة الدفع',
            'payment_date_hiri' => 'تاريخ الدفع هجري',
            'payment_notes' => 'ملاحظات الدفعة',
            'received' => 'وضع الاستلام',
            'role' => 'الدور',
            'message_date' => 'تاريخ الرسالة',
            'phone_used' => 'ايميل/رقم',
            'type_notifcation' => 'قناة الاتصال',
            'session_id' => 'معرف الجلسة',
            'recipient_email' => 'ايميل المستلم',
            'recipient_phone' => 'هاتف المستلم',
            'sent_date' => 'تاريخ التذكير',
            'session_number' => 'رقم الجلسة',
            'session_date' => 'تاريخ الجلسة',
            'session_hour' => 'ساعة الجلسة',
            'session_date_hjri' => 'تاريخ الجلسة هجري',
            'current_year' => 'السنة الحالية',
            'company_name' => 'اسم الشركة',
            'slogan' => 'الشعار',
            'about' => 'من نحن',
            'host_email' => 'استضافة الإيميل',
            'username_email' => 'اسم مستخدم الإيميل',
            'password_email' => 'كلمة سر الإيميل',
            'port_email' => 'البورت',
            'host_whatsapp' => 'استضافة الواتساب',
            'token_whatsapp' => 'توكين الواتساب',
            'logo' => 'اللوغو',
            'message_text' => 'نص الرسالة',
            'type_template' => 'نوع الرسالة',
            'for_whom' => 'موجهة لمن',
            'checked' => 'مقروءة',
            'priority' => 'الأولوية',
            'type_case' => 'نوع القضية',
            'client_name' => 'اسم الموكل'
            
        ];

        // مصفوفة لترجمة أسماء الجداول
        $tableTranslations = [
            'cases' => 'القضايا',
            'admin' => 'المسؤولين',
            'adversaries' => 'الخصوم',
            'ask_join' => 'طلبات الانضمام',
            'clients' => 'الموكلين',
            'costs_type' => 'أنواع المصاريف',
            'courts' => 'المحاكم',
            'departments' => 'الدوائر',
            'documents' => 'الوثائق',
            'events' => 'الأحداث',
            'expenses' => 'مصاريف الجلسات',
            'files' => 'الملفات',
            'helpers' => 'الإداريين',
            'lawyer' => 'المحامين',
            'managers_office' => 'مدراء المكاتب',
            'message' => 'الرسائل',
            'offices' => 'المكاتب',
            'overhead_costs' => 'التكاليف العامة',
            'page_permissions' => 'صلاحيات الصفحات',
            'payments' => 'الدفعات',
            'powers' => 'الأدوار',
            'reminder_due' => 'تذكيرات الاستحقاقات المالية',
            'sent_notifications_sessions' => 'تذكيرات الجلسات',
            'sessions' => 'الجلسات',
            'setting' => 'الاعدادات',
            'templates' => 'الرسائل المخصصة',
            'todos' => 'الإشعارات/ المهام',
            'types_of_cases' => 'أنواع القضايا',
            // أضف المزيد حسب الحاجة
        ];

        // مصفوفة لتعريف الأعمدة المعرّفة لكل جدول
        $primaryKeys = [
            'cases' => 'case_id',
            'admin' => 'admin_id',
            'ask_join' => 'user_id',
            'documents' => 'document_id',
            'events' => 'event_id',
            'message' => 'message_id',
            'offices' => 'office_id',
            'powers' => 'power_id',
            'sessions' => 'sessions_id',
            'clients' => 'client_id',
            'lawyer' => 'lawyer_id',
            
            // أضف المزيد حسب الحاجة
        ];

        // مصفوفة للأعمدة المطلوبة بشكل أساسي لكل جدول
        $requiredColumns = [
            'cases' => ['client_id', 'case_title', 'lawyer_id', 'office_id'],            
        ];

        $columns_without_id = [];
        $primaryKey = '';
        $selected_table = '';

        if (isset($_POST['table_name'])) {
            $selected_table = $_POST['table_name'];

            // استعلام SQL لاسترجاع أسماء الأعمدة في الجدول
            $sqlColumns = "SHOW COLUMNS FROM $selected_table";
            $stmtColumns = $conn->prepare($sqlColumns);
            $stmtColumns->execute();
            $columns = $stmtColumns->fetchAll(PDO::FETCH_COLUMN);

            // تحديد العمود المعرّف للجدول المختار
            $primaryKey = isset($primaryKeys[$selected_table]) ? $primaryKeys[$selected_table] : 'id';
            
            // استبعاد عمود المعرّف
            $columns_without_id = array_diff($columns, [$primaryKey]);
        }

        if (isset($_POST['save_data'])) {
            $selected_columns = $_POST['columns'];
            array_unshift($selected_columns, $primaryKey); // إضافة العمود المعرّف كأول عمود دائمًا

            $fileName = $_FILES['import_file']['name'];
            $allow_ext = ['xls', 'xlsx', 'csv'];
            $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

            if (in_array($file_ext, $allow_ext)) {
                $inputFileNamePath = $_FILES['import_file']['tmp_name'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
                $data = $spreadsheet->getActiveSheet()->toArray();
                $count = 0;
                $importData = [];
                foreach ($data as $row) {
                    if ($count > 0) {
                        $importDataRow = [];
                        $importDataRow[$primaryKey] = isset($row[0]) ? $row[0] : ''; // يفترض أن المعرّف في العمود الأول من ملف Excel

                        foreach ($selected_columns as $index => $column) {
                            if ($column !== $primaryKey) {
                                $importDataRow[$column] = isset($row[$index]) ? $row[$index] : '';
                            }
                        }

                        $importData[] = $importDataRow;
                    } else {
                        $count = 1;
                    }
                }
                $_SESSION['imported_data'] = $importData;
            } else {
                header('Location: import_cases.php?error=Invalid file extension!');
                exit;
            }
        } else {
            $importData = isset($_SESSION['imported_data']) ? $_SESSION['imported_data'] : [];
        }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استيراد البيانات</title>
  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style-import-data.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        body {
            direction: rtl;
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        th, td {
            white-space: nowrap;
        }
        #progressBar {
            width: 100%;
            height: 30px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        #progress {
            width: 0%;
            height: 100%;
            background-color: #4CAF50;
        }
        .container {
            min-width: 90%;
        }
        .container-young {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .form-control, .form-select {
            border-radius: 0.25rem;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .modal-content {
            border-radius: 8px;
        }
        .form-w {
            min-width: 90%;
        }
        .table-n {
            min-width: 90%;
        }
        .help-icon {
            cursor: pointer;
            font-size: 24px;
            color: #007bff;
        }
        .instructions {
            display: none;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            line-height: 1.6;
            margin-top: 15px;
            margin-right:auto;
            margin-left:auto;
            max-width:90%;
        }
        .instructions p {
            margin-bottom: 15px;
        }
        .instructions ol {
            margin-bottom: 15px;
        }
        .instructions ol li {
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container">
        <div class="container-young">
            <h3>استيراد البيانات <i class="fas fa-info-circle help-icon" onclick="toggleInstructions()"></i></h3>
              <div style="direction:rtl !important;" class="text-center">
                <a href="home.php" style="min-width:90% !important;" class="btn btn-light">الرئيسية</a>
            </div>
            <div class="instructions">
                <p>يرجى اتباع الخطوات التالية لإنشاء أو تحديث البيانات:</p>
                <ol>
                    <li>اختر الجدول المراد إنشاء أو تحديث البيانات فيه.</li>
                    <li>اختر الأعمدة التي تريد تحديثها أو إدخال بيانات جديدة فيها.</li>
                    <li>قم بإسقاط البيانات في ملف Excel بما يتوافق مع الأعمدة المختارة.</li>
                </ol>
                <p>احرص على أن يكون المعرف في العمود الأول عند التحديث. في حال الإنشاء، ضع معرف بكلمة <strong>new</strong>.</p>
                <p>يمكنك الاطلاع على عينة من الجدول قبل الإنشاء فيه بالضغط على زر تصدير.</p>
            </div>

            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php } ?>
            <div class="container mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="table_name">اختر الجدول:</label>
                                <select class="form-select" name="table_name" onchange="this.form.submit()" required>
                                    <option value="" disabled selected>اختر الجدول</option>
                                    <?php foreach ($tables as $table) {
                                        $translatedTable = isset($tableTranslations[$table]) ? $tableTranslations[$table] : $table;
                                        $selected = ($table == $selected_table) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($table) . '" ' . $selected . '>' . htmlspecialchars($translatedTable) . '</option>';
                                    } ?>
                                </select>
                                <button type="button" class="btn btn-link" id="export-file">تصدير</button>
                            </div>
                            <div class="form-group mb-3">
                                <label for="columns">اختر الأعمدة للتحديث:</label>
                                <select class="form-select" name="columns[]" multiple required>
                                    <?php
                                    foreach ($columns_without_id as $column) {
                                        $translatedColumn = isset($columnTranslations[$column]) ? $columnTranslations[$column] : $column;
                                        $required = isset($requiredColumns[$selected_table]) && in_array($column, $requiredColumns[$selected_table]) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($column) . '" ' . $required . '>' . htmlspecialchars($translatedColumn) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                    </div>
                    <?php if ($pages['import']['write'] && $pages['import']['add']) : ?>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="import_file">اختر الملف للاستيراد:</label>
                            <input type="file" name="import_file" class="form-control" required>
                        </div>
                        <button type="submit" name="save_data" class="btn btn-primary">استيراد</button>
                    </div>
                    <?php endif; ?>
                        </form>
                </div>
            </div>

            <div id="progressBar" style="display: none;">
                <div id="progress"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mt-3">
                    <thead>
                        <tr>
                            <th>معرف</th>
                            <?php 
                            if (!empty($_POST['columns'])) {
                                foreach ($_POST['columns'] as $column) {
                                    if ($column !== $primaryKey) {
                                        $translatedColumn = isset($columnTranslations[$column]) ? $columnTranslations[$column] : $column;
                                        echo '<th>' . htmlspecialchars($translatedColumn, ENT_QUOTES, 'UTF-8') . '</th>';
                                    }
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <form action="update.php" method="post" id="updateCases">
                            <?php
                            if (!empty($importData)) {
                                foreach ($importData as $key => $row) {
                                    echo '<tr>';
                                    echo '<td><input type="text" class="form-control required-field" name="data[' . htmlspecialchars($key) . '][' . htmlspecialchars($primaryKey) . ']" value="' . htmlspecialchars($row[$primaryKey] ?? '', ENT_QUOTES, 'UTF-8') . '" readonly></td>';
                                    foreach ($_POST['columns'] as $column) {
                                        if ($column !== $primaryKey) {
                                            $field_class = 'form-control required-field';
                                            $value = htmlspecialchars($row[$column] ?? '', ENT_QUOTES, 'UTF-8');
                                            echo '<td><input class="' . $field_class . '" type="text" name="data[' . htmlspecialchars($key) . '][' . htmlspecialchars($column) . ']" value="' . $value . '" readonly></td>';
                                            echo '<input type="hidden" name="data[' . htmlspecialchars($key) . '][columns][]" value="' . htmlspecialchars($column) . '">';
                                        }
                                    }
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </form>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col d-flex">
                    <?php if ($pages['import']['write'] && $pages['import']['add']) : ?>
                        <button id="confirmInsert" class="btn btn-primary me-2 disabled">تأكيد وإدخال/تحديث البيانات</button>
                    <?php endif; ?>
                    <form action="" method="POST" class="d-inline mx-2">
                        <button id="clearButton" type="submit" name="clear_data" class="btn btn-secondary">مسح</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="export_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" class="shadow p-3 form-w" action="req/export-file.php">
                    <?php if (isset($_GET['poperror'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($_GET['poperror']) ?>
                        </div>
                    <?php } ?>
                    <input type="hidden" name="selected_table">
                    <input type="hidden" name="selected_columns">
                    <div class="mb-3">
                        <select name="number_of_records" id="number_of_records" class="form-control">
                            <option value="some">أول خمس صفوف/عينة</option>
                            <option value="all">كل الجدول</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="export_file_type" class="form-control">
                            <option value="xlsx">XLSX</option>
                            <option value="xls">XLS</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="export_excel_btn" class="btn btn-primary">تصدير</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    const primaryKey = '<?= $primaryKey ?>';
</script>
<script src="../js/import_cases.js"></script>

    <script>
     $(document).ready(function() {
        
        $('.close-modal1').click(function() {
            $('#export_modal').modal('hide');
        }); });
        $('.close-modal2').click(function() {
            $('#export_modal').modal('hide');
        }); 
</script>
</html>



<?php 

} else {
    header("Location: ../../login.php");
    exit;
}
} else {
header("Location: ../../login.php");
exit;
} 
?>