<?php 

if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {
     include "../DB_connection.php";
     include "data/setting.php";
        
        $lawyer_id = $_SESSION['user_id'];
        $setting = getSetting($conn, $lawyer_id);
?>
<?php 
}else {
  header("Location: ../login.php");
  exit;
} 
?>