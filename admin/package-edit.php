<?php
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "logo.php"; ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <title>edit package</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin - show package</title>
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
                <?php
                $package_id = $_GET['id'];
                $sql = 'SELECT * from packages WHERE id = ?';
                $stmt = $conn->prepare($sql);
                $stmt->execute([$package_id]);
                $package = $stmt->fetch(PDO::FETCH_ASSOC);

                if (empty($package)) {
                    echo '<h1> this package is not found';
                } else {

                ?>
                    <div class="container">
                        <form action="req/add_package.php" method="POST">
                            <div class="form-container col-md-12 mx-auto">
                                <h2 class="form-title">تعديل باقة </h2>

                                <!-- الصف الأول: اسم الباقة و وصف الباقة -->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="planName">اسم الباقة</label>
                                        <input type="text" class="form-control" id="planName" name="plan_name" value="<?= $package['name'] ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="planDescription">وصف الباقة</label>
                                        <textarea class="form-control" id="planDescription" name="plan_description" rows="2" required><?= $package['description'] ?></textarea>
                                    </div>
                                </div>

                                <!-- الصف الثاني: فئة الباقة و مدة الباقة -->
                                <div class="row">
                                    <div class="form-group col-md-6">

                                        <label for="planStatus">حالة الباقة</label>
                                        <select class="form-control" id="planStatus" name="plan_status" required>
                                            <option value="">اختر الحالة</option>
                                            <option value="1" <?php if ($package['status'] == 1) echo 'selected' ?>>مفعلة</option>
                                            <option value="0" <?php if ($package['status'] == 0) echo 'selected' ?>>غير مفعلة</option>
                                        </select>


                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="planDuration">مدة الباقة</label>
                                        <select class="form-control" id="planDuration" name="plan_duration" required>
                                            <option value="">اختر مدة الباقة</option>
                                            <option value="1" <?php if ($package['duration'] == 1) echo 'selected' ?>>شهرية</option>
                                            <option value="2" <?php if ($package['durations'] == 2) echo 'selected' ?>>سنوية</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="planPrice">سعر الباقة</label>
                                        <input type="number" class="form-control" id="planPrice" name="plan_price" value="<?= $package['price'] ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="planCategory">فئة الباقة</label>
                                        <select class="form-control" id="planCategory" name="plan_category" required>
                                    <option value="" >اختر الفئة</option>
                                    <option value="1" <?php if ($package['category'] == 1) echo 'selected' ?>>المحامين</option>
                                    <option value="2" <?php if ($package['category'] == 2) echo 'selected' ?>>المكاتب</option>
                                    <option value="3" <?php if ($package['category'] == 3) echo 'selected' ?>>الشركات</option>
                                </select>

                                    </div>
                                </div>

                                <!-- خدمات الباقة -->
                                <div id="servicesSection" >
                                    <h3 class="section-title">خدمات الباقة</h3>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="numCases">عدد القضايا</label>
                                            <input type="number" class="form-control" id="numCases" name="num_cases" value="<?= $package['num_cases'] ?>" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="numClients">عدد الموكلين</label>
                                            <input type="number" class="form-control" id="numClients" name="num_clients" value="<?= $package['num_clients'] ?>" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="numHelpers">عدد الإداريين</label>
                                            <input type="number" class="form-control" id="numHelpers" name="num_helpers" value="<?= $package['num_helpers'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="numMessages">عدد الرسائل</label>
                                            <input type="text" class="form-control" id="numMessages" name="num_messages" value="<?= $package['num_messages'] ?>" reguired>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="numDocuments">عدد الوثائق</label>
                                            <input type="number" class="form-control" id="numDocuments" name="num_documents" value="<?= $package['num_documents'] ?>" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="numTasks">عدد المهام</label>
                                            <input type="number" class="form-control" id="numTasks" name="num_tasks" value="<?= $package['num_tasks'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="numSessions">عدد الجلسات</label>
                                            <input type="number" class="form-control" id="numSessions" name="num_sessions" value="<?= $package['num_sessions'] ?>" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="numEvents">عدد الأحداث</label>
                                            <input type="number" class="form-control" id="numEvents" name="num_events" value="<?= $package['num_events'] ?>" required>
                                        </div>
                                        <?php if ($package['category'] == 2 || $package['category'] == 3) { ?>
                                            <div class="form-group col-md-4">
                                                <label for="numLawyers">عدد المحامين</label>
                                                <input type="number" class="form-control" id="numLawyers" name="num_lawyers" value="<?= $package['num_lawyers'] ?>" disabled>
                                            </div>

                                        <?php }
                                        if ($package['category'] == 3) { ?>
                                            <div class="form-group col-md-4">
                                                <label for="numOffices">عدد المكاتب</label>
                                                <input type="number" class="form-control" id="numOffices" name="num_offices" value="<?= $package['num_offices'] ?>" disabled>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-submit btn-block">تعديل الباقة</button>
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

                }
            } else {
                header("Location: ../login.php");
                exit;
            }
        } else {
            header("Location: ../login.php");
            exit;
        }
?>