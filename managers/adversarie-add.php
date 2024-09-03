<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {
      
       include "../DB_connection.php";
       include "logo.php";

       include 'permissions_script.php';
        if ($pages['adversaries']['add'] == 0) {
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

      

       $office_id = isset($_SESSION['office_id']) ? $_SESSION['office_id'] : '';
       
       
 

       if (isset($_GET['phone'])) $phone = $_GET['phone'];
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Add adversary</title>

	
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/style.css">


  <style>
    *{
      direction: rtl;
    }
    .error {
        border-color: #dc3545 !important; /* Change border color for invalid fields */
    }


  </style>
</head>

<body>
    <?php 
        include "inc/navbar.php";
     ?>
     <div class="container mt-5">
     <div class="btn-group mb-3" style="direction:ltr;">
        <a href="home.php" class="btn btn-light">الرئيسية</a>
        <a href="adversaries.php" class="btn btn-dark">رجوع</a>
      </div>        

        <form method="post" class="shadow p-3 form-w" action="req/adversary-add.php" id="clientForm">
            <h3>إضافة خصم جديد</h3>
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
            <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">المحامي</label>
                            <select id="lawyer_id" class="form-control" name="lawyer_id">
                            <option value="">اختر المحامي...</option>
                            <?php
                                if (!empty($OfficeId)) {
                                    $sql_lawyers = "SELECT * FROM lawyer WHERE office_id IN ($OfficeId) ORDER BY lawyer_id";
                                    $stmt_lawyers = $conn->prepare($sql_lawyers);
                                    $stmt_lawyers->execute();
                                    if ($stmt_lawyers->rowCount() > 0) {
                                        while ($row = $stmt_lawyers->fetch(PDO::FETCH_ASSOC)) {
                                            $lawyer_id = $row["lawyer_id"];
                                            $lawyer_name = $row["lawyer_name"];
                                            $lawyer_email = $row["lawyer_email"];
                                            echo "<option value='$lawyer_id' data-client-email='$lawyer_email'>$lawyer_name</option>";
                                        }
                                    } else {
                                        echo "<option value=''>لا توجد محامون مرتبطين بك</option>";
                                    }
                                } else {
                                    echo "<option value=''>لا توجد مكاتب مرتبطة بك</option>";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الاسم الأول</label>
                    <input type="text" class="form-control" value="<?=$fname?>" name="fname">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العائلة</label>
                    <input type="text" class="form-control" value="<?=$lname?>" name="lname">
                </div>
                <input type="hidden" id="office_id" class="form-control" value="<?=$OfficeId?>" name="office_id">
                <div class="col-md-6 mb-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" class="form-control" value="<?=$city?>" name="city">
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
                    <label class="form-label">الهاتف</label>
                    <input type="text" class="form-control" value="<?=$phone?>" name="phone">
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
    document.getElementById('clientForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        var form = event.target;
        var requiredFields = [
            { name: 'fname', message: 'يرجى ملء حقل الاسم.' },
            { name: 'gender', message: 'يرجى اختيار الجنس.' },
            { name: 'lname', message: 'يرجى ملء حقل الهاتف.' },
            { name: 'lawyer_id', message: 'يرجى ملء حقل الهاتف.' },
            { name: 'office_id', message: 'يرجى اختيار الدور.' }
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
            form.submit(); // Submit the form if all fields are valid
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



