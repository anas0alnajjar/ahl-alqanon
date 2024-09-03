<?php 

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    // Pagination
    include "../DB_connection.php";
    include "logo.php";
    
    $page_number = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
    $total_records_per_page = 10;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Base SQL query with INNER JOIN to get office name
    $sql = "SELECT adversaries.*, offices.office_name FROM adversaries INNER JOIN offices ON adversaries.office_id = offices.office_id";

    if (empty($search)) {
        // Add ORDER BY clause to the SQL query
        $sql .= " ORDER BY adversaries.id DESC ";
    }

    // Prepare statement with placeholders
    if (!empty($search)) {
        $sql .= " WHERE fname LIKE ? OR lname LIKE ? OR CONCAT(fname, ' ', lname) LIKE ? OR offices.office_name LIKE ?
                    ORDER BY adversaries.id DESC
                    LIMIT " . (int)$offset . ', ' . (int)$total_records_per_page;
        $stmt = $conn->prepare($sql);
        $searchParam = "%$search%";
        $stmt->bindParam(1, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(2, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(3, $searchParam, PDO::PARAM_STR);
        $stmt->bindParam(4, $searchParam, PDO::PARAM_STR);
    } else {
        $sql .= " LIMIT " . (int)$offset . ', ' . (int)$total_records_per_page;
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->fetchAll();

    // Count total records
    if (!empty($search)) {
        $total_records_query = $conn->prepare("SELECT COUNT(*) FROM adversaries INNER JOIN offices ON adversaries.office_id = offices.office_id WHERE fname LIKE ? OR lname LIKE ? OR CONCAT(fname, ' ', lname) LIKE ? OR offices.office_name LIKE ?");
        $total_records_query->bindParam(1, $searchParam, PDO::PARAM_STR);
        $total_records_query->bindParam(2, $searchParam, PDO::PARAM_STR);
        $total_records_query->bindParam(3, $searchParam, PDO::PARAM_STR);
        $total_records_query->bindParam(4, $searchParam, PDO::PARAM_STR);
        $total_records_query->execute();
        $total_records = $total_records_query->fetchColumn();
    } else {
        $total_records = $conn->query("SELECT COUNT(*) FROM adversaries")->fetchColumn();
    }

    $total_pages = ceil($total_records / $total_records_per_page);
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Adversaries</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .modal-body .form-group, .modal-body .form-check {
            margin-bottom: 1rem;
        }
        .modal-body .form-check-inline {
            margin-right: 1rem;
            flex-basis: 45%;
        }

        label{
            cursor: pointer;
        }

    </style>
</head>
<body>


    <?php 
        include "inc/navbar.php";
    ?>

    <?php 
    if (!empty($result) || (empty($result) && !empty($search))) {
    ?>
    <div class="container mt-5" style="direction: rtl;">
    <div class="btn-group" style="direction:ltr;">
    <a href="home.php" class="btn btn-light">الرئيسية</a>
        <a href="adversarie-add.php" class="btn btn-dark">إضافة خصم</a>
    </div>    
        <?php if (isset($_GET['success']) || (empty($result) && !empty($search))) { ?>
            <div class="alert alert-info mt-3 n-table" role="alert">
                <?php 
                if (isset($_GET['success'])) {
                    echo $_GET['success'];
                } else {
                    echo "لم يتم إيجاد ما يطابق بحثك!";
                }
                ?>
            </div>
        <?php } ?>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger mt-3 n-table" role="alert">
                <?=$_GET['error']?>
            </div>
        <?php } ?>

        <div class="table-responsive">
        <form action="adversaries.php" class="mt-3 " method="GET">
        <div class="input-group mb-3 n-table" style="direction: ltr;">
            
            <input type="text" style="direction:rtl;" class="form-control" name="search" placeholder="ابحث هنا..." value="<?php echo htmlentities($search); ?>">
            
            <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            
        </div>
        </form>
            <table class="table table-bordered mt-3 n-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">الاسم</th>
                        <th scope="col">المكتب</th>
                        <th scope="col">الاجراءات</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $i = 1;
                foreach ($result as $row): ?>
                    <tr>
                        <th scope="row"><?=$i?></th>
                        <td>
                            <a style="text-decoration:none;" href="adversarie-view.php?id=<?=$row['id']?>">
                            <?=$row['fname'] . ' ' . $row['lname']?>
                            </a>
                        </td>
                        <td>
                            <?=$row['office_name']?>
                        </td>
                        <td style="text-wrap:nowrap;">
                            <a href="get-adversarie-info.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm m-auto">تعديل</a>
                            <a href="adversarie-delete.php?id=<?=$row['id']?>" class="btn btn-danger m-auto btn-sm">حذف</a>
                        </td>
                    </tr>
                    <?php $i++; // Increment $i for the next row ?> 
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>

<nav aria-label="Page navigation example">
            <ul class="pagination" style="direction: ltr;float:right;">
                <!-- Previous Page -->
                <li class="page-item <?php echo ($page_number <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page_number=<?php echo ($page_number - 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">Previous</a>
                </li>

                <!-- First Page -->
                <?php if ($page_number > 3): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page_number=1<?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">1</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for ($i = max(1, $page_number - 2); $i <= min($total_pages, $page_number + 2); $i++): ?>
                    <li class="page-item <?php echo ($page_number == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page_number=<?php echo $i; ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Last Page -->
                <?php if ($page_number < $total_pages - 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page_number=<?php echo $total_pages; ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>

                <!-- Next Page -->
                <li class="page-item <?php echo ($page_number >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page_number=<?php echo ($page_number + 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">Next</a>
                </li>
            </ul>
        </nav>
        </div>
    </div>
    <?php } else { ?>
        <div class="alert alert-info d-flex align-items-center mt-3" role="alert" style="max-width: 400px; margin-left: auto;margin-right: 2rem;justify-content: space-around;">
            <span>لا يوجد خصوم حتى الآن</span>
            <a href="adversarie-add.php" type="button" style="text-align: right;" class="btn btn-dark">إضافة خصم</a>
        </div>
    <?php } ?>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    
    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(4) a").addClass('active');
        });
    </script>
    <script>
        $(document).ready(function() {
    $('.btn-danger').click(function(e) {
        e.preventDefault(); // لمنع إعادة تحميل الصفحة
        
        // احصل على رابط الزر
        var url = $(this).attr('href');
        
        // استخدم Swal لعرض تنبيه تأكيد
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف الخصم بشكل دائم!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذف!'
        }).then((result) => {
            if (result.isConfirmed) {
                // إذا تأكد المستخدم،  قم بإعادة توجيهه إلى صفحة الحذف
                window.location.href = url;
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
?>
