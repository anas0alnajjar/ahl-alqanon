<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once '../../DB_connection.php';

    if (isset($_POST['export_excel_btn'])) {
        $file_ext_name = $_POST['export_file_type'];
        $fileName = "exported_data";

        $selected_table = $_POST['selected_table'];
        $selected_columns = explode(',', $_POST['selected_columns']);
        $number_of_records = $_POST['number_of_records'];

        // تحويل أسماء الأعمدة إلى اللغة العربية
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

         $translated_columns = array_map(function($col) use ($columnTranslations) {
            return isset($columnTranslations[$col]) ? $columnTranslations[$col] : $col;
        }, $selected_columns);

        // تأكد من تضمين عمود المعرف في الاستعلام
        $primaryKey = isset($primaryKeys[$selected_table]) ? $primaryKeys[$selected_table] : 'id';
        if (!in_array($primaryKey, $selected_columns)) {
            array_unshift($selected_columns, $primaryKey);
            array_unshift($translated_columns, isset($columnTranslations[$primaryKey]) ? $columnTranslations[$primaryKey] : $primaryKey);
        }

        $columns_str = implode(", ", $selected_columns);

        if ($number_of_records == 'some') {
            $query = "SELECT $columns_str FROM $selected_table LIMIT 5";
        } else {
            $query = "SELECT $columns_str FROM $selected_table";
        }

        $stmt = $conn->query($query);
        if ($stmt) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // إعداد رؤوس الأعمدة
                $columnLetter = 'A';
                foreach ($translated_columns as $col) {
                    $sheet->setCellValue($columnLetter . '1', $col);
                    $columnLetter++;
                }

                $rowCount = 2;
                foreach ($result as $data) {
                    $columnLetter = 'A';
                    foreach ($selected_columns as $col) {
                        $sheet->setCellValue($columnLetter . $rowCount, $data[$col]);
                        $columnLetter++;
                    }
                    $rowCount++;
                }

                ob_start(); // بدء التخزين المؤقت للإخراج
                switch ($file_ext_name) {
                    case 'xlsx':
                        $writer = new Xlsx($spreadsheet);
                        $final_filename = $fileName . '.xlsx';
                        break;
                    case 'xls':
                        $writer = new Xls($spreadsheet);
                        $final_filename = $fileName . '.xls';
                        break;
                    case 'csv':
                        $writer = new Csv($spreadsheet);
                        $final_filename = $fileName . '.csv';
                        break;
                    default:
                        $writer = new Xlsx($spreadsheet);
                        $final_filename = $fileName . '.xlsx';
                }

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . urlencode($final_filename) . '"');
                $writer->save('php://output');
                ob_end_flush(); // إنهاء التخزين المؤقت للإخراج وإرسال جميع المخرجات إلى المتصفح
            } else {
                $_SESSION['message'] = "No Record Found";
                header('Location: ../import_cases.php');
                exit(0);
            }
        } else {
            echo "Failed to execute query!";
        }
    }
} else {
    header("Location: ../../login.php");
    exit;
}
?>