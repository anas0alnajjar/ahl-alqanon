<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {
       include "../DB_connection.php";
       include "information.php";
       include "logo.php";
       include 'permissions_script.php';
       
       if ($pages['control']['read'] == 0) {
        header("Location: home.php");
        exit();
    }
    
    include "get_office.php";
    $user_id = $_SESSION['user_id'];
    $office_id = getOfficeId($conn, $user_id);
 

    $lawyer_id = getLawyerId($user_id, $conn);
    
        
 ?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="../css/style-dash.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المحامي - الصفحة الرئيسية</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    

        <!-- CSS for full calendar -->
        <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" /> -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">

    <!-- Bootstrap CSS and JS -->
    
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@3/dark.css">
    
    
    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    
    
    
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    

    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
        @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');

        * {
            direction: rtl;
        }

        .text-left {
            text-align: right;
        }

        .overflow-x-auto {
            scrollbar-width: thin;
            max-height: 600px;
            scroll-behavior: smooth;
        }

        td a {
            text-decoration: none;
        }

        .card {
            transition: transform 0.3s ease-in-out;
        }



        .bg-gradient {
            background: linear-gradient(90deg, rgba(58,123,213,1) 0%, rgba(58,213,158,1) 100%);
        }
        .delete-event-btn {
        position: absolute;
        
        left:0;
        top: 0 !important;
        margin: 5px;
        padding: 3px 8px;
        border-radius: 50%;
        background-color: red;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 10px;
        display: none;
        z-index: 999999999999 !important;
        opacity: 0.7;
    }
    .card {
        max-height: 600px !important;
    }
    #calendar{
        transition: transform 0.3s ease-in-out;

    }

.close{
    position: absolute;
    left: 5px;
    font-size: x-large;
}
.fc-scroller.fc-day-grid-container {
    scrollbar-width: thin !important;
}
.fc-content {
text-wrap:wrap !important;
}
.custom-card {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
        }
.custom-card:hover {
    transform: translateY(-10px);
        }

        .pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}
        
.fc-title{
    text-wrap: wrap;
}

@media only screen and (max-width: 600px) {
    #calendar {
        font-size: smaller;
    }
    .fc-title {
        white-space: normal; /* هذا لتفعيل التفاف النص */
        font-size: xx-small;
    }
    h2 {
        font-size: medium;
        margin-top: 3px;
    }
    .fc-scroller.fc-day-grid-container {
        scrollbar-width: thin !important;
        min-height: 188.5px !important;
    }
    .fc-toolbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .fc-toolbar .fc-left, .fc-toolbar .fc-right {
        margin-bottom: 10px;
    }

    .fc-view-container {
        overflow-x: scroll;
    }
}
#chart-container{
            width: 80%;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            #chart-container {
                width: 100%;
                margin: 0 auto;
            }
        }
        .d-none {
    display: none;
}
#client-select {
        margin: 20px 0;
        padding: 10px;
        font-size: 16px;
    }
    #client-chart {
        max-width: 100%;
        height: auto;
    }
    #chart-container {
            position: relative;
            height: 50vh;
            width: 100%;
            margin-bottom: 10px;
        }
</style>
</head>
<body class="bg-gray-50 font-inter text-gray-800">
<!-- Start popup dialog box -->
<!-- Start popup dialog box -->
<div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">إضافة حدث جديد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="event_form">
                    <div class="alert alert-danger" role="alert" id="error-message" style="display: none;">
                        <!-- سيتم عرض رسالة الخطأ هنا -->
                    </div>
                    <div class="form-group col-md-12 mb-2">
                        <label for="event_name">اسم الحدث</label>
                        <input type="text" name="event_name" id="event_name" class="form-control" placeholder="أدخل اسم الحدث">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12 mb-2">
                            <label for="event_start_date">تاريخ بدء الحدث</label>
                            <input type="datetime-local" name="event_start_date" id="event_start_date" class="form-control start-event" placeholder="تاريخ بدء الحدث" required>
                        </div>
                        <?php if ($pages['add_old_session']['add'] == 0) : ?>
                        <div class="row" id="error">
                            <p style="color:red;font-size:smaller">غير مسموح لك إضافة حدث بتاريخ قديم!</p>
                        </div>
                        <?php endif; ?>
                        <div class="form-group col-md-12 mb-2">
                            <label for="event_end_date">تاريخ انتهاء الحدث</label>
                            <input type="datetime-local" name="event_end_date" id="event_end_date" class="form-control" placeholder="تاريخ انتهاء الحدث" required>
                            <input type="hidden" name="lawer_name" id="lawer_name" value="<?= $lawyer_id ?>" required>
                            <input type="hidden" name="client_name" id="client_name" value="<?= $user_id ?>" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_event()">حفظ الحدث</button>
            </div>
        </div>
    </div>
