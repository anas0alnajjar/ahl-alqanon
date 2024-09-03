<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['id'])) {

  if ($_SESSION['role'] == 'Admin') {
     include "../../DB_connection.php";

     

     function removeVerification($id, $conn){
        $sql  = "DELETE FROM types_of_cases
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
        header("Location: ../types_case.php?success=$sm");
        exit;
     }else {
        $em = "Unknown error occurred";
        header("Location: ../types_case.php?error=$em");
        exit;
     }


  }else {
    header("Location: ../types_case.php");
    exit;
  } 
}else {
	header("Location: ../types_case.php");
	exit;
} 