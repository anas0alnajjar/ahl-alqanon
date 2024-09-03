<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['lawyer_id'])) {

  if ($_SESSION['role'] == 'Admins') {
     include "../DB_connection.php";
     include "data/lawyers.php";

     include 'permissions_script.php';
     if ($pages['lawyers']['delete'] == 0) {
         header("Location: home.php");
         exit();
     }


     $id = $_GET['lawyer_id'];
     if (removeLawyer($id, $conn)) {
     	$sm = "تم الحذف بنجاح!";
        header("Location: lawyers.php?success=$sm");
        exit;
     }else {
        $em = "Unknown error occurred";
        header("Location: lawyers.php?error=$em");
        exit;
     }


  }else {
    header("Location: lawyers.php");
    exit;
  } 
}else {
	header("Location: lawyers.php");
	exit;
} 