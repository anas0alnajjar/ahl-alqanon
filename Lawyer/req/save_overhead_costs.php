<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {

    include "../../DB_connection.php";
    include '../permissions_script.php';

    if ($pages['expenses']['add'] == 0) {
        header("Location: ../home.php");
        exit();
    }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // احصل على البيانات من body باستخدام JSON
    $data = json_decode(file_get_contents("php://input"), true);
    
    $office_id = $data['office_id'] ?? '';
    $amount = $data['amount'] ?? '';
    $cost_type = $data['cost_type'] ?? '';
    $date_geo = $data['date_geo'] ?? '';
    $date_hijri = $data['date_hijri'] ?? '';
    $notes = $data['notes'] ?? '';

    // تتبع القيم المستلمة
    error_log("Office ID: $office_id, Amount: $amount, Cost Type: $cost_type, Date Geo: $date_geo, Date Hijri: $date_hijri, Notes: $notes");

    // تحقق من أن جميع القيم موجودة قبل المتابعة
    if (!empty($office_id) && !empty($amount) && !empty($cost_type) && !empty($date_geo) && !empty($date_hijri)) {
        try {
            // إعداد الاستعلام
            $sql = "INSERT INTO overhead_costs (office_id, amount, type_id, pay_date, pay_date_hijri, notes_expenses) VALUES (:office_id, :amount, :cost_type, :date_geo, :date_hijri, :notes)";
            $stmt = $conn->prepare($sql);

            // ربط القيم بالاستعلام
            $stmt->bindParam(':office_id', $office_id);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':cost_type', $cost_type);
            $stmt->bindParam(':date_geo', $date_geo);
            $stmt->bindParam(':date_hijri', $date_hijri);
            $stmt->bindParam(':notes', $notes);

            // تنفيذ الاستعلام
            $stmt->execute();

            // إرسال استجابة النجاح
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            // إرسال استجابة الخطأ
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        // إرسال استجابة الخطأ
        echo json_encode(['status' => 'error', 'message' => 'يرجى تعبئة جميع الحقول المطلوبة']);
    }
} else {
    // إرسال استجابة الخطأ
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح']);
}
} else {
    header("Location: ../genral_expenses.php");
    exit;
}
} else {
header("Location: ../genral_expenses.php");
exit;
}
?>
