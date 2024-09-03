<?php
// تعيين الجلسة
session_start();

// تضمين ملف الاتصال بقاعدة البيانات
include "../../DB_connection.php";

// التحقق من استقبال بيانات النموذج بشكل صحيح
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تهيئة مصفوفة لتخزين بيانات النموذج
    $formData = $_POST;
    // التحقق مما إذا كان المعرف 'client_id' موجودًا في بيانات النموذج
    if (!isset($formData['client_id'])) {
        echo json_encode(['error' => "Missing 'client_id' parameter."]);
        exit;
    }

    $clientId = $formData['client_id']; // الحصول على معرف الموكل
    $officeId = $formData['office_idClient'];

    try {
        // بدء عملية المعاملة
        $conn->beginTransaction();

        // إزالة المعرف 'client_id' من بيانات النموذج لأنه ليس للتحديث
        unset($formData['client_id']);
        unset($formData['office_idClient']);
        $formData['office_id'] = $officeId;

        // إعداد عبارة SQL لتحديث سجل الموكل
        $sql = "UPDATE clients SET ";
        $updates = [];

        // بناء عبارة SQL UPDATE
        foreach ($formData as $key => $value) {
            $updates[] = "$key = ?";
        }
        $sql .= implode(", ", $updates) . " WHERE client_id = ?";

        // تحضير وتنفيذ عبارة SQL
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_merge(array_values($formData), [$clientId]));

        // تأكيد عملية المعاملة
        $conn->commit();
        
        // إرجاع رسالة النجاح
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // إلغاء المعاملة في حالة حدوث خطأ
        $conn->rollBack();
        
        echo json_encode(['error' => "Unable to update record. " . $e->getMessage()]);
    }
} else {
    // إعادة توجيه المستخدم إلى الصفحة الرئيسية إذا لم يكن الطلب من نوع POST
    header("Location: cases.php");
    exit();
}
?>



