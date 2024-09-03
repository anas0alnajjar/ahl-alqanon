<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['id'])) {

    if ($_SESSION['role'] == 'Admins') {
      
       include "../DB_connection.php";
       include "logo.php";

       include 'permissions_script.php';
        if ($pages['assistants']['write'] == 0) {
            header("Location: home.php");
            exit();
        }

       $helper_id = $_GET['id'];
       function getHelperById($id, $conn){
        $sql = "SELECT * FROM helpers
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
    
        if ($stmt->rowCount() == 1) {
        $helper = $stmt->fetch();
        return $helper;
        } else {
        return 0;
        }
    }
       $helper = getHelperById($helper_id, $conn);

       if ($helper == 0) {
         header("Location: helpers.php");
         exit;
       }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Edit Helpers</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <style>
                body {
            direction: rtl;
            background-color: #f8f9fa; /* لون خلفية للصفحة */

        }

        .form-w {
            background: #fff; /* لون خلفية النموذج */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* ظل للنموذج */
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
        }

        @media (min-width: 576px) {
            .form-row {
                display: flex;
                gap: 10px;
            }

            .form-row .mb-3 {
                flex: 1;
                margin: 0;
            }

            .form-row .btn-primary {
                width: auto;
            }
        }
        .iti {
            position: relative;
            display: block;
        }

        .iti__country-list {
            left:0;
        }
    </style>
</head>
<body>
    <?php 
        include "inc/navbar.php";
     ?>
    <div class="container mt-5" style="direction: rtl;">
    <div class="btn-group" style="direction:ltr;">   
    <?php if ($pages['user_management']['read']) : ?>     
            <a href="users.php" class="btn btn-secondary">إدارة المستخدمين</a>  
    <?php endif; ?>
    <?php if ($pages['assistants']['read']) : ?> 
            <a href="helpers.php" class="btn btn-dark">الإداريين</a>
    <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-md-8">
            <form method="post" class="shadow p-3 mt-3 form-w" id="edit_form" action="req/helper-edit.php">
                <h3>تعديل معلومات الإداري</h3>
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger mt-3 n-table" role="alert">
                        <?= $_GET['error'] ?>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success mt-3 n-table" role="alert">
                        <?= $_GET['success'] ?>
                    </div>
                <?php } ?>
                <hr>
                <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
                <div id="success-message" class="alert alert-success d-none" role="alert"></div>
                <div class="mb-3">
                    <label class="form-label">الاسم </label>
                    <input type="text" class="form-control" value="<?= $helper['helper_name'] ?>" name="helper_name">
                </div>
                <div class="mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <div style="min-width:100%;">
                        <input style="direction:ltr;" type="tel" id="lawyerPhone" class="form-control" value="<?= $helper['phone'] ?>" name="phone">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" class="form-control" value="<?= $helper['username'] ?>" name="username" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">الدور</label>
                    <select id="role_id" class="form-select" name="role_id" required>
                    <option value="" disabled selected>اختر الدور</option>
                        <?php
                        $admin_id = $_SESSION['user_id'];

                        // جلب المكاتب المرتبطة بالآدمن
                        $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                        $stmt_offices = $conn->prepare($sql_offices);
                        $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                        $stmt_offices->execute();
                        $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                        if (!empty($offices)) {
                            $office_ids = implode(',', $offices);

                            // جلب الأدوار المرتبطة بالمكاتب
                            $sql = "SELECT DISTINCT p.power_id, p.role
                                    FROM powers p
                                    JOIN offices o ON p.office_id = o.office_id
                                    WHERE o.office_id IN ($office_ids) OR p.default_role_helper = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($stmt->rowCount() > 0) {
                                foreach ($result as $row) {
                                    $role_id = $helper['role_id'];
                                    $id = $row["power_id"];
                                    $role = $row["role"];
                                    $selected = ($id == $role_id) ? "selected" : "";
                                    echo "<option value='$id' $selected>$role</option>";
                                }
                            } else {
                                echo "<option value='' disabled>لا توجد أدوار مرتبطة</option>";
                            }
                        } else {
                            echo "<option value='' disabled>لا توجد مكاتب مرتبطة</option>";
                        }
                        ?>

                    </select>
                </div>
                <div class="mb-3">
                <label class="form-label">المكتب</label>
                <select id="office_id" class="form-select" name="office_id" onchange="fetchLawyers()">
                                <option value="" disabled selected>اختر المكتب</option>
                                    <?php
                                    // جلب المكاتب المرتبطة بالآدمن
                                    $sql = "SELECT `office_id`, `office_name` FROM offices WHERE admin_id = :admin_id";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if ($stmt->rowCount() > 0) {
                                        foreach ($result as $row) {
                                            $id = $row["office_id"];
                                            $office_name = $row["office_name"];
                                            $office_id = $helper['office_id'];
                                            $selected = ($id == $office_id) ? "selected" : "";
                                            echo "<option value='$id' $selected>$office_name</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>لا توجد مكاتب مرتبطة</option>";
                                    }
                                    ?>

                            </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">المحامي</label>
                    <select id="lawyer_id" class="form-select" name="lawyer_id" data-selected-lawyer="<?=$helper['lawyer_id']?>" style="display:none;">
                        <option value="" disabled>اختر المحامي</option>
                        <!-- خيارات المحامين ستُضاف هنا بواسطة JavaScript -->
                    </select>

                </div>
                <div class="mb-3">
                    <label class="form-label">الرقم القومي</label>
                    <input type="text" class="form-control" value="<?= $helper['national_helper'] ?>" name="national_helper">
                </div>
                <div class="mb-3">
                    <label class="form-label">رقم جواز السفر</label>
                    <input type="text" class="form-control" value="<?= $helper['passport_helper'] ?>" name="passport_helper">
                </div>
                <div class="row">
                <div class="col-md-6 mt-5" >
                    <div class="input-group" >
                    <div class="form-check custom-checkbox">
                    <label class="form-label mx-2" for="stop">إيقاف مؤقت</label>    
                    <input style="position: absolute;left: 20px;" type="checkbox" class="form-check-input" id="stop" name="stop" <?php if($helper['stop'] == 1) echo 'checked'; ?> value="1">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">تاريخ الإيقاف</label>
                    <input type="date" class="form-control" value="<?=$helper['stop_date']?>" name="stop_date">
                </div>
                </div>
                <input type="hidden" value="<?= $helper['id'] ?>" name="id">
                <button type="submit" class="btn btn-primary">تحديث</button>
            </form>
        </div>
        <div class="col-md-4">
            <form method="post" class="shadow p-3 my-3 form-w" id="change_password" action="req/helper-change.php">
                <h3>تغيير كلمة السر</h3>
                <?php if (isset($_GET['perror'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_GET['perror'] ?>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['psuccess'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?= $_GET['psuccess'] ?>
                    </div>
                <?php } ?>
                <hr>
                <div class="mb-3">
                    <label class="form-label">كلمة سر الآدمن</label>
                    <div class="input-group mb-3" style="direction: ltr;">
                        <input type="password" class="form-control" name="admin_pass" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">كلمة السر الجديدة</label>
                    <div class="input-group mb-3" style="direction: ltr;">
                        <input type="text" class="form-control" name="new_pass" id="passInput" required>
                        <button class="btn btn-secondary" type="button" id="gBtn">عشوائي</button>
                    </div>
                </div>
                <input type="hidden" value="<?= $helper['id'] ?>" name="helper_id">
                <div class="mb-3">
                    <label class="form-label">تأكيد كلمة السر</label>
                    <input style="direction: ltr;" type="text" class="form-control" name="c_new_pass" id="passInput2" required>
                </div>
                <button type="submit" class="btn btn-primary">تغيير</button>
            </form>
        </div>
    </div>
</div>


     
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        function makePass(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
              result += characters.charAt(Math.floor(Math.random() * 
         charactersLength));

           }
           var passInput = document.getElementById('passInput');
           var passInput2 = document.getElementById('passInput2');
           passInput.value = result;
           passInput2.value = result;
        }

        var gBtn = document.getElementById('gBtn');
        gBtn.addEventListener('click', function(e){
          e.preventDefault();
          makePass(12);
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#lawyerPhone");
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
            <script>
            function fetchLawyers() {
                var officeId = document.getElementById('office_id').value;
                var lawyerSelect = document.getElementById('lawyer_id');
                var selectedLawyerId = lawyerSelect.getAttribute('data-selected-lawyer'); // الحصول على المحامي الحالي المختار
                
                if (officeId) {
                    // قم بعمل طلب AJAX للحصول على المحامين بناءً على office_id
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', 'api/fetch_lawyers.php?office_id=' + officeId, true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var response = JSON.parse(xhr.responseText);
                            lawyerSelect.style.display = 'block';
                            lawyerSelect.innerHTML = '<option value="">اختر المحامي</option>'; // إعادة تعيين الخيارات
                            response.forEach(function(lawyer) {
                                var option = document.createElement('option');
                                option.value = lawyer.lawyer_id;
                                option.textContent = lawyer.lawyer_name;
                                if (lawyer.lawyer_id == selectedLawyerId) {
                                    option.selected = true; // تحديد المحامي الحالي
                                }
                                lawyerSelect.appendChild(option);
                            });
                        }
                    };
                    xhr.send();
                } else {
                    lawyerSelect.style.display = 'none';
                    lawyerSelect.innerHTML = '<option value="" disabled>اختر المحامي</option>';
                }
            }

            // استدعاء الدالة عند تحميل الصفحة إذا كان هناك مكتب محدد
            document.addEventListener('DOMContentLoaded', function() {
                var officeId = document.getElementById('office_id').value;
                if (officeId) {
                    fetchLawyers();
                }
            });
    </script>

</body>
</html>
<?php 

  }else {
    header("Location: helpers.php");
    exit;
  } 
}else {
	header("Location: helpers.php");
	exit;
} 

?>