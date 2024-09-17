<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        $tables = array(
            'cases' => array('label' => 'القضايا', 'icon' => 'fa-balance-scale', 'url' => 'cases.php'),
            'clients' => array('label' => 'الموكلين', 'icon' => 'fa-users', 'url' => 'clients.php'),
            'lawyer' => array('label' => 'المحامين', 'icon' => 'fa-user-tie', 'url' => 'lawyers.php'),
            'documents' => array('label' => 'الوثائق', 'icon' => 'fa-file-text', 'url' => 'documents.php'),
            'todos' => array('label' => 'المهام', 'icon' => 'fa-tasks', 'url' => 'tasks.php'),
            'message' => array('label' => 'الرسائل', 'icon' => 'fa-envelope', 'url' => 'message.php'),
            'ask_join' => array('label' => 'طلبات الانضمام', 'icon' => 'fa-comments', 'url' => 'requests.php'),
            'languages' => array('label' => 'languages', 'icon' => 'fa-comments', 'url' => 'requests.php'),
        );
        


        

        include "../DB_connection.php";
        include '../language.php'; 
        include "logo.php";
        foreach ($tables as $table => $data) {
            $sql = "SELECT COUNT(*) AS count FROM $table";
            
            // إذا كان الجدول هو "todos"، قم بإضافة الشرط إلى الاستعلام
            if ($table == 'todos') {
                $sql .= " WHERE checked != 1";
            }
            
            $result = $conn->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $count = $row['count'];
            
            // تخزين قيمة الصفوف في المصفوفة باسم الجدول
            $tableCounts[$table] = $count;
        }
        
        
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/yshstyle.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <!-- هذا من أجل متصفح فاير فوكس بحال لم يكن مستجيباً -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <style>
body {
  /*  background-color: #f8f9fa; */

    background-color: #cfccc0;
    font-family: "Cairo", sans-serif;
    direction: rtl;
}

#containerFulid {
    padding-top: 20px;
}

.card {
    border: none;
    transition: all 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.card-body {
    text-align: center;
    padding: 10px;
}
.card-body:hover {
    text-align: center;
    padding: 10px;
}
.card-title {
    font-size: 1.5rem;
    margin-bottom: 10px;
    
}

.card-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.badge {
   /* background-color: #ffc107;*/
    background-color: #272c3f;
    border-radius: 50%;
    padding: 5px 10px;
    position: absolute;
    top: 10px;
    right: 10px;
    color: #fff;
    font-size: 0.8rem;
}

a {
    color: unset;
    text-decoration: none;
}
.card-title{
    color:green;
}
/* أنيميشن للألوان */
@keyframes colorChange {
    0% { background-position: 0% 30%; }
    100% { background-position: 30% 20%; }
}

.card:hover {
 /*   background: linear-gradient(230deg, #fd7921, #fbb03b, #3db9fc, #8e44ad, #fd7921); */
    background: #272c3f;
    background-size: 200% 200%;
    -webkit-animation: gradientBG 5s ease infinite;
    -moz-animation: gradientBG 5s ease infinite;
    animation: gradientBG 5s ease infinite;
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}

.card:hover .card-body {
    /* color: white; تغيير لون النص إلى الأبيض */
    color: #cfccc0; /* color: #ff0000; تغيير لون النص إلى الأبيض */
}

.card-body {
    transition: color 0.5s ease; /* تأثير انتقالي على تغيير لون النص */
}



    </style>

</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container-fluid" id="containerFulid">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <div class="col">
                <div class="card cases-card">
                <a href="cases.php">
                    <div class="card-body">
                        <i class="fa fa-balance-scale card-icon"></i>
                        <h5 class="card-title"><?= __('cases') ?></h5>
                        <span class="badge bg-primary rounded-pill">
                            <i class="fas fa-balance-scale"></i> <?=$tableCounts['cases']?> <?= __('cases') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card clients-card">
                <a href="clients.php">  
                  <div class="card-body">
                        <i class="fa fa-users card-icon"></i>
                        <h5 class="card-title "><?= __('clients') ?></h5>
                        <span class="badge bg-primary rounded-pill">
                            <i class="fas fa-user"></i> <?=$tableCounts['clients']?> <?= __('clients') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card lawyers-card">
                <a href="lawyers.php">
                    <div class="card-body">
                        <i class="fas fa-user-tie card-icon"></i>
                        <h5 class="card-title"><?= __('lawyers') ?></h5>
                        <span class="badge bg-secondary rounded-pill">
                            <i class="fas fa-user-tie"></i> <?=$tableCounts['lawyer']?> <?= __('lawyer') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card documents-card">
                <a href="documents.php">  
                  <div class="card-body">
                        <i class="fa fa-file-text card-icon"></i>
                        <h5 class="card-title"><?= __('documents') ?></h5>
                        <span class="badge bg-info rounded-pill">
                            <i class="fas fa-file"></i> <?=$tableCounts['documents']?> <?= __('documents') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card">
                <a href="tasks.php">  
                  <div class="card-body">
                        <i class="fa fa-tasks card-icon"></i>
                        <h5 class="card-title"><?=__('tasks')?></h5>
                        <span class="badge bg-success rounded-pill">
                            <i class="fas fa-tasks"></i> <?=$tableCounts['todos']?> <?= __('task') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card messages-card">
                <a href="message.php">
                    <div class="card-body">
                        <i class="fa fa-envelope card-icon"></i>
                        <h5 class="card-title"><?= __('messages') ?></h5>
                        <span class="badge bg-warning rounded-pill">
                            <i class="fas fa-envelope"></i> <?=$tableCounts['message']?> <?= __('messages') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card requests-card">
                <a href="requests.php">  
                  <div class="card-body">
                        <i class="fas fa-comments card-icon"></i>
                        <h5 class="card-title"><?= __('requests') ?></h5>
                        <span class="badge bg-danger rounded-pill">
                            <i class="fas fa-user-plus"></i> <?=$tableCounts['ask_join']?> <?= __('requests') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card">
                <a href="settings.php">  
                  <div class="card-body">
                        <i class="fa fa-cogs card-icon"></i>
                        <h5 class="card-title"><?= __('setting') ?></h5>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card">
                <a href="../logout.php">  
                  <div class="card-body">
                        <i class="fas fa-sign-out-alt card-icon"></i>
                        <h5 class="card-title"><?= __('log_out') ?></h5>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="card cases-card">
                <a href="languages.php">
                    <div class="card-body">
                    <i class="fa fa-language card-icon"></i>
                        <h5 class="card-title"><?= __('languages') ?></h5>
                        <span class="badge bg-primary rounded-pill">
                        <i class="fa fa-language"></i></i> <?=$tableCounts['languages']?> <?= __('languages') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#navLinks li:nth-child(1) a").addClass('active');
        });
    </script>
<?php include "inc/footer.php"; ?>    
</body>

</html>
<?php 

  } else {
    header("Location: ../login.php");
    exit;
  } 
} else {
    header("Location: ../login.php");
    exit;
} 

?>