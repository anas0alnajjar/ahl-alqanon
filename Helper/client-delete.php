<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role']) &&
    isset($_GET['client_id'])) {

    if ($_SESSION['role'] == 'Helper') {
        include "../DB_connection.php";
        include "data/client.php";
        include 'permissions_script.php';

        // إعدادات لتسجيل الأخطاء في ملف errors.txt
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__ . '/../errors.txt');

        if ($pages['clients']['delete'] == 0) {
            header("Location: home.php");
            exit();
        }

        function deleteClientData($client_id, $conn) {
            error_log("-----------------Starting deleteClientData-----------------");
            error_log("Starting deleteClientData for client_id: $client_id");

            // الحصول على معرف القضايا المرتبطة بالعميل
            $stmt = $conn->prepare("SELECT case_id FROM cases WHERE client_id = ?");
            $stmt->execute([$client_id]);
            $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cases as $case) {
                $case_id = $case['case_id'];
                error_log("Deleting data for case_id: $case_id");
                
                // الحصول على أسماء الملفات المرتبطة بالقضية
                $stmt = $conn->prepare("SELECT file_path FROM files WHERE case_id = ?");
                
                $stmt->execute([$case_id]);
                $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // حذف الملفات من النظام
                foreach ($files as $file) {
                    $filePath = '../Lawyer/files/' . $file['file_path']; 
                    if (file_exists($filePath)) {
                        unlink($filePath);
                        error_log("Deleted file: $filePath");
                    } else {
                        error_log("File not found: " . $filePath); // سجل خطأ في حالة عدم وجود الملف
                    }
                }

                // الحصول على أسماء الملفات المرتبطة بالعقود
                $stmt = $conn->prepare("SELECT attachments FROM documents WHERE case_id = ?");
                $stmt->execute([$case_id]);
                $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // حذف الملفات من النظام
                foreach ($documents as $docu) {
                    $filePathDoc = '../pdf/' . $docu['attachments']; 
                    if (file_exists($filePathDoc)) {
                        unlink($filePathDoc);
                        error_log("Deleted document file: $filePathDoc");
                    } else {
                        error_log("File not found: " . $filePathDoc); // سجل خطأ في حالة عدم وجود الملف
                    }
                }

                // إجراء عملية الحذف في قاعدة البيانات
                $tables_to_delete_from = [
                    'cases', 'sessions', 'expenses', 'payments', 'documents', 'files', 
                    'reminder_due', 'sent_notifications_sessions', 'todos'
                ];
                
                foreach ($tables_to_delete_from as $table) {
                    $stmt = $conn->prepare("DELETE FROM $table WHERE case_id = ?");
                    if (!$stmt->execute([$case_id])) {
                        error_log("Failed to delete from $table for case_id: $case_id");
                    } else {
                        error_log("Deleted from $table for case_id: $case_id");
                    }
                }
            }

            // حذف البيانات الأخرى المرتبطة بالعميل
            $tables_to_delete_client_from = [
                'clients', 'events', 'documents'
            ];

            foreach ($tables_to_delete_client_from as $table) {
                $stmt = $conn->prepare("DELETE FROM $table WHERE client_id = ?");
                if (!$stmt->execute([$client_id])) {
                    error_log("Failed to delete from $table for client_id: $client_id");
                } else {
                    error_log("Deleted from $table for client_id: $client_id");
                }
            }
            error_log("-----------------Ending deleteClientData-----------------");
        }

        $id = $_GET['client_id'];
        deleteClientData($id, $conn);
        if (removeClient($id, $conn)) {
            $sm = "تم الحذف بنجاح!";
            header("Location: clients.php?success=$sm");
            exit();
        } else {
            $em = "حدث خطأ غير معروف";
            header("Location: clients.php?error=$em");
            exit();
        }

    } else {
        header("Location: ../cases.php");
        exit();
    }
} else {
    header("Location: ../cases.php");
    exit();
}
?>
