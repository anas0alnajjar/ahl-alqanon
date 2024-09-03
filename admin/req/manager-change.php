<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
    	

if (isset($_POST['admin_pass']) &&
    isset($_POST['new_pass'])   &&
    isset($_POST['c_new_pass']) &&
    isset($_POST['manager_id'])) {
    
    include '../../DB_connection.php';
    include "../data/admin.php";

    $admin_pass = $_POST['admin_pass'];
    $new_pass = $_POST['new_pass'];
    $c_new_pass = $_POST['c_new_pass'];

    $manager_id = $_POST['manager_id'];
    $id = $_SESSION['admin_id'];
    
    $data = 'manager_id='.$manager_id.'#change_password';

    if (empty($admin_pass)) {
		$em  = "كلمة سر الآدمن مطلوبة";
		header("Location: ../manager-edit.php?perror=$em&$data");
		exit;
	}else if (empty($new_pass)) {
		$em  = "كلمة السر الجديدة مطلوبة";
		header("Location: ../manager-edit.php?perror=$em&$data");
		exit;
	}else if (empty($c_new_pass)) {
		$em  = "تأكيد كلمة السر مطلوب";
		header("Location: ../manager-edit.php?perror=$em&$data");
		exit;
	}else if ($new_pass !== $c_new_pass) {
        $em  = "كلمات السر غير متطابقة";
        header("Location: ../manager-edit.php?perror=$em&$data");
        exit;
    }else if (!adminPasswordVerify($admin_pass, $conn, $id)) {
        $em  = "كلمة سر الآدمن غير صحيحة";
        header("Location: ../manager-edit.php?perror=$em&$data");
        exit;
    }else {
        // hashing the password
        $new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

        $sql = "UPDATE managers_office SET
                `manager_password` = ?
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$new_pass, $manager_id]);
        $sm = "تم تحديث كلمة المرور بنجاح!";
        header("Location: ../manager-edit.php?psuccess=$sm&$data");
        exit;
	}
    
  }else {
  	$em = "An error occurred";
    header("Location: ../manager-edit.php?error=$em&$data");
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
