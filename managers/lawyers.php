<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Managers') {
    // Pagination
    include "../DB_connection.php";
    include "logo.php";

    include 'permissions_script.php';
    if ($pages['lawyers']['read'] == 0) {
        header("Location: home.php");
        exit();
    }

    include "get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);
    
    $page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
    $total_records_per_page = 6;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';


    if (!empty($OfficeId)) {

        $sql = "SELECT * FROM `lawyer` WHERE office_id IN ($OfficeId)";

        if (!empty($search)) {
            $searchParam = "%$search%";
            $sql .= " AND (lawyer.lawyer_name LIKE :searchParam OR lawyer.lawyer_email LIKE :searchParam OR lawyer.lawyer_phone LIKE :searchParam OR lawyer.lawyer_address LIKE :searchParam OR lawyer.lawyer_city LIKE :searchParam)";
        }

        $sql .= " ORDER BY lawyer_id DESC LIMIT :offset, :total_records_per_page";

        // Prepare statement
        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $stmt->bindParam(':searchParam', $searchParam, PDO::PARAM_STR);
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':total_records_per_page', $total_records_per_page, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetchAll();

        // Count total records
        if (empty($search)) {
            $total_records = $conn->query("SELECT COUNT(*) FROM lawyer WHERE office_id IN ($OfficeId)")->fetchColumn();
        } else {
            $count_sql = "SELECT COUNT(*) FROM lawyer WHERE office_id IN ($OfficeId) AND (lawyer.lawyer_name LIKE :searchParam OR lawyer.lawyer_email LIKE :searchParam OR lawyer.lawyer_phone LIKE :searchParam OR lawyer.lawyer_address LIKE :searchParam OR lawyer.lawyer_city LIKE :searchParam)";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bindParam(':searchParam', $searchParam, PDO::PARAM_STR);
            $count_stmt->execute();
            $total_records = $count_stmt->fetchColumn();
        }

        $total_pages = ceil($total_records / $total_records_per_page);


        // ...

    } 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Lawyers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/yshstyle.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
                body {
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-title a {
            color: #333;
            text-decoration: none;
        }
        .card-title a:hover {
            color: #007bff;
        }
        .btn-link {
            text-decoration: none;
        }
        .pagination {
            justify-content: center;
        }
        #lawyer_logo_style {
            position: absolute;
            bottom: 0;
            width: 70px;
            max-height: 70px;
            left: 0;
            opacity: 0.7;
            padding: 2px;
        }


