<?php
session_start();
include '../DB_connection.php';

if (isset($_POST['manager_email']) && isset($_POST['verification_code'])) {
    $manager_email = $_POST['manager_email'];
    $verification_code = $_POST['verification_code'];

    if (empty($manager_email) || empty($verification_code)) {
        $response = ['error' => 'جميع الحقول مطلوبة'];
        echo json_encode($response);
        exit;
    }

    if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $verification_code) {
        unset($_SESSION['verification_code']);

        // البيانات من الجلسة
        $manager_data = $_SESSION['manager_data'];
        $manager_name = $manager_data['manager_name'];
        $manager_address = $manager_data['manager_address'];
        $manager_email = $manager_data['manager_email'];
        $manager_gender = $manager_data['manager_gender'];
        $username = $manager_data['username'];
        $manager_password = password_hash($manager_data['manager_password'], PASSWORD_DEFAULT);
        $manager_city = $manager_data['manager_city'];
        $manager_phone = $manager_data['manager_phone'];
        $office_name = $manager_data['office_name'];

        // فحص جدول requests لمعرفة إذا كان هناك قبول تلقائي وعدد الأيام
        $sql_requests = "SELECT automatic_acceptance, days FROM requests WHERE id = 1 LIMIT 1";
        $stmt_requests = $conn->prepare($sql_requests);
        $stmt_requests->execute();
        $request_settings = $stmt_requests->fetch(PDO::FETCH_ASSOC);

        $auto_accept = $request_settings['automatic_acceptance'];
        $days = $request_settings['days'];

        // جلب الدور الافتراضي للمدير
        $default_role_sql = "SELECT power_id FROM powers WHERE default_role_manager = 1 LIMIT 1";
        $default_role_stmt = $conn->prepare($default_role_sql);
        $default_role_stmt->execute();
        $default_role = $default_role_stmt->fetch(PDO::FETCH_ASSOC);

        if ($auto_accept) {
            // إعداد المكتب الجديد
            $stop_date = date('Y-m-d', strtotime("+$days days"));

            // إدخال المكتب في جدول offices
            $sql_insert_office = "INSERT INTO offices (office_name, stop, admin_id, stop_date) VALUES (?, 1, 1, ?)";
            $stmt_insert_office = $conn->prepare($sql_insert_office);
            $stmt_insert_office->execute([$office_name, $stop_date]);
            $office_id = $conn->lastInsertId();

            // إدخال المدير في جدول managers_office
            $sql_insert_manager = "INSERT INTO managers_office (manager_name, manager_email, manager_phone, username, manager_password, manager_address, manager_gender, role_id, office_id, stop, stop_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
            $stmt_insert_manager = $conn->prepare($sql_insert_manager);
            $stmt_insert_manager->execute([$manager_name, $manager_email, $manager_phone, $username, $manager_password, $manager_address, $manager_gender, $default_role['power_id'] ?? NULL, $office_id, $stop_date]);

            $manager_id = $conn->lastInsertId();

            // تسجيل دخول المدير وتحويله للصفحة الرئيسية
            $_SESSION['user_id'] = $manager_id;
            $_SESSION['role'] = 'Managers';

            $response = ['success' => 'تمت معالجة طلبك للانضمام بنجاح. تستطيع تسجيل الدخول والمباشرة في العمل.', 'auto_accept' => true];
            echo json_encode($response);
            exit;
        } else {
            // إعداد المكتب بدون تاريخ توقف
            $stop_date = NULL;

            // إدخال المكتب في جدول offices
            $sql_insert_office = "INSERT INTO offices (office_name, stop, admin_id, stop_date) VALUES (?, 1, 1, ?)";
            $stmt_insert_office = $conn->prepare($sql_insert_office);
            $stmt_insert_office->execute([$office_name, $stop_date]);
            $office_id = $conn->lastInsertId();

            // إدخال المدير في جدول managers_office
            $sql_insert_manager = "INSERT INTO managers_office (manager_name, manager_email, manager_phone, username, manager_password, manager_address, manager_gender, role_id, office_id, stop, stop_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
            $stmt_insert_manager = $conn->prepare($sql_insert_manager);
            $stmt_insert_manager->execute([$manager_name, $manager_email, $manager_phone, $username, $manager_password, $manager_address, $manager_gender, $default_role['power_id'] ?? NULL, $office_id, $stop_date]);

            $response = ['success' => 'تمت معالجة طلبك للانضمام بنجاح. سنتواصل معك في أقرب وقت ممكن.', 'auto_accept' => false];
            echo json_encode($response);
            exit;
        }
    } else {
        $response = ['error' => 'رمز التحقق غير صحيح'];
        echo json_encode($response);
        exit;
    }
} else {
    $response = ['error' => 'جميع الحقول مطلوبة'];
    echo json_encode($response);
    exit;
}
?>
