<?php 

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    // Pagination
    include "../DB_connection.php";
    include "logo.php";
    
    $page_number = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
    $total_records_per_page = 6;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Base SQL query
    $sql = "SELECT * FROM `managers_office`";

    if (empty($search)) {
        // Add ORDER BY clause to the SQL query
        $sql .= " ORDER BY id DESC ";}

    // Prepare statement with placeholders
    if (!empty($search)) {
        $sql .= " WHERE manager_name LIKE ? OR manager_email LIKE ? OR manager_phone LIKE ? OR manager_address LIKE ? OR 
                    manager_city LIKE ?
                    LIMIT " . (int)$offset . ', ' . (int)$total_records_per_page;
        $stmt = $conn->prepare($sql);
        $searchParam = "%$search%";
        $stmt->bindParam(1, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(2, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(3, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(4, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(5, $searchParam, PDO::PARAM_STR);

    } else {
        $sql .= " LIMIT " . (int)$offset . ', ' . (int)$total_records_per_page;
        $stmt = $conn->prepare($sql);

    }

    $stmt->execute();
    $result = $stmt->fetchAll();
    if (empty($search)) {
        $total_records = $conn->query("SELECT COUNT(*) FROM managers_office")->fetchColumn();
    } else {
        $sql = "SELECT COUNT(*) FROM managers_office WHERE manager_name LIKE ? OR manager_email LIKE ? OR manager_phone LIKE ? OR manager_address LIKE ? OR manager_city LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchParam = "%$search%";
        $stmt->bindParam(1, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(2, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(3, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(4, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(5, $searchParam, PDO::PARAM_STR);
        $stmt->execute();
        $total_records = $stmt->fetchColumn();
    }
    
    // Rest of your code to handle pagination
    // ...

    $total_pages = ceil($total_records / $total_records_per_page);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Managers</title>
    
    
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    
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
        #lawyer_logo_style{
            position: absolute;
            bottom: 0;
            width: 100px;
            max-height: 100px;
            left: 0;
            opacity: 0.7;
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

    <!-- Modal for Manager Details -->
<div class="modal fade" id="ManagerModal" tabindex="-1" aria-labelledby="ManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ManagerModalLabel">تفاصيل المدير</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:70vh;overflow:auto;">
                <!-- سيتم تحميل تفاصيل المدير هنا بواسطة AJAX -->
            </div>
        </div>
    </div>
</div>


<body>
    <?php 
        include "inc/navbar.php";
    ?>

    <?php 
    if (!empty($result) || (empty($result) && !empty($search))) {
    ?>
    <div class="container mt-5" style="direction: rtl;">
    <div class="btn-group mb-3" style="direction:ltr;">
                <a href="home.php" class="btn btn-light">الرئيسية</a>
                <a href="manager-add.php" class="btn btn-dark">إضافة مدير</a>
             </div> 

        
        <form action="managers.php" class="mt-3" method="GET">
            <div class="input-group mb-3" style="max-width:100%;min-width:80%;direction: ltr" >
                <input type="text" style="direction:rtl;" class="form-control" name="search" placeholder="ابحث..." value="<?php echo htmlentities($search); ?>" >
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
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#ManagerModal" data-id="<?=$row['id']?>" class="openManagerModal"><?=$row['manager_name']?></a>
                                </h5>
                                <p class="card-text"><strong>الإيميل:</strong> <?=$row['manager_email']?></p>
                                <p class="card-text"><strong>الهاتف:</strong> <?=$row['manager_phone']?></p>
                                <p class="card-text"><strong>المدينة:</strong> <?=$row['manager_city']?></p>
                                
                                <a href="manager-edit.php?manager_id=<?=$row['id']?>" class="btn btn-warning btn-sm">تعديل</a>
                                <a href="req/manager-delete.php?manager_id=<?=$row['id']?>" class="btn btn-danger btn-sm managers-delete">حذف</a>
                            </div>
                            <div class="col-md-4">
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
        <div class="alert alert-info d-flex align-items-center mt-3 mx-5" role="alert" style="direction: rtl; float: right; max-width: 600px; min-width: 450px;justify-content:space-between;">
            <span>لا يوجد مدراء مكاتب حتى الآن!</span>
            <a href="manager-add.php" class="btn btn-primary">أضف مدير جديد</a>
        </div>
    <?php } ?>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
<script>
$(document).ready(function(){
    $('.openManagerModal').on('click', function(){
        var managerId = $(this).data('id');
        $.ajax({
            url: 'manager-view.php',
            type: 'GET',
            data: { manager_id: managerId },
            success: function(response){
                $('#ManagerModal .modal-body').html(response);
                bindImageUpload();
            }
        });
    });

    function bindImageUpload() {
        // هنا يمكنك إضافة كود تحميل الصور إذا كان مطلوبًا
    }
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.managers-delete').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // منع التحويل التلقائي للرابط
                var url = this.getAttribute('href');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن هذا!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، احذفها!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>
<script>
    $(document).ready(function(){
        $("#navLinks li:nth-child(3) a").addClass('active');
});
</script>

</body>
</html>

<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>
