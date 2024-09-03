<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['manager_id'])) {

  if ($_SESSION['role'] == 'Client') {
     include "../../DB_connection.php";

     include '../permissions_script.php';

        if ($pages['managers']['delete'] == 0) {
            header("Location: ../home.php");
            exit();
        }

     

     function removeVerification($id, $conn){
        $sql  = "DELETE FROM managers_office
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $re   = $stmt->execute([$id]);
        if ($re) {
            return 1;
        } else {
            return 0;
        }
    }

     $id = $_GET['manager_id'];
     if (removeVerification($id, $conn)) {
     	$sm = "تم الحذف بنجاح";
        header("Location: ../managers.php?success=$sm");
        exit;
     }else {
        $em = "Unknown error occurred";
        header("Location: ../managers.php?error=$em");
        exit;
     }


  }else {
    header("Location: ../managers.php");
    exit;
  } 
}else {
	header("Location: ../managers.php");
	exit;
} 