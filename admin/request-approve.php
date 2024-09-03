<?php 

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {

    include '../DB_connection.php';
    
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
    
        $sql_select_user = "SELECT * FROM ask_join WHERE user_id=?";
        $stmt_select_user = $conn->prepare($sql_select_user);
        $stmt_select_user->execute([$user_id]);
        $user_data = $stmt_select_user->fetch();

        if ($user_data) {
            try {
                $conn->beginTransaction();

                // التحقق من وجود دور افتراضي ومكتب افتراضي
                $default_role_sql = "SELECT power_id FROM powers WHERE default_role = 1 LIMIT 1";
                $default_office_sql = "SELECT office_id FROM offices WHERE default_office = 1 LIMIT 1";

                $default_role_stmt = $conn->prepare($default_role_sql);
                $default_office_stmt = $conn->prepare($default_office_sql);

                $default_role_stmt->execute();
                $default_office_stmt->execute();

                $default_role = $default_role_stmt->fetch(PDO::FETCH_ASSOC);
                $default_office = $default_office_stmt->fetch(PDO::FETCH_ASSOC);

                // التحقق من وجود قيمة لعدد الأيام في جدول requests
                $sql_check_days = "SELECT days FROM requests WHERE id = 1 LIMIT 1";
                $stmt_check_days = $conn->prepare($sql_check_days);
                $stmt_check_days->execute();
                $request_settings = $stmt_check_days->fetch(PDO::FETCH_ASSOC);

                if ($request_settings && $request_settings['days'] > 0) {
                    $stop = 1;
                    $stop_date = date('Y-m-d', strtotime("+" . $request_settings['days'] . " days"));
                } else {
                    $stop = 0;
                    $stop_date = NULL;
                }

                if ($user_data['as_a'] == 1) {
                    // إدخال بيانات المستخدم إلى جدول clients
                    $sql_insert_client = "INSERT INTO clients (first_name, last_name, email, phone, username, `password`, `address`, gender, date_of_birth, city, role_id, office_id, stop, stop_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert_client = $conn->prepare($sql_insert_client);
                    $stmt_insert_client->execute([
                        $user_data['first_name'],
                        $user_data['last_name'],
                        $user_data['email'],
                        $user_data['phone'],
                        $user_data['username'],
                        $user_data['password'],
                        $user_data['address'],
                        $user_data['gender'],
                        $user_data['date_of_birth'],
                        $user_data['city'],
                        $default_role['power_id'] ?? NULL,
                        $default_office['office_id'] ?? NULL,
                        $stop,
                        $stop_date
                    ]);
                } elseif ($user_data['as_a'] == 2) {
                    // إدخال بيانات المستخدم إلى جدول lawyer
                    $sql_insert_lawyer = "INSERT INTO lawyer (lawyer_name, date_of_birth, lawyer_email, lawyer_phone, username, lawyer_password, lawyer_address, lawyer_gender, lawyer_city, role_id, office_id, stop, stop_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert_lawyer = $conn->prepare($sql_insert_lawyer);
                    $stmt_insert_lawyer->execute([
                        $user_data['first_name'] . ' ' . $user_data['last_name'],
                        $user_data['date_of_birth'],
                        $user_data['email'],
                        $user_data['phone'],
                        $user_data['username'],
                        $user_data['password'],
                        $user_data['address'],
                        $user_data['gender'],
                        $user_data['city'],
                        $default_role['power_id'] ?? NULL,
                        $default_office['office_id'] ?? NULL,
                        $stop,
                        $stop_date
                    ]);
                }

                // حذف الطلب من ask_join
                $sql_delete_user = "DELETE FROM ask_join WHERE user_id=?";
                $stmt_delete_user = $conn->prepare($sql_delete_user);
                $stmt_delete_user->execute([$user_id]);

                $conn->commit();

                header("Location: requests.php?success=تم قبول الطلب بنجاح");
                exit;
            } catch (Exception $e) {
                $conn->rollBack();
                header("Location: requests.php?error=حدث خطأ أثناء معالجة الطلب: " . $e->getMessage());
                exit;
            }
        } else {
            header("Location: requests.php?error=طلب غير صالح، راجع الدعم الفني");
            exit;
        }
    } else {
        header("Location: requests.php?error=طلب غير صالح، راجع الدعم الفني");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
