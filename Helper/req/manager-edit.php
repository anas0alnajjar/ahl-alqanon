<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
        

if (isset($_POST['manager_name'])      &&
    isset($_POST['username'])   &&
    isset($_POST['manager_id']) &&
    isset($_POST['manager_address'])    &&
    isset($_POST['manager_email']) &&
    isset($_POST['manager_gender'])        &&
    isset($_POST['date_of_birth']) &&
    isset($_POST['city'])       &&
    isset($_POST['phone'])  ) {
    
    include '../../DB_connection.php';

    
    function usernamelIsUnique($uname, $conn, $manager_id = 0) {
        if ($manager_id == 0) {
            $sql = "SELECT username FROM `admin` WHERE username = ?
                    UNION
                    SELECT username FROM lawyer WHERE username = ?
                    UNION
                    SELECT username FROM helpers WHERE username = ?
                    UNION
                    SELECT username FROM clients WHERE username = ?
                    UNION
                    SELECT username FROM managers_office WHERE username = ?
                    UNION
                    SELECT username FROM ask_join WHERE username = ?";
      
            $stmt = $conn->prepare($sql);
            $stmt->execute([$uname, $uname, $uname,$uname, $uname, $uname]);
            
            if ($stmt->rowCount() > 0) {
                return 0;
            } else {
                return 1;
            }
        } else {
            $sql = "SELECT username FROM `admin` WHERE username = ?
                    UNION
                    SELECT username FROM lawyer WHERE username = ?
                    UNION
                    SELECT username FROM clients WHERE username = ?
                    UNION
                    SELECT username FROM helpers WHERE username = ?
                    UNION
                    SELECT username FROM managers_office WHERE username = ?  AND id != ?
                    UNION
                    SELECT username FROM ask_join WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$uname, $uname,  $uname, $uname, $uname, $manager_id, $uname]);
            
            if ($stmt->rowCount() > 0) {
                return 0;
            } else {
                return 1;
            }
        }
      }


    $manager_name = $_POST['manager_name'];
    $uname = $_POST['username'];
    $manager_address = $_POST['manager_address'];
    $manager_gender = $_POST['manager_gender'];
    $manager_email = $_POST['manager_email'];
    $date_of_birth = $_POST['date_of_birth'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];

    
    $manager_national = $_POST['manager_national'];
    $manager_passport = $_POST['manager_passport'];

    $manager_id = $_POST['manager_id'];
    

    $data = 'manager_id='.$manager_id;


    if (empty($manager_name)) {
        $em  = "الاسم مطلوب";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (empty($uname)) {
        $em  = "اسم المستخدم مطلوب";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (!usernamelIsUnique($uname, $conn, $manager_id)) {
        $em  = "اسم المستخدم مأخوذ، اختر واحد آخر";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (empty($manager_address)) {
        $em  = "العنوان مطلوب";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (empty($manager_gender)) {
        $em  = "الجنس مطلوب";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (empty($manager_email)) {
        $em  = "الإيميل مطلوب";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (empty($date_of_birth)) {
        $em  = "تاريخ الميلاد مطلوب";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (empty($city)) {
        $em  = "المدينة مطلوبة";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else if (empty($phone)) {
        $em  = "رقم الهاتف مطلوب";
        header("Location: ../manager-profile.php?error=$em&$data");
        exit;
    }else {
        $sql = "UPDATE managers_office SET
                username = ?, manager_name=?, `manager_email`=? ,manager_gender = ?, manager_city=?, manager_address=?, date_of_birth=?, manager_phone=?, manager_national=?, manager_passport=?
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname,$manager_name, $manager_email, $manager_gender,$city, $manager_address, $date_of_birth, $phone, $manager_national, $manager_passport, $manager_id]);
        $sm = "تم التحديث بنجاح!";
        header("Location: ../manager-profile.php?success=$sm&$data");
        exit;
    }
    
  }else {
    $em = "An error occurred";
    header("Location: ../managers.php?error=$em");
    exit;
  }

  }else {
    header("Location: ../../logout.php");
    exit;
  } 
}else {
    header("Location: ../../logout.php");
    exit;
} 
