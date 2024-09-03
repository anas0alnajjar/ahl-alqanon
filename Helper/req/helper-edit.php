<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
        

if (isset($_POST['helper_name'])      &&
    isset($_POST['phone']) ) {
    
    include '../../DB_connection.php';
    include '../permissions_script.php';
        if ($pages['assistants']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }




    $helper_name = $_POST['helper_name'];
    $phone = $_POST['phone'];
    

    $national_helper = $_POST['national_helper'];
    $passport_helper = $_POST['passport_helper'];

    

    $helper_id = $_POST['id'];
    $office_id = $_POST['office_id'];

    

    $data = 'id='.$helper_id;

    if (empty($helper_name)) {
        $em  = "اسم المساعد مطلوب";
        header("Location: ../helper-profile.php?error=$em&$data");
        exit;
    }else if (empty($phone)) {
        $em  = "رقم الهاتف مطلوب";
        header("Location: ../helper-profile.php?error=$em&$data");
        exit;
    }else {
        $sql = "UPDATE helpers SET helper_name = ?, `phone`=? , passport_helper=?, national_helper=?, office_id=?
                WHERE `id`=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$helper_name , $phone, $passport_helper, $national_helper, $office_id, $helper_id]);
        $sm = "تم التحديث بنجاح!";
        header("Location: ../helper-profile.php?success=$sm&$data");
        exit;
    }
    
  }else {
    $em = "An error occurred";
    header("Location: ../index.php?error=$em");
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
