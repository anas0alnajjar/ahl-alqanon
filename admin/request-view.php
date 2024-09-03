<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
       include "../DB_connection.php";
       include "logo.php";

       if(isset($_GET['user_id'])){

       $user_id = $_GET['user_id'];
       
       
    function getRequestById($id, $conn){
        $sql = "SELECT * FROM ask_join
                WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
    
        if ($stmt->rowCount() == 1) {
        $request = $stmt->fetch();
        return $request;
        } else {
        return 0;
        }
    }

    $request = getRequestById($user_id, $conn); 
 ?>
<!DOCTYPE html>
<html lang="ar">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Request - View</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php 
        include "inc/navbar.php";
        if ($request != 0) {
     ?>
     <div class="container mt-5" style="direction: rtl;">
         <div class="card" style="width: 22rem;">
          <img src="../img/student-<?=$request['gender']?>.png" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title text-center">@<?=$request['username']?></h5>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item">الاسم الأول: <?=$request['first_name']?></li>
            <li class="list-group-item">الاسم الأخير: <?=$request['last_name']?></li>
            <li class="list-group-item">اسم المستخدم: <?=$request['username']?></li>
            <li class="list-group-item">تاريخ الميلاد: <?=$request['date_of_birth']?></li>
            <li class="list-group-item">البريد الإلكتروني: <?=$request['email']?></li>
            <li class="list-group-item">الجنس: <?=$request['gender']?></li>
            <li class="list-group-item">الهاتف: <?=$request['phone']?></li>
            <li class="list-group-item">المدينة: <?=$request['city']?></li>
          </ul>
          <div class="card-body">
            <a href="requests.php" class="card-link">الرجوع</a>
          </div>
        </div>
     </div>
     <?php 
        }else {
          header("Location: requests.php");
          exit;
        }
     ?>
     
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(9) a").addClass('active');
        });
    </script>

</body>
</html>
<?php 

    }else {
        header("Location: requests.php");
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
