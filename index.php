<?php 
include "DB_connection.php";
include "data/setting.php";
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
<!DOCTYPE html>
<html lang="ar">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>مرحبًا بك في <?=$setting['company_name']?></title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/yshstyle.css">
	<link rel="icon" href="img/<?=$setting['logo']?>">
	
	<style>
		body {
			font-family: 'Cairo', sans-serif;
		/*	background: linear-gradient(45deg, #1d1d1d, #323232); */
			color: #272c3f;
			overflow-x: hidden;
		}

		.navbar {
		/*	background-color: #212529; */
		    margin-top: 110px;

			background-color: #272c3f !important;
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

		.black-fill {
			background: rgba(0, 0, 0, 0.7);
			padding: 50px 0;
		}

		.welcome-text h4, .welcome-text p {
			animation: fadeInUp 2s;
		}

		.card {
			background: rgba(255, 255, 255, 0.1);
			border: none;
			animation: fadeInUp 1.5s;
		}

		.card img {
			animation: bounce 2s infinite;
		}

		.card-title {
			color: #272c3f;
		}

		.btn-primary {
			background-color: #272c3f;
			border: none;
			transition: background-color 0.3s;
		}

		.btn-primary:hover {
			background-color: #cfccc0;
			color: #272c3f;
		}

		@keyframes spin2 {
			from {opacity: 0; transform: translateY(10px);}
			to {opacity: 1; transform: translateY(0);}
		}

		@keyframes fadeInUp {
			from {opacity: 0; transform: translateY(50px);}
			to {opacity: 1; transform: translateY(0);}
		}

		@keyframes bounce {
			0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
			40% {transform: translateY(-20px);}
			60% {transform: translateY(-10px);}
		}

		#contactIcon {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #cfccc0;
            color: #272c3f;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s;
            z-index: 1000;
        }
        #contactIcon:hover {
            background-color: #272c3f;
            color: #cfccc0;
        }
        .hidden-section {
            display: none !important;
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
.btn-primary{
    --bs-btn-color:#cfccc0;
    --bs-btn-bg:#272c3f;
    --bs-btn-border-color:#272c3f;
    --bs-btn-hover-color:#cfccc0;
    --bs-btn-hover-bg:#272c3f;
    --bs-btn-hover-border-color:#272c3f;
    --bs-btn-focus-shadow-rgb:#272c3f;
    --bs-btn-active-color:#cfccc0;
    --bs-btn-active-border-color:#272c3f;
 /*   --bs-btn-active-shadow:inset 0 3px 5px rgba(0, 0, 0, 0.125);*/
      --bs-btn-active-shadow:#272c3f;
    --bs-btn-disabled-color:#cfccc0;
    --bs-btn-disabled-bg:#272c3f;
    --bs-btn-disabled-border-color: #272c3f;
}
div:where(.swal2-container) button:where(.swal2-styled).swal2-confirm {
    background-color: #272c3f;
    color: #cfccc0;
}
#about .card-1 h5 :hover{
	background-color: #272c3f !important;
    
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.card:hover .card-body {
	color: unset !important;
	background-color: unset !important;
}

p {
	text-align: justify;
	line-height: normal;
}
	</style>
</head>
<body class="body-home">
    <div class="black-fill"><br /><br />
    	<div class="container" style="direction: rtl;">
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="homeNav" style="direction: ltr;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="img/<?=$setting['logo']?>" width="40">
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
          <a class="nav-link" href="#about">من نحن</a>
        </li>
		<li class="nav-item">
          <a class="nav-link" href="packages.php">العروض والأسعار</a>
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

<!--
        <section class="welcome-text d-flex justify-content-center align-items-center flex-column">
        	<h4>مرحبًا بك في <?=$setting['company_name']?></h4>
			<div class="col-md-10 mt-5 order-md-1">
			      <div class="card-body">
			        <p class="card-text"><?=$setting['slogan']?></p>
			      </div>
			    </div>
        </section>
   -->
   
        <section id="about" class="d-flex justify-content-center align-items-center flex-column">
        	<div class="card mb-3 card-1">
        	            <p>	<h3 style="color:#cfccc0;text-align: center;">مرحبًا بك في <?=$setting['company_name']?></h3></p>
<br>
			  <div class="row g-0">
			    <div class="col-md-4 order-md-2">
			        <br>
			      <img src="img/<?=$setting['logo']?>" class="img-fluid rounded-start" alt="Company Logo">
			    </div>
			    <div class="col-md-8 order-md-1">
			      <div class="card-body">
			       <!-- <h5 class="card-title">معلومات عنا</h5>-->
			        <p class="card-text"><?=$setting['slogan']?></p>
			      </div>
			    </div>
			  </div>
			</div>
        </section>  
      
        <section id="about" class="d-flex justify-content-center align-items-center flex-column">
        	<div class="card mb-3 card-1">
			  <div class="row g-0">
			    <div class="col-md-4 order-md-2">
			      <img src="img/<?=$setting['logo']?>" class="img-fluid rounded-start" alt="Company Logo">
			    </div>
			    <div class="col-md-8 order-md-1">
			      <div class="card-body">
			        <h5 class="card-title">معلومات عنا</h5>
			        <p class="card-text"><?=$setting['about']?></p>
			      </div>
			    </div>
			  </div>
			</div>
        </section>
        
        
		<div id="contactIcon">
        	<i class="fas fa-envelope"></i>
		</div>
		<section id="contact" class="d-flex justify-content-center align-items-center flex-column section hidden-section">
		<form id="contactForm" method="post">
			<h3>اتصل بنا</h3>
			<div id="alert-container"></div>
			<div class="mb-3">
				<label for="exampleInputEmail1" class="form-label">البريد الإلكتروني</label>
				<input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp">
				<div id="emailHelp" class="form-text">لن نشارك بريدك الإلكتروني مع أي شخص آخر.</div>
			</div>
			<div class="mb-3">
				<label class="form-label">الاسم الكامل</label>
				<input type="text" name="full_name" class="form-control">
			</div>
			<div class="mb-3">
				<label class="form-label">الرسالة</label>
				<textarea class="form-control" name="message" rows="4"></textarea>
			</div>
			<button type="submit" class="btn btn-primary">إرسال</button>
		</form>



        </section>
		<?php if ($setting['allow_joining']) : ?>
			<!-- <section id="ask-join" class="d-flex justify-content-center align-items-center flex-column">
				<form method="post" action="index.php">
					<h3>طلب الانضمام</h3>
					<div class="mb-3 text-center" style="direction:ltr;">
						<select id="ask_join" class="form-select" name="ask_join">
							<option value="3">كمدير مكتب</option>
							<option value="2">كمحامي</option>
							<option value="1">كعميل</option>
						</select>
					</div>
				<button type="submit" class="btn btn-primary">إرسال</button>
				</form>
			</section> -->
		<?php endif; ?>
		
		  <div class="text-center text-light mt-5">
        <a style="color:#cfccc0;" href="HTTPS://AHL-ALQANON.COM">WWW.AHL-ALQANON.COM</a>
        </div>
		
        <div class="text-center text-light mt-5">
        	جميع الحقوق محفوظة &copy;
			<?=$setting['current_year']?>  - <?php echo date("Y");?>

			<?=$setting['company_name']?>.
        </div>
		<div class="spinner" id="spinner"></div>

    	</div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
    document.addEventListener("DOMContentLoaded", function() {
        const contactIcon = document.getElementById("contactIcon");
        const contactSection = document.getElementById("contact");
        
        contactIcon.addEventListener("mouseenter", function() {
            contactSection.classList.remove("hidden-section");
            contactSection.scrollIntoView({ behavior: "smooth" }); // يمكنك تعديل السلوك حسب الرغبة
        });

    });
	</script>	
	<script src="https://www.google.com/recaptcha/api.js?render=<?=$setting['site_key']?>"></script>
	<script src="js/libraries/jquery-3.6.0.min.js"></script>
	<script src="js/libraries/sweetalert2.min.js"></script>

	<script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // عرض قسم الاتصال إذا كان الباراميتر موجوداً في URL
            if (<?php echo $showContact ? 'true' : 'false'; ?>) {
                document.getElementById('contact').classList.remove('hidden-section');
                document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
            }

            grecaptcha.ready(function() {
                $('#contactForm').on('submit', function(e) {
                    e.preventDefault();
                    $("#spinner").fadeIn();
                    grecaptcha.execute('<?=$setting['site_key']?>', {action: 'submit'}).then(function(token) {
                        var form = $('#contactForm');
                        var alertContainer = $('#alert-container');
                        $.ajax({
                            type: 'POST',
                            url: 'req/contact.php',
                            data: form.serialize() + '&g-recaptcha-response=' + token,
                            success: function(response) {
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
                                    });
                                    form.trigger('reset'); // إفراغ الحقول بعد النجاح
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
<?php } else {
	header("Location: login.php");
	exit;
} ?>