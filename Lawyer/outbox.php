<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    include "../DB_connection.php";
    include "logo.php";
    include 'permissions_script.php';
    if ($pages['outbox']['read'] == 0) {
        header("Location: home.php");
        exit();
    }

    // دوال لجلب البيانات من الجداول المختلفة
    include "data/outbox.php";
     
    include "get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);
    
    $notifications = getAllNotifications($conn, $user_id);
    $reminders = getAllReminders($conn, $user_id);
    $cases = getAllCases($conn, $user_id);
    $tasks = getAllTasks($conn, $user_id);
    $helpers = getHelpers($conn, $user_id);


?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutBox</title>
    
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .btn-action {
            margin-left: 5px;
        }
        .accordion-button {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h2 {
            font-size: medium;
        }
    </style>
</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5" style="width: 90%; max-width: 100%;">
        <h4 class="text-center p-3">صندوق الصادر</h4>
          <div style="direction:rtl !important;" class="mb-3">
            <a href="home.php" class="btn btn-light w-100">الرئيسية</a>
        </div>
        
            <!-- تصنيفات الإشعارات -->
            <div class="accordion" id="outboxAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNotifications">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotifications" aria-expanded="true" aria-controls="collapseNotifications">
                            إشعارات الجلسات
                        </button>
                    </h2>
                    <div id="collapseNotifications" class="accordion-collapse collapse" aria-labelledby="headingNotifications" data-bs-parent="#outboxAccordion">
                        <div class="accordion-body" style="max-height: 600px;overflow: auto;">
                            <?php if (!empty($notifications)) { ?>
                                <div class="list-group" style="direction: rtl;">
                                <?php foreach ($notifications as $notification) { 
                                    $case_title = isset($cases[$notification['case_id']]) ? $cases[$notification['case_id']] : "تم حذفها!";
                                ?>
                                <a href="#<?php //echo $notification['case_id']; ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= $notification['recipient_email'] ? 'إيميل' : 'واتساب' ?></h5>
                                        <small><?= date('d M Y', strtotime($notification['sent_date'])) ?></small>
                                    </div>
                                    <p class="mb-1">القضية: <?= $case_title ?></p>
                                    <small><?= $notification['recipient_email'] ? $notification['recipient_email'] : $notification['recipient_phone'] ?></small>
                                </a>
                                <?php } ?>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-info mt-3" role="alert">
                                لا توجد إشعارات جلسات.
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingReminders">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReminders" aria-expanded="false" aria-controls="collapseReminders">
                            تذكيرات الاستحقاقات المالية
                        </button>
                    </h2>
                    <div id="collapseReminders" class="accordion-collapse collapse" aria-labelledby="headingReminders" data-bs-parent="#outboxAccordion">
                        <div class="accordion-body" style="max-height: 600px;overflow: auto;">
                            <?php if (!empty($reminders)) { ?>
                                <div class="list-group" style="direction: rtl;">
                                <?php foreach ($reminders as $reminder) { 
                                    $case_title = isset($cases[$reminder['case_id']]) ? $cases[$reminder['case_id']] : "تم حذفها!";
                                ?>
                                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= $reminder['type_notifcation'] ?></h5>
                                        <small><?= date('d M Y, H:i', strtotime($reminder['message_date'])) ?></small>
                                    </div>
                                    <p class="mb-1">الموكل رقم: <?= $reminder['client_id'] ?> - القضية: <?= $case_title ?></p>
                                    <small><?= $reminder['message'] ?></small>
                                </a>
                                <?php } ?>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-info mt-3" role="alert">
                                لا توجد تذكيرات استحقاقات مالية.
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTasks">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTasks" aria-expanded="false" aria-controls="collapseTasks">
                            مهام الإداريين
                        </button>
                    </h2>
                    <div id="collapseTasks" class="accordion-collapse collapse" aria-labelledby="headingTasks" data-bs-parent="#outboxAccordion">
                        <div class="accordion-body" style="max-height: 600px;overflow: auto;">
                            <?php if (!empty($tasks)) { ?>
                                <div class="list-group" style="direction: rtl;">
                                <?php foreach ($tasks as $task) { ?>
                                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= $task['lawyer'] ?></h5>
                                        <small><?= date('d M Y, H:i', strtotime($task['date_time'])) ?></small>
                                    </div>
                                    <p class="mb-1">المساعد: <?= $task['helper'] ?></p>
                                    <small><?= $task['title'] ?></small>
                                </a>
                                <?php } ?>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-info mt-3" role="alert">
                                لا توجد مهام مرسلة حتى الآن.
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            // $("#navLinks li:nth-child(7) a").addClass('active');

            // فلاتر البحث
            $('#filterSender, #filterRecipient, #filterType').on('input change', function(){
                var sender = $('#filterSender').val().toLowerCase();
                var recipient = $('#filterRecipient').val().toLowerCase();
                var type = $('#filterType').val().toLowerCase();

                $('.list-group-item').filter(function(){
                    $(this).toggle(
                        ($(this).find('.mb-1').text().toLowerCase().indexOf(sender) > -1) &&
                        ($(this).find('.small').text().toLowerCase().indexOf(recipient) > -1) &&
                        ($(this).find('.mb-1').text().toLowerCase().indexOf(type) > -1)
                    );
                });
            });
        });
    </script>
</body>
</html>
<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>
