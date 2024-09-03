<?php 

if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {
     include "../DB_connection.php";
     include "data/setting.php";
        
        $helper_id = $_SESSION['user_id'];
        $setting = getSetting($conn, $helper_id);
?>
<?php 
}else {
  header("Location: ../login.php");
  exit;
} 
?>