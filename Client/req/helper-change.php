<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Client') {
    	

if (isset($_POST['new_pass'])   &&
    isset($_POST['c_new_pass']) &&
    isset($_POST['helper_id'])) {
    
    include '../../DB_connection.php';
    include "../data/admin.php";
    include '../permissions_script.php';
        if ($pages['assistants']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }

    $new_pass = $_POST['new_pass'];
    $c_new_pass = $_POST['c_new_pass'];

    $helper_id = $_POST['helper_id'];
    $id = $_SESSION['user_id'];
    
    $data = 'id='.$helper_id.'#change_password';

    if (empty($new_pass)) {
		$em  = "كلمة السر الجديدة مطلوبة";
		header("Location: ../helper-profile.php?perror=$em&$data");
		exit;
	}else if (empty($c_new_pass)) {
		$em  = "تأكيد كلمة السر مطلوب";
		header("Location: ../helper-profile.php?perror=$em&$data");
		exit;
	}else if ($new_pass !== $c_new_pass) {
        $em  = "كلمات السر غير متطابقة";
        header("Location: ../helper-profile.php?perror=$em&$data");
        exit;
    }else {
        // hashing the password
        $new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

        $sql = "UPDATE helpers SET
                `pass` = ?
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$new_pass, $helper_id]);
        $sm = "تم تحديث كلمة السر بنجاح!";
        header("Location: ../helper-profile.php?psuccess=$sm&$data");
        exit;
	}
    
  }else {
  	$em = "حدث خطأ، راجع الدعم الفني";
    header("Location: ../helper-profile.php?error=$em&$data");
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
