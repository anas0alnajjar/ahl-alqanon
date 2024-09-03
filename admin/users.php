<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "logo.php";
    ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="إدارة مستخدمين يتضمن الآدمن، المحامين، الإداريين، الموكلين ومديري المكاتب. قم بإدارة وتعديل الصلاحيات والبيانات لكل فئة من هؤلاء المستخدمين بكفاءة وسهولة.">

    <title>Admin - Users</title>
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_cases.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

    <style>
        td, th {
            text-align: center;
        }
        div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:last-child {
            padding-right: unset !important;
            max-height: 1200px;
            overflow: auto;
            scroll-behavior: smooth;
            scrollbar-width: unset !important;
            scrollbar-color: black !important;
        }
        .dataTables_length{
            margin-bottom: 2%;
        }

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

        .form-check-inline {
            margin: 0 10px 10px 0; /* إضافة تباعد بين العناصر */
        }
        .form-check-label {
            margin-right: 5px; /* تباعد بسيط بين المدخل والتسمية */
        }
        .modal-body {
            max-height: 70vh; /* تحديد أقصى ارتفاع لمحتوى المودال */
            overflow-y: auto; /* إضافة تمرير عند تجاوز المحتوى للارتفاع المحدد */
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_paginate{
            direction: ltr;
        }
        .iti {
            position: relative;
            display: block;
        }

        .iti__country-list {
            left:0;
        }
        .table-responsive {
            scrollbar-width: none !important;
        }
        #records_filter {
            display:none !important;
        }
    </style>
    
</head>

<body>

<?php include "inc/navbar.php"; ?>

<main class="container mt-5">
    <div class="btn-group" style="direction:ltr;">
        <button id="add_user_btn"  class="btn btn-dark ">اضافة مستخدم</button>
        <select name="user_type" id="user_type" class="form-control-sm" style="border-left:none;border-radius: 0px 5px 5px 0;">
            <option value="" selected disabled>اختر نوع المستخدم</option>
            <option value="1">محامي</option>
            <option value="2">عميل</option>
            <option value="3">إداري</option>
            <option value="4">مدير مكتب</option>
            <option value="5">آدمن</option>
        </select>
    </div>
    
    <div class="input-group mt-3 text-center" style="max-width: 100%; min-width: 80%; direction: ltr;">
        <input type="search" class="form-control" id="searchInput" placeholder="ابحث هنا..." onkeyup="filterTable()" aria-controls="records">
        <div class="input-group-append">
            <button style="border-radius: 0;" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
        </div>
    </div>
    
    <div class="btn-group w-100 mt-2" style="direction:ltr;">
        <a href="home.php" class="btn btn-light">الرئيسية</a>
    </div>

    <hr>
    <?php if (isset($_GET['success'])) { ?>
        <div class="alert alert-info mt-3 n-table" role="alert" style="max-width:100% !important;">
            <?php echo $_GET['success']; ?>
        </div>
    <?php } ?>
    
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <!-- Table -->
                                <div class="table-responsive" style="padding:10px;">
                                    <table id="records" class="display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>اسم المستخدم</th>
                                                <th>الدور</th>
                                                <th>النوع</th>
                                                <th>الاجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Helpers Modal -->
