<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && isset($_GET['id'])) {

    if ($_SESSION['role'] == 'Admins') {
        include "../../DB_connection.php";
        include '../permissions_script.php';

        // إعدادات لتسجيل الأخطاء في ملف errors.txt
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__ . '/../../errors.txt');

        if ($pages['offices']['delete'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        function deleteCaseData($office_id, $conn) {
            error_log("Starting deleteCaseData for office_id: $office_id");

            // الحصول على معرف القضايا المرتبطة بالمكتب
            $stmt = $conn->prepare("SELECT case_id FROM cases WHERE office_id = ?");
            $stmt->execute([$office_id]);
            $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cases as $case) {
                $case_id = $case['case_id'];
                error_log("Deleting data for case_id: $case_id");
                
                // الحصول على ملف هوية الموكل
                $stmt = $conn->prepare("SELECT id_picture FROM cases WHERE case_id = ?");
                $stmt->execute([$case_id]);
                $client_picture = $stmt->fetch(PDO::FETCH_ASSOC);

                // الحصول على أسماء الملفات المرتبطة بالقضية
                $stmt = $conn->prepare("SELECT file_path FROM files WHERE case_id = ?");
                $stmt->execute([$case_id]);
                $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // حذف الملفات من النظام
                foreach ($files as $file) {
                    $filePath = '../../Lawyer/files/' . $file['file_path']; 
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
                    $filePathDoc = '../../pdf/' . $docu['attachments']; 
                    if (file_exists($filePathDoc)) {
                        unlink($filePathDoc);
                        error_log("Deleted document file: $filePathDoc");
                    } else {
                        error_log("File not found: " . $filePathDoc); // سجل خطأ في حالة عدم وجود الملف
                    }
                }

                // حذف ملف الهوية من النظام
                if ($client_picture) {
                    $picturePath = '../../uploads/' . $client_picture['id_picture'];
                    if (file_exists($picturePath)) {
                        unlink($picturePath);
                        error_log("Deleted client picture: $picturePath");
                    } else {
                        error_log("Picture not found: " . $picturePath); // سجل خطأ في حالة عدم وجود ملف الهوية
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
        }

        function removeOfficeData($office_id, $conn) {
            error_log("Starting removeOfficeData for office_id: $office_id");

            // حذف جميع البيانات المرتبطة بالمكتب
            $tables_to_delete_from = [
                'lawyer', 'managers_office', 'types_of_cases', 'overhead_costs', 
                'clients', 'templates', 'profiles', 'powers', 'headers', 'helpers', 
                'adversaries', 'costs_type', 'courts', 'departments', 'documents'
            ];

            foreach ($tables_to_delete_from as $table) {
                $stmt = $conn->prepare("DELETE FROM $table WHERE office_id = ?");
                if (!$stmt->execute([$office_id])) {
                    error_log("Failed to delete from $table for office_id: $office_id");
                } else {
                    error_log("Deleted from $table for office_id: $office_id");
                }
            }

            // حذف المكتب نفسه
            $stmt = $conn->prepare("DELETE FROM offices WHERE office_id = ?");
            if (!$stmt->execute([$office_id])) {
                error_log("Failed to delete office with office_id: $office_id");
                return false;
            }
            error_log("Deleted office with office_id: $office_id");
            return true;
        }

        $id = $_GET['id'];
        deleteCaseData($id, $conn);
        if (removeOfficeData($id, $conn)) {
            $sm = "تم الحذف بنجاح";
            header("Location: ../offices.php?success=$sm");
            exit();
        } else {
            $em = "حدث خطأ غير معروف";
            header("Location: ../offices.php?error=$em");
            exit();
        }

    } else {
        header("Location: ../offices.php");
        exit();
    } 
} else {
    header("Location: ../offices.php");
    exit();
} 
?>
