<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['id'])) {

  if ($_SESSION['role'] == 'Helper') {
     include "../../DB_connection.php";
     include '../permissions_script.php';
     if ($pages['judicial_circuits']['delete'] == 0) {
         header("Location: ../home.php");
         exit();
     }

     

     function removeVerification($id, $conn){
        $sql  = "DELETE FROM departments
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $re   = $stmt->execute([$id]);
        if ($re) {
            return 1;
        } else {
            return 0;
        }
    }

     $id = $_GET['id'];
     if (removeVerification($id, $conn)) {
     	$sm = "تم الحذف بنجاح";
        header("Location: ../departments_types.php?success=$sm");
        exit;
     }else {
        $em = "Unknown error occurred";
        header("Location: ../departments_types.php?error=$em");
        exit;
     }


  }else {
    header("Location: ../departments_types.php");
    exit;
  } 
}else {
	header("Location: ../departments_types.php");
	exit;
} 