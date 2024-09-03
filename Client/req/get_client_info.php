<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    include "../../DB_connection.php";

    if (isset($_POST['client_id'])) {
        $client_id = $_POST['client_id'];

        // الاستعلام لجلب معلومات الموكل والمحامي
        $query = "
        SELECT 
            cl.client_id,
            cl.office_id,
            cl.first_name,
            cl.last_name,
            cl.address,
            cl.date_of_birth,
            cl.email,
            cl.gender,
            cl.phone,
            cl.city,
            COUNT(DISTINCT c.case_id) AS num_cases,
            COUNT(DISTINCT s.session_date) AS num_sessions,
            l.lawyer_name,
            l.lawyer_logo
        FROM 
            clients cl
        LEFT JOIN 
            cases c ON cl.client_id = c.client_id
        LEFT JOIN 
            sessions s ON c.case_id = s.case_id
        LEFT JOIN 
            lawyer l ON c.lawyer_id = l.lawyer_id
        WHERE 
            cl.client_id = ?
        GROUP BY
            cl.client_id, l.lawyer_name;
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([$client_id]);
        $client_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($client_info) {
            // الاستعلام لجلب اللوغو الخاص بالمكتب
            $office_id = $client_info['office_id'];
            $logo_query = "SELECT logo FROM profiles WHERE office_id = ?";
            $logo_stmt = $conn->prepare($logo_query);
            $logo_stmt->execute([$office_id]);
            $office_logo = $logo_stmt->fetch(PDO::FETCH_ASSOC);

            if ($office_logo && !empty($office_logo['logo'])) {
                $client_info['logo_path'] = "../../profiles_photos/" . $office_logo['logo'];
            } else {
                $admin_id = $_SESSION['user_id'];
                // إذا لم يكن هناك لوغو خاص بالمكتب، يتم جلب اللوغو الخاص بالمسؤول
                $admin_query = "SELECT logo FROM setting WHERE admin_id = $admin_id"; // تعديل id إلى 1
                $admin_stmt = $conn->prepare($admin_query);
                $admin_stmt->execute();
                $admin_logo = $admin_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin_logo && !empty($admin_logo['logo'])) {
                    $client_info['logo_path'] = "../../img/" . $admin_logo['logo'];
                } else {
                    $client_info['logo_path'] = "../../img/default_logo.png"; // صورة افتراضية في حال عدم وجود لوغو
                }
            }

            echo json_encode($client_info);
        } else {
            echo json_encode(['error' => 'No client found with the provided ID.']);
        }
    } else {
        echo json_encode(['error' => 'Client ID not provided.']);
    }
} else {
    header("Location: ../logout.php");
    exit;
}
?>
