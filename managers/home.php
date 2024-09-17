<?php 
session_start();

if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {
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

        // الحصول على office_id الخاص بالمدير
        $sql_office = "SELECT office_id FROM managers_office WHERE id = :manager_id";
        $stmt_office = $conn->prepare($sql_office);
        $stmt_office->bindParam(':manager_id', $user_id);
        $stmt_office->execute();
        $office_id = $stmt_office->fetchColumn();

        // التحقق من أن office_id تم العثور عليه
        if ($office_id) {
            foreach ($tables as $table => $data) {
                if (in_array($table, array('cases', 'clients', 'lawyer', 'documents'))) {
                    $sql = "SELECT COUNT(*) AS count FROM $table WHERE office_id = :office_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':office_id', $office_id);
                } elseif ($table == 'todos') {
                    // استعلام متقدم لجلب المهام المرتبطة بمكتب المدير
                    $sql = "SELECT COUNT(*) AS count 
                            FROM todos 
                            WHERE read_by_manager != 1
                            AND (
                                client_id IN (SELECT client_id FROM clients WHERE office_id = :office_id) 
                                OR lawyer_id IN (SELECT lawyer_id FROM lawyer WHERE office_id = :office_id) 
                                OR case_id IN (SELECT case_id FROM cases WHERE office_id = :office_id) 
                                OR helper_id IN (SELECT id FROM helpers WHERE office_id = :office_id)
                            )";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':office_id', $office_id);
                } else {
                    $sql = "SELECT COUNT(*) AS count FROM $table";
                    $stmt = $conn->prepare($sql);
                }

                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $count = $row['count'];

                // تخزين قيمة الصفوف في المصفوفة باسم الجدول
                $tableCounts[$table] = $count;
            }

        } else {
            echo "No office found for this manager.";
        }


        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);
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
    <link rel="stylesheet" href="../css/yshstyle.css">
    <style>
body {
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
    background: #272c3f;

}

.card:hover {

    background-color: #cfccc0;

}


.card-body {
    text-align: center;
    padding: 20px;
}

.card-title {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #cfccc0;

}
.card-title:hover{
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #272c3f !important;    
}

.card-icon {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #cfccc0;
}
.card-icon:hover {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #272c3f;    

}
.badge {
    background-color: #272c3f;
    border-radius: 50%;
    padding: 5px 10px;
    position: absolute;
    top: 10px;
    right: 10px;
    color: #cfccc0;
    font-size: 0.8rem;
}
.badge:hover {
    background-color: #cfccc0;
    border-radius: 50%;
    padding: 5px 10px;
    position: absolute;
    top: 10px;
    right: 10px;
    color: #272c3f;
    font-size: 0.8rem;
}
a {
    color: unset;
    text-decoration: none;
}

/* أنيميشن للألوان */
@keyframes colorChange {
    0% { background-position: 0% 30%; }
    100% { background-position: 30% 20%; }
}

.card:hover {
  /*    background: linear-gradient(230deg, #fd7921, #fbb03b, #3db9fc, #8e44ad, #fd7921); */
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
    color: #cfccc0; /* color: #ff0000; تغيير لون النص إلى الأبض */
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
        <div class="col">
            <div class="card">
            <a id="office-info" href="#">
                <div class="card-body">
                    <i class="fa fa-building  card-icon"></i>
                    <h5 class="card-title"><?= __('office') ?></h5>
                </div>
                </a>
            </div>
        </div>
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
            <a href="manager-profile.php?manager_id=<?=$user_id?>">  
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

    <!-- Edit Modal -->
    <div style="direction:rtl;" class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="max-height:90vh;overflow:auto;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">إعدادت الطباعة/اسم المكتب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;left: 5px;"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="officeInput" class="form-label">اسم المكتب</label>
                            <input type="text" class="form-control" id="officeInput" name="office_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="header_image_edit" class="form-label">الهيدر</label>
                            <input type="file" class="form-control" id="header_image_edit" name="header_image" accept="image/*">
                            <img id="header_image_preview_edit" src="#" alt="Header Image" style="display:none; width: 100%; margin-top: 10px;">
                        </div>
                        <div class="mb-3">
                            <label for="footer_text_edit" class="form-label">الفوتر</label>
                            <textarea name="footer_text" id="footer_text_edit" class="form-control"></textarea>
                        </div>
                        <input type="hidden" id="officeId" name="id">
                        <button type="submit" class="btn btn-primary">تحديث</button>
                    </form>
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
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("office-info").addEventListener("click", function(event) {
                event.preventDefault();
                
                // استخدام AJAX لجلب البيانات من الخادم
                $.ajax({
                    url: 'get_office_data.php',
                    type: 'GET',
                    data: { id: <?php echo $OfficeId; ?> },
                    success: function(data) {
                        var officeData = JSON.parse(data);

                        // تعبئة البيانات في الحقول المناسبة
                        document.getElementById("officeId").value = officeData.id;
                        document.getElementById("officeInput").value = officeData.office_name;
                        if (officeData.header_image && officeData.header_image !== "null") {
                            document.getElementById("header_image_preview_edit").src = "../../uploads/" + officeData.header_image;
                            document.getElementById("header_image_preview_edit").style.display = 'block';
                        } else {
                            document.getElementById("header_image_preview_edit").style.display = 'none';
                            document.getElementById("header_image_preview_edit").src = "#";
                        }

                        document.getElementById("footer_text_edit").value = officeData.footer_text;

                        var editModal = new bootstrap.Modal(document.getElementById("editModal"));
                        editModal.show();
                    }
                });
            });

            document.getElementById('header_image_edit').addEventListener('change', function() {
                const [file] = this.files;
                if (file) {
                    document.getElementById('header_image_preview_edit').src = URL.createObjectURL(file);
                    document.getElementById('header_image_preview_edit').style.display = 'block';
                }
            });

            // تحديث البيانات باستخدام AJAX
            $('#editForm').on('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: 'req/office-edit.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم التحديث',
                            text: 'تم تحديث بيانات المكتب بنجاح',
                            confirmButtonText: 'موافق'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#editModal').modal('hide');
                                // تحديث البيانات على الصفحة إذا لزم الأمر
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء التحديث',
                            confirmButtonText: 'موافق'
                        });
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