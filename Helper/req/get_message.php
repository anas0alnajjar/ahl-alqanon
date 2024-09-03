<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Helper') {
include "../../DB_connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $messageId = $_GET['id'];

    // جلب بيانات الرسالة من قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM templates WHERE id = ?");
    $stmt->bindParam(1, $messageId, PDO::PARAM_INT);
    $stmt->execute();
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($message) {
        // تحويل المتغيرات النصية إلى أسماء عربية
        $variables = [
            '{$client_first_name}' => 'الاسم الأول للعميل',
            '{$client_last_name}' => 'اسم العائلة للعميل',
            '{$case_title}' => 'عنوان القضية',
            '{$dueDate}' => 'تاريخ الجلسة',
            '{$lawyer_name}' => 'اسم المحامي',
            '{$dueHour}' => 'ساعة الجلسة',
            '{$amountMoney}' => 'مبلغ الدفعة',
            '{$payment_date}' => 'تاريخ الدفع ميلادي',
            '{$payment_date_hiri}' => 'تاريخ الدفع هجري'
        ];

        $message_text_arabic = strtr($message['message_text'], $variables);

        // إعداد البيانات للعرض ك JSON
        $message['message_text'] = $message_text_arabic; // تعديل الرسالة إلى النص بالأسماء العربية

        echo json_encode(['status' => 'success', 'data' => $message]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم العثور على الرسالة.']);
    }
    exit;
}
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
