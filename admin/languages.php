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
        <title>languages</title>

        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">
        


        <link rel="stylesheet" href="../css/style.css">


        <link rel="icon" href="../img/<?= $setting['logo'] ?>">


        <style>
            * {
                direction: rtl;
            }

            .dt-buttons.btn-group .btn {
                background-color: #f8f9fa;
                /* لون الخلفية الفاتح */
                color: #343a40;
                /* لون النص */
                border: none;
                /* إزالة حدود الأزرار */
                padding: 5px 10px;
                /* تباعد الداخلي للأزرار */
                border-radius: 5px;
                /* زوايا مدورة */
                margin-top: 2px;
            }

            .dt-buttons.btn-group .btn:hover {
                background-color: #e2e6ea;
                /* تغيير لون الخلفية عند التحويم */
            }

            .dt-buttons.btn-group .btn-custom {
                background-color: #f8f9fa;
                /* لون الخلفية الفاتح */
                color: #343a40;
                /* لون النص */
                border: none;
                /* إزالة حدود الأزرار */
                padding: 5px 10px;
                /* تباعد الداخلي للأزرار */
                border-radius: 5px;
                /* زوايا مدورة */
            }

            .dt-buttons.btn-group .btn-custom:hover {
                background-color: #e2e6ea;
                /* تغيير لون الخلفية عند التحويم */
            }

            svg:not(:root).svg-inline--fa {
                overflow: visible;
                margin-left: 8px !important;
            }


            div.table-responsive>div.dataTables_wrapper>div.row>div[class^="col-"]:last-child {
                padding-right: unset !important;
                max-height: 500px;
                overflow: auto;
                scroll-behavior: smooth;
                scrollbar-width: unset !important;
                scrollbar-color: black !important;
            }

            .dataTables_length {
                margin-bottom: 2%;
            }

            .dataTables_wrapper .dataTables_paginate {
                direction: ltr;
            }

            .table-responsive {
                scrollbar-width: none !important;
            }

            #records_filter {
                display: none !important;
            }

            td,
            th {
                text-align: center;
                border: 1px solid #dddddd;

                padding: 8px;
            }

            tr:nth-child(even) {
                background-color: #dddddd;
            }

            .flag-icon {
                display: inline-block;
                width: 2em;
                height: 1.5em;
                background-size: cover;
            }

            .flag-icon-us {
                background-image: url('../flags/4x3/us.svg');
            }

            .custom-checkbox {
                transform: scale(1.5);
                /* تكبير حجم الـ checkbox بنسبة 50% */
                margin-right: 10px;
                /* المسافة بين الـ checkbox والـ label */
            }
        </style>

    </head>

    <body>
        <?php include "inc/navbar.php"; ?>
        <main class="container mt-5">
            <div class="btn-group mb-3" style="direction:ltr;">
                <a href="home.php" class="btn btn-light">الرئيسية</a>
                <a href="add_language.php" class="btn btn-dark">اضافة لغة جديدة</a>
            </div>
            <?php
            include "../DB_connection.php";
            $sql = "SELECT * FROM languages ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) { ?>
                <h1>لم يتم إضافة أي لغة </h1>
            <?php
            } else {
            ?>

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <!-- Table -->
                                            <div class="table-responsive">
                                                <table id="records" class="display responsive nowrap" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>اسم اللغة</th>
                                                            <th>كود اللغة</th>
                                                            <th>افتراضية</th>
                                                            <th>الاجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        <?php

                                                        foreach ($results as $re) { ?>
                                                            <tr>
                                                                <td>
                                                                    <?= $re['id'] ?>
                                                                </td>
                                                                <td>
                                                                    <?= $re['name'] ?>
                                                                </td>
                                                                <td>
                                                                    <?= $re['code'] ?>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" class="custom-checkbox"  
                                                                        <?php if ($re['is_default'] == 1) echo 'checked'; ?> disabled>
                                                                </td>
                                                                <td>
                                                                    <button id="delete" class="btn btn-danger" onclick="deleteLanguage(<?= $re['id'] ?>)">DELETE</button>
                                                                    <button id="update" class="btn btn-primary" onclick="updateLanguage(<?= $re['id'] ?>)">UPDATE</button>
                                                                    <button id="trans" class="btn btn-secondary" onclick="goToTranslation(<?= $re['id'] ?>)">translations</button>
                                                                </td>


                                                            </tr>
                                                        <?php
                                                        }
                                                        ?>
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
            <?php
            }
            ?>
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
        <script>
            function deleteLanguage(id) {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم حذف هذه اللغة نهائياً!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، احذفها!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // إذا قام المستخدم بالموافقة على الحذف، قم بتوجيهه لصفحة الحذف
                        window.location.href = 'req/language-delete.php?language_id=' + id;
                    }
                })
            }

            function updateLanguage(id) {
                window.location.href = 'language-edit.php?language_id=' + id;

            }

            function goToTranslation(id) {
                window.location.href = 'translations.php?language_id=' + id;
            }
        </script>
    </body>

    </html>
<?php
} else {
    header("location: ../login.php");
    exit;
}
?>