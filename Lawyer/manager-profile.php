<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['manager_id'])) {

    if ($_SESSION['role'] == 'Lawyer') {
      
       include "../DB_connection.php";
       include "logo.php";

       include "data/lawyers.php";
       include 'permissions_script.php';


       function getManagerById($id, $conn){
        $sql = "SELECT * FROM managers_office WHERE id =?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
     
        if ($stmt->rowCount() == 1) {
          $lawyer = $stmt->fetch();
          return $lawyer;
        } else {
         return 0;
        }
     }
       
       
       
       
       $manager_id = $_GET['manager_id'];
       $manager = getManagerById($manager_id, $conn);

       if ($manager == 0) {
         header("Location: home.php");
         exit;
       }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Profile</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />	

	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  
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
    </style>
</head>
<body>
    <?php 
    include "inc/navbar.php"; 
    include "inc/footer.php";
    ?>
    <div class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr;">        
            <a href="home.php" class="btn btn-secondary">الرئيسية</a>  
        </div>
        <div class="row">
            <div class="col-md-8">
            <form method="post" class="shadow p-3 form-w" action="req/manager-edit.php">
                    <h3>المعلومات الشخصية</h3><hr>
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
                            <input type="text" class="form-control" value="<?=$manager['manager_name']?>" name="manager_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان</label>
                            <input type="text" class="form-control" value="<?=$manager['manager_address']?>" name="manager_address">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الايميل</label>
                            <input type="email" class="form-control" value="<?=$manager['manager_email']?>" name="manager_email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الولادة</label>
                            <input type="date" class="form-control" value="<?=$manager['date_of_birth']?>" name="date_of_birth">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الجنس</label><br>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Male" <?php if($manager['manager_gender'] == 'Male') echo 'checked'; ?> name="manager_gender">
                                <label class="form-check-label" for="gender">ذكر</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Female" <?php if($manager['manager_gender'] == 'Female') echo 'checked'; ?> name="manager_gender">
                                <label class="form-check-label" for="gender">أنثى</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" value="<?=$manager['username']?>" name="username">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" value="<?=$manager['manager_city']?>" name="city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" value="<?=$manager['manager_phone']?>" name="phone">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الرقم القومي</label>
                            <input type="text" class="form-control" value="<?=$manager['manager_national']?>" name="manager_national">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم جواز السفر</label>
                            <input type="text" class="form-control" value="<?=$manager['manager_passport']?>" name="manager_passport">
                        </div>
                    </div>
                    <input type="hidden" value="<?=$manager['id']?>" name="manager_id">
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </form>
            </div>
            <div class="col-md-4">
                <form method="post" class="shadow p-3 form-w" action="req/manager-change.php" id="change_password">
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
                        <label class="form-label">كلمة السر الجديدة</label>
                        <div class="input-group mb-3" style="direction: ltr;">
                            <input type="password" class="form-control" name="new_pass" id="passInput">
                            <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                        </div>
                    </div>
                    <input type="hidden" value="<?=$manager['id']?>" name="manager_id">
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