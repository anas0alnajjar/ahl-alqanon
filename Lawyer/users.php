<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    include "logo.php";
    include 'permissions_script.php';
    if ($pages['user_management']['read'] == 0) {
        header("Location: home.php");
        exit();
    }

     
    include "get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);
    ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="X-UA-Compatible" content="ie=edge"> -->
    <meta name="description" content="إدارة مستخدمين يتضمن الآدمن، المحامين، الإداريين، الموكلين ومديري المكاتب. قم بإدارة وتعديل الصلاحيات والبيانات لكل فئة من هؤلاء المستخدمين بكفاءة وسهولة.">

    <title>Users</title>
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
                    <input type="hidden" value="<?=$user_id?>" name="lawyer_id555" id="lawyer_id555">
                        <input type="hidden" value="<?=$OfficeId?>" name="office_id" id="office_id">
                        <div class="form-group col-md-6">
                            <label class="mb-2" for="helper_name">الاسم</label>
                            <input type="text" class="form-control" id="helper_name" name="helper_name" required>
                        </div>
                        <div class="form-group col-md-6">
                        <label class="form-label">الدور</label>
                        <select id="role_idHelper" class="form-select" name="role_id" required>
                        <option value="" disabled selected>اختر الدور</option>
                        <?php
                        if (!empty($user_id)) {
                            // إعداد الاستعلام باستخدام الاستعلام المحضر
                            $sql_roles = "SELECT power_id, role, default_role_helper FROM powers WHERE FIND_IN_SET(:user_id, lawyer_id) OR default_role_helper = 1";
                            $stmt_roles = $conn->prepare($sql_roles);
                            // ربط قيمة user_id بالاستعلام
                            $stmt_roles->bindParam(':user_id', $user_id, PDO::PARAM_STR);
                            $stmt_roles->execute();
                            $result2 = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

                            if (count($result2) > 0) {
                                foreach ($result2 as $row2) {
                                    $id = $row2["power_id"];
                                    $role = $row2["role"];
                                    $default = $row2["default_role_helper"];
                                    $selected = ($default == 1) ? "selected" : "";
                                    echo "<option value='$id' $selected>$role</option>\n";
                                }
                            } else {
                                echo "<option value='' disabled>لا توجد أدوار مرتبطة بك</option>\n";
                            }
                        } else {
                            echo "<option value='' disabled>لا توجد مكاتب مرتبطة بك</option>\n";
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



    <?php include "inc/navbar.php"; ?>
    <main class="container mt-5">
    <?php if ($pages['user_management']['add']) : ?>
        <div class="btn-group" style="direction:ltr;">
        <button id="add_user_btn"  class="btn btn-dark ">اضافة مستخدم</button>
        <select name="user_type" id="user_type" class="form-control-sm" style="border-left:none;border-radius: 0px 5px 5px 0;">
            <option value="" selected disabled>اختر نوع المستخدم</option>
            <?php if ($pages['clients']['add']) : ?>
                <option value="2">موكل</option>
            <?php endif; ?>
            <?php if ($pages['assistants']['add']) : ?>
                <option value="3">إداري</option>
            <?php endif; ?>
        </select>
        
        </div>
    <?php endif; ?>
    
        <div class="input-group mt-3 text-center" style="max-width: 100%; min-width: 80%; direction: ltr;">
            <input type="search" class="form-control" id="searchInput" placeholder="ابحث هنا..." onkeyup="filterTable() aria-controls="records"">
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
                <?php 
                if (isset($_GET['success'])) {
                    echo $_GET['success'];
                } 
                ?>
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

    <script>var pages = <?php echo $permissions_json; ?>;</script>
    <script src="../js/script_users_for_users.js"></script>
    
    

    <script>
        document.getElementById('add_user_btn').addEventListener('click', function() {
            var userType = document.getElementById('user_type').value;
            if (userType == 2) {
                window.location.href = 'client-add.php';
            } else if (userType == 3) {
                $('#helperModal').modal('show');
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
    var phoneHelper = $('#phoneHelper').val();
    var role_id = $('#role_idHelper').val();

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
    if (phoneHelper === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد هاتف الإداري',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
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


</body>

</html>
<?php 
  } else {
    header("Location: ../login.php");
    exit;
  } 

?>