<div class="modal fade" id="helperModal" tabindex="-1" aria-labelledby="helperModalLabel" aria-hidden="true" style="text-align:right; direction:rtl;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="helperForm" method="POST">    
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label class="mb-2" for="pass">المحامي</label>
                        <select id="lawyer_id555" class="form-select col-md-12" name="lawyer_id555">
                        <option value="">اختر المحامي...</option>

                            <?php
                        $lawyer_id_session = isset($_SESSION['lawyer_id']) ? $_SESSION['lawyer_id'] : '';
                        $sqlLawyers = "SELECT * FROM lawyer ORDER BY lawyer_id";
                        $resultLawyers = $conn->query($sqlLawyers);

                        // فحص إذا كان هناك نتائج
                        if ($resultLawyers->rowCount() > 0) {
                            // عرض الموكلين المرتبطين بالمحامي
                            while ($rowLawyer = $resultLawyers->fetch(PDO::FETCH_ASSOC)) {
                                $lawyer_id54 = $rowLawyer["lawyer_id"];
                                $lawyer_name54 = $rowLawyer["lawyer_name"];
                                $lawyer_email54 = $rowLawyer["lawyer_email"];
                                
                                echo "<option value='$lawyer_id54' data-client-email='$lawyer_email54'>$lawyer_name54</option>";
                            }
                        }
                        ?>
                        </select>
                        </div>
                        
                    <div class="form-group col-md-6">
                        <label class="form-label">المكتب</label>
                            <select id="office_id" class="form-select" name="office_id">
                                <option value="" disabled selected>اختر المكتب</option>
                                <?php
                                    $sqlOffices = "SELECT `office_id`, `office_name` FROM offices";
                                    $resultOffices = $conn->query($sqlOffices);
                                    if ($resultOffices->rowCount() > 0) {
                                        while ($rowOffice = $resultOffices->fetch(PDO::FETCH_ASSOC)) {
                                            $id = $rowOffice["office_id"];
                                            $office_name = $rowOffice["office_name"];
                                            echo "<option value='$id'>$office_name</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="mb-2" for="helper_name">الاسم</label>
                            <input type="text" class="form-control" id="helper_name" name="helper_name" required>
                        </div>
                        <div class="form-group col-md-6">
                        <label class="form-label">الدور</label>
                        <select id="role_idHelper" class="form-select" name="role_id" required>
                            <option value="" disabled selected>اختر الدور</option>
                            <?php
                            $sqlRoles = "SELECT `power_id`, `role`, `default_role_helper` FROM powers";
                            $resultRoles = $conn->query($sqlRoles);
                            if ($resultRoles->rowCount() > 0) {
                                while ($rowRole = $resultRoles->fetch(PDO::FETCH_ASSOC)) {
                                    $id = $rowRole["power_id"];
                                    $role = $rowRole["role"];
                                    $default_role_helper = $rowRole["default_role_helper"];
                                    $selected = ($default_role_helper == 1) ? "selected" : "";
                                    echo "<option value='$id' $selected>$role</option>";
                                }
                            }
                            ?>
                        </select>

                        </div>
                        <div class="form-group col-md-6">
                        <label class="mb-2" for="phone">الهاتف</label>
                            <div style="min-width:100%;">
                                <input style="direction:ltr;" type="tel" id="phoneHelper" class="form-control" name="phone">
                            </div>
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
                <button type="button" class="btn btn-secondary" id="colse555" data-dismiss="modal">إغلاق</button>
                <button id="saveHelper" type="button" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="addUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">إضافة مستخدم جديد</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="position: absolute;left: 10px;"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form id="addUserForm" enctype= "multipart/form-data">
                    <div class="mb-3">
                        <label for="username" class="form-label">اسم المستخدم</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة السر</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">الاسم الأول</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">الاسم الأخير</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminLogo" class="form-label">اللوغو</label>
                        <input type="file" class="form-control" id="adminLogo" name="admin_logo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الدور</label>
                        <select id="role_id" class="form-select" name="role_id" required>
                            <option value="" disabled selected>اختر الدور</option>
                            <?php
                                $sqlPowers = "SELECT `power_id`, `role` FROM powers";
                                $resultPowers = $conn->query($sqlPowers);
                                if ($resultPowers->rowCount() > 0) {
                                    while ($rowPower = $resultPowers->fetch(PDO::FETCH_ASSOC)) {
                                        $id = $rowPower["power_id"];
                                        $role = $rowPower["role"];
                                        echo "<option value='$id'>$role</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div id="case-info" class="tab-pane fade show active mb-3">
                        <div id="accordionMain">
                            <div class="card">
                                <div id="heading1" class="card-header">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse1"> إعدادات الواتساب </button>
                                    </h2>
                                </div>
                                <div id="collapse1" class="collapse show" data-parent="#accordionMain">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="host_whatsapp">استضافة الواتساب</label>
                                                <a href="https://user.ultramsg.com/signup.php?lang=ar" target="_blank"><small>انقر هنا</small></a>
                                                <input style="direction:ltr;" class="form-control" type="text" name="host_whatsapp" id="host_whatsapp" value="" placeholder="https://api.ultramsg.com/Put Yout instanceID Here/messages/chat">
                                            </div>
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="token_whatsapp">توكين الواتساب</label>
                                                <input style="direction:ltr;" class="form-control" type="text" name="token_whatsapp" id="token_whatsapp" value="" placeholder="For example: kp38uy15lk2zncmnjdjqg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="email-info" class="tab-pane fade show active mb-3">
                        <div id="accordionMain">
                            <div class="card">
                                <div id="heading2" class="card-header">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse2"> إعدادات الإيميل </button>
                                    </h2>
                                </div>
                                <div id="collapse2" class="collapse show" data-parent="#accordionMain">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="host_email">استضافة الإيميل</label>
                                                <input style="direction:ltr;" class="form-control" type="text" name="host_email" id="host_email" value="" placeholder="smtp.gmail.com OR smtp.hostinger.com">
                                            </div>
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="username_email">اسم مستخدم الإيميل</label>
                                                <input style="direction:ltr;" class="form-control" type="text" name="username_email" id="username_email" value="" placeholder="For example: anas@gmail.com OR anas@mywebsite.com">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="password_email">كلمة السر</label>
                                                <input style="direction:ltr;" class="form-control" type="password" name="password_email" id="password_email" value="" placeholder="Your passwordApp From google or Your password hostiger Email" autocomplete="none">
                                            </div>
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="port_email">بورت الإرسال</label>
                                                <input style="direction:ltr;" class="form-control" type="number" name="port_email" id="port_email" value="" placeholder="Usually it's 465">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3 mx-5" style="height: 30px; display: none;direction:ltr;" id="progress-bar-container">
                            <div style="direction:ltr;" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" id="progress-bar">0%</div>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- تضمين ملفات JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="../js/script_users.js"></script>

<script>
    $(document).ready(function(){
        $("#navLinks li:nth-child(8) a").addClass('active');
    });
</script>
<script>
    document.getElementById('add_user_btn').addEventListener('click', function() {
        var userType = document.getElementById('user_type').value;
        if (userType == 1) {
            window.location.href = 'lawyer-add.php';
        } else if (userType == 2) {
            window.location.href = 'client-add.php';
        } else if (userType == 3) {
            $('#helperModal').modal('show');
        } else if (userType == 4) {
            window.location.href = 'manager-add.php';
        } else if (userType == 5) {
            $('#addUserModal').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يرجى اختيار نوع المستخدم الذي ترغب في إضافته.'
            });
        }
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

<script>
    $(document).ready(function() {
        $('#addUserForm').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: 'req/add-admin.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            $('#progress-bar-container').show();
                            $('#progress-bar').css('width', percentComplete + '%');
                            $('#progress-bar').text(percentComplete.toFixed(0) + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    try {
                        response = typeof response === 'string' ? JSON.parse(response) : response;
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء تحليل الرد من الخادم.'
                        });
                        return;
                    }

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تمت الإضافة',
                            text: response.message
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء الإضافة'
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

</body>

</html>
<?php 
  } else {
    header("Location: ../login.php");
    exit;
  } 
?>
