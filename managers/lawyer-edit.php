<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['lawyer_id'])) {

    if ($_SESSION['role'] == 'Managers') {
      
       include "../DB_connection.php";
       include "logo.php";

       include "data/lawyers.php";

       include 'permissions_script.php';
        if ($pages['lawyers']['write'] == 0) {
            header("Location: home.php");
            exit();
        }

        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);
    
       $lawyer_id = $_GET['lawyer_id'];
       $lawyer = getLawyerById($lawyer_id, $conn);

       if ($lawyer == 0) {
         header("Location: lawyers.php");
         exit;
       }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Edit lawyer</title>
	
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
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
        .custom-checkbox .form-check-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            margin: 0;
            padding: 0;
            border: 2px solid #007bff; /* لون الحدود */
            border-radius: 5px;
            position: relative;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .custom-checkbox .form-check-input:checked {
            background-color: #007bff; /* لون الخلفية عند التحديد */
            border-color: #007bff; /* لون الحدود عند التحديد */
        }

        .custom-checkbox .form-check-input:checked::after {
            content: '\2714'; /* علامة الصح البيضاء */
            font-size: 16px;
            color: white; /* لون علامة الصح */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-checkbox .form-check-label {
            margin-right: 5px; /* تعديل المسافة بين النص والعلامة */
            cursor: pointer;
            display: inline-block;
            vertical-align: middle; /* لمحاذاة النص بشكل أفضل مع الـ checkbox */
            line-height: 1.5; /* تعديل طول السطر */
        }
        .form-label{
            cursor: pointer;
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
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr;">    
        <?php if ($pages['user_management']['read']) : ?>    
            <a href="users.php" class="btn btn-secondary">إدارة المستخدمين </a>  
        <?php endif; ?>
            <a href="lawyers.php" class="btn btn-dark">المحامين</a>
        </div>
        <div class="row">
            <div class="col-md-8">
            <form method="post" class="shadow p-3" action="req/lawyer-edit.php">
                    <h3>تعديل معلومات المحامي</h3><hr>
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?=$_GET['error']?>
                        </div>
                    <?php } ?>
                    <?php if (isset($_GET['success'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?=$_GET['success']?>
                        </div>
                    <?php } ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" value="<?=$lawyer['lawyer_name']?>" name="fname">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان</label>
                            <input type="text" class="form-control" value="<?=$lawyer['lawyer_address']?>" name="address">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الايميل</label>
                            <input type="email" class="form-control" value="<?=$lawyer['lawyer_email']?>" name="email_address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الولادة</label>
                            <input type="date" class="form-control" value="<?=$lawyer['date_of_birth']?>" name="date_of_birth">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الجنس</label><br>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Male" <?php if($lawyer['lawyer_gender'] == 'Male') echo 'checked'; ?> name="gender">
                                <label class="form-check-label" for="gender">ذكر</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Female" <?php if($lawyer['lawyer_gender'] == 'Female') echo 'checked'; ?> name="gender">
                                <label class="form-check-label" for="gender">أنثى</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" value="<?=$lawyer['username']?>" name="username">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" value="<?=$lawyer['lawyer_city']?>" name="city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <div style="min-width:100%;">
                                <input style="direction:ltr;" type="tel" id="lawyerPhone" class="form-control" value="<?=$lawyer['lawyer_phone']?>" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الرقم القومي</label>
                            <input type="text" class="form-control" value="<?=$lawyer['lawyer_national']?>" name="lawyer_national">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم جواز السفر</label>
                            <input type="text" class="form-control" value="<?=$lawyer['lawyer_passport']?>" name="lawyer_passport">
                        </div>
                    </div>
                    <div class=" row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="preferred_date">الدور</label>
                            <select id="role_id" class="form-select" name="role_id" required>
                            <option value="" disabled selected>اختر الدور</option>
                                <?php

                                    if (!empty($OfficeId)) {
                                        // جلب الأدوار المرتبطة بالمكاتب
                                        $sql_roles = "SELECT power_id, role FROM powers WHERE office_id IN ($OfficeId) OR default_role_lawyer = 1";
                                        $stmt_roles = $conn->prepare($sql_roles);
                                        $stmt_roles->execute();
                                        $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

                                        if ($stmt_roles->rowCount() > 0) {
                                            foreach ($roles as $row) {
                                                $role_id = $lawyer['role_id'];
                                                $id = $row["power_id"];
                                                $role = $row["role"];
                                                $selected = ($id == $role_id) ? "selected" : "";
                                                echo "<option value='$id' $selected>$role</option>";
                                            }
                                        } else {
                                            echo "<option value='' disabled>لا توجد أدوار متاحة</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>لا توجد مكاتب مرتبطة</option>";
                                    }
                                ?>
                                </select>
                        </div>
                        <input type="hidden" value="<?=$OfficeId?>" name="office_id" id="office_id">
                        <div class="col-md-6">
                            <label class="form-label" for="preferred_date">التاريخ المفضل</label>
                            <select name="preferred_date" class="form-select" id="preferred_date">
                                <option value="" disabled>اختر نوعاً</option>
                                <option value="0" <?php echo ($lawyer['preferred_date'] == 0) ? 'selected' : ''; ?>>هجري</option>
                                <option value="1" <?php echo ($lawyer['preferred_date'] == 1) ? 'selected' : ''; ?>>ميلادي</option>
                                <option value="2" <?php echo ($lawyer['preferred_date'] == 2) ? 'selected' : ''; ?>>الاثنين</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                    <div class="form-check custom-checkbox col-md-6 mt-5">
                        <input type="checkbox" class="form-check-input" id="stop" name="stop" <?php if($lawyer['stop'] == 1) echo 'checked'; ?> value="1">
                        <label class="form-label" for="stop">إيقاف مؤقت</label>
                    </div>
                    <div class="col-md-6">
                            <label class="form-label">تاريخ الإيقاف</label>
                            <input type="date" class="form-control" value="<?=$lawyer['stop_date']?>" name="stop_date">
                    </div>
                    </div>


                    <input type="hidden" value="<?=$lawyer['lawyer_id']?>" name="lawyer_id">
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </form>
            </div>
            <div class="col-md-4">
                <form method="post" class="shadow p-3 form-w" action="req/lawyer-change.php" id="change_password">
                    <h3>تغيير كلمة السر</h3><hr>
                    <?php if (isset($_GET['perror'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?=$_GET['perror']?>
                        </div>
                    <?php } ?>
                    <?php if (isset($_GET['psuccess'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?=$_GET['psuccess']?>
                        </div>
                    <?php } ?>
                    <div class="mb-3">
                        <label class="form-label">كلمة سر المدير</label>
                        <input type="password" class="form-control" name="admin_pass" style="direction: ltr;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة السر الجديدة</label>
                        <div class="input-group mb-3" style="direction: ltr;">
                            <input type="password" class="form-control" name="new_pass" id="passInput">
                            <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                        </div>
                    </div>
                    <input type="hidden" value="<?=$lawyer['lawyer_id']?>" name="lawyer_id">
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة السر</label>
                        <input type="text" class="form-control" name="c_new_pass" id="passInput2" style="direction: ltr;">
                    </div>
                    <button type="submit" class="btn btn-primary">تغيير</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script>

        // Function to generate random password
        function makePass(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$!';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            var passInput = document.getElementById('passInput');
            var passInput2 = document.getElementById('passInput2');
            passInput.value = result;
            passInput2.value = result;
        }

        // Event listener for random password button
        var gBtn = document.getElementById('gBtn');
        gBtn.addEventListener('click', function(e){
            e.preventDefault();
            makePass(8); // Generate 8-character random password
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
    <?php include "inc/footer.php"; ?>  
</body>
</html>
<?php 

  }else {
    header("Location: lawyers.php");
    exit;
  } 
}else {
	header("Location: login.php");
	exit;
} 

?>