<?php

if (!function_exists('getOfficeId')) {
    function getOfficeId($conn, $user_id) {
        // استعلام للحصول على OFFICE_ID من جدول managers_office بناءً على USER_ID
        $sql = "SELECT office_id FROM clients WHERE client_id = ?";
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


if (!function_exists('getLawyerId')) {
    function getLawyerId($client_id, $conn) {
        // استعلام للحصول على معرف المحامي
        $sql_lawyer_id = "SELECT lawyer_id FROM clients WHERE client_id = :client_id";
        
        // تحضير الاستعلام
        $stmt_lawyer_id = $conn->prepare($sql_lawyer_id);
        
        // ربط المعرف المساعد بالاستعلام
        $stmt_lawyer_id->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        
        // تنفيذ الاستعلام
        $stmt_lawyer_id->execute();
        
        // الحصول على معرف المحامي
        $lawyer_id = $stmt_lawyer_id->fetchColumn();
        
        // إرجاع معرف المحامي
        return $lawyer_id;
    }
}

if (!function_exists('get_clients_for_client')) {
    function get_clients_for_client($conn, $client_id) {
        $sql_clients = "
            SELECT DISTINCT cl.client_id, cl.first_name, cl.last_name, cl.email 
            FROM clients cl 
            JOIN cases ca ON cl.client_id = ca.client_id OR FIND_IN_SET(cl.client_id, ca.plaintiff)
            WHERE ca.client_id = :client_id OR FIND_IN_SET(:client_id, ca.plaintiff)
            ORDER BY cl.client_id;
        ";
        $stmt_clients = $conn->prepare($sql_clients);
        $stmt_clients->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $stmt_clients->execute();
        return $stmt_clients->fetchAll(PDO::FETCH_ASSOC);
    }
}


if (!function_exists('get_adversaries_for_client')) {
    function get_adversaries_for_client($conn, $client_id) {
        $sql = "
            SELECT DISTINCT adv.id, adv.fname, adv.lname
            FROM adversaries adv
            JOIN cases ca ON FIND_IN_SET(adv.id, ca.defendant)
            WHERE ca.client_id = :client_id OR FIND_IN_SET(:client_id, ca.plaintiff)
            ORDER BY adv.id DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


if (!function_exists('getCases')) {
    function getCases($conn, $client_id) {
        $sql = "SELECT DISTINCT c.case_id, c.case_title 
                FROM cases c
                WHERE c.client_id = :client_id OR FIND_IN_SET(:client_id, c.plaintiff)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
