<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {

include "../../DB_connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newMessage = $_POST['message_text'];

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

    // التحقق من وجود رسالة بنفس المعايير
    $stmt = $conn->prepare("SELECT COUNT(*) FROM templates WHERE type_template = 3 AND office_id = ? AND for_whom = ?");
    $stmt->execute([$office_id, $for_whom]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'هناك رسالة من النوع نفسه وللفئة المستهدفة نفسها. بإمكانك تعديلها بدلاً من إنشاء رسالة جديدة.']);
        exit;
    }

    // حفظ الرسالة الجديدة
    $stmt = $conn->prepare("INSERT INTO templates (message_text, type_template, office_id, for_whom) VALUES (?, 3, ?, ?)");
    $stmt->bindParam(1, $newMessage, PDO::PARAM_STR);
    $stmt->bindParam(2, $office_id, PDO::PARAM_INT);
    $stmt->bindParam(3, $for_whom, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
    exit;
}

} else {
    header("Location: ../../logout.php");
    exit;
}
?>