<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
    // Pagination
    include "../DB_connection.php";
    include "logo.php";

    include 'permissions_script.php';
    if ($pages['assistants']['read'] == 0) {
        header("Location: home.php");
        exit();
    }

    
    include "get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);
    
    $page_number = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
    $total_records_per_page = 10;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    if (!empty($OfficeId)) {

        // Base SQL query
        $sql = "SELECT * FROM helpers WHERE lawyer_id = $user_id";

        if (empty($search)) {
            // Add ORDER BY clause to the SQL query
            $sql .= " ORDER BY id DESC";
        } else {
            // Prepare statement with placeholders
            $sql .= " AND (helper_name LIKE ? OR username LIKE ?)
                      ORDER BY id DESC
                      LIMIT " . (int)$offset . ", " . (int)$total_records_per_page;
            $stmt = $conn->prepare($sql);
            $searchParam = "%$search%";
            $stmt->bindParam(1, $searchParam, PDO::PARAM_STR);
            $stmt->bindParam(2, $searchParam, PDO::PARAM_STR);
        }

        if (empty($search)) {
            $sql .= " LIMIT " . (int)$offset . ", " . (int)$total_records_per_page;
            $stmt = $conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->fetchAll();

        // Count total records
        $total_records_sql = "SELECT COUNT(*) FROM helpers WHERE lawyer_id = $user_id";
        if (!empty($search)) {
            $total_records_sql .= " AND (helper_name LIKE ? OR username LIKE ?)";
            $stmt_count = $conn->prepare($total_records_sql);
            $stmt_count->bindParam(1, $searchParam, PDO::PARAM_STR);
            $stmt_count->bindParam(2, $searchParam, PDO::PARAM_STR);
            $stmt_count->execute();
            $total_records = $stmt_count->fetchColumn();
        } else {
            $total_records = $conn->query($total_records_sql)->fetchColumn();
        }
        $total_pages = ceil($total_records / $total_records_per_page);
    } else {
        // If the admin has no offices, set an empty result
        $result = [];
        $total_records = 0;
        $total_pages = 1;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpers</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
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
       .modal-body {
            max-height: 70vh; /* تحديد أقصى ارتفاع لمحتوى المودال */
            overflow-y: auto; /* إضافة تمرير عند تجاوز المحتوى للارتفاع المحدد */
        }

        .iti {
            position: relative;
            display: block;
        }

        .iti__country-list {
            left:0;
        }
    </style>
</head>
<body>
<!-- Modal -->
<div class="modal fade" id="newHelperModal" tabindex="-1" aria-labelledby="newHelperModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newHelperModalLabel">تفاصيل المساعد</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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




    <?php 
        include "inc/navbar.php";
    ?>

    <?php 
    if (!empty($result) || (empty($result) && !empty($search))) {
    ?>
    <div class="container mt-5" style="direction: rtl;">
 
        
       

        <div class="table-responsive">
          <div class="btn-group mb-3" style="direction:ltr;">
            <a href="home.php" class="btn btn-light">الرئيسية</a>
        <?php if ($pages['assistants']['add']) : ?>            
            <button id="addHelper" class="btn btn-dark">إضافة إداري</button>
        <?php endif; ?>
         </div> 
         
        <form action="helpers.php" class="mt-3 " method="GET">
        <div class="input-group mb-3 n-table" style="direction: ltr;">
            
            <input type="text" style="direction:rtl;" class="form-control" name="search" placeholder="ابحث عن إداري هنا..." value="<?php echo htmlentities($search); ?>">
            
            <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            
        </div>
        </form>
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
            <table class="table table-bordered mt-3 n-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">الاسم</th>
                    <?php if ($pages['assistants']['write'] || $pages['assistants']['delete']) : ?>
                        <th scope="col">الاجراءات</th>
                    <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $i = 1;
                foreach ($result as $row): ?>
                    <tr>
                        <th scope="row"><?=$i?></th>
                        <td>
                            <a style="text-decoration:none;" href="#" data-bs-toggle="modal" data-bs-target="#newHelperModal" data-id="<?=$row['id']?>" class="openNewHelperModal">
                                <?=$row['helper_name']?>
                            </a>

                        </td>
                        <?php if ($pages['assistants']['write'] || $pages['assistants']['delete']) : ?>
                        <td style="text-wrap:nowrap;">
                            <?php if ($pages['assistants']['write']) : ?>
                                <a href="get-helper-info.php?id=<?=$row['id']?>" class="btn btn-warning m-auto btn-sm">تعديل</a>
                            <?php endif; ?>
                            <?php if ($pages['assistants']['delete']) : ?>
                                <a href="helper-delete.php?id=<?=$row['id']?>" class="btn btn-danger btn-sm m-auto delete-button" data-id="<?=$row['id']?>">حذف</a>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
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
    <?php $exist = 1; ?>
    <?php } else { ?>
        <?php $exist = 0; ?>
        <div class="alert alert-info d-flex align-items-center mt-3" role="alert" style="max-width: 400px; margin-left: auto;margin-right: 2rem;justify-content: space-around;">
            <span>لا يوجد إداريين حتى الآن</span>
            <?php if ($pages['assistants']['add']) : ?>   
            <button type="button" style="text-align: right;" class="btn btn-dark" id="addHelper55">إضافة إداري</button>
            <?php endif; ?>
        </div>
    <?php } ?>

    <!-- Helpers Modal -->
<div class="modal fade" id="helperModal" tabindex="-1" aria-labelledby="helperModalLabel" aria-hidden="true" style="text-align:right; direction:rtl;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helperModalLabel">إضافة إداري</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="helperForm" method="POST">    
                    <div class="row">
                        <input type="hidden" name="lawyer_id555" id="lawyer_id555" value="<?=$user_id?>">                        
                        <input type="hidden" value="<?=$OfficeId?>" name="office_id" id="office_id">

                        <div class="form-group col-md-6">
                            <label class="mb-2" for="helper_name">الاسم</label>
                            <input type="text" class="form-control" id="helper_name" name="helper_name" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label" for="role_idHelper">الدور</label>
                            <select id="role_idHelper" class="form-select" name="role_id" required>
                            <option value='' disabled selected>اختر الدور ..</option>  
                            <?php
                                if (!empty($user_id)) {
                                    // إعداد الاستعلام باستخدام الاستعلام المحضر
                                    $sql_roles = "SELECT power_id, role FROM powers WHERE FIND_IN_SET(:user_id, lawyer_id)";
                                    $stmt_roles = $conn->prepare($sql_roles);
                                    // ربط قيمة user_id بالاستعلام
                                    $stmt_roles->bindParam(':user_id', $user_id, PDO::PARAM_STR);
                                    $stmt_roles->execute();
                                    $result2 = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($result2) > 0) {
                                        foreach ($result2 as $row2) {
                                            $id = $row2["power_id"];
                                            $role = $row2["role"];
                                            echo "<option value='$id'>$role</option>\n";
                                        }
                                    } else {
                                        // echo "<option value=''>لا توجد أدوار مرتبطة بك</option>\n";
                                    }
                                } else {
                                    // echo "<option value=''>لا توجد مكاتب مرتبطة بك</option>\n";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="mb-2" for="phoneHelper">الهاتف</label>
                            <input type="tel" class="form-control" id="phoneHelper" name="phone" style="direction:ltr;">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="mb-2" for="usernameHelper">اسم المستخدم</label>
                            <input type="text" class="form-control" id="usernameHelper" name="username" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="mb-2" for="pass">كلمة السر</label>
                            <input type="password" class="form-control" id="pass" name="pass" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="mb-2" for="national_helper">الرقم القومي</label>
                            <input type="text" class="form-control" id="national_helper" name="national_helper" required>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="mb-2" for="passport_helper">رقم جواز السفر</label>
                            <input type="text" class="form-control" id="passport_helper" name="passport_helper" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="colse555" type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                <button type="button" id="saveHelper" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </div>
</div>

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
        <?php if ($exist) : ?>  
        document.getElementById('addHelper').addEventListener('click', function() {
                $('#helperModal').modal('show');
        });
        <?php endif; ?>
        document.getElementById('addHelper55').addEventListener('click', function() {
                $('#helperModal').modal('show');
        });

    </script>


            <script>
        $(document).ready(function(){ 
    $('#colse555').on('click', function(){
        $('#helperModal').modal('hide');
    });
    $('.close').on('click', function(){
        $('#helperModal').modal('hide');
    });

    $('#saveHelper').on('click', function(){
    var userName = $('#usernameHelper').val();
    var helperName = $('#helper_name').val();
    var pass = $('#pass').val();
    var lawyer_id = $('#lawyer_id555').val();
    var role_id = $('#role_idHelper').val();
    if (userName === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد اسم المستخدم',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (pass === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد كلمة السر',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (helperName === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد اسم الإداري',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (lawyer_id === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد المحامي',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (role_id === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد الدور',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }

    $.ajax({
        url: 'req/save_helper.php',
        type: 'POST',
        data: $('#helperForm').serialize(),
        success: function(response){
            var jsonResponse = JSON.parse(response);
            if (jsonResponse.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح',
                    text: jsonResponse.message
                }).then(function(){
                    $('#helperModal').modal('hide');
                    $('#helperForm')[0].reset();
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: jsonResponse.message
                });
            }
        },
        error: function(){
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء حفظ البيانات'
            });
        }
    });
});


});
    </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#phoneHelper");
            if (input) {
                var iti = window.intlTelInput(input, {
                    initialCountry: "auto",
                    geoIpLookup: function(callback) {
                        fetch('https://ipinfo.io/json', { headers: { 'Accept': 'application/json' }})
                            .then(response => response.json())
                            .then(data => callback(data.country))
                            .catch(() => callback("us"));
                    },
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
                });

                input.addEventListener('blur', function() {
                    var phoneNumber = input.value;
                    var regionCode = iti.getSelectedCountryData().iso2;
                    try {
                        var parsedNumber = libphonenumber.parsePhoneNumberFromString(phoneNumber, regionCode.toUpperCase());
                        if (parsedNumber && parsedNumber.isValid()) {
                            input.value = parsedNumber.formatInternational();
                        } else {
                            alert('الرجاء إدخال رقم هاتف صحيح');
                        }
                    } catch (error) {
                        alert('حدث خطأ أثناء معالجة الرقم، الرجاء المحاولة مرة أخرى');
                    }
                });
            }
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

<?php include "inc/footer.php"; ?>  
</body>
</html>

<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>
