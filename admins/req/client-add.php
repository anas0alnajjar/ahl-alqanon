<?php
session_start();
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admins') {
        if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email_address']) && isset($_POST['phone']) && isset($_POST['office_id'])) {

            include '../../DB_connection.php';
            include "../data/usernamelIsUnique.php";

            // القيم الأساسية
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email_address = $_POST['email_address'];
            $phone = $_POST['phone'];
            $office_id = $_POST['office_id'];

            // القيم الاختيارية
            $uname = isset($_POST['username']) && !empty($_POST['username']) ? $_POST['username'] : '';
            $pass = isset($_POST['pass']) && !empty($_POST['pass']) ? password_hash($_POST['pass'], PASSWORD_DEFAULT) : '';
            $address = isset($_POST['address']) ? $_POST['address'] : '';
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
            $city = isset($_POST['city']) ? $_POST['city'] : '';
            $father_name = isset($_POST['father_name']) ? $_POST['father_name'] : '';
            $grandfather_name = isset($_POST['grandfather_name']) ? $_POST['grandfather_name'] : '';
            $national_num = isset($_POST['national_num']) ? $_POST['national_num'] : '';
            $alhi = isset($_POST['alhi']) ? $_POST['alhi'] : '';
            $street_name = isset($_POST['street_name']) ? $_POST['street_name'] : '';
            $num_build = isset($_POST['num_build']) ? $_POST['num_build'] : '';
            $num_unit = isset($_POST['num_unit']) ? $_POST['num_unit'] : '';
            $zip_code = isset($_POST['zip_code']) ? $_POST['zip_code'] : '';
            $subnumber = isset($_POST['subnumber']) ? $_POST['subnumber'] : '';
            $client_passport = isset($_POST['client_passport']) ? $_POST['client_passport'] : '';
            $role_id = isset($_POST['role_id']) ? $_POST['role_id'] : '';
            $lawyer_id = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : null;
            $receive_whatsupp = isset($_POST['receive_whatsupp']) ? 1 : 0;
            $receive_emails = isset($_POST['receive_emails']) ? 1 : 0;

            // التحقق من الحقول الأساسية
            if (empty($fname)) {
                $response['message'] = 'الاسم الأول مطلوب';
                echo json_encode($response);
                exit;
            } else if (empty($lname)) {
                $response['message'] = 'الاسم الأخير مطلوب';
                echo json_encode($response);
                exit;
            } else if (empty($email_address)) {
                $response['message'] = 'البريد الإلكتروني مطلوب';
                echo json_encode($response);
                exit;
            } else if (empty($phone)) {
                $response['message'] = 'رقم الهاتف مطلوب';
                echo json_encode($response);
                exit;
            } else if (empty($office_id)) {
                $response['message'] = 'المكتب مطلوب';
                echo json_encode($response);
                exit;
            }

            // التحقق من اسم المستخدم وكلمة السر إذا كانت موجودة
            if (!empty($uname)) {
                if (!usernamelIsUnique($uname, $conn)) {
                    $response['message'] = 'اسم المستخدم مأخوذ اختر واحد آخر';
                    if (empty($pass)) {
                        $response['message'] .= ' وكلمة السر مطلوبة';
                    }
                    echo json_encode($response);
                    exit;
                }

                if (empty($pass)) {
                    $response['message'] = 'كلمة السر مطلوبة';
                    echo json_encode($response);
                    exit;
                }
            }

            // إدخال البيانات في قاعدة البيانات
            $sql = "INSERT INTO clients (first_name, last_name, email, phone, username, `password`, office_id, address, gender, city, date_of_birth, 
                                          father_name, grandfather_name, national_num, alhi, street_name, num_build, num_unit, zip_code, 
                                          subnumber, receive_whatsupp, receive_emails, client_passport, role_id, lawyer_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $lname, $email_address, $phone, $uname, $pass, $office_id, $address, $gender, $city, $date_of_birth,
                            $father_name, $grandfather_name, $national_num, $alhi, $street_name, $num_build, $num_unit, $zip_code,
                            $subnumber, $receive_whatsupp, $receive_emails, $client_passport, $role_id, $lawyer_id]);

            $response['success'] = true;
            $response['message'] = 'تم إضافة الموكل بنجاح!';
            echo json_encode($response);
            exit;
        } else {
            $response['message'] = 'حدث خطأ أثناء الإضافة';
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'غير مصرح';
        echo json_encode($response);
        exit;
    }
} else {
    $response['message'] = 'غير مصرح';
    echo json_encode($response);
    exit;
}
?>
