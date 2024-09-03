<?php

if (!function_exists('getOfficeId')) {
    function getOfficeId($conn, $user_id) {
        // استعلام للحصول على OFFICE_ID من جدول managers_office بناءً على USER_ID
        $sql = "SELECT office_id FROM managers_office WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() == 1) {
            $office = $stmt->fetch();
            return $office['office_id'];
        } else {
            return null; // في حال لم يتم العثور على OFFICE_ID
        }
    }
}

?>
