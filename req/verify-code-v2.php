<?php
session_start();
include '../DB_connection.php';

if (isset($_POST['email_address']) && isset($_POST['verification_code'])) {
    $email_address = $_POST['email_address'];
    $verification_code = $_POST['verification_code'];

    if (empty($email_address) || empty($verification_code)) {
        $response = ['error' => 'جميع الحقول مطلوبة'];
        echo json_encode($response);
        exit;
    }

    if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $verification_code) {
        unset($_SESSION['verification_code']);

        $user_data = $_SESSION['user_data'];
        $fname = $user_data['fname'];
        $lname = $user_data['lname'];
        $uname = $user_data['username'];
        $pass = $user_data['password'];
        $address = $user_data['address'];
        $gender = $user_data['gender'];
        $email_address = $user_data['email_address'];
        $date_of_birth = $user_data['date_of_birth'];
        $city = $user_data['city'];
        $phone = $user_data['phone'];
        $as_a = $user_data['as_a'];

        $sql_requests = "SELECT automatic_acceptance, days FROM requests WHERE id = 1 LIMIT 1";
        $stmt_requests = $conn->prepare($sql_requests);
        $stmt_requests->execute();
        $request_settings = $stmt_requests->fetch(PDO::FETCH_ASSOC);

        $auto_accept = $request_settings['automatic_acceptance'];
        $days = $request_settings['days'];

        if ($as_a == 1) {
            $default_role_sql = "SELECT power_id FROM powers WHERE default_role_client = 1 LIMIT 1";
            $email_check_sql = "SELECT email FROM clients WHERE email = ?";
        } elseif ($as_a == 2) {
            $default_role_sql = "SELECT power_id FROM powers WHERE default_role_lawyer = 1 LIMIT 1";
            $email_check_sql = "SELECT lawyer_email FROM lawyer WHERE lawyer_email = ?";
        }

        $default_office_sql = "SELECT office_id FROM offices WHERE default_office = 1 LIMIT 1";

        $default_role_stmt = $conn->prepare($default_role_sql);
        $default_office_stmt = $conn->prepare($default_office_sql);
        $email_check_stmt = $conn->prepare($email_check_sql);

        $default_role_stmt->execute();
        $default_office_stmt->execute();
        $email_check_stmt->execute([$email_address]);

        if ($email_check_stmt->rowCount() > 0) {
            $response = ['error' => 'البريد الإلكتروني مستخدم بالفعل'];
            echo json_encode($response);
            exit;
        }

        $default_role = $default_role_stmt->fetch(PDO::FETCH_ASSOC);
        $default_office = $default_office_stmt->fetch(PDO::FETCH_ASSOC);

        if ($auto_accept) {
            $stop_date = date('Y-m-d', strtotime("+$days days"));

            if ($as_a == 1) {
                $sql_insert_client = "INSERT INTO clients (first_name, last_name, email, phone, username, `password`, `address`, gender, date_of_birth, city, stop, stop_date, role_id, office_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)";
                $stmt_insert_client = $conn->prepare($sql_insert_client);
                $stmt_insert_client->execute([$fname, $lname, $email_address, $phone, $uname, $pass, $address, $gender, $date_of_birth, $city, $stop_date, $default_role['power_id'] ?? NULL, $default_office['office_id'] ?? NULL]);
                
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['role'] = 'Client';
                $user_type = 'client';
            } elseif ($as_a == 2) {
                $sql_insert_lawyer = "INSERT INTO lawyer (lawyer_name, date_of_birth, lawyer_email, lawyer_phone, username, lawyer_password, lawyer_address, lawyer_gender, lawyer_city, stop, stop_date, role_id, office_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)";
                $stmt_insert_lawyer = $conn->prepare($sql_insert_lawyer);
                $stmt_insert_lawyer->execute(["$fname $lname", $date_of_birth, $email_address, $phone, $uname, $pass, $address, $gender, $city, $stop_date, $default_role['power_id'] ?? NULL, $default_office['office_id'] ?? NULL]);

                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['role'] = 'Lawyer';
                $user_type = 'lawyer';
            }

            $response = ['success' => 'تم التحقق بنجاح. تستطيع تسجيل الدخول والمباشرة في العمل.', 'auto_accept' => true, 'user_type' => $user_type];
            echo json_encode($response);
            exit;
        } else {
            $pass = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO ask_join(username, `password`, first_name, last_name, `address`, gender, email, date_of_birth, city, phone, as_a) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$uname, $pass, $fname, $lname, $address, $gender, $email_address, $date_of_birth, $city, $phone, $as_a]);
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
