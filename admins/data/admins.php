<?php 
function getAdminById($id, $conn){
    $sql = "SELECT 
                admin.*,
                setting.host_email,
                setting.username_email,
                setting.password_email,
                setting.port_email,
                setting.host_whatsapp,
                setting.token_whatsapp,
                setting.logo
            FROM 
                admin
            LEFT JOIN 
                setting ON setting.admin_id = admin.admin_id
            WHERE 
                admin.admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
 
    if ($stmt->rowCount() == 1) {
      $admin = $stmt->fetch();
      return $admin;
    } else {
     return 0;
    }
 }
 

?>