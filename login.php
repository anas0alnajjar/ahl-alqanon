<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include "DB_connection.php";
include "data/setting.php";
$setting = getSetting($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>دخول - أهل القانون</title>
	
	<link rel="stylesheet" href="css/bootstrap5-2.css">
	<link rel="stylesheet" href="css/font-awesome.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/yshstyle.css">
	<link rel="icon" href="img/<?=$setting['logo']?>">
	<script src="https://www.google.com/recaptcha/api.js?render=<?=$setting['site_key']?>"></script>
	<script src="js/libraries/sweetalert2.min.js"></script>
	<script src="js/libraries/jquery-3.6.0.min.js"></script>
    
	<style>
		* {
			direction: rtl;
			font-family: 'Cairo', sans-serif;
		}

		body {
			/* background: linear-gradient(135deg, #ff7e5f, #feb47b); */
		/*	background: linear-gradient(135deg, #6a372b, #f5f4f3); */
		    background-color: #cfccc0;
			height: 100vh;
			display: flex;
			justify-content: center;
			align-items: center;
			overflow: hidden;
			margin:0px;
		}

		.login-container {
			background: white;
			border-radius: 15px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
			overflow: hidden;
			max-width: 400px;
			width: 100%;
			height: 100%;
			padding: 5px;
			position: relative;
			animation: fadeIn 1s ease-in-out;
		}

		.login-container img {
			width: 100px;
			display: block;
			margin: 0 auto 0px;
		}

		.login-container h3 {
			text-align: center;
			margin-bottom: 5px;
			color: #272c3f;
			font-weight: bold;
		}
		.login-container h4 {
			text-align: center;
			margin-bottom: 0px;
			color: #272c3f;
			font-weight: bold;
		}
		.login-container .form-control {
			border-radius: 50px;
			padding: 10px 15px;
		}

		.login-container .btn-primary {
			border-radius: 50px;
			background: #272c3f;
			border: none;
			padding: 10px 20px;
			font-size: 16px;
			font-weight: bold;
			transition: background 0.3s;
		}

		.login-container .btn-primary:hover {
			background: #cfccc0;
			color: #272c3f;
			border-radius: 50px;

		}

		.login-container a {
			display: block;
			text-align: center;
			margin-top: 10px;
			color: #272c3f;
			transition: color 0.3s;
		}

		.login-container a:hover {
			color: #cfccc0;
		}

		.text-light {
			margin-top: 10px;
		
			color: #cfccc0 /* #eee */;
			text-align: center;
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
			display: none; /* Hidden by default */
			position: fixed; /* Stay in place */
			z-index: 1000; /* Sit on top */
			left: 50%;
			top: 50%;
			transform: translate(-50%, -50%); /* Center the spinner */
			width: 100px; /* Set width */
			height: 100px; /* Set height */
		}

		.spinner.active {
			display: block; /* Show spinner when active */
		}

		.spinner:before {
			content: '';
			display: block;
			width: 100px;
			height: 100px;
			border-radius: 50%;
			border: 5px solid #272c3f;
			border-color: #272c3f transparent #272c3f transparent;
			animation: spin 1.2s linear infinite; /* Animation for the spinner */
		}

		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
	</style>
</head>
<body>
    <div class="login-container">
        <form id="loginForm" style="width: 100% !important;" class="login">
            <div class="text-center">
                <img style="width:200px;" src="img/<?=$setting['logo']?>" alt="Logo">
            </div>
            <h4 style="text-wrap: nowrap;">تسجيل الدخول</h3>
            <div class="mb-3">
                <label class="form-label">اسم المستخدم</label>
                <input style="direction: ltr;" type="text" class="form-control" name="uname" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input style="direction: ltr;" type="password" class="form-control" name="pass" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
            <a href="index.php" class="text-decoration-none">الرجوع الي الرئيسية</a>
            <?php if ($setting['allow_joining']) : ?>
                <!--<a href="index.php#ask-join" class="text-decoration-none">تقديم طلب انضمام</a>-->
                <a href="join.php" class="text-decoration-none">
                    انضم إلينا (للمكاتب)

                 </a>
                <a href="ask_join.php?as_a=2" class="text-decoration-none">
                    انضم إلينا (للمحامين)
                </a>
                <a href="forget_password.php" class="text-decoration-none">
نسيت كلمة المرور                </a>
              <!--  <a href="ask_join.php?as_a=1" class="text-decoration-none">
                    انضم إلينا (للموكلين)
                </a>-->

            <?php endif; ?>
            <?php if ($setting['allow_joining'] != 1) : ?>
                <a href="index.php" class="text-decoration-none">الرئيسية</a>
            <?php endif; ?>
        </form>
        <div class="text-light">
         &copy; جميع الحقوق محفوظة.
        </div>
        <div class="spinner" id="spinner">
        </div>
    </div>

	<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // الاستماع إلى رد الخادم
        grecaptcha.ready(function() {
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                $("#spinner").fadeIn();
                grecaptcha.execute('<?=$setting['site_key']?>', {action: 'submit'}).then(function(token) {
                    var form = document.getElementById('loginForm');
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'g-recaptcha-response';
                    input.value = token;
                    form.appendChild(input);
                    $.ajax({
                        type: 'POST',
                        url: 'req/login.php',
                        data: $(form).serialize(),
                        success: function(response) {
                            $("#spinner").fadeOut();
                            let res = JSON.parse(response);
                            if (res.status === 'error') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: res.message,
                                    showCancelButton: true,
                                    confirmButtonText: 'أخبر الدعم الفني',
                                    cancelButtonText: 'إلغاء'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'index.php?showContact=true';
                                    }
                                });
                            } else if (res.status === 'success') {
                                window.location.href = res.redirect;
                                $("#spinner").fadeOut();
                            }
                        }
                    });
                });
            });
        });
    });
</script>


</body>

</html>
