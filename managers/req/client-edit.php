<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {

        if (isset($_POST['fname']) &&
            isset($_POST['lname']) &&
            isset($_POST['client_id']) &&
            isset($_POST['address']) &&
            isset($_POST['email']) &&
            isset($_POST['gender']) &&
            isset($_POST['date_of_birth']) &&
            isset($_POST['city']) &&
            isset($_POST['phone'])) {

            include '../../DB_connection.php';
            include "../data/client.php";

            include '../permissions_script.php';
            if ($pages['clients']['write'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $address = $_POST['address'];
            $gender = $_POST['gender'];
            $email_address = $_POST['email'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $client_id = $_POST['client_id'];

            $father_name = $_POST['father_name'];
            $grandfather_name = $_POST['grandfather_name'];
            $national_num = $_POST['national_num'];
            $alhi = $_POST['alhi'];
            $street_name = $_POST['street_name'];
            $num_build = $_POST['num_build'];
            $num_unit = $_POST['num_unit'];
            $zip_code = $_POST['zip_code'];
            $subnumber = $_POST['subnumber'];
            
            $role_id = $_POST['role_id'];
            $office_id = $_POST['office_id'];
            $client_passport = $_POST['client_passport'];
            $stop_date = $_POST['stop_date'];
            $lawyer_id = $_POST['lawyer_id'];

            
            $receive_whatsupp = isset($_POST['receive_whatsupp']) ? 1 : 0;
            $receive_emails = isset($_POST['receive_emails']) ? 1 : 0;
            $stop_account = isset($_POST['stop']) ? 1 : 0;

            $data = 'client_id='.$client_id;

            if (empty($fname)) {
                $em  = "الاسم الأول مطلوب";
                header("Location: ../client-edit.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em  = "الاسم الأخير مطلوب";
                header("Location: ../client-edit.php?error=$em&$data");
                exit;
            } else if (empty($gender)) {
                $em  = "الجنس مطلوب";
                header("Location: ../client-edit.php?error=$em&$data");
                exit;
            } else if (empty($email_address)) {
                $em  = "الإيميل مطلوب";
                header("Location: ../client-edit.php?error=$em&$data");
                exit;
            } else if (empty($phone)) {
                $em  = "رقم الجوال مطلوب";
                header("Location: ../client-edit.php?error=$em&$data");
                exit;
            }

            $uname = isset($_POST['username']) && !empty($_POST['username']) ? $_POST['username'] : '';
            $pass = isset($_POST['pass']) && !empty($_POST['pass']) ? password_hash($_POST['pass'], PASSWORD_DEFAULT) : '';

            if (!empty($uname)) {
                // تحقق من فريدة اسم المستخدم
                if (!usernamelIsUnique($uname, $conn, $client_id)) {
                    $em  = "اسم المستخدم موجود، اختر واحد آخر";
                    header("Location: ../client-edit.php?error=$em&$data");
                    exit;
                }
            }

            // بناء جملة SQL بشكل ديناميكي لتحديث الحقول المتاحة
            $sql = "UPDATE clients SET
            first_name=?, last_name=?, `address`=?, gender=?, city=?, email=?, date_of_birth=?, phone=?,
            father_name=?, grandfather_name=?, national_num=?, alhi=?, street_name=?, num_build=?, num_unit=?, zip_code=?, subnumber=?, receive_whatsupp=?, receive_emails=?,
            client_passport=?, role_id=?, `stop`=?, office_id=?, stop_date=?, lawyer_id=? ";

            $params = [$fname, $lname, $address, $gender, $city, $email_address, $date_of_birth, $phone, $father_name, $grandfather_name, $national_num, $alhi, $street_name, $num_build, $num_unit, $zip_code, $subnumber, $receive_whatsupp, $receive_emails, $client_passport, $role_id, $stop_account, $office_id, $stop_date, $lawyer_id];

            if (!empty($uname)) {
                $sql .= ", username=?";
                $params[] = $uname;
            }

            if (!empty($pass)) {
                $sql .= ", `password`=?";
                $params[] = $pass;
            }

            $sql .= " WHERE client_id=?";
            $params[] = $client_id;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $sm = "تم التحديث بنجاح!";
            header("Location: ../client-edit.php?success=$sm&$data");
            exit;

        } else {
            $em = "An error occurred";
            header("Location: ../clients.php?error=$em");
            exit;
        }

    } else {
        header("Location: ../../logout.php");
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
