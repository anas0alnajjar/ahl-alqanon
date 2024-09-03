<?php
include "DB_connection.php";
include "data/setting.php";
$setting = getSetting($conn);



if ($setting != 0) {

    if ($setting['allow_joining'] == 0) {
        header("Location: index.php");
        exit();
    }

    ?>


    <!DOCTYPE html>
    <html lang="ar">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Welcome to <?= $setting['company_name'] ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/yshstyle.css">
        <link rel="icon" href="img/<?= $setting['logo'] ?>">
        <style>
            body {
            /*   background: linear-gradient(to right, #ff7e5f, #feb47b);*/
            	background-color: #cfccc0;
                color: #272c3f;
                font-family: Arial, sans-serif;
                animation: fadeIn 2s ease-in;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .navbar {
                margin-bottom: 10px;
            }

            .form-w {
                background: #ffffff;
                border-radius: 10px;
                padding: 10px;
                animation: slideIn 1s ease-out;
            }

            .container {
                width: 90%;
                /* العرض الافتراضي على الأجهزة الصغيرة */
                margin: 0 auto;
            }

            /* الأنماط للأجهزة الكبيرة */
            @media (min-width: 768px) {
                .container {
                    width: 50%;
                }
            }

            @keyframes slideIn {
                from {
                    transform: translateY(50px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .btn-primary {
                background-color: #272c3f;
                color: #cfccc0;
                border: none;
            }

            .btn-primary:hover {
                background-color: #cfccc0;
                color: #272c3f;

            }

            .alert {
                animation: bounceIn 1s;
            }

            @keyframes bounceIn {
                0% {
                    transform: scale(0.5);
                    opacity: 0;
                }

                70% {
                    transform: scale(1.2);
                    opacity: 1;
                }

                100% {
                    transform: scale(1);
                }
            }

            .mb-3 {
                direction: rtl;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .spinner {
                display: none;
                /* Hidden by default */
                position: fixed;
                /* Stay in place */
                z-index: 1000;
                /* Sit on top */
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                /* Center the spinner */
                width: 100px;
                /* Set width */
                height: 100px;
                /* Set height */
            }

            .spinner.active {
                display: block;
                /* Show spinner when active */
            }

            .spinner:before {
                content: '';
                display: block;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                border: 5px solid #007bff;
                border-color: #007bff transparent #007bff transparent;
                animation: spin 1.2s linear infinite;
                /* Animation for the spinner */
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>

    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php">
                        <img src="img/<?= $setting['logo'] ?>" width="40" alt="Company Logo">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent" style="direction:rtl;">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">تسجيل الدخول</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="index.php">الرئيسية</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php#about">من نحن</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?showContact=true">اتصل بنا</a>
                            </li>
                        </ul>

                    </div>
                </div>
            </nav>

            <section class="d-flex justify-content-center align-items-center flex-column">
                <form method="post" class="shadow p-3 form-w" id="joinForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" name="manager_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان</label>
                            <input type="text" class="form-control" name="manager_address" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الإيميل</label>
                            <input type="email" class="form-control" name="manager_email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الجنس</label><br>
                            <input type="radio" value="Male" checked name="manager_gender"> ذكر
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="Female" name="manager_gender"> أنثى
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كلمة السر</label>
                            <div class="input-group" style="direction:ltr;">
                                <input type="text" class="form-control" name="manager_password" id="passInput" required>
                                <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" name="manager_city" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الهاتف</label>
                            <input type="tel" id="managerPhone" class="form-control" style="direction:ltr;"
                                name="manager_phone" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="office_name" class="form-label">اسم المكتب</label>
                            <input type="text" class="form-control" name="office_name" id="office_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12 d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary w-100">تسجيل</button>
                        </div>
                    </div>
                </form>
            </section>


            <footer class="text-center text-light mt-4">
        	جميع الحقوق محفوظة &copy;
			<?=$setting['current_year']?>  - <?php echo date("Y");?>

			<?=$setting['company_name']?>.
            </footer>
            <div class="spinner" id="spinner">
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function makePass(length) {
                var result = '';
                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;
                for (var i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                document.getElementById('passInput').value = result;
            }

            document.getElementById('gBtn').addEventListener('click', function (e) {
                e.preventDefault();
                makePass(8);
            });
        </script>
        <script src="https://www.google.com/recaptcha/api.js?render=<?= $setting['site_key'] ?>"></script>
        <script src="js/libraries/jquery-3.6.0.min.js"></script>
        <script src="js/libraries/sweetalert2.min.js"></script>
        <script>
            grecaptcha.ready(function () {
                $('#joinForm').on('submit', function (e) {
                    e.preventDefault();
                    // console.log("Form submitted");
                    $("#spinner").fadeIn();
                    grecaptcha.execute('<?= $setting['site_key'] ?>', { action: 'submit' }).then(function (token) {
                        // console.log("Recaptcha token received:", token);
                        var form = $('#joinForm');
                        $.ajax({
                            type: 'POST',
                            url: 'req/ask-to-add-manager.php',
                            data: form.serialize() + '&g-recaptcha-response=' + token,
                            success: function (response) {
                                // console.log("Response from server:", response);
                                var res = JSON.parse(response);
                                if (res.error) {
                                    $("#spinner").fadeOut();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'خطأ',
                                        text: res.error,
                                    });
                                } else if (res.success) {
                                    $("#spinner").fadeOut();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'نجاح',
                                        text: res.success,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            Swal.fire({
                                                title: 'إدخال رمز التحقق',
                                                input: 'text',
                                                inputAttributes: {
                                                    autocapitalize: 'off'
                                                },
                                                showCancelButton: true,
                                                confirmButtonText: 'تحقق',
                                                showLoaderOnConfirm: true,
                                                preConfirm: (verification_code) => {
                                                    return $.ajax({
                                                        type: 'POST',
                                                        url: 'req/verify-code.php',
                                                        data: {
                                                            manager_email: $('[name="manager_email"]').val(),
                                                            verification_code: verification_code
                                                        },
                                                        success: function (response) {
                                                            // console.log("Verification response from server:", response);
                                                            var res = JSON.parse(response);
                                                            if (res.error) {
                                                                Swal.showValidationMessage(
                                                                    `فشل التحقق: ${res.error}`
                                                                );
                                                            } else {
                                                                var successMessage = res.auto_accept ?
                                                                    'تم التحقق بنجاح. تستطيع تسجيل الدخول والمباشرة في العمل.' :
                                                                    'تم التحقق بنجاح. تمت معالجة طلبك للانضمام بنجاح. سنتواصل معك في أقرب وقت ممكن.';
                                                                Swal.fire({
                                                                    icon: 'success',
                                                                    title: 'نجاح',
                                                                    text: successMessage,
                                                                }).then(() => {
                                                                    form.trigger('reset');
                                                                    if (res.auto_accept) {
                                                                        window.location.href = 'managers/home.php';
                                                                    }
                                                                });
                                                            }
                                                        },
                                                        error: function (xhr, status, error) {
                                                            Swal.showValidationMessage(
                                                                `حدث خطأ أثناء التحقق: ${xhr.responseText}`
                                                            );
                                                        }
                                                    });
                                                },
                                                allowOutsideClick: () => !Swal.isLoading()
                                            });
                                        }
                                    });


                                }
                            },
                            error: function (xhr, status, error) {
                                $("#spinner").fadeOut();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: 'حدث خطأ أثناء إرسال البيانات: ' + xhr.responseText,
                                });
                            }
                        });
                    });
                });
            });
        </script>






    </body>

    </html>
<?php } else {
    header("Location: login.php");
    exit;
} ?>