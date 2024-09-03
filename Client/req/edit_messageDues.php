<?php

session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Client') {
include "../../DB_connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newMessage = $_POST['message_text'];
    $messageId = $_POST['id'];

    // تحويل الأسماء العربية إلى المتغيرات الحقيقية قبل الحفظ
    $variables = [
        'الاسم الأول للعميل' => '{$client_first_name}',
        'اسم العائلة للعميل' => '{$client_last_name}',
        'عنوان القضية' => '{$case_title}',
        'مبلغ الدفعة' => '{$amountMoney}',
        'تاريخ الدفع ميلادي' => '{$payment_date}',
        'تاريخ الدفع هجري' => '{$payment_date_hiri}'
    ];

    foreach ($variables as $key => $value) {
        $newMessage = str_replace($key, $value, $newMessage);
    }

    $office_id = $_POST['office_id'];
    $for_whom = $_POST['for_whom'];

    // تعديل الرسالة في قاعدة البيانات
    $stmt = $conn->prepare("UPDATE templates SET message_text = ?, office_id = ?, for_whom = ? WHERE id = ?");
    $stmt->bindParam(1, $newMessage, PDO::PARAM_STR);
    $stmt->bindParam(2, $office_id, PDO::PARAM_INT);
    $stmt->bindParam(3, $for_whom, PDO::PARAM_INT);
    $stmt->bindParam(4, $messageId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
    exit;
}
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
