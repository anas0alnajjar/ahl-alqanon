<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
// استقبال اسم الملف من الرابط
$file_name = isset($_GET['file']) ? $_GET['file'] : '';

// مسار الملف
$file_path = $_SERVER['DOCUMENT_ROOT'] . "/pdf/" . $file_name;

// تحديد نوع الملف
header('Content-Type: application/pdf');

// تحديد الحجم
header('Content-Length: ' . filesize($file_path));

// تحديد اسم الملف عند التحميل
header('Content-Disposition: attachment; filename="' . $file_name . '"');

// قراءة وعرض الملف
readfile($file_path);
?>
<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>