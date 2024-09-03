<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        if (isset($_POST['fname']) &&
            isset($_POST['lname']) &&
            isset($_POST['client_id']) &&
            isset($_POST['email']) &&
            isset($_POST['phone']) &&
            isset($_POST['office_id'])) {

            include '../../DB_connection.php';
            include "../data/client.php";

            // القيم الأساسية
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email_address = $_POST['email'];
            $phone = $_POST['phone'];
            $client_id = $_POST['client_id'];
            $office_id = $_POST['office_id'];

            // القيم الاختيارية
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
            $role_id = isset($_POST['role_id']) ? $_POST['role_id'] : '';
            $client_passport = isset($_POST['client_passport']) ? $_POST['client_passport'] : '';
            $stop_date = isset($_POST['stop_date']) ? $_POST['stop_date'] : '';
            $lawyer_id = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : null;
            $receive_whatsupp = isset($_POST['receive_whatsupp']) ? 1 : 0;
            $receive_emails = isset($_POST['receive_emails']) ? 1 : 0;
            $stop_account = isset($_POST['stop']) ? 1 : 0;

            $data = 'client_id='.$client_id;

            // التحقق من الحقول الأساسية
            if (empty($fname)) {
                $em  = "الاسم الأول مطلوب";
                header("Location: ../client-edit.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em  = "الاسم الأخير مطلوب";
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
            } else if (empty($office_id)) {
                $em  = "المكتب مطلوب";
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
            first_name=?, last_name=?, email=?, phone=?, office_id=?, address=?, gender=?, city=?, date_of_birth=?, 
            father_name=?, grandfather_name=?, national_num=?, alhi=?, street_name=?, num_build=?, num_unit=?, zip_code=?, 
            subnumber=?, receive_whatsupp=?, receive_emails=?, client_passport=?, role_id=?, `stop`=?, stop_date=?, lawyer_id=? ";

            $params = [$fname, $lname, $email_address, $phone, $office_id, $address, $gender, $city, $date_of_birth,
                       $father_name, $grandfather_name, $national_num, $alhi, $street_name, $num_build, $num_unit, $zip_code,
                       $subnumber, $receive_whatsupp, $receive_emails, $client_passport, $role_id, $stop_account, $stop_date, $lawyer_id];

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
            $em = "حدث خطأ أثناء التحديث";
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
?>
