<?php 

if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {
     include "../DB_connection.php";
     include "data/setting.php";
        
        $admin_id = $_SESSION['user_id'];
        $setting = getSetting($conn, $admin_id);
?>
<?php 
}else {
  header("Location: ../login.php");
  exit;
} 
?>