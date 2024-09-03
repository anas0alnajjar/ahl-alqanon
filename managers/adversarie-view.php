<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {
       include "../DB_connection.php";
       include "logo.php";

       include 'permissions_script.php';
       if ($pages['adversaries']['read'] == 0) {
           header("Location: home.php");
           exit();
       }


       if(isset($_GET['id'])){

       $adver_id = $_GET['id'];
       
        function getAdeverById($id, $conn){
            $sql = "SELECT * FROM adversaries
                    WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
        
            if ($stmt->rowCount() == 1) {
            $adver = $stmt->fetch();
            return $adver;
            } else {
            return 0;
            }
        }

       $adver = getAdeverById($adver_id, $conn);    
 ?>
<!DOCTYPE html>
<html lang="ar">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Adversarie View</title>

	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>
<body>
    <?php 
        include "inc/navbar.php";
        if ($adver != 0) {
     ?>
<div class="container mt-5" style="direction: rtl;">
    <div class="card shadow-sm mx-auto" style="max-width: 90%;">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="card-title">@<?= htmlspecialchars($adver['fname'] . ' ' . $adver['lname'] ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"> الاسم الأول: <?= htmlspecialchars($adver['fname'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                <li class="list-group-item"> الاسم الأخير: <?= htmlspecialchars($adver['lname'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                <li class="list-group-item"> المدينة: <?= htmlspecialchars($adver['city'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                <li class="list-group-item"> العنوان: <?= htmlspecialchars($adver['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                <li class="list-group-item"> سنة الولادة: <?= htmlspecialchars($adver['date_of_birth'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                <li class="list-group-item"> الهاتف: <?= htmlspecialchars($adver['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                <li class="list-group-item">الجنس: <?= htmlspecialchars($adver['gender'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                

            </ul>
        </div>
        <div class="card-footer text-center">
            <a href="adversaries.php" class="btn btn-dark">العودة</a>
            <a href="home.php" class="btn btn-secondary">الرئيسية</a>
        </div>
    </div>
</div>

     <?php 
        }else {
          header("Location: cases.php");
          exit;
        }
     ?>
     
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	


</body>
</html>
<?php 

    }else {
        header("Location: cases.php");
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
