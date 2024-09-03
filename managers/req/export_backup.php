<?php

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";

        try {
            // الحصول على قائمة الجداول في قاعدة البيانات
            $tables = array();
            $sql = "SHOW TABLES";
            $result = $conn->query($sql);
            $tables = $result->fetchAll(PDO::FETCH_COLUMN);

            if (empty($tables)) {
                throw new Exception("No tables found in the database.");
            }

            // إعداد النص لنسخ هيكل وبيانات الجداول
            $sqlScript = "";
            $index = 0;
            foreach ($tables as $table) { 
                // جلب هيكل الجدول
                $query = "SHOW CREATE TABLE $table";
                $result = $conn->query($query);
                $row = $result->fetch(PDO::FETCH_ASSOC);

                // تعديل النص ليتضمن IF NOT EXISTS
                $createTableSql = preg_replace('/CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $row['Create Table'], 1);
                $sqlScript .= "\n\n" . $createTableSql . ";\n\n";

                // جلب البيانات من الجدول
                $query = "SELECT * FROM $table";
                $result = $conn->query($query);
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);

                // إعداد النص للبيانات
                foreach ($rows as $row) {
                    $sqlScript .= "INSERT INTO $table VALUES(";
                    $sqlScript .= implode(", ", array_map(function ($value) use ($conn) {
                        return $value !== null ? $conn->quote($value) : 'NULL';
                    }, array_values($row)));
                    $sqlScript .= ");\n";
                }
                echo json_encode(array('current' => $index + 1, 'total' => count($tables)));
                flush();
                ob_flush();
            }

            // حفظ النص إلى ملف الباك أب
            $backupFileName = $db_name . '_backup_' . time() . '.sql';
            file_put_contents($backupFileName, $sqlScript);

            // إنشاء ملف ZIP
            $zipFileName = $db_name . '_backup_' . time() . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
                // إضافة ملف الباك أب إلى ملف ZIP
                $zip->addFile($backupFileName, basename($backupFileName));
                $zip->close();

                // إعداد الاستجابة لتنزيل ملف ZIP
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename=' . basename($zipFileName));
                header('Content-Length: ' . filesize($zipFileName));
                readfile($zipFileName);

                // حذف ملفات الباك أب والZIP بعد التنزيل
                unlink($backupFileName);
                unlink($zipFileName);
                exit;
            } else {
                throw new Exception("Failed to create ZIP file!");
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            // حذف الملفات في حال حدوث أي خطأ
            if (file_exists($backupFileName)) {
                unlink($backupFileName);
            }
            if (file_exists($zipFileName)) {
                unlink($zipFileName);
            }
        }
    } else {
        header("Location: ../../login.php");
        exit;
    }
} else {
    header("Location: ../../login.php");
    exit;
}
?>
