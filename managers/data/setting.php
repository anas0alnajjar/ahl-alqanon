<?php 

function getSetting($conn, $user_id) {
    // الحصول على OFFICE_ID من جدول managers_office بناءً على USER_ID
    $sqlOffice = "SELECT office_id FROM managers_office WHERE id = ?";
    $stmtOffice = $conn->prepare($sqlOffice);
    $stmtOffice->execute([$user_id]);
    
    if ($stmtOffice->rowCount() == 1) {
        $office = $stmtOffice->fetch();
        $office_id = $office['office_id'];

        // الحصول على ADMIN_ID من جدول OFFICES بناءً على OFFICE_ID
        $sqlAdmin = "SELECT admin_id FROM offices WHERE office_id = ?";
        $stmtAdmin = $conn->prepare($sqlAdmin);
        $stmtAdmin->execute([$office_id]);

        if ($stmtAdmin->rowCount() == 1) {
            $admin = $stmtAdmin->fetch();
            $admin_id = $admin['admin_id'];

            // الحصول على الإعدادات من جدول setting بناءً على ADMIN_ID
            $sqlSetting = "SELECT * FROM setting WHERE admin_id = ?";
            $stmtSetting = $conn->prepare($sqlSetting);
            $stmtSetting->execute([$admin_id]);

            if ($stmtSetting->rowCount() == 1) {
                $settings = $stmtSetting->fetch();
                return $settings;
            }
        }
    }
    
    // في حال لم يتم العثور على إعدادات مرتبطة، نستخدم إعدادات الإدمن ذو المعرف 1
    $sqlDefaultSetting = "SELECT * FROM setting WHERE admin_id = 1";
    $stmtDefaultSetting = $conn->prepare($sqlDefaultSetting);
    $stmtDefaultSetting->execute();
    
    if ($stmtDefaultSetting->rowCount() == 1) {
        $defaultSettings = $stmtDefaultSetting->fetch();
        return $defaultSettings;
    } else {
        return 0; // لم يتم العثور على إعدادات حتى للإدمن ذو المعرف 1
    }
}
?>
