<?php 

if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {
     include "../DB_connection.php";
     include "data/setting.php";
        $setting = getSetting($conn);
?>
<?php 
}else {
  header("Location: ../login.php");
  exit;
} 
?>