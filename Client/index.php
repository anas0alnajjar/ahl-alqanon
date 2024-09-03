<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Client') {
        include "../DB_connection.php";
        include "logo.php";
    
        header("refresh:2;url=home.php");
    


?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <title>Loading...</title>
 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');
        h1 {
    font-family: "Cairo", sans-serif;
    font-optical-sizing: auto;
    font-weight: 700;
    font-size: 2rem;
    text-align: center; /* تحويله إلى وسط الصفحة */
    direction: rtl;
    font-style: normal;
    font-variation-settings: "slnt" 0;
    position: absolute; /* جعلها موضعية */
    left: 50%; /* تحديد الجزء الأيسر من العنصر إلى 50% من عرض الشاشة */
    
    transform: translate(-39%, -28%);
}

    </style>


</head>
<body>
<div class="body">
  <span></span>
  <span></span>
  <span></span>
  <span></span>
  <span></span>
  <div class="base">
    <span></span>
    <div class="face"></div>
  </div>
</div>
<div class="longfazers">
  <span></span>
  <span></span>
  <span></span>
  <span></span>
</div>
<h1>جار تجهيز بيئة العمل وتأمين البيانات</h1>

</body>
</html>

<?php 

  }else {
    header("Location: ../login.php");
    exit;
  } 
}else {
	header("Location: ../login.php");
	exit;
} 

?>