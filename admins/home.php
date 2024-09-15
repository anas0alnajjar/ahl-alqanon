<?php 
session_start();

if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admins') {
        $user_id = $_SESSION['user_id'];
        $tables = array(
            'cases' => array('label' => 'القضايا', 'icon' => 'fa-balance-scale', 'url' => 'cases.php'),
            'clients' => array('label' => 'الموكلين', 'icon' => 'fa-users', 'url' => 'clients.php'),
            'lawyer' => array('label' => 'المحامين', 'icon' => 'fa-user-tie', 'url' => 'lawyers.php'),
            'documents' => array('label' => 'الوثائق', 'icon' => 'fa-file-text', 'url' => 'documents.php'),
            'todos' => array('label' => 'المهام', 'icon' => 'fa-tasks', 'url' => 'tasks.php'),
            'message' => array('label' => 'الرسائل', 'icon' => 'fa-envelope', 'url' => 'message.php'),
            'ask_join' => array('label' => 'طلبات الانضمام', 'icon' => 'fa-comments', 'url' => 'requests.php'),
        );

        include "../DB_connection.php";
        include '../language.php'; 

        include "logo.php";
        include 'permissions_script.php';
        
        // الحصول على جميع office_ids الخاصة بالآدمن
        $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
        $stmt_offices = $conn->prepare($sql_offices);
        $stmt_offices->bindParam(':admin_id', $user_id);
        $stmt_offices->execute();
        $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

        // تحويل مصفوفة office_ids إلى سلسلة مفصولة بفواصل
        $office_ids = implode(',', $offices);
        
        foreach ($tables as $table => $data) {
            if (!empty($office_ids) && in_array($table, array('cases', 'clients', 'lawyer', 'documents'))) {
                $sql = "SELECT COUNT(*) AS count FROM $table WHERE office_id IN ($office_ids)";
            } elseif ($table == 'todos') {
                // استعلام متقدم لجلب المهام المرتبطة بمكاتب الآدمن
                $sql = "SELECT COUNT(*) AS count 
                        FROM todos 
                        WHERE read_by_admin != 1
                        AND (
                            client_id IN (SELECT client_id FROM clients WHERE office_id IN ($office_ids)) 
                            OR lawyer_id IN (SELECT lawyer_id FROM lawyer WHERE office_id IN ($office_ids)) 
                            OR case_id IN (SELECT case_id FROM cases WHERE office_id IN ($office_ids)) 
                        )";
            } else {
                $sql = "SELECT COUNT(*) AS count FROM $table";
            }
            
            $result = $conn->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $count = $row['count'];
            
            // تخزين قيمة الصفوف في المصفوفة باسم الجدول
            $tableCounts[$table] = $count;
        }

        $sql_permissions = "SELECT
                                page_permissions.page_name,
                                page_permissions.can_read AS page_read,
                                page_permissions.can_write AS page_write,
                                page_permissions.can_add AS page_add,
                                page_permissions.can_delete AS page_delete
                            FROM
                                powers
                            JOIN offices ON powers.office_id = offices.office_id
                            LEFT JOIN page_permissions ON powers.power_id = page_permissions.role_id
                            WHERE powers.power_id = :admin_id";
        
        $stmt_permissions = $conn->prepare($sql_permissions);
        $stmt_permissions->bindParam(':admin_id', $user_id);
        $stmt_permissions->execute();
        $permissions = $stmt_permissions->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <!-- هذا من أجل متصفح فاير فوكس بحال لم يكن مستجيباً -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    
    <link rel="stylesheet" href="../css/style.css">
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
<?php include "inc/footer.php"; ?>    
    
    
    <?php include "inc/navbar.php"; ?>
    <div class="container-fluid" id="containerFulid">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if ($pages['cases']['read']) : ?>
        <div class="col cases">
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
    <?php endif; ?>
    <?php if ($pages['clients']['read']) : ?>
        <div class="col clients">
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
    <?php endif; ?>
    <?php if ($pages['lawyers']['read']) : ?>
        <div class="col lawyers">
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
    <?php endif; ?>
    <?php if ($pages['documents']['read']) : ?>
        <div class="col documents">
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
    <?php endif; ?>
    <?php if ($pages['notifications']['read']) : ?>
        <div class="col notifications">
            <div class="card">
            <a href="tasks.php">  
              <div class="card-body">
                    <i class="fa fa-tasks card-icon"></i>
                    <h5 class="card-title"><?= __('tasks') ?></h5>
                    <span class="badge bg-success rounded-pill">
                        <i class="fas fa-tasks"></i> <?=$tableCounts['todos']?> <?= __('task') ?>
                    </span>
                </div>
                </a>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($pages['inbox']['read']) : ?>
        <div class="col inbox">
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
    <?php endif; ?>
    <?php if ($pages['join_requests']['read']) : ?>
        <div class="col join_requests">
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
    <?php endif; ?>
        <div class="col">
            <div class="card">
            <a href="admin-profile.php?admin_id=<?=$user_id?>">  
            <div class="card-body">
                    <i class="fa fa-user card-icon"></i>
                    <h5 class="card-title"><?= __('profile') ?></h5>
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
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#navLinks li:nth-child(1) a").addClass('active');
        });
    </script>



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
