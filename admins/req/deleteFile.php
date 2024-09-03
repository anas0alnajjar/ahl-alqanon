<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admins') {
        include "../../DB_connection.php";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $fileId = $data['id'];

            if (empty($fileId)) {
                echo json_encode(['success' => false, 'message' => 'لم يتم تحديد معرف الملف.']);
                exit;
            }

            // جلب اسم الملف بناءً على ID
            $stmt = $conn->prepare('SELECT file_path FROM files WHERE id = :id');
            $stmt->execute(['id' => $fileId]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($file) {
                $filePath = '../../Lawyer/files/' . $file['file_path'];

                // حذف الملف من المجلد
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        // حذف السجل من قاعدة البيانات
                        $stmt = $conn->prepare('DELETE FROM files WHERE id = :id');
                        if ($stmt->execute(['id' => $fileId])) {
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'فشل في حذف السجل من قاعدة البيانات']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'فشل في حذف الملف من الخادم']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'الملف غير موجود على الخادم']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'الملف غير موجود']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'طلب غير صالح']);
        }
    } else {
        header("Location: ../cases.php");
        exit;
    }
} else {
    header("Location: ../cases.php");
    exit;
}
?>
