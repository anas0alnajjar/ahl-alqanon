<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Client') {
include "../../DB_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $office_id = $_POST['office_id'];
    $amount = $_POST['amount'];
    $cost_type = $_POST['cost_type'];
    $date_geo = $_POST['date_geo'];
    $date_hijri = $_POST['date_hijri'];
    $notes = $_POST['notes'];

    if (!empty($id) && !empty($office_id) && !empty($amount) && !empty($cost_type) && !empty($date_geo) && !empty($date_hijri)) {
        $sql = "UPDATE overhead_costs SET 
                    office_id = :office_id, 
                    amount = :amount, 
                    type_id = :cost_type, 
                    pay_date = :date_geo, 
                    pay_date_hijri = :date_hijri, 
                    notes_expenses = :notes 
                WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':office_id', $office_id);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':cost_type', $cost_type);
        $stmt->bindParam(':date_geo', $date_geo);
        $stmt->bindParam(':date_hijri', $date_hijri);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'تعذر تحديث البيانات']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'الرجاء تعبئة جميع الحقول المطلوبة']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طلب غير صالح']);
}
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
