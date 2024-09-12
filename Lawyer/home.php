<?php 
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {
        $user_id = $_SESSION['user_id'];
        $tables = array(
            'cases' => array('label' => 'القضايا', 'icon' => 'fa-balance-scale', 'url' => 'cases.php'),
            'clients' => array('label' => 'الموكلين', 'icon' => 'fa-users', 'url' => 'clients.php'),
            'documents' => array('label' => 'الوثائق', 'icon' => 'fa-file-text', 'url' => 'documents.php'),
            'todos' => array('label' => 'المهام', 'icon' => 'fa-tasks', 'url' => 'tasks.php'),
            'helpers' => array('label' => 'المساعدين', 'icon' => 'fa-tasks', 'url' => 'helpers.php'),
        );

        include "../DB_connection.php";
        include '../language.php'; 
        include "logo.php";
        include 'permissions_script.php';

        try {
            // الحصول على office_id الخاص بالمحامي
            $sql_office = "SELECT office_id FROM lawyer WHERE lawyer_id = :lawyer_id";
            $stmt_office = $conn->prepare($sql_office);
            $stmt_office->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
            $stmt_office->execute();
            $office_id = $stmt_office->fetchColumn();

            // التحقق من أن office_id تم العثور عليه
            if ($office_id) {
                $tableCounts = [];
                foreach ($tables as $table => $data) {
                    if (in_array($table, array('cases', 'clients', 'helpers'))) {
                        $sql = "SELECT COUNT(*) AS count FROM $table WHERE lawyer_id = :lawyer_id";
                    } elseif ($table == 'todos') {
                        // استعلام متقدم لجلب المهام المرتبطة
                        $sql = "SELECT COUNT(*) AS count 
                                FROM todos 
                                WHERE read_by_lawyer IS NULL
                                AND (lawyer_id = :lawyer_id 
                                     OR case_id IN (SELECT case_id FROM cases WHERE lawyer_id = :lawyer_id))";
                    } elseif ($table == 'documents') {
                        // استعلام خاص لجلب الوثائق
                        $sql = "SELECT COUNT(documents.document_id) AS count
                                FROM documents 
                                LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                                LEFT JOIN clients ON documents.client_id = clients.client_id 
                                LEFT JOIN cases ON documents.case_id = cases.case_id 
                                WHERE documents.lawyer_id = :lawyer_id 
                                OR clients.lawyer_id = :lawyer_id";
                    } else {
                        continue; // في حالة وجود جداول أخرى لا تحتاج إلى التحقق
                    }

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count = $row['count'];

                    // تخزين قيمة الصفوف في المصفوفة باسم الجدول
                    $tableCounts[$table] = $count;
                }

                // هنا يمكنك طباعة النتائج أو استخدامها حسب الحاجة
                // على سبيل المثال:
                foreach ($tableCounts as $table => $count) {

                }

            } else {
                $errors =  "No office found for this lawyer.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

?>
<?php
try {
    require_once "get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);
    
    // الاستعلام لجلب لوغو المكتب
    $office_logo_query = "SELECT * FROM profiles WHERE office_id = ?";
    $office_logo_stmt = $conn->prepare($office_logo_query);
    $office_logo_stmt->execute([$OfficeId]);
    $office_logo_result = $office_logo_stmt->fetch(PDO::FETCH_ASSOC);
    $office_logo = $office_logo_result ? $office_logo_result['logo'] : null;

    // الاستعلام لجلب معرف الآدمن من جدول offices
    $admin_id_query = "SELECT admin_id FROM offices WHERE office_id = ?";
    $admin_id_stmt = $conn->prepare($admin_id_query);
    $admin_id_stmt->execute([$OfficeId]);
    $admin_id_result = $admin_id_stmt->fetch(PDO::FETCH_ASSOC);
    $admin_id = $admin_id_result ? $admin_id_result['admin_id'] : null;

    $admin_logo = null;

    // التحقق من وجود admin_id قبل استخدامه
    if (!empty($admin_id)) {
        // الاستعلام لجلب لوغو الآدمن
        $admin_logo_query = "SELECT logo FROM setting WHERE admin_id = ?";
        $admin_logo_stmt = $conn->prepare($admin_logo_query);
        $admin_logo_stmt->execute([$admin_id]);
        $admin_logo_result = $admin_logo_stmt->fetch(PDO::FETCH_ASSOC);
        $admin_logo = $admin_logo_result ? $admin_logo_result['logo'] : null;
    }

    // الاستعلام لجلب لوغو المحامي
    $lawyer_logo_query = "SELECT lawyer_logo FROM lawyer WHERE lawyer_id = ?";
    $lawyer_logo_stmt = $conn->prepare($lawyer_logo_query);
    $lawyer_logo_stmt->execute([$user_id]);
    $lawyer_logo_result = $lawyer_logo_stmt->fetch(PDO::FETCH_ASSOC);
    $lawyer_logo = $lawyer_logo_result ? $lawyer_logo_result['lawyer_logo'] : null;

    // تحديد مسار اللوغو النهائي
    if (!empty($lawyer_logo)) {
        $logo_path = "../img/lawyers/" . $lawyer_logo;
    } elseif (!empty($office_logo)) {
        $logo_path = "../../profiles_photos/" . $office_logo;
    } else {
        $logo_path = "../../img/" . $admin_logo;
    }
} catch (PDOException $e) {
    // التعامل مع الأخطاء
    error_log("Database error: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
    exit();
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
    <link rel="stylesheet" href="../css/yshstyle.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-1eH6QY7EDqrIPf5bJoGNNd5tWJbF3xxzVJ/Or+EBkpemVBm0uw4ZyVZdunFw+JABmVxVBCjQihFvCe/n+VqhQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    
    <!-- هذا من أجل متصفح فاير فوكس بحال لم يكن مستجيباً -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />


    <link rel="stylesheet" href="../css/style.css">
    
    <style>
body {
    background-color: #cfccc0; /* f8f9fa*/
    font-family: "Cairo", sans-serif;
    direction: rtl;
}

#containerFulid {
    padding-top: 10px; /* update from 20 to 10 */
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
    color:#cfccc0; 

    /* color:white; */
}

.card-body {
    text-align: center;
    padding: 10px;
    color:#cfccc0; 

}
.card-body:hover {
    text-align: center;
    padding: 10px;
    color:#272c3f; 

}
.card-title {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #cfccc0;
}
.card-title:hover {

    color:#272c3f !important;

}
.card-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}
.card-icon:hover {
    font-size: 3rem;
    margin-bottom: 20px;
    color:#272c3f !important;
}
.badge {
    background-color: #cfccc0; /* ffc107*/
    border-radius: 50%;
    padding: 5px 10px;
    position: absolute;
    top: 10px;
    right: 10px;
    color: #cfccc0;
    font-size: 0.8rem;
}

a {
    color: unset;
    text-decoration: none;
}

.card-body {
    transition: color 0.5s ease; /* تأثير انتقالي على تغيير لون النص */
}

#lawyer_logo_style{
    max-width: 100px;
    max-height: 100px;
    float: left;
    padding: 0;
    margin: 0;
    position: absolute;
    left: 10px;
    top: 4px;
    opacity: 0.5;
    border-radius: 50%;
}
.logo-container {
    position: relative;
    margin-bottom: 1rem;
    text-align: center;
    margin: 1rem auto;
    width: 150px;
    height: 150px;
}
.logo-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    position: relative;
}
.logo-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.edit-icon {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    color: #cfccc0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s;
}
.logo-container:hover .edit-icon {
    opacity: 1;
}
.card-title {
    margin-bottom: 1rem;
    font-size: 1.5rem;
    color: #cfccc0;;
    text-align:center;
}
.list-group-item {
    background-color: transparent;
    border: none;
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
    color: #cfccc0;
    text-align: right;
}
.card-link {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    color: #cfccc0;
    background-color: #272c3f;
    border-radius: 25px;
    text-decoration: none;
    transition: background-color 0.3s;
}
.card-link:hover {
    background-color: #cfccc0;
    color: #272c3f;
    /* color: white; */
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
                    <h5 class="card-title ">الموكلين</h5>
                    <span class="badge bg-primary rounded-pill">
                        <i class="fas fa-user"></i> <?=$tableCounts['clients']?> عميل
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
                    <h5 class="card-title"><?=__('documents')?></h5>
                    <span class="badge bg-info rounded-pill">
                        <i class="fas fa-file"></i> <?=$tableCounts['documents']?> وثائق/عقود
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
                    <h5 class="card-title">المهام</h5>
                    <span class="badge bg-success rounded-pill">
                        <i class="fas fa-tasks"></i> <?=$tableCounts['todos']?> مهمة/إشعار
                    </span>
                </div>
                </a>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($pages['join_requests']['read']) : ?>
        <div class="col join_requests">
            <div class="card requests-card">
            <a href="helpers.php">  
            <div class="card-body">
                <i class="fas fa-user-tie card-icon"></i> <!-- أيقونة جديدة -->
                <h5 class="card-title">الإداريين</h5>
                <span class="badge bg-danger rounded-pill">
                    <i class="fas fa-user-plus"></i> <?=$tableCounts['helpers']?> إداري
                </span>
            </div>
            </a>
            </div>
        </div>
    <?php endif; ?>
        
        <div class="col">
            <div class="card">
            <a href="#" data-bs-toggle="modal" data-bs-target="#lawyerModal" data-id="<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>" class="openLawyerModal">
                    <div class="card-body">
                        <i class="fa fa-user card-icon"></i>
                        <h5 class="card-title">الملف الشخصي</h5>
                        <div class="col-md-4">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#lawyerModal" data-id="<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>" class="openLawyerModal">
                                <img id="lawyer_logo_style" src="<?= htmlspecialchars($logo_path, ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="Lawyer Logo">
                            </a>
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
                    <h5 class="card-title">تسجيل الخروج</h5>
                </div>
                </a>
            </div>
        </div>
    </div>
</div>

    <!-- Modal -->
    <div class="modal fade" id="lawyerModal" tabindex="-1" aria-labelledby="lawyerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="lawyerModalLabel">المعلومات الشخصية</h5>
            <button style="position: absolute;left: 10px;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="max-height:70vh;overflow:auto;">
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
    $('.openLawyerModal').on('click', function(){
        var lawyerId = $(this).data('id');
        $.ajax({
            url: 'lawyer-view.php',
            type: 'GET',
            data: {lawyer_id: lawyerId},
            success: function(response){
                $('#lawyerModal .modal-body').html(response);
                bindImageUpload();
            }
        });
    });

    function bindImageUpload() {
        $('#id_picture').on('change', function() {
            var formData = new FormData();
            formData.append('lawyer_logo', this.files[0]);
            formData.append('lawyer_id', $(this).data('id'));

            $.ajax({
                url: 'req/update_logo.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            $('#lawyer-logo').attr('src', "../img/lawyers/" + jsonResponse.new_logo);
                        } else {
                            alert('حدث خطأ أثناء تحديث اللوغو.');
                        }
                    } catch (e) {
                        alert('حدث خطأ غير متوقع.');
                    }
                },
                error: function() {
                    alert('حدث خطأ في الاتصال بالخادم.');
                }
            });
        });
    }
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
