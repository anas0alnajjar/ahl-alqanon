<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Managers') {
        if (isset($_POST['fname']) &&
            isset($_POST['username']) &&
            isset($_POST['pass']) &&
            isset($_POST['address']) &&
            isset($_POST['gender']) &&
            isset($_POST['email_address']) &&
            isset($_POST['date_of_birth']) &&
            isset($_POST['city']) &&
            isset($_POST['phone'])) {
                
            include '../../DB_connection.php';
            include "../data/usernamelIsUnique.php";
            include '../permissions_script.php';
            if ($pages['lawyers']['add'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            $fname = $_POST['fname'];
            $uname = $_POST['username'];
            $address = $_POST['address'];
            $email_address = $_POST['email_address'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $gender = $_POST['gender'];
            $pass = $_POST['pass'];
            
            $lawyer_national = $_POST['lawyer_national'];
            $lawyer_passport = $_POST['lawyer_passport'];
            $role_id = $_POST['role_id'];
            $office_id = $_POST['office_id'];
            
            $data = 'fname='.$fname.'&username='.$uname.'&address='.$address.'&gender='.$gender.'&email_address='.$email_address.'&date_of_birth='.$date_of_birth.'&city='.$city.'&phone='.$phone .'&lawyer_national='.$lawyer_national.'&lawyer_passport='.$lawyer_passport.'&role_id='.$role_id.'&office_id='.$office_id;
            
            if (empty($fname)) {
                $em = "الاسم مطلوب";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }
            if (empty($uname)) {
                $em = "اسم المستخدم مطلوب";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }
            if (empty($pass)) {
                $em = "كلمة السر مطلوبة";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }
            if (empty($gender)) {
                $em = "الجنس مطلوب";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }
            if (empty($email_address)) {
                $em = "الإيميل مطلوب";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }
            if (empty($phone)) {
                $em = "الهاتف مطلوب";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }
            if (empty($role_id)) {
                $em = "يجب تحديد الدور";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }
            
            // تحقق من أن اسم المستخدم فريد
            if (!usernamelIsUnique($uname, $conn)) {
                $em = "اسم المستخدم مأخوذ، اختر واحداً آخر";
                header("Location: ../lawyer-add.php?error=$em&$data");
                exit;
            }

            // إذا تم التحقق من جميع الحقول، قم بإضافة المحامي
            $pass = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO lawyer(username, lawyer_password, lawyer_name, lawyer_address, lawyer_gender, lawyer_email, date_of_birth, lawyer_city, lawyer_phone, lawyer_national, lawyer_passport, role_id, office_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$uname, $pass, $fname, $address, $gender, $email_address, $date_of_birth, $city, $phone, $lawyer_national, $lawyer_passport, $role_id, $office_id]);
            $sm = "تم إضافة المحامي بنجاح";
            header("Location: ../lawyer-add.php?success=$sm");
            exit;
        } else {
            $em = "حدث خطأ غير متوقع";
            header("Location: ../lawyer-add.php?error=$em");
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
