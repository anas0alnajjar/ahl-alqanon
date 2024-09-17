<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "logo.php";

?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin - Add package</title>
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
            <link href="../css/flag-icons.min.css" rel="stylesheet">


            <link rel="stylesheet" href="../css/style.css">


            <link rel="icon" href="../img/<?= $setting['logo'] ?>">


            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
            <style>
                * {
                    direction: rtl;
                }

                .form-container {
                    margin-top: 20px;
                    background-color: #ffffff;
                    padding: 30px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                    border-radius: 8px;
                }

                .form-title {
                    text-align: center;
                    margin-bottom: 20px;
                    color: #333;
                }

                label {
                    font-weight: bold;
                }

                .btn-submit {
                    background-color: #007bff;
                    color: white;
                }

                .btn-submit:hover {
                    background-color: #0056b3;
                }

                input.form-control,
                textarea.form-control,
                select.form-control {
                    background-color: #f9f9f9;
                    /* لون خلفية فاتح */
                    border: 1px solid #ddd;
                    /* إضافة حد فاتح */
                    padding: 10px;
                    /* مسافة داخلية لزيادة المساحة داخل الحقل */
                    border-radius: 5px;
                    /* جعل الزوايا مستديرة */
                    transition: border-color 0.3s ease-in-out;
                    /* إضافة تأثير عند التفاعل مع الحقل */
                }

                /* تغيير لون الحدود عند التركيز على الحقل */
                input.form-control:focus,
                textarea.form-control:focus,
                select.form-control:focus {
                    border-color: #007bff;
                    /* تغيير لون الحد عند التركيز على الحقل */
                    outline: none;
                    /* إزالة الخط المحيط */
                }

                /* تنسيق أزرار الإرسال */
                .btn-submit {
                    background-color: #007bff;
                    /* لون خلفية الزر */
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    transition: background-color 0.3s ease-in-out;
                    /* تأثير تغيير اللون */
                }

                /* تغيير لون الزر عند التحويم */
                .btn-submit:hover {
                    background-color: #0056b3;
                    /* لون داكن عند التحويم */
                }

                .form-group {
                    margin-bottom: 20px;
                    /* تباعد بين كل حقل */
                }

                .btn-submit {
                    transition: background-color 0.3s ease-in-out;
                    /* تأثير التغيير في اللون عند التحويم */
                }

                .btn-submit:hover {
                    background-color: #0056b3;
                }

                .section-title {
                    margin-top: 30px;
                    margin-bottom: 15px;
                    font-size: 1.5rem;
                    color: #007bff;
                }

                .hidden-section {
                    display: none;
                }
            </style>


            <!-- تضمين ملفات SweetAlert2 JavaScript -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        </head>

        <body>
            <?php include "inc/navbar.php"; ?>
            <div class="container-fluid mt-5" style="max-width: 90%;">
                <div class="btn-group" style="direction:ltr;">
                    <a href="home.php" class="btn btn-light">الرئيسية</a>
                    <a href="packages.php" class="btn btn-dark">الرجوع</a>
                </div>
                <div class="container">
                    <form action="req/add_package.php" method="POST">
                    <div class="form-container col-md-12 mx-auto">
                        <h2 class="form-title">إضافة باقة جديدة</h2>

                        <!-- الصف الأول: اسم الباقة و وصف الباقة -->
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="planName">اسم الباقة</label>
                                <input type="text" class="form-control" id="planName" name="plan_name" placeholder="أدخل اسم الباقة" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="planDescription">وصف الباقة</label>
                                <textarea class="form-control" id="planDescription" name="plan_description" rows="2" placeholder="أدخل وصف الباقة" required></textarea>
                            </div>
                        </div>

                        <!-- الصف الثاني: فئة الباقة و مدة الباقة -->
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="planStatus">حالة الباقة</label>
                                <select class="form-control" id="planStatus" name="plan_status" required>
                                    <option value="" >اختر الحالة</option>
                                    <option value="1" >مفعلة</option>
                                    <option value="0">غير مفعلة</option>
                                </select>

                            </div>
                            <div class="form-group col-md-6">
                                <label for="planDuration">مدة الباقة</label>
                                <select class="form-control" id="planDuration" name="plan_duration" required>
                                    <option value="">اختر مدة الباقة</option>
                                    <option value="1">شهرية</option>
                                    <option value="2">سنوية</option>

                                </select>
                            </div>
                        </div>
                        <div class="row">
                        <div class="form-group col-md-6">
                                <label for="planPrice">سعر الباقة</label>
                                <input type="number" class="form-control" id="planPrice" name="plan_price" placeholder="أدخل سعر الباقة" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="planCategory">فئة الباقة</label>
                                <select class="form-control" id="planCategory" name="plan_category" required>
                                    <option value="">اختر الفئة</option>
                                    <option value="1">المحامين</option>
                                    <option value="2">المكاتب</option>
                                    <option value="3">الشركات</option>
                                </select>

                            </div>
                        </div>

                        <!-- خدمات الباقة -->
                        <div id="servicesSection" class="hidden-section">
                            <h3 class="section-title">خدمات الباقة</h3>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="numCases">عدد القضايا</label>
                                    <input type="number" class="form-control" id="numCases" name="num_cases" placeholder="أدخل عدد القضايا">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="numClients">عدد الموكلين</label>
                                    <input type="number" class="form-control" id="numClients" name="num_clients" placeholder="أدخل عدد الموكلين">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="numHelpers">عدد الإداريين</label>
                                    <input type="number" class="form-control" id="numHelpers" name="num_helpers" placeholder="أدخل عدد الإداريين">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="numMessages">عدد الرسائل</label>
                                    <input type="number" class="form-control" id="numMessages" name="num_messages" placeholder="أدخل عدد الرسائل">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="numDocuments">عدد الوثائق</label>
                                    <input type="number" class="form-control" id="numDocuments" name="num_documents" placeholder="أدخل عدد الوثائق">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="numTasks">عدد المهام</label>
                                    <input type="number" class="form-control" id="numTasks" name="num_tasks" placeholder="أدخل عدد المهام">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="numSessions">عدد الجلسات</label>
                                    <input type="number" class="form-control" id="numSessions" name="num_sessions" placeholder="أدخل عدد الجلسات">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="numEvents">عدد الأحداث</label>
                                    <input type="number" class="form-control" id="numEvents" name="num_events" placeholder="أدخل عدد الأحداث">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="numLawyers">عدد المحامين</label>
                                    <input type="number" class="form-control" id="numLawyers" name="num_lawyers" placeholder="أدخل عدد المحامين">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="numOffices">عدد المكاتب</label>
                                    <input type="number" class="form-control" id="numOffices" name="num_offices" placeholder="أدخل عدد الجلسات">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-submit btn-block">إضافة الباقة</button>
                    </div>
                    </form>

                </div>


                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    document.getElementById('planCategory').addEventListener('change', function() {
                        const selectedCategory = this.value;
                        const servicesSection = document.getElementById('servicesSection');

                        if (selectedCategory === "") {
                            servicesSection.classList.add('hidden-section');
                        } else {
                            servicesSection.classList.remove('hidden-section');

                            // إضافة أو إخفاء الحقول حسب نوع الباقة
                            if (selectedCategory === '1') {
                                // على سبيل المثال، يمكن تخصيص حقول معينة للمحامين فقط
                                document.getElementById('numLawyers').parentElement.style.display = 'none';
                                document.getElementById('numOffices').parentElement.style.display = 'none';
                            } else if (selectedCategory === '2') {
                                // يمكن تخصيص حقول معينة للمكاتب فقط
                                document.getElementById('numOffices').parentElement.style.display = 'none';
                                if (document.getElementById('numLawyers').parentElement.style.display = 'none') {
                                    document.getElementById('numLawyers').parentElement.style.display = 'block';

                                }
                            } else if (selectedCategory === '3') {
                                if (document.getElementById('numLawyers').parentElement.style.display = 'none') {
                                    document.getElementById('numLawyers').parentElement.style.display = 'block';
                                    if (document.getElementById('numOffices').parentElement.style.display = 'none') {
                                        document.getElementById('numOffices').parentElement.style.display = 'block'

                                    }
                                }
                            }
                        }
                    });
                </script>
        </body>
<?php

    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>