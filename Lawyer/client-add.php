<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Lawyer') {
      
       include "../DB_connection.php";
       include "logo.php";

       include 'permissions_script.php';
        if ($pages['clients']['add'] == 0) {
            header("Location: home.php");
            exit();
        }

        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);

       $fname = isset($_SESSION['fname']) ? $_SESSION['fname'] : '';
       $lname = isset($_SESSION['lname']) ? $_SESSION['lname'] : '';
       $address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
       $email_address = isset($_SESSION['email_address']) ? $_SESSION['email_address'] : '';
       $date_of_birth = isset($_SESSION['date_of_birth']) ? $_SESSION['date_of_birth'] : '';
       $city = isset($_SESSION['city']) ? $_SESSION['city'] : '';
       $phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : '';
       $gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';

      
       $father_name = isset($_SESSION['father_name']) ? $_SESSION['father_name'] : '';
       $grandfather_name = isset($_SESSION['grandfather_name']) ? $_SESSION['grandfather_name'] : '';
       $national_num = isset($_SESSION['national_num']) ? $_SESSION['national_num'] : '';
       $alhi = isset($_SESSION['alhi']) ? $_SESSION['alhi'] : '';
       $street_name = isset($_SESSION['street_name']) ? $_SESSION['street_name'] : '';
       $num_build = isset($_SESSION['num_build']) ? $_SESSION['num_build'] : '';
       $num_unit = isset($_SESSION['num_unit']) ? $_SESSION['num_unit'] : '';
       $zip_code = isset($_SESSION['zip_code']) ? $_SESSION['zip_code'] : '';
       $subnumber = isset($_SESSION['subnumber']) ? $_SESSION['subnumber'] : '';
       $uname = isset($_SESSION['username']) ? $_SESSION['username'] : '';
       $office_id = isset($_SESSION['office_id']) ? $_SESSION['office_id'] : '';
       
       $role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : '';
       $client_passport = isset($_SESSION['client_passport']) ? $_SESSION['client_passport'] : '';
 

       if (isset($_GET['phone'])) $phone = $_GET['phone'];
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Add Client</title>

	
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">


  <style>
    *{
      direction: rtl;
    }
    .error {
        border-color: #dc3545 !important; /* Change border color for invalid fields */
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
        include "inc/footer.php";
     ?>
     <div class="container mt-5">
     <div class="btn-group mb-3" style="direction:ltr;">
        <a href="users.php" class="btn btn-light user_management">إدارة المستخدمين</a>
        <a href="clients.php" class="btn btn-dark">الموكلين</a>
      </div>        

        <form method="post" class="shadow p-3 form-w" action="req/client-add.php" id="clientForm">
            <h3>إضافة موكل جديد</h3>
            <hr>
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

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الاسم الأول</label>
                    <input type="text" class="form-control" value="<?=$fname?>" name="fname">
                    <input type="hidden" class="form-control" value="<?=$user_id?>" name="lawyer_id">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العائلة</label>
                    <input type="text" class="form-control" value="<?=$lname?>" name="lname">
                    <input type="hidden" class="form-control" value="clients" name="clients">
                </div>
                <div class="col-md-6 mb-3">
                <!--    <label class="form-label">اسم الأب</label>-->
                    <input type="hidden" class="form-control" value="<?=$father_name?>" name="father_name">
                </div>
                <div class="col-md-6 mb-3">
                  <!--  <label class="form-label">اسم الجد</label>
                  -->
                    <input type="hidden" class="form-control" value="<?=$grandfather_name?>" name="grandfather_name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان</label>
                    <input type="text" class="form-control" value="<?=$address?>" name="address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الإيميل</label>
                    <input type="text" class="form-control" value="<?=$email_address?>" name="email_address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">سنة الولادة</label>
                    <input type="date" class="form-control" value="<?=$date_of_birth?>" name="date_of_birth">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الجنس</label><br>
                    <input type="radio" value="Male" <?php if(!isset($_SESSION['gender']) || $_SESSION['gender'] == 'Male') echo 'checked'; ?> name="gender"> ذكر
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" value="Female" <?php if(isset($_SESSION['gender']) && $_SESSION['gender'] == 'Female') echo 'checked'; ?> name="gender"> أنثى
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الرقم القومي</label>
                    <input type="text" class="form-control" value="<?=$national_num?>" name="national_num">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم جواز السفر</label>
                    <input type="text" class="form-control" value="<?=$client_passport?>" name="client_passport">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الهاتف</label>
                    <div style="min-width:100%;">
                        <input style="direction:ltr;" type="tel" id="clientPhone" class="form-control" value="<?=$phone?>" name="phone">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" class="form-control" value="<?=$city?>" name="city">
                </div>
    

   
          
                <div class="col-md-6 mb-3">
                    <label class="form-label">الحي</label>
                    <input type="text" class="form-control" value="<?=$alhi?>" name="alhi">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الشارع</label>
                    <input type="text" class="form-control" value="<?=$street_name?>" name="street_name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم المبنى</label>
                    <input type="text" class="form-control" value="<?=$num_build?>" name="num_build">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الوحدة</label>
                    <input type="text" class="form-control" value="<?=$num_unit?>" name="num_unit">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الرمز البريدي</label>
                    <input type="text" class="form-control" value="<?=$zip_code?>" name="zip_code">
                </div>
              <!--  <div class="col-md-6 mb-3">
                    <label class="form-label">الرقم الفرعي</label>
                    <input type="text" class="form-control" value="<?=$subnumber?>" name="subnumber">
                </div>-->
                <div class="col-md-6 mb-3">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="receive_emails" checked value="1"> استلام التذكيرات عبر الإيميل
                    </label>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="receive_whatsupp" checked value="1"> استلام الرسائل عبر الواتس آب
                    </label>
                </div>
               
                <div class="col-md-6 mb-3">
                    <label class="form-label">الدور</label>
                    <select id="role_id" class="form-select" name="role_id">
                        <option value="" disabled selected>اختر الدور</option>
                        <?php
                            if (!empty($user_id)) {
                                // إعداد الاستعلام باستخدام الاستعلام المحضر
                                $sql_roles = "SELECT power_id, role, default_role_client FROM powers WHERE FIND_IN_SET(:user_id, lawyer_id) OR default_role_client = 1";
                                $stmt_roles = $conn->prepare($sql_roles);
                                // ربط قيمة user_id بالاستعلام
                                $stmt_roles->bindParam(':user_id', $user_id, PDO::PARAM_STR);
                                $stmt_roles->execute();
                                $result2 = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

                                if (count($result2) > 0) {
                                    foreach ($result2 as $row2) {
                                        $id = $row2["power_id"];
                                        $role = $row2["role"];
                                        $default = $row2["default_role_client"];
                                        $selected = ($default == 1) ? "selected" : "";
                                        echo "<option value='$id' $selected>$role</option>\n";
                                    }
                                } else {
                                    echo "<option value='' disabled>لا توجد أدوار مرتبطة بك</option>\n";
                                }
                            } else {
                                echo "<option value='' disabled>لا توجد مكاتب مرتبطة بك</option>\n";
                            }
                        ?>
                    </select>

                </div>
                <input type="hidden" value="<?=$OfficeId?>" name="office_id" id="office_id">
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" class="form-control" value="<?=$uname?>" name="username">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">كلمة السر</label>
                    <div class="input-group" style="direction:ltr;">
                        <input style="direction:ltr;" type="text" class="form-control" name="pass" id="passInput">
                        <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-sm w-100" style="min-width:250px;">تسجيل</button>
            </div>
        </form>
     </div>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(10) a").addClass('active');
        });

        function makePass(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@!#';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            var passInput = document.getElementById('passInput');
            passInput.value = result;
        }

        var gBtn = document.getElementById('gBtn');
        gBtn.addEventListener('click', function(e){
          e.preventDefault();
          makePass(8);
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#clientPhone");
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
document.getElementById('clientForm').addEventListener('submit', function(event) {
    event.preventDefault(); // منع الإرسال الافتراضي للنموذج
    var form = event.target;
    var formData = new FormData(form); // جمع بيانات النموذج

    // التحقق من الحقول المطلوبة
    var requiredFields = [
        { name: 'fname', message: 'يرجى ملء حقل الاسم.' },
        { name: 'username', message: 'يرجى ملء حقل اسم المستخدم.' },
        { name: 'pass', message: 'يرجى ملء حقل كلمة السر.' },
     //   { name: 'email_address', message: 'يرجى ملء حقل الإيميل.' },
        { name: 'gender', message: 'يرجى اختيار الجنس.' },
        { name: 'phone', message: 'يرجى ملء حقل الهاتف.' },
      //  { name: 'lname', message: 'يرجى ملء حقل العائلة.' },
        { name: 'role_id', message: 'يرجى اختيار الدور.' }
    ];

    var isValid = true;
    var firstInvalidField = null;

    requiredFields.forEach(function(field) {
        var input = form.querySelector(`[name="${field.name}"]`);
        if (!input || !input.value) {
            input.classList.add('error');
            isValid = false;
            if (!firstInvalidField) {
                firstInvalidField = input;
            }
        } else {
            input.classList.remove('error');
        }
    });

    // Check for select field specifically
    var roleSelect = form.querySelector('#role_id');
    if (!roleSelect.value) {
        roleSelect.classList.add('error');
        isValid = false;
        if (!firstInvalidField) {
            firstInvalidField = roleSelect;
        }
    } else {
        roleSelect.classList.remove('error');
    }

    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يرجى ملء جميع الحقول المطلوبة.',
            customClass: {
                popup: 'swal2-custom-popup'
            }
        });
        if (firstInvalidField) {
            firstInvalidField.focus();
        }
    } else {
        // إرسال بيانات النموذج عبر AJAX
        fetch('req/client-add.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح',
                    text: data.message,
                    customClass: {
                        popup: 'swal2-custom-popup'
                    }
                });
                form.reset(); // إعادة تعيين النموذج
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message,
                    customClass: {
                        popup: 'swal2-custom-popup'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء إرسال البيانات.',
                customClass: {
                    popup: 'swal2-custom-popup'
                }
            });
        });
    }
});

document.getElementById('gBtn').addEventListener('click', function(e) {
    e.preventDefault();
    var passInput = document.getElementById('passInput');
    passInput.value = Math.random().toString(36).slice(-8);
});

</script>

</body>

</html>
<?php 

  }else {
    header("Location: ../login.php");
    exit;
  } 
}else {
	header("Location: ../login.php");
	exit;
} 

?>



