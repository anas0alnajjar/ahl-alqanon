<?php
// session_start();

// if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
//     if ($_SESSION['role'] == 'Admin') {

// // التحقق من وجود المعرف في الرابط
// if (isset($_GET['id'])) {
//     $id = $_GET['id'];

//     // إعداد الاتصال بقاعدة البيانات
//     require '../../DB_connection.php';

//     // استعلام استرجاع البيانات من جدول roles بناءً على المعرف المحدد
//     $stmt = $conn->prepare("SELECT * FROM powers WHERE power_id = :id");
//     $stmt->execute(['id' => $id]);
//     $result = $stmt->fetch(PDO::FETCH_ASSOC);

//     // التحقق من وجود نتيجة
//     if ($result) {
//         echo json_encode($result);
//     } else {
//         echo json_encode([]);
//     }
// }
// } else {
//     header("Location: ../../logout.php"); // إعادة التوجيه لتسجيل الخروج
//     exit;}
 
// } else {
// header("Location: ../../logout.php"); // إعادة التوجيه لتسجيل الخروج
// exit;
// }
?>
