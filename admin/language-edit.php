<?php

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "logo.php";
        function getlanguageById($id, $conn){
            $sql = 'SELECT * FROM languages WHERE id =?';
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            if ($stmt->rowCount() == 1) {
                $language = $stmt->fetch();
                return $language;
            } else {
                return false;
            }
        }

        if(isset($_GET['language_id'])){
            $language_id = $_GET['language_id'];
            $language = getLanguageById($language_id, $conn);

            if ($language === false) {
                header("Location: languages.php?error1=");
                exit;
            }
        } else {
            header("Location: languages.php?error=");
            exit;
        }

?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin - Add language</title>

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />


            <link rel="stylesheet" href="../css/style.css">
            <link rel="icon" href="../img/<?= $setting['logo'] ?>">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />



            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">


            <!-- تضمين ملفات SweetAlert2 JavaScript -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
            <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

            <style>
                * {
                    direction: rtl;
                }

                .invalid {
                    border: 1px solid #dc3545 !important;
                }
                .error-message{
                    color : red;


                }

                /* تنسيق الجدول للشاشات الصغيرة */
                @media screen and (max-width: 767px) {
                    .table-responsive {
                        overflow-x: auto;
                    }

                    .table-responsive table {
                        width: 100%;
                        max-width: 100%;
                    }

                    .table-responsive .table-bordered th,
                    .table-responsive .table-bordered td {
                        white-space: nowrap;
                    }

                    .table-responsive th,
                    .table-responsive td {
                        padding: 0.5rem;
                        vertical-align: top;
                        border-top: 1px solid #dee2e6;
                    }

                    .table-responsive th {
                        font-weight: bold;
                    }

                    .table-responsive thead th {
                        vertical-align: bottom;
                    }
                }

                .card .form-group {
                    margin-bottom: 1rem;
                }

                .card-body {
                    padding: 1rem;
                }

                .btn-block {
                    width: 100%;
                }

                .bootstrap-datetimepicker-widget {
                    top: 0 !important;
                    bottom: auto !important;
                    right: auto !important;
                    left: 0 !important;
                }

                .data-switch-button {
                    display: none !important;
                }

                .error {
                    border-color: #dc3545 !important;
                    /* Change border color for invalid fields */
                }

                .iti {
                    position: relative;
                    display: block;
                }

                .iti__country-list {
                    left: 0;
                }

                select {
                    max-height: 35px !important;
                    display: none;
                }
            </style>
        </head>

        <body>
            <?php include "inc/navbar.php"; ?>


            <div class="container-fluid mt-5" style="max-width: 90%;">
                <div class="btn-group" style="direction:ltr;">
                    <a href="home.php" class="btn btn-light">الرئيسية</a>
                    <a href="languages.php" class="btn btn-dark">الرجوع</a>
                </div>
                <form id="edit-language-form" class="shadow p-3 mt-5" action="req/update_language.php" enctype="multipart/form-data" method="POST">
                    <h3>تعديل اللغة</h3>
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $_GET['error'] ?>
                        </div>
                    <?php } ?>
                    <?php if (isset($_GET['success'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?= $_GET['success'] ?>
                        </div>
                    <?php } ?>

                    <div class="col-md-6 mb-3">
                        <label for="language-name-id" class="form-label">اسم اللغة</label>
                        <input type="text" class="form-control" name="language_name" id="language-name-id" value="<?=$language['name']?>" >
                        <div id="name-error" class="error-message"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="language-code-id" class="form-label">رمز اللغة</label>
                        <input type="text" class="form-control" name="language_code" id="language-code-id" value="<?=$language['code']?>" >
                        <div id="code-error" class="error-message"></div>
                    </div>
                    <input type="hidden" name="is_default" value="0">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <input type="checkbox" name="is_default" id="is_default_id" 
                            <?php if($language['is_default']== 1) echo 'checked' ?> >
                            <label for="is_default_id" class="form-label mb-0 me-2">تعيين كلغة افتراضية</label>

                        </div>
                    </div>
                    <input type="hidden" name="language_id" value="<?=$language['id']?>">
                    <div class="col-md-6 mb-3">
                        <input type="submit" class="btn btn-primary" value="حفظ">
                    </div>

                </form>
                <script>
    var form = document.getElementById("edit-language-form");
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        document.getElementById("name-error").textContent = '';
        document.getElementById("code-error").textContent = '';
        var language_name = document.getElementById("language-name-id").value; 
        var language_code = document.getElementById("language-code-id").value;
        var haserror = false;
        if (language_name === "") {
            document.getElementById("name-error").textContent = 'يرجى ملء حقل اسم اللغة';
            haserror = true;
        }
        if (language_code === "") {
            document.getElementById("code-error").textContent = 'يرجى ملء حقل رمز اللغة';
            haserror = true;
        }
        if (!haserror) {
            form.submit();
        }
    });
    var checkbox = document.getElementById('is_default_id');
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            this.value = "1"; 
                        } else {
                            this.value = "0"; 
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