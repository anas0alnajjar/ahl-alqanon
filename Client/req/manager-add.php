<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {

        

        if (isset($_POST['manager_name']) &&
            isset($_POST['username']) &&
            isset($_POST['manager_password']) &&
            isset($_POST['manager_address']) &&
            isset($_POST['manager_gender']) &&
            isset($_POST['mangaer_email']) &&
            isset($_POST['date_of_birth']) &&
            isset($_POST['manager_city']) &&
            isset($_POST['manager_phone'])) {
                
            include '../../DB_connection.php';
            include "../data/usernamelIsUnique.php";
            include '../permissions_script.php';

            if ($pages['managers']['add'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            $manager_name = $_POST['manager_name'];
            $uname = $_POST['username'];
            $address = $_POST['manager_address'];
            $email_address = $_POST['mangaer_email'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['manager_city'];
            $phone = $_POST['manager_phone'];
            $gender = $_POST['manager_gender'];
            $pass = $_POST['manager_password'];
            
            $manager_national = $_POST['manager_national'];
            $manager_passport = $_POST['manager_passport'];
            $role_id = $_POST['role_id'];
            $office_id = $_POST['office_id'];

            
            $data = 'manager_name='.$manager_name.'&username='.$uname.'&address='.$address.'&gender='.$gender.'&email_address='.$email_address.'&date_of_birth='.$date_of_birth.'&city='.$city.'&phone='.$phone .'&manager_national='.$manager_national.'&manager_passport='.$manager_passport.'&role_id='.$role_id .'&office_id='.$office_id;
            
            if (empty($manager_name)) {
                $em = "الاسم مطلوب";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($uname)) {
                $em = "اسم المستخدم مطلوب";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($pass)) {
                $em = "كلمة السر مطلوبة";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($address)) {
                $em = "العنوان مطلوب";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($gender)) {
                $em = "الجنس مطلوب";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($email_address)) {
                $em = "الإيميل مطلوب";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($date_of_birth)) {
                $em = "تاريخ الولادة مطلوب";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($city)) {
                $em = "المدينة مطلوبة";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($phone)) {
                $em = "الهاتف مطلوب";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            if (empty($role_id)) {
                $em = "يجب تحديد الدور";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }
            
            // تحقق من أن اسم المستخدم فريد
            if (!usernamelIsUnique($uname, $conn)) {
                $em = "اسم المستخدم مأخوذ، اختر واحداً آخر";
                header("Location: ../manager-add.php?error=$em&$data");
                exit;
            }

            // إذا تم التحقق من جميع الحقول، قم بإضافة المحامي
            $pass = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO managers_office(username, manager_password, manager_name, manager_address, manager_gender, manager_email, date_of_birth, manager_city, manager_phone, manager_national, manager_passport, role_id, office_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$uname, $pass, $manager_name, $address, $gender, $email_address, $date_of_birth, $city, $phone, $manager_national, $manager_passport, $role_id, $office_id]);
            $sm = "تم إضافة مدير المكتب بنجاح";
            header("Location: ../manager-add.php?success=$sm");
            exit;
        } else {
            $em = "حدث خطأ غير متوقع";
            header("Location: ../manager-add.php?error=$em");
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
