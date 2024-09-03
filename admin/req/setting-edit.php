<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
    	
if (isset($_POST['company_name']) &&
    isset($_POST['slogan']) &&
    isset($_POST['about']) && 
    isset($_POST['current_year'])) {
    
    include '../../DB_connection.php';

    $company_name = $_POST['company_name'];
    $slogan = $_POST['slogan'];
    $about = $_POST['about'];
    $current_year = $_POST['current_year'];

    $host_whatsapp = $_POST['host_whatsapp'];
    $token_whatsapp = $_POST['token_whatsapp'];
    $host_email = $_POST['host_email'];
    $password_email = $_POST['password_email'];
    $port_email = $_POST['port_email'];
    $username_email = $_POST['username_email'];

    $site_key = $_POST['site_key'];
    $secret_key = $_POST['secret_key'];
    $api_map = $_POST['api_map'];
    $allow_joining = isset($_POST['allow_joining']) ? 1 : 0;
    $allow_check = isset($_POST['allow_check']) ? 1 : 0;

    if (empty($company_name)) {
        $em  = "Company name is required";
        header("Location: ../settings.php?error=$em");
        exit;
    } else if (empty($slogan)) {
        $em  = "Slogan is required";
        header("Location: ../settings.php?error=$em");
        exit;
    } else if (empty($about)) {
        $em  = "About is required";
        header("Location: ../settings.php?error=$em");
        exit;
    } else {
        $id = 1;
        $sql  = "UPDATE setting 
                 SET company_name=?,
                     slogan=?,
                     about=?,
                     current_year=?, 
                     host_whatsapp=?,
                     token_whatsapp=?,
                     host_email=?,
                     username_email=?,
                     password_email=?,
                     port_email=?,
                     site_key=?,
                     secret_key=?,
                     api_map=?,
                     allow_joining=?,
                     allow_check=?
                 WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$company_name, $slogan, $about, $current_year, $host_whatsapp, $token_whatsapp, $host_email, $username_email, $password_email, $port_email, $site_key, $secret_key, $api_map, $allow_joining, $allow_check, $id]);
        $sm = "تم تحديث الاعدادات بنجاح";
        header("Location: ../settings.php?success=$sm");
        exit;
    }
    
} else {
    $em = "An error occurred";
    header("Location: ../section.php?error=$em");
    exit;
}

    } else {
        header("Location: ../../logout.php");
        exit;
    } 
} else {
    header("Location: ../../logout.php");
    exit;
} 
?>
