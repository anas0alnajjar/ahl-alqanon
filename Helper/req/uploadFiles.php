<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {
        include "../../DB_connection.php";

        if (isset($_FILES["upload_image"]) && isset($_POST["id"])) {
            $id = $_POST["id"];
            $target_dir = '../../Lawyer/files/'; // تعديل المسار ليكون صحيحًا

            // الاسم الأصلي للملف
            $file_name = $_FILES["upload_image"]["name"];

            // تعقيد الاسم وإضافته إلى المسار
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_basename = pathinfo($file_name, PATHINFO_FILENAME);
            $new_file_name = $file_basename . "_" . uniqid() . "." . $file_extension;
            $target_file = $target_dir . $new_file_name;
            
            // التأكد من وجود المجلد وإنشائه إذا لم يكن موجودًا
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            if (move_uploaded_file($_FILES["upload_image"]["tmp_name"], $target_file)) {
                // حفظ بيانات الملف في قاعدة البيانات
                $sql = "INSERT INTO files (file_name, file_path, case_id) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$file_name, $new_file_name, $id]);

                echo "تم رفع الملف بنجاح";
            } else {
                echo "حدث خطأ أثناء رفع الملف";
            }
        } else {
            echo "لم يتم اختيار أي ملف أو case_id مفقود";
        }
    } else {
        header("Location: cases.php");
        exit;
    }
} else {
    header("Location: cases.php");
    exit;
}
?>
