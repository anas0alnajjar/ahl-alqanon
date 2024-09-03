<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
    include "../../DB_connection.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_office_id'], $_POST['old_office_id'])) {
        $new_office_id = $_POST['new_office_id'];
        $old_office_id = $_POST['old_office_id'];

        try {
            $conn->beginTransaction();

            // جلب اسم المكتب القديم والجديد
            $stmt_old_office = $conn->prepare("SELECT office_name FROM offices WHERE office_id = ?");
            $stmt_old_office->execute([$old_office_id]);
            $old_office_name = $stmt_old_office->fetchColumn();

            $stmt_new_office = $conn->prepare("SELECT office_name FROM offices WHERE office_id = ?");
            $stmt_new_office->execute([$new_office_id]);
            $new_office_name = $stmt_new_office->fetchColumn();

            // جلب القضايا المرتبطة بالمكتب القديم
            $stmt_cases = $conn->prepare("SELECT case_id, notes FROM cases WHERE office_id = ?");
            $stmt_cases->execute([$old_office_id]);
            $cases = $stmt_cases->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cases as $case) {
                $case_id = $case['case_id'];
                $old_notes = $case['notes'];

                $transfer_notes = "\nالمكتب القديم: $old_office_name\nتاريخ النقل من المكتب ($old_office_name): " . date('Y-m-d');

                $new_notes = $old_notes . $transfer_notes;

                // تحديث حقل notes بالقيمة الجديدة
                $stmt_update_notes = $conn->prepare("UPDATE cases SET notes = :new_notes WHERE case_id = :case_id");
                $stmt_update_notes->bindParam(':new_notes', $new_notes);
                $stmt_update_notes->bindParam(':case_id', $case_id);
                $stmt_update_notes->execute();
            }

            // تحديث جميع الجداول المرتبطة بالمكتب القديم لتشير إلى المكتب الجديد
            $tables = ['lawyer', 'managers_office', 'types_of_cases', 'overhead_costs', 'clients', 'templates', 'cases', 'profiles', 'powers', 'headers', 'helpers', 'adversaries', 'costs_type', 'courts', 'departments', 'documents'];

            foreach ($tables as $table) {
                $sql = "UPDATE $table SET office_id = :new_office_id WHERE office_id = :old_office_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':new_office_id', $new_office_id);
                $stmt->bindParam(':old_office_id', $old_office_id);
                $stmt->execute();
            }

            $conn->commit();

            echo json_encode(['success' => true, 'message' => 'تم الترحيل بنجاح']);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء عملية الترحيل: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'بيانات غير مكتملة']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'غير مصرح لك بالوصول']);
}
?>
