<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    include "../DB_connection.php";
    include 'permissions_script.php';
        if ($pages['inbox']['read'] == 0) {
            header("Location: home.php");
            exit();
        }
    include "logo.php";
    include "data/message.php";
    $messages = getAllMessages($conn);
    
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - InBox</title>
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
            margin: 2%;
        }
        .accordion-flush .accordion-item .accordion-button {
    border-radius: 0;
    margin: auto;
}
    </style>
    <script>
        $(document).ready(function() {
            let typingTimer;
            const doneTypingInterval = 500; // وقت الانتظار بالمللي ثانية

            $('#searchInput').on('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            });

            $('#searchInput').on('keydown', function() {
                clearTimeout(typingTimer);
            });

            function doneTyping() {
                let query = $('#searchInput').val();
                if (query.length > 0) {
                    $.ajax({
                        url: 'req/search_messages.php',
                        type: 'GET',
                        data: { query: query },
                        success: function(response) {
                            let messages = JSON.parse(response);
                            let accordionContent = '';
                            if (messages.length > 0) {
                                messages.forEach(message => {
                                    accordionContent += `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-heading_${message.message_id}">
                                                <button style="margin:auto !important;" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse_${message.message_id}" aria-expanded="false" aria-controls="flush-collapse_${message.message_id}">
                                                    ${message.sender_full_name}
                                                    <span class="text-muted small ms-auto">${new Date(message.date_time).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                                                </button>
                                            </h2>
                                            <div id="flush-collapse_${message.message_id}" class="accordion-collapse collapse" aria-labelledby="flush-heading_${message.message_id}" data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body" style="direction: rtl;">
                                                    ${message.message}
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <div>البريد الإلكتروني: <b>${message.sender_email}</b></div>
                                                        <div style="text-wrap:nowrap;">
                                                            <button class="btn btn-success btn-sm btn-action reply-btn" data-id="${message.message_id}">رد</button>
                                                        <?php if ($pages['inbox']['add']) : ?>
                                                            <button class="btn btn-danger btn-sm btn-action delete-btn" data-id="${message.message_id}">حذف</button>
                                                        <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
                                });
                            } else {
                                accordionContent = '';
                                Swal.fire({
                                    icon: 'info',
                                    title: 'لا توجد نتائج مطابقة!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                            $('#accordionFlushExample').html(accordionContent);
                        }
                    });
                } else {
                        location.reload();
                }
            }
        });
    </script>
</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5" style="width: 90%; max-width: 100%;">
        <h4 class="text-center p-3">صندوق الوارد</h4>
        <div style="direction:rtl !important;">
            <a href="home.php" class="btn btn-light w-100">الرئيسية</a>
        </div>
        <div class="input-group mt-3 text-center" style="max-width: 100%; min-width: 80%; direction: ltr;">
            <input style="direction:rtl;" type="text" class="form-control" id="searchInput" placeholder="ابحث هنا..." onkeyup="filterTable()">
            <div class="input-group-append">
                <button style="border-radius: 0;" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            </div>
        </div>
        <hr/>
        <?php if ($messages != 0) { ?>
        <div class="accordion accordion-flush" id="accordionFlushExample">
            <?php foreach ($messages as $message) { ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-heading_<?=$message['message_id']?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse_<?=$message['message_id']?>" aria-expanded="false" aria-controls="flush-collapse_<?=$message['message_id']?>">
                        <?=$message['sender_full_name']?>
                        <span class="text-muted small ms-auto"><?=date('d M Y, H:i', strtotime($message['date_time']))?></span>
                    </button>
                </h2>
                <div id="flush-collapse_<?=$message['message_id']?>" class="accordion-collapse collapse" aria-labelledby="flush-heading_<?=$message['message_id']?>" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body" style="direction: rtl;">
                        <?=$message['message']?>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>البريد الإلكتروني: <b><?=$message['sender_email']?></b></div>
                            <div style="text-wrap:nowrap;">
                                <button class="btn btn-success btn-sm btn-action reply-btn" data-id="<?=$message['message_id']?>">رد</button>
                                <button class="btn btn-danger btn-sm btn-action delete-btn" data-id="<?=$message['message_id']?>">حذف</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="alert alert-info w-100 mt-5" role="alert">
            فارغ!
        </div>
        <?php } ?>
    </div>
    <!-- نافذة الرد -->
    <div style="direction:rtl;" class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="replyModalLabel">الرد على الرسالة</h5>
                    <button style="position: absolute;left: 5%;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <form id="replyForm">
                        <div class="mb-3">
                            <label for="replyMessage" class="form-label">رسالتك</label>
                            <textarea class="form-control" id="replyMessage" rows="3" required></textarea>
                        </div>
                        <input type="hidden" id="replyMessageId">
                        <button type="submit" class="btn btn-primary">إرسال الرد</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            // $("#navLinks li:nth-child(7) a").addClass('active');

            // Handle delete message
            $('.delete-btn').on('click', function(){
                const messageId = $(this).data('id');
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن هذا!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، احذفها!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'req/delete_message.php',
                            type: 'POST',
                            data: { message_id: messageId },
                            success: function(response) {
                              console.log(response); 
                              Swal.fire(
                                    'تم الحذف!',
                                    'تم حذف الرسالة بنجاح.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'خطأ!',
                                    'حدث خطأ، يرجى المحاولة مرة أخرى.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Handle reply to message
            $('.reply-btn').on('click', function(){
                const messageId = $(this).data('id');
                $('#replyMessageId').val(messageId);
                $('#replyModal').modal('show');
            });

            // Handle reply form submission
            $('#replyForm').on('submit', function(event){
                event.preventDefault();
                const messageId = $('#replyMessageId').val();
                const replyMessage = $('#replyMessage').val();

                $.ajax({
                    url: 'reply_message.php',
                    type: 'POST',
                    data: { message_id: messageId, reply_message: replyMessage },
                    success: function(response) {
                        Swal.fire(
                            'تم الإرسال!',
                            'تم إرسال الرد بنجاح.',
                            'success'
                        ).then(() => {
                            $('#replyModal').modal('hide');
                            $('#replyForm')[0].reset();

                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ، يرجى المحاولة مرة أخرى.',
                            'error'
                        );
                    }
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
