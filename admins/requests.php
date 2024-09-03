<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    // Database Connection
    include "../DB_connection.php";
    include 'permissions_script.php';
    if ($pages['join_requests']['read'] == 0) {
        header("Location: home.php");
        exit();
    }
    include "logo.php";
    
    // Pagination & Search
    $page_number = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
    $total_records_per_page = 10;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Base SQL Queries
    $sql = "SELECT * FROM ask_join";
    $count_sql = "SELECT COUNT(*) FROM ask_join";

    // Add search conditions if search is not empty
    if (!empty($search)) {
        $searchParam = "%$search%";
        $whereClause = " WHERE LOWER(first_name) LIKE :search 
                         OR LOWER(last_name) LIKE :search 
                         OR LOWER(address) LIKE :search 
                         OR LOWER(city) LIKE :search 
                         OR LOWER(email) LIKE :search 
                         OR phone LIKE :search";
        $sql .= $whereClause;
        $count_sql .= $whereClause;
    }

    // Add pagination to the main SQL query
    $sql .= " ORDER BY user_id DESC LIMIT :offset, :total_records_per_page";

    // Prepare and Execute Statements
    $stmt = $conn->prepare($sql);
    $count_stmt = $conn->prepare($count_sql);

    // Bind parameters
    if (!empty($search)) {
        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        $count_stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':total_records_per_page', $total_records_per_page, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count_stmt->execute();
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $total_records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Requests</title>
    
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
        body {
            direction: rtl;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table-container {
            margin-top: 20px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            direction: ltr;
        }
        .form-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            direction: ltr;
        }
        
        .modal-content {
            overflow-y: auto;
        }


        .form-group .input-group {
            direction: ltr;
        }
        
    </style>
</head>
<body>
    <?php 
        include "inc/navbar.php";
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center mb-4">طلبات الانضمام</h2>
                <?php if ($pages['join_requests']['add']) : ?>
                 <div class="btn-group mb-3" style="direction:ltr;">
                    <button id="add_user_btn"  class="btn btn-dark ">اضافة طلب</button>
                    <select name="user_type" id="user_type" class="form-control-sm" style="border-left:none;border-radius: 0px 5px 5px 0;">
                        <option value="" selected disabled>اختر نوع المستخدم</option>
                        <option value="2">محامي</option>
                        <option value="1">عميل</option>
                    </select>
                </div>
                <?php endif; ?>
                <div style="direction:rtl !important;">
                    <a href="home.php" class="btn btn-light w-100 mb-3">الرئيسية</a>
                </div>
                <div class="form-container">
                    <form style="min-width: 100%;" action="requests.php" method="GET" class="d-flex justify-content-center">
                        <input style="direction:rtl;" type="text" name="search" class="form-control me-2" placeholder="البحث..." value="<?php echo htmlentities($search); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </form>
                </div>
                <?php if (isset($_GET['success']) || (empty($result) && !empty($search))): ?>
                    <div class="alert alert-info mt-3" role="alert">
                        <?php 
                        if (isset($_GET['success'])) {
                            echo $_GET['success'];
                        }else if (!empty($search)){
                            echo "لم يتم إيجاد ما يطابق بحثك!";
                        }
                        else {
                            echo "لا يوجد طلبات حتى الآن!";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <?=$_GET['error']?>
                    </div>
                <?php endif; ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">الاسم الأول</th>
                                    <th scope="col">الكنية</th>
                                    <th scope="col">البريد الإلكتروني</th>
                                    <th scope="col">مقدم من</th>
                                    <th scope="col">الاجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            if (!empty($result)):
                                $i = 1 + $offset;
                                foreach ($result as $row): ?>
                                    <tr>
                                        <th scope="row"><?=$i?></th>
                                        <td>
                                            <a href="request-view.php?user_id=<?=$row['user_id']?>" style="text-decoration:none;color:#222;">
                                                <?=$row['first_name']?>
                                            </a>
                                        </td>
                                        <td><?=$row['last_name']?></td>
                                        <td><a href="mailto:<?=$row['email']?>" style="text-decoration:none;color:#222;"><?=$row['email']?></a></td>
                                        <td>
                                            <?php 
                                                if ($row['as_a'] == 1) {
                                                    echo "عميل";
                                                } elseif ($row['as_a'] == 2) {
                                                    echo "محامي";
                                                } else {
                                                    echo "Unknown";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                        <?php if ($pages['join_requests']['add']) : ?>
                                            <a href="request-approve.php?user_id=<?=$row['user_id']?>" class="btn btn-success btn-sm">قبول</a>
                                        <?php endif; ?>
                                        <?php if ($pages['join_requests']['delete']) : ?>
                                            <a href="request-reject.php?user_id=<?=$row['user_id']?>" class="btn btn-danger btn-sm">رفض</a>
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <!-- Previous Page -->
                            <li class="page-item <?php echo ($page_number <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="requests.php?page_number=<?php echo ($page_number - 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">السابق</a>
                            </li>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($page_number == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="requests.php?page_number=<?php echo $i; ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next Page -->
                            <li class="page-item <?php echo ($page_number >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="requests.php?page_number=<?php echo ($page_number + 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">التالي</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
            
            <!-- Modal -->
    <div class="modal fade" id="joinModal" tabindex="-1" aria-labelledby="joinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="joinModalLabel">نموذج الانضمام</h5>
                <button style="position: absolute;left: 10px;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="joinForm" method="post">
                    <div id="alert-container"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الأول</label>
                            <input type="text" class="form-control" name="fname">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الأخير</label>
                            <input type="text" class="form-control" name="lname">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="text" class="form-control" name="address">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الإيميل</label>
                            <input type="email" class="form-control" name="email_address">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">سنة الميلاد</label>
                            <input type="date" class="form-control" name="date_of_birth">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الجنس</label><br>
                            <input type="radio" value="Male" name="gender" checked> ذكر
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="Female" name="gender"> أنثى
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" name="username">
                            <input type="hidden" class="form-control" name="as_a" id="as_a" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">كلمة السر</label>
                            <div class="input-group" style="direction:ltr;">
                                <input type="text" class="form-control" name="pass" id="passInput">
                                <button type="button" class="btn btn-secondary" id="gBtn">عشوائي</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الهاتف</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">إرسال</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script>
    document.getElementById('add_user_btn').addEventListener('click', function() {
            const userType = document.getElementById('user_type').value;
            if(userType) {
                document.getElementById('as_a').value = userType;
                   $('#joinModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى اختيار نوع المستخدم الذي ترغب في إضافته.'
                });
            }
        });

        document.getElementById('gBtn').addEventListener('click', function() {
            const passInput = document.getElementById('passInput');
            passInput.value = Math.random().toString(36).slice(-8);
        });
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('joinForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);

        fetch('req/save_join.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // إغلاق المودال
                    var modal = bootstrap.Modal.getInstance(document.getElementById('joinModal'));
                    modal.hide();
                    // إعادة تحديث الصفحة
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.error,
                });
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ غير متوقع',
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