<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
       include "../DB_connection.php";
       include "../logo.php";
       include "data/client.php";

       if(isset($_GET['client_id'])){

       $client_id = $_GET['client_id'];

       $client = getClientById($client_id, $conn);    
 ?>
<!DOCTYPE html>
<html lang="ar">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Client View</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php 
        include "inc/navbar.php";
        if ($client != 0) {
     ?>
     <div class="container mt-5" style="direction: rtl;">
         <div class="card" style="width: 22rem;">
          <img src="../img/student-<?=$client['gender']?>.png" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title text-center">@<?=$client['username']?></h5>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item">الاسم الأول: <?=$client['first_name']?></li>
            <li class="list-group-item">الاسم الأخير: <?=$client['last_name']?></li>
            <li class="list-group-item">اسم المستخدم: <?=$client['username']?></li>
            <li class="list-group-item">العنوان: <?=$client['address']?></li>
            <li class="list-group-item">تاريخ الميلاد: <?=$client['date_of_birth']?></li>
            <li class="list-group-item">البريد الإلكتروني: <?=$client['email']?></li>
            <li class="list-group-item">الجنس: <?=$client['gender']?></li>
            <li class="list-group-item">الهاتف: <?=$client['phone']?></li>
            <li class="list-group-item">المدينة: <?=$client['city']?></li>
          </ul>
          <div class="card-body">
            <a href="clients.php" class="card-link">العودة</a>
          </div>
        </div>
     </div>
     <?php 
        }else {
          header("Location: clients.php");
          exit;
        }
     ?>
     
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(3) a").addClass('active');
        });
    </script>

</body>
</html>
<?php 

    }else {
        header("Location: client.php");
        exit;
    }

  }else {
    header("Location: ../login.php");
    exit;
  } 
}else {
	header("Location: ../login.php");
	exit;
} 

?>
