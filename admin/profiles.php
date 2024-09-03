<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "logo.php";
    ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - Profiles</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_cases.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

<style>
.dt-buttons.btn-group .btn {
    background-color: #f8f9fa; /* لون الخلفية الفاتح */
    color: #343a40; /* لون النص */
    border: none; /* إزالة حدود الأزرار */
    padding: 5px 10px; /* تباعد الداخلي للأزرار */
    border-radius: 5px; /* زوايا مدورة */
    margin-top: 2px;
}

.dt-buttons.btn-group .btn:hover {
    background-color: #e2e6ea; /* تغيير لون الخلفية عند التحويم */
}

.dt-buttons.btn-group .btn-custom {
    background-color: #f8f9fa; /* لون الخلفية الفاتح */
    color: #343a40; /* لون النص */
    border: none; /* إزالة حدود الأزرار */
    padding: 5px 10px; /* تباعد الداخلي للأزرار */
    border-radius: 5px; /* زوايا مدورة */
}

.dt-buttons.btn-group .btn-custom:hover {
    background-color: #e2e6ea; /* تغيير لون الخلفية عند التحويم */
}

.dataTables_wrapper .dataTables_paginate{
            direction: ltr;
        }
        td, th {
            text-align: right;
        }
        div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:last-child {
            padding-right: unset !important;
            max-height: unset !important;
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
        .table-responsive{
            padding:10px;
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
    <div class="btn-group mb-3" style="direction:ltr;">
            <a href="home.php" class="btn btn-light">الرئيسية</a>
            <a href="office_profile.php" class="btn btn-dark">إضافة صفحة تعريفية جديدة</a>
    </div>
    <div class="input-group mb-3 text-center" style="max-width: 100%; min-width: 80%; direction: ltr;">
        <input type="text" class="form-control" id="searchInput" placeholder="ابحث هنا..." onkeyup="filterTable()">
        <div class="input-group-append">
            <button style="border-radius: 0;" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
    </div>
    </div>
    <hr>
    <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-info mt-3 n-table" role="alert">
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
                        <div class="table-responsive" >
                        <table id="records" class="display responsive nowrap" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>المكتب</th>
                <th>المسؤول</th>
                <th>العنوان</th>
                <th>الهاتف</th>
                <th>الإيميل</th>
                <th>وصف مختصر</th>
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

    <script src="../js/script_profiles.js"></script>
    
        

    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(5) a").addClass('active');
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