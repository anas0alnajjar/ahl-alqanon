<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include '../../DB_connection.php';
        foreach ($_POST['translations'] as $translation) {
            if (isset($translation['id']) && isset($translation['translated_text'])) {
                $translated_text = $translation['translated_text'];
                $translation_id = $translation['id'];
                $data = 'language_id=' . $_POST['language_id'];
                if (empty($translation['id'])) {
                    $em  = "حدث خطأ ما";
                    header("Location: ../translations-edit.php?error=$em&$data");
                    exit;
                } else {
                    $sql = "UPDATE translations SET
                        translated_text=? 
                        WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$translated_text, $translation_id]);
                    $sm = " اللغة تم تحديثها";
                    header("Location: ../translations-edit.php?success=$sm&$data");
                }
            } else {
                $em = "حدث خطأ ما";
                header("Location: ../translations-edit.php?error=$em&$data");
                exit;
            }
        }
    } else {
        header("Location: ../../logout.php");
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
