<?php 
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {
        $user_id = $_SESSION['user_id'];
        $tables = array(
            'cases' => array('label' => 'القضايا', 'icon' => 'fa-balance-scale', 'url' => 'cases.php'),
            'clients' => array('label' => 'الموكلين', 'icon' => 'fa-users', 'url' => 'clients.php'),
            'documents' => array('label' => 'الوثائق', 'icon' => 'fa-file-text', 'url' => 'documents.php'),
            'todos' => array('label' => 'المهام', 'icon' => 'fa-tasks', 'url' => 'tasks.php')
        );

        include "../DB_connection.php";
        include '../language.php'; 
        include "logo.php";
        include 'permissions_script.php';

        try {
            // الحصول على office_id الخاص بالمساعد
            $sql_office = "SELECT office_id FROM helpers WHERE id = :helper_id";
            $stmt_office = $conn->prepare($sql_office);
            $stmt_office->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $stmt_office->execute();
            $office_id = $stmt_office->fetchColumn();

            // التحقق من أن office_id تم العثور عليه
            if ($office_id) {
                $tableCounts = [];
                foreach ($tables as $table => $data) {
                    if ($table == 'cases') {
                        // جلب عدد القضايا التي بها معرف المساعد
                        $sql = "SELECT COUNT(*) AS count FROM cases WHERE FIND_IN_SET(:helper_id, helper_name)";
                    } elseif ($table == 'clients') {
                        // جلب عدد العملاء المرتبطين بالقضايا التي بها معرف المساعد
                        $sql = "SELECT COUNT(DISTINCT clients.client_id) AS count 
                                FROM clients 
                                JOIN cases ON (clients.client_id = cases.client_id OR FIND_IN_SET(clients.client_id, cases.plaintiff))
                                WHERE FIND_IN_SET(:helper_id, cases.helper_name)";
                    } elseif ($table == 'documents') {
                        // جلب عدد الوثائق المرتبطة بمعرف المساعد
                        $sql = "SELECT COUNT(documents.document_id) AS count
                                FROM documents 
                                LEFT JOIN cases ON documents.case_id = cases.case_id 
                                WHERE FIND_IN_SET(:helper_id, cases.helper_name)";
                    } elseif ($table == 'todos') {
                        // جلب عدد المهام غير المقروءة من قبل المساعد
                        $sql = "SELECT COUNT(*) AS count 
                                FROM todos 
                                WHERE read_by_helper != 1
                                AND (helper_id = :helper_id 
                                     OR case_id IN (SELECT case_id FROM cases WHERE FIND_IN_SET(:helper_id, helper_name)))";
                    } else {
                        continue; // في حالة وجود جداول أخرى لا تحتاج إلى التحقق
                    }

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count = $row['count'];

                    // تخزين قيمة الصفوف في المصفوفة باسم الجدول
                    $tableCounts[$table] = $count;
                }


            } else {
                $errors =  "No office found for this helper.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-1eH6QY7EDqrIPf5bJoGNNd5tWJbF3xxzVJ/Or+EBkpemVBm0uw4ZyVZdunFw+JABmVxVBCjQihFvCe/n+VqhQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    
    <!-- هذا من أجل متصفح فاير فوكس بحال لم يكن مستجيباً -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />


    <link rel="stylesheet" href="../css/style.css">
    <style>
body {
    background-color: #f8f9fa;
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
    /* color:white; */
}

.card-body {
    text-align: center;
    padding: 20px;
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
    background-color: #ffc107;
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

.card-body {
    transition: color 0.5s ease; /* تأثير انتقالي على تغيير لون النص */
}


.card-link:hover {
    background-color: #0056b3;
    /* color: white; */
}
.list-group-flush {
    border-radius: 0;
    text-align: right;
}

    </style>

</head>
<body>
<?php include "inc/footer.php"; ?>    
    
    
    <?php include "inc/navbar.php"; ?>
                    <?php if (isset($errors)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?=$errors?>
                        </div>
                        
                    <?php exit; } ?>
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
                    <h5 class="card-title"><?= __('tasks')?></h5>
                    <span class="badge bg-success rounded-pill">
                        <i class="fas fa-tasks"></i> <?=$tableCounts['todos']?> <?= __('task')?>
                    </span>
                </div>
                </a>
            </div>
        </div>
    <?php endif; ?>
        
        <div class="col">
            <div class="card">
            <a style="text-decoration:none;" href="#" data-bs-toggle="modal" data-bs-target="#newHelperModal" data-id="<?=$user_id?>" class="openNewHelperModal">
                    <div class="card-body">
                        <i class="fa fa-user card-icon"></i>
                        <h5 class="card-title"><?= __('profile')?></h5>
                        <div class="col-md-4">
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card">
            <a href="../logout.php">  
              <div class="card-body">
                    <i class="fas fa-sign-out-alt card-icon"></i>
                    <h5 class="card-title"><?= __('profile')?></h5>
                </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="newHelperModal" tabindex="-1" aria-labelledby="newHelperModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="max-height:90vh;overflow:auto;scrollbar-width:thin;">
      <div class="modal-header">
        <h5 class="modal-title" id="newHelperModalLabel">المعلومات الشخصية</h5>
        <button style="position: absolute;left: 20px;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- سيتم تحميل المحتوى هنا عبر AJAX -->
            
        </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
      </div>
    </div>
  </div>
</div>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $("#navLinks li:nth-child(1) a").addClass('active');
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.openNewHelperModal').on('click', function(){
                var helperId = $(this).data('id');
                $.ajax({
                    url: 'helper-view.php',
                    type: 'GET',
                    data: {id: helperId},
                    success: function(response){
                        $('#newHelperModal .modal-body').html(response);
                    }
                });
            });
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
