<?php
include "DB_connection.php";
include "data/setting.php";
include "fetch-packages.php";
$setting = getSetting($conn);


if ($setting != 0) {
    if (isset($_POST['ask_join']) && $_POST['ask_join'] == 1) {
        header("Location: ask_join.php?as_a=1");
        exit;
    } else if (isset($_POST['ask_join']) && $_POST['ask_join'] == 2) {
        header("Location: ask_join.php?as_a=2");
        exit;
    } else if (isset($_POST['ask_join']) && $_POST['ask_join'] == 3) {
        header("Location: join.php");
        exit;
    }

    $showContact = isset($_GET['showContact']) && $_GET['showContact'] == 'true' ? true : false;

?>

    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Subscription Plans</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">

        <style>
            * {
                direction: rtl;
            }

            body {
                background-color: #cfccc0;
                /* اللون الخلفي المطلوب */
            }

            /* Navbar Styles */
            .navbar {
                background-color: #000;
                /* لون الـ navbar أسود */
                width: 80%;
                margin-left: 150px;
                margin-right: 40px;



            }

            .navbar-brand,
            .navbar-nav .nav-link {
                color: white;
            }

            .navbar-brand:hover,
            .navbar-nav .nav-link:hover {
                color: #007bff;
            }

            .navbar-brand img {
                animation: spin2 5s infinite linear;
            }

            .navbar-nav .nav-link {
                color: #cfccc0 !important;
                transition: color 0.3s;
            }

            .navbar-nav .nav-link:hover {
                color: #272c3f !important;
            }

            /* Header Styles */
            .custom-header {
                position: relative;
                width: 100%;
                height: 250px;
                /* ارتفاع الـ header */
                background-color: #333;
                /* لون داكن للـ header */
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
            }

            .custom-header h1 {
                font-size: 3rem;
                text-transform: uppercase;
                letter-spacing: 2px;
            }

            /* التثبيت للشاشات الكبيرة */
            @media (min-width: 992px) {
                .custom-header {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    z-index: 1000;
                    /* اجعل الـ header فوق باقي المحتويات */
                }

                #lawyers,
                #offices,
                #companies {
                    scroll-margin-top: 250px;
                    /* مسافة تعادل ارتفاع الـ header */
                }

                /* لتعويض المساحة التي يشغلها الـ header الثابت */
                .main-content {
                    padding-top: 250px;
                    /* نفس ارتفاع الـ header */
                }
            }



            /* Category Section and Content Styles */
            .category-section {
                margin-top: 40px;
            }

            .category-title {
                margin-bottom: 30px;
                text-align: center;
                font-weight: bold;
                font-size: 1.8rem;
                color: #333;
                text-transform: uppercase;
                letter-spacing: 2px;
                padding-bottom: 10px;
                margin: 0 auto;
                /* border-bottom: 2px solid #007bff; /* الخط السفلي */
                width: 50%;
                transition: transform 0.3s ease-in-out, border-bottom 0.3s ease-in-out;
            }

            .category-title:hover {
                transform: scale(1.1);

            }

            .services {
                display: flex;
                flex-direction: column;
                align-items: center;

            }

           

            .services-row {
                /* display: flex;*/
                /* justify-content: center;*/


            }

            .services-row-item {
                min-width: 50%;


            }

            .card {
                background-color: #2c2c2c;
                /* لون البطاقة داكن */
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                margin-bottom: 10px;
            }

            .card:hover {
                transform: translateY(-10px);
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            }

            .card-header {
                background-color: #4a4a4a;
                /* اللون المقترح للـ header */
                color: white;
                font-weight: bold;
                text-align: center;
                padding: 15px;
                border-radius: 12px 12px 0 0;
                transition: transform 0.3s ease-in-out;
            }

            .card-header:hover {
                transform: scale(1.05);
            }

            .card-body {
                text-align: center;
                padding: 25px;
            }

            .card-title {
                font-size: 2rem;
                color: #fff;
                /* لون النص أبيض */
                margin-bottom: 20px;
                transition: transform 0.3s ease-in-out;
            }

            .card-title:hover {
                transform: scale(1.1);
            }

            .card-text {
                font-size: 1.1rem;
                color: #dcdcdc;
                /* لون نص فاتح */
                margin-bottom: 20px;
            }
            .card-text-des {
                font-size: 1.1rem;
                color: #dcdcdc;
                /* لون نص فاتح */
                margin-bottom: 20px;
            }

            .btn-primary {
                background-color: #007bff;
                border: none;
                padding: 10px 20px;
                font-size: 1rem;
                border-radius: 30px;
                transition: background-color 0.3s ease, box-shadow 0.3s ease;
                color: white;
            }

            .btn-primary:hover {
                background-color: transparent;
                border: 2px solid #007bff;
                color: #007bff;
            }

            .main-title {
                text-align: center;
                font-size: 3rem;
                color: #333;
                font-weight: bold;
                margin-bottom: 50px;
                text-transform: uppercase;
                letter-spacing: 3px;
                position: relative;
                transition: transform 0.3s ease-in-out;
            }

            .main-title:hover {
                transform: scale(1.1);
            }

            .main-title::after {
                content: '';
                position: absolute;
                width: 100px;
                height: 4px;
                background-color: #007bff;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
            }

            .body-header {

                background: url(../img/bg.jpg);
                background-size: cover;
                background-attachment: fixed;

            }

            /*span {
                margin-left: 10px;
            }*/

            .fas.fa-star {
                color: #007bff;
                margin-left: 5px;
            }
        </style>
    </head>

    <body>


        <!-- Custom Header -->
        <header class="custom-header body-header">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="homeNav" style="direction: rtl;">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">
                        <img src="img/<?= $setting['logo'] ?>" width="40">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent" style="direction:rtl;">
                        <ul class="navbar-nav me-right mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">تسجيل الدخول</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">الرئيسية</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php#about">من نحن</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    العروص و الأسعار
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="#lawyers">
                                            باقات المحامين
                                        </a></li>
                                    <li><a class="dropdown-item" href="#offices">
                                            باقات المكاتب
                                        </a></li>
                                    <li><a class="dropdown-item" href="#companies">
                                            باقات الشركات
                                        </a></li>
                                </ul>
                            </li>
                            <?php if ($setting['allow_joining']) : ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        اطلب الانضمام
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="join.php">
                                                انضم إلينا للمكاتب
                                            </a></li>
                                        <li><a class="dropdown-item" href="ask_join.php?as_a=2">
                                                انضم إلينا للمحامين
                                            </a></li>
                                        <!-- <li><a class="dropdown-item" href="ask_join.php?as_a=1">
                  انضم الينا للموكلين
              </a></li>-->
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>

        </header>

        <!-- Main Content -->

        <div class="container mt-5 main-content">
            <!-- Main Title -->
            <h1 class="main-title">اختر باقتك و ابدأ معنا</h1>

            <!-- Section for Lawyers -->
            <div class="category-section ">
                <div id="lawyers" class="category-title">باقات المحامين</div>
                <?php
                $lawyerPackages = get_packages_by_category($conn, 1);
                if (empty($lawyerPackages)) {
                ?>
                    <h3>لم يتم إضافة باقات محامين </h3>

                <?php } else { ?>


                    <div class="row justify-content-center">
                        <?php foreach ($lawyerPackages as $package) { ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header"><?= $package['name'] ?></div>
                                    <div class="card-body">
                                        <h3 class="card-title">$<?= $package['price'] ?> /<?= $package['duration'] == 1 ? 'شهر' : 'سنة' ?></h3>
                                        <p class="card-text-des" style="max-height: 35px;"><?= $package['description'] ?></p>
                                        <div class="services" style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_cases'] ?> قضية</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_clients'] ?> موكل</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_helpers'] ?> مساعد</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_documents'] ?> وثيقة</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_messages'] ?> رسالة</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_tasks'] ?> مهمة</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_sessions'] ?> جلسة</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_events'] ?> حدث</span>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary">Select</button>
                                    </div>
                                </div>

                            </div>
                    <?php }
                    } ?>

                    </div>
            </div>

            <!-- Section for Offices -->
            <div class="category-section">
                <div id="offices" class="category-title">باقات المكاتب</div>
                <?php
                $officePackages = get_packages_by_category($conn, 2);
                if (empty($officePackages)) {
                ?>
                    <h3>لم يتم إضافة باقات مكاتب </h3>

                <?php } else { ?>
                    <div class="row justify-content-center">
                        <?php foreach ($officePackages as $package) { ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header"><?= $package['name'] ?></div>
                                    <div class="card-body">
                                        <h3 class="card-title">$<?= $package['price'] ?> /<?= $package['duration'] == 1 ? 'شهر' : 'سنة' ?></h3>
                                        <p class="card-text-des"><?= $package['description'] ?></p>
                                        <div class="services" style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_cases'] ?> قضية</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_clients'] ?> موكل</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_helpers'] ?> مساعد</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_documents'] ?> وثيقة</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_messages'] ?> رسالة</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_tasks'] ?> مهمة</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_sessions'] ?> جلسة</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_events'] ?> حدث</span>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary">Select</button>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>

                    </div>

            </div>


            <!-- Section for Companies -->
            <div class="category-section">
                <div id="companies" class="category-title">باقات الشركات</div>
                <?php
                $officePackages = get_packages_by_category($conn, 3);
                if (empty($officePackages)) {
                ?>
                    <h3>لم يتم إضافة باقات شركات </h3>

                <?php } else { ?>
                    <div class="row justify-content-center">
                        <?php foreach ($officePackages as $package) { ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header"><?= $package['name'] ?></div>
                                    <div class="card-body">
                                        <h3 class="card-title">$<?= $package['price'] ?> /<?= $package['duration'] == 1 ? 'شهر' : 'سنة' ?></h3>
                                        <p class="card-text-des"><?= $package['description'] ?></p>
                                        <div class="services" style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                        <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_offices'] ?> مكتب</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_lawyers'] ?> محامي</span>
                                            </div>    
                                        <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_cases'] ?> قضية</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_clients'] ?> موكل</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_helpers'] ?> مساعد</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_documents'] ?> وثيقة</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_messages'] ?> رسالة</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_tasks'] ?> مهمة</span>
                                            </div>
                                            <div class="services-row" style="display: flex; justify-content: space-between; width: 100%;">
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_sessions'] ?> جلسة</span>
                                                <span class="card-text" style="flex: 1;"><i class="fas fa-star"></i> <?= $package['num_events'] ?> حدث</span>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary">Select</button>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>

                    </div>

            </div>
        </div>
        </div>





        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php } ?>