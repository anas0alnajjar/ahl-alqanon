<?php 

if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {
     include "../DB_connection.php";
     include "data/setting.php";
        
        $client_id = $_SESSION['user_id'];
        $setting = getSetting($conn, $client_id);
?>
<?php 
}else {
  header("Location: ../login.php");
  exit;
} 
?>