</div>
<!-- End popup dialog box -->

    <?php include "inc/navbar.php"; ?>    

    <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen transition-all main">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row gap-6 mb-6">
                <!-- Cards section -->
                <div class="lg:flex-1 grid grid-cols-1 md:grid-cols-2 gap-6" style="">
                    <!-- Card 1 -->
                    <div class="custom-card card-1" data-count="<?php echo $open_sessions_result['sessions_coming']; ?>" data-percentage="<?php echo round($percentage); ?>" data-text="عدد الجلسات القادمة">
                        <div class="flex justify-between mb-4">
                            <div>
                                <div class="text-xl font-semibold mb-1 text-count"></div>
                                <div class="text-sm font-medium text-description"></div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <a href="sessions.php"><button style="color: white;" type="button" class="btn btn-link"><i class="fa fa-spinner fa-spin"></i></button></a>
                            <div class="w-full bg-gray-100 rounded-full h-4 relative overflow-hidden">
                                <div class="progress-bar h-full absolute right-0 top-0 bg-gradient-to-l from-blue-500 to-green-500 rounded-full p-1"></div>
                                <div class="w-2 h-2 rounded-full bg-white ml-auto"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-600 ml-4 text-percentage"></span>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="custom-card card-4" data-count="<?php echo $total_cases_result['total_cases_count'] ?>" data-text="عدد القضايا">
                        <div class="flex justify-between mb-4">
                            <div>
                                <a href="cases.php"><i style="position: absolute;left: 10px;" class="fas fa-gavel text-gray-400 text-3xl pulse"></i></a>
                                <div class="text-xl font-semibold mb-1 text-count"></div>
                                <div class="text-sm font-medium text-description"></div>
                            </div>
                        </div>
                        <?php if ($pages['cases']['add']) : ?>
                        <a href="add_case.php" class="text-white font-medium text-sm hover:text-gray-200">أضف قضية</a>
                        <?php endif; ?>
                    </div>
   

                    <!-- Card 5 -->
                  <div class="custom-card card-5" data-count="<?php if (isset($monthly_payment_result['monthly_paid']) && $monthly_payment_result['monthly_paid'] > 0) { echo $monthly_payment_result['monthly_paid']; } ?>" data-text="الدفعات الغير مستلمة لهذا الشهر">

                        <div class="flex justify-between mb-4">
                            <div>
                                <i style="position: absolute;left: 10px;" class="fas fa-hand-holding-usd text-gray-400 text-3xl pulse"></i> 
                                <div class="text-xl font-semibold mb-1 text-count">
                                    
                                </div>
                                <div class="text-sm font-medium text-description"></div>
                            </div>
                        </div>
                        <p class="text-white font-medium text-sm hover:text-gray-200">
                            <?php if (empty($monthly_payment_result['monthly_paid']) || $monthly_payment_result['monthly_paid'] === 0) { echo "لا يوجد أي دفعات مستحقة لهذا الشهر"; } ?>
                        </p>
                    </div>


                </div>

                <!-- Calendar section -->
                <div class="lg:flex-1" id="calendar-container" style="overflow: auto;scrollbar-width: none !important;">
                <div class="bg-white border border-gray-100 shadow-md p-6 rounded-md h-full">
                    <div class="w-full h-full">
                        <div class="alert alert-success" role="alert" id="success-message" style="display: none;"></div>
                        <div id="calendar" style=""></div> <!-- Adjust width and height as needed -->
                    </div>
                </div>
                </div>  
            </div>




            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md card">
                    <div class="flex justify-between mb-4 items-start">
                        <div class="font-medium">إدارة القضايا</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[540px]" data-tab-for="order" data-page="active" id="active-cases">
                            <thead>
                                <tr>
                                    <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">اسم القضية</th>
                                    <!-- <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">الموكل</th> -->
                                    <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">وكالة/قضية</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md card">
                    <div class="flex justify-between mb-4 items-start">
                        <div class="font-medium">إدارة الجلسات</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[540px]" id="sessions-cases">
                            <thead>
                                <tr>
                                    <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">اسم القضية</th>
                                    <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">تستحق في</th>
                                    <!-- <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">الموكل</th> -->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md lg:col-span-2 card">
                    <div class="flex justify-between mb-4 items-start">
                        <div class="font-medium">راقب الجلسات</div>
                    </div>
                        <div id="chart-container">

                        <select id="case-filter">
                            <option value="ALL" selected>كل القضايا</option>

                            <!-- سيتم إضافة الخيارات هنا بواسطة JavaScript -->
                        </select>

                            <canvas id="case-chart"></canvas>
                        </div>
                </div>
                <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md card">
                    <div class="flex justify-between mb-4 items-start">
                        <div class="font-medium">المستحقات المالية</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[460px]" id="clientDate">
                            <thead>
                                <tr style='text-wrap:nowrap;'>
                                    <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">مبلغ الدفعة القادمة</th>
                                    <th class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">تاريخ الاستحقاق</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </main>


    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/scriptDashboard.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- JS for jQuery -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- JS for full calendar -->
    


