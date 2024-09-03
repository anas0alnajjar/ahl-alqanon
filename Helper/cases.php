<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
    include "logo.php";
    include 'permissions_script.php';
    if ($pages['cases']['read'] == 0) {
        header("Location: home.php");
        exit();
    }

    ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CASES</title>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    
    <link rel="stylesheet" href="../css/style_cases.css">
    <link rel="stylesheet" href="../css/style.css">
    

    <link rel="icon" href="../img/<?=$setting['logo']?>">
        
        
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
svg:not(:root).svg-inline--fa {
    overflow: visible;
    margin-left: 8px !important;
}

th {
            text-align: center;
        }
        div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:last-child {
            padding-right: unset !important;
            max-height: 500px;
            overflow: auto;
            scroll-behavior: smooth;
            scrollbar-width: unset !important;
            scrollbar-color: black !important;
        }
        .dataTables_length{
            margin-bottom: 2%;
        }

        .dataTables_wrapper .dataTables_paginate{
            direction: ltr;
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
    <?php include "inc/footer.php"; ?>    
    <?php include "inc/navbar.php"; ?>
    <main class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr;">
            <a href="home.php" class="btn btn-light">الرئيسية</a>
            <a href="add_case.php" class="btn btn-dark cases-add">اضافة قضية جديدة</a>
        </div>
      <div class="input-group mb-3 text-center" style="max-width: 100%; min-width: 80%; direction: ltr;">
        <input type="text" class="form-control" id="searchInput" placeholder="ابحث هنا..." onkeyup="filterTable()">
        <div class="input-group-append">
            <button style="border-radius: 0;" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
        </div>
    </div>
    <hr>
        <div class="row justify-content-center">
            <div class="col-md-12" style="padding:0;">
            <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <!-- Table -->
                        <div class="table-responsive" style="padding:10px;">
                            <table class="table table-borderless display nowrap" id="records" style="width:100%">
                                <thead style=""> 
                                    <tr style="">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
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

    <script src="../js/script_cases_users.js"></script>
    




</body>

</html>
<?php 
  } else {
    header("Location: ../login.php");
    exit;
  } 

?>