.card-title {
    margin-bottom: 1rem;
    font-size: 1.5rem;
    color: #333;
    text-align:center;
}
.list-group-item {
    background-color: transparent;
    border: none;
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
    color: #555;
    text-align: right;
}
.card-link {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    color: #fff;
    background-color: #007bff;
    border-radius: 25px;
    text-decoration: none;
    transition: background-color 0.3s;
}
.card-link:hover {
    background-color: #0056b3;
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
    color: #fff;
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
    </style>
</head>
<body>
    
    <!-- Modal -->
<div class="modal fade" id="lawyerModal" tabindex="-1" aria-labelledby="lawyerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="lawyerModalLabel">تفاصيل المحامي</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <?php 
        include "inc/navbar.php";
    ?>

    <?php 
    if (!empty($result) || (empty($result) && !empty($search))) {
    ?>
    <div class="container mt-5" style="direction: rtl;">

         <div class="btn-group mb-3" style="direction:ltr;">
                <a href="home.php" class="btn btn-light">الرئيسية</a>
            <?php if ($pages['lawyers']['add']) : ?>
                <a href="lawyer-add.php" class="btn btn-dark">إضافة محامي</a>
            <?php endif; ?>
             </div> 
        
        <form action="lawyers.php" class="mt-3" method="GET">
        <div class="input-group mb-3" style="max-width:100%;min-width:80%;direction: ltr" >
                <input type="text" style="direction:rtl;" class="form-control" name="search" placeholder=" ابحث عن محامي هنا..." value="<?php echo htmlentities($search); ?>" >
                <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            </div>
        </form>
        
        <?php if (isset($_GET['success']) || (empty($result) && !empty($search))) { ?>
            <div class="alert alert-info" role="alert">
                <?php 
                if (isset($_GET['success'])) {
                    echo $_GET['success'];
                } else {
                    echo "لا يوجد ما يطابق بحثك!";
                }
                ?>
            </div>
        <?php } ?>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?=$_GET['error']?>
            </div>
        <?php } ?>

        <?php if (!empty($result)) { ?>
            <div class="row">
                <?php foreach ($result as $row): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="text-align:right;">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#lawyerModal" data-id="<?=$row['lawyer_id']?>" class="openLawyerModal"><?=$row['lawyer_name']?></a>
                                </h5>
                                <p class="card-text"><strong>الإيميل:</strong> <?=$row['lawyer_email']?></p>
                                <p class="card-text"><strong>الهاتف:</strong> <?=$row['lawyer_phone']?></p>
                                <p class="card-text"><strong>المدينة:</strong> <?=$row['lawyer_city']?></p>
                            <?php if ($pages['lawyers']['add']) : ?>
                                <a href="lawyer-edit.php?lawyer_id=<?=$row['lawyer_id']?>" class="btn btn-warning btn-sm">تعديل</a>
                            <?php endif; ?>
                            <?php if ($pages['lawyers']['add']) : ?>
                                <a href="lawyer-delete.php?lawyer_id=<?=$row['lawyer_id']?>" class="btn btn-danger btn-sm delete-button" data-id="<?=$row['lawyer_id']?>">حذف</a>
                            <?php endif; ?>
                            </div>
                            <?php
                            // الاستعلام لجلب لوغو المكتب
                            $office_logo_query = "SELECT logo FROM profiles WHERE office_id = ?";
                            $office_logo_stmt = $conn->prepare($office_logo_query);
                            $office_logo_stmt->execute([$row['office_id']]);
                            $office_logo_result = $office_logo_stmt->fetch(PDO::FETCH_ASSOC);
                            $office_logo = $office_logo_result ? $office_logo_result['logo'] : null;
                            
                            // الاستعلام لجلب لوغو الآدمن
                            $admin_logo_query = "SELECT logo FROM setting WHERE id = 1";
                            $admin_logo_stmt = $conn->prepare($admin_logo_query);
                            $admin_logo_stmt->execute();
                            $admin_logo_result = $admin_logo_stmt->fetch(PDO::FETCH_ASSOC);
                            $admin_logo = $admin_logo_result ? $admin_logo_result['logo'] : null;
                            
                            // تحديد مسار اللوغو النهائي
                            if (!empty($row['lawyer_logo'])) {
                                $logo_path = "../img/lawyers/" . $row['lawyer_logo'];
                            } elseif (!empty($office_logo)) {
                                $logo_path = "../../profiles_photos/" . $office_logo;
                            } else {
                                $logo_path = "../../img/" . $admin_logo;
                            }
                            ?>
                            
                            <div class="col-md-4">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#lawyerModal" data-id="<?=$row['lawyer_id']?>" class="openLawyerModal">
                                    <img id="lawyer_logo_style" src="<?=$logo_path?>" class="card-img-top" alt="Lawyer Logo">
                                </a>
                            </div>

                        </div>
                    </div>
                    
                <?php endforeach; }?>
                

            <nav aria-label="Page navigation example">
    
                <ul class="pagination" style="direction: ltr;float:right;">
                
                    <!-- Previous Page -->
                    <li class="page-item <?php echo ($page_number <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page_number=<?php echo ($page_number - 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">Previous</a>
                    </li>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page_number == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page_number=<?php echo $i; ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Page -->
                    <li class="page-item <?php echo ($page_number >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page_number=<?php echo ($page_number + 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">Next</a>
                    </li>
                    
                </ul>
                
            </nav>
        </div>
    </div>
    <?php } else { ?>
        <div  class="alert alert-info w-450 d-flex align-items-center justify-content-between mt-3 mx-5" role="alert">
            <span>لا يوجد محامين حتى الآن!</span>
            <a href="lawyer-add.php" class="btn btn-primary">أضف محامي جديد</a>
        </div>
    <?php } ?>

    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    

    <script>
    document.addEventListener("DOMContentLoaded", function() {
    var deleteButtons = document.querySelectorAll(".delete-button");
    deleteButtons.forEach(function(button) {
        button.addEventListener("click", function(event) {
            event.preventDefault(); // لمنع الانتقال الفوري للرابط

            var id = this.getAttribute("data-id");
            var href = this.getAttribute("href");

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لا يمكنك التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذفها!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href; // الانتقال إلى رابط الحذف إذا وافق المستخدم
                }
            });
        });
    });
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
<?php include "inc/footer.php"; ?>  
</body>
</html>

<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>