<!-- جديد -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

    
    

    

    


    <script src="../js/get_data_for_clients.js"></script>
    <!-- To Concert Calendar System -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>    
    <script>
        $(document).ready(function() {
    $('.close').click(function() {
        $('#event_entry_modal').modal('hide');
}); });
    </script>

<script>
        function randomColor() {
            const r = Math.floor(Math.random() * 256);
            const g = Math.floor(Math.random() * 256);
            const b = Math.floor(Math.random() * 256);
            return { r, g, b, rgba: `rgba(${r}, ${g}, ${b}, 0.9)` };
        }

        function isLight({ r, g, b }) {
            // Calculate luminance
            const luminance = 0.299 * r + 0.587 * g + 0.114 * b;
            return luminance > 186;
        }

        function applyRandomColor(card, index) {
            const { r, g, b, rgba } = randomColor();
            card.style.background = `linear-gradient(136deg, ${rgba} 1%, ${rgba} 5%)`;
            const textColor = isLight({ r, g, b }) ? 'black' : 'white';
            card.style.color = textColor;

            const countElement = card.querySelector('.text-count');
            const descriptionElement = card.querySelector('.text-description');
            const percentageElement = card.querySelector('.text-percentage');
            const progressBarElement = card.querySelector('.progress-bar');

            countElement.textContent = card.getAttribute('data-count');
            descriptionElement.textContent = card.getAttribute('data-text');
            if (percentageElement && progressBarElement) {
                const percentage = card.getAttribute('data-percentage');
                percentageElement.textContent = `${percentage}%`;
                progressBarElement.style.width = `${percentage}%`;
            }

            card.classList.add(`unique-animation-${index}`);
            
        }

        document.querySelectorAll('.custom-card').forEach((card, index) => applyRandomColor(card, index));
    </script>
    
<?php if ($pages['add_old_session']['add'] == 0) : ?>
<script>
    function validateDate(input) {
        var today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        var hijriInput = input.closest('.form-group').parentElement.querySelector('.start-event');
        var errorDiv = document.getElementById('error');

        if (input.value < today) {
            input.value = ''; // تفريغ الحقل الميلادي
            if (hijriInput) {
                hijriInput.value = ''; // تفريغ الحقل الهجري
                hijriInput.classList.add('is-invalid');
                hijriInput.classList.remove('is-valid');
            }
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');

            if (errorDiv) {
                errorDiv.classList.remove('d-none'); // إظهار الرسالة
            }
        } else {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
            if (hijriInput) {
                hijriInput.classList.add('is-valid');
                hijriInput.classList.remove('is-invalid');
            }

            if (errorDiv) {
                errorDiv.classList.add('d-none'); // إخفاء الرسالة
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        function attachEventListeners() {
            // التحقق من تواريخ الجلسات عند تغيير التاريخ الميلادي
            document.querySelectorAll('.start-event').forEach(function(input) {
                input.addEventListener('change', function() {
                    validateDate(input);
                });
            });
        }

        attachEventListeners();

        // مراقبة التغييرات في قيم الحقول بشكل دوري
        var prevGeoValues = new Map();

        setInterval(function() {
            document.querySelectorAll('.start-event').forEach(function(geoInput) {
                var currentValue = geoInput.value;
                var prevValue = prevGeoValues.get(geoInput) || '';
                if (currentValue !== prevValue) {
                    prevGeoValues.set(geoInput, currentValue);
                    validateDate(geoInput);
                }
            });
        }, 1000); // تحقق كل ثانية
    });
</script>
<?php endif; ?>     
     

</body>
</html>
<?php 

  }else {
    header("Location: ../index.php");
    exit;
  } 
}else {
    header("Location: ../index.php");
    exit;
} 

?>
