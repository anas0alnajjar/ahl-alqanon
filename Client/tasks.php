<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {
        include '../DB_connection.php';
        include 'logo.php';
        include 'permissions_script.php';
        include "get_office.php";
        if ($pages['notifications']['read'] == 0) {
            header("Location: home.php");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
        $cases = getCases($conn, $user_id);
        $lawyer_id = getLawyerId($user_id, $conn);
    
        // Pagination
        $page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
        $total_records_per_page = 4;
        $offset = ($page_number - 1) * $total_records_per_page;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $caseFilter = isset($_GET['caseFilter']) ? $_GET['caseFilter'] : '';
        $dateFilter = isset($_GET['dateFilter']) ? $_GET['dateFilter'] : 'all';
    
        // Base SQL query
        $sql = "SELECT todos.*, 
                CONCAT(clients.first_name, ' ', clients.last_name) AS client_name, 
                helpers.helper_name
                FROM todos
                LEFT JOIN clients ON todos.client_id = clients.client_id
                LEFT JOIN helpers ON todos.helper_id = helpers.id
                LEFT JOIN cases ON todos.case_id = cases.case_id
                WHERE (todos.client_id = :client_id OR todos.case_id IN (SELECT case_id FROM cases WHERE client_id = :client_id OR FIND_IN_SET(:client_id, plaintiff)))";
    
        // Add filters to the query
        if (!empty($search)) {
            $sql .= " AND (todos.title LIKE :search 
                    OR clients.first_name LIKE :search
                    OR todos.task_title LIKE :search
                    OR clients.last_name LIKE :search
                    OR helpers.helper_name LIKE :search)";
        }
    
        if (!empty($caseFilter)) {
            $sql .= " AND todos.case_id = :caseFilter";
        }
    
        // Add date filter
        if ($dateFilter == 'today') {
            $sql .= " AND DATE(todos.date_time) = CURDATE()";
        } elseif ($dateFilter == 'week') {
            $sql .= " AND WEEK(todos.date_time) = WEEK(CURDATE())";
        } elseif ($dateFilter == 'month') {
            $sql .= " AND MONTH(todos.date_time) = MONTH(CURDATE())";
        }
    
        $sql .= " ORDER BY todos.id DESC LIMIT :offset, :total_records_per_page";
    
        $stmt = $conn->prepare($sql);
    
        // Bind parameters
        $stmt->bindParam(':client_id', $user_id, PDO::PARAM_INT);
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        if (!empty($caseFilter)) {
            $stmt->bindParam(':caseFilter', $caseFilter, PDO::PARAM_INT);
        }
    
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':total_records_per_page', $total_records_per_page, PDO::PARAM_INT);
    
        $stmt->execute();
        $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Count total records for pagination
        $count_sql = "SELECT COUNT(*) FROM todos
                      LEFT JOIN clients ON todos.client_id = clients.client_id
                      LEFT JOIN helpers ON todos.helper_id = helpers.id
                      LEFT JOIN cases ON todos.case_id = cases.case_id
                      WHERE (read_by_client != 1 OR read_by_client IS NULL)
                      AND (todos.client_id = :client_id OR todos.case_id IN (SELECT case_id FROM cases WHERE client_id = :client_id OR FIND_IN_SET(:client_id, plaintiff)))";
        if (!empty($search)) {
            $count_sql .= " AND (todos.title LIKE :search 
                    OR clients.first_name LIKE :search
                    OR todos.task_title LIKE :search
                    OR clients.last_name LIKE :search
                    OR helpers.helper_name LIKE :search)";
        }
        if (!empty($caseFilter)) {
            $count_sql .= " AND todos.case_id = :caseFilter";
        }
    
        if ($dateFilter == 'today') {
            $count_sql .= " AND DATE(todos.date_time) = CURDATE()";
        } elseif ($dateFilter == 'week') {
            $count_sql .= " AND WEEK(todos.date_time) = WEEK(CURDATE())";
        } elseif ($dateFilter == 'month') {
            $count_sql .= " AND MONTH(todos.date_time) = MONTH(CURDATE())";
        }
    
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bindParam(':client_id', $user_id, PDO::PARAM_INT);
        if (!empty($search)) {
            $count_stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        if (!empty($caseFilter)) {
            $count_stmt->bindParam(':caseFilter', $caseFilter, PDO::PARAM_INT);
        }
    
        
        $count_stmt->execute();
        $total_records = $count_stmt->fetchColumn();
        $total_pages = ceil($total_records / $total_records_per_page);
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    
    
    
    <style>
         .checked {
    color: #999 !important;
    text-decoration: line-through !important;
    
  }

body {
        background-color: #f8f9fa;
    }
    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .todo-item {
        transition: all 0.3s ease;
    }
    .todo-item:hover {
        background-color: #e9ecef;
    }
    .modal-content {
        direction: rtl;
    }

    .checked {
            text-decoration: line-through;
        }
        .todo-item {
            background-color: #f8f9fa;
        }
        .remove-to-do {
            cursor: pointer;
        }
        h5{
            line-height: normal;
        }
        i  {
            cursor: pointer;
        }
        .read-icon {
        font-size: 1.5em;
        color: green;
    }

    .unread-icon {
        font-size: 1.5em;
        color: gray;
        cursor: pointer;
    }

    .remove-to-do, .edit-to-do, .download-icon {
        font-size: 1.5em;
        cursor: pointer;
    }

    .remove-to-do:hover, .edit-to-do:hover, .download-icon:hover {
        color: darkred;
    }

    .action-icons {
        display: flex;
        gap: 40px;
    }

    .todo-item {
        background-color: #f9f9f9;
        transition: background-color 0.3s ease;
    }

    .todo-item:hover {
        background-color: #e9e9e9;
    }
        

    </style>
</head>
<body>


<?php 
    include "inc/navbar.php";
?>

<div class="container mt-5">
        <div class="row mt-3" style="justify-content: end;">
            <div class="" style="max-width:100%;">
                <div class="card">
                    <div class="card-header text-center py-3">
                    <form action="tasks.php" class="mt-3" method="GET" id="searchForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <select id="dateFilter" name="dateFilter" class="form-control">
                                    <option value="all" <?= ($dateFilter == 'all') ? 'selected' : '' ?>>كل الإشعارات</option>
                                    <option value="today" <?= ($dateFilter == 'today') ? 'selected' : '' ?>>إشعارات اليوم</option>
                                    <option value="week" <?= ($dateFilter == 'week') ? 'selected' : '' ?>>إشعارات الأسبوع</option>
                                    <option value="month" <?= ($dateFilter == 'month') ? 'selected' : '' ?>>إشعارات الشهر</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select id="caseFilter" name="caseFilter" class="form-control">
                                    <option value="">كل القضايا</option>
                                    <?php foreach ($cases as $case): ?>
                                        <option value="<?= htmlspecialchars($case['case_id']) ?>" <?= ($caseFilter == $case['case_id']) ? 'selected' : '' ?>><?= htmlspecialchars($case['case_title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="input-group mb-3" style="direction: ltr;">
                            <input type="text" class="form-control" name="search" placeholder="ابحث هنا..." value="<?php echo htmlentities($search); ?>">
                            <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </form>
                    </div>
                    <div class="card-body">
                        <a href="home.php" class="btn btn-light mb-3 w-100">
                            الرئيسية
                        </a>
                        <?php if ($pages['notifications']['add']) : ?>
                        <button type="button" class="btn btn-success mb-3 w-100" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            إضافة إشعار/مهمة
                        </button>
                        <?php endif; ?>
                        <div class="todo-list">
                            <?php if($total_records <= 0){ ?>
                                <div class="alert alert-info text-center">لا يوجد مهام حتى الآن</div>
                            <?php } ?>
                            <?php foreach($todos as $todo) { ?>
                                <div class="todo-item border p-3 mb-2 rounded" style="direction:rtl;text-align:right;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <?php if($todo['read_by_client']) { ?>
                                                <i class="fas fa-check-square text-success me-2 read-icon" data-todo-id="<?php echo $todo['id']; ?>" title="إبقاءها غير مقروءة" style="font-size: 1.5em;"></i> <!-- أيقونة القراءة -->
                                            <?php } else { ?>
                                                <i class="far fa-square text-secondary me-2 unread-icon" data-todo-id="<?php echo $todo['id']; ?>" title="تحديدها كمقروءة" style="font-size: 1.5em; cursor: pointer;"></i> <!-- أيقونة الغير مقروءة -->
                                            <?php } ?>
                                            <span class="task-title <?php echo $todo['read_by_client'] ? 'checked' : ''; ?> mb-0">
                                                <?php echo !empty($todo['task_title']) ? $todo['task_title'] : $todo['title']; ?>
                                            </span>
                                        </div>

                                        <div class="action-icons">
                                            <?php if (!empty($todo['task_attach'])): ?>
                                                <a href="../../uploads/<?php echo $todo['task_attach']; ?>" download class="text-primary me-2" title="تحميل الملف" style="font-size: 1.5em;">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($pages['notifications']['delete']) : ?>
                                                <span class="remove-to-do text-danger" data-todo-id="<?php echo $todo['id']; ?>" title="حذف" style="font-size: 1.5em; cursor: pointer;"><i class="fas fa-trash-alt"></i></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        <?php if (!empty($todo['task_title'])): ?>
                                            <div>وصف المهمة: <?php echo $todo['title']; ?></div> <!-- عرض الوصف كـ "وصف المهمة" -->
                                        <?php endif; ?>
                                        <div>تاريخ الإنشاء: <?php echo date('Y-m-d \في\ الساعة h:i:s A', strtotime($todo['date_time'])); ?></div>
                                    </div>
                                    <?php if ($pages['notifications']['write']) : ?>
                                    <div class="" style="direction:ltr;text-align:left;">
                                        <span class="edit-to-do text-warning me-2" data-todo-id="<?php echo $todo['id']; ?>" title="تعديل" style="font-size: 1.5em; cursor: pointer;"><i class="fas fa-edit"></i> تعديل</span>
                                    </div>
                                    <?php endif; ?>

                                    <!-- قسم لعرض حالة القراءة -->
                                    <div class="text-muted small mt-2 d-flex justify-content-start align-items-center">
                                        <?php if($todo['read_by_lawyer']) { echo '<div class="me-3"><i class="fas fa-eye text-warning"></i> تم قراءتها من قبل المحامي</div>'; } ?>
                                    </div>
                                </div>
                            <?php } ?>


                        </div>
                        <!-- Pagination -->
                        <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <!-- Previous Page -->
        <li class="page-item <?php echo ($page_number <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_number=<?php echo max(1, $page_number - 1); ?>&search=<?php echo urlencode($search); ?>&caseFilter=<?php echo urlencode($caseFilter); ?>&dateFilter=<?php echo urlencode($dateFilter); ?>">السابق</a>
        </li>

        <!-- First Page -->
        <?php if ($page_number > 3): ?>
            <li class="page-item">
                <a class="page-link" href="?page_number=1&search=<?php echo urlencode($search); ?>&caseFilter=<?php echo urlencode($caseFilter); ?>&dateFilter=<?php echo urlencode($dateFilter); ?>">1</a>
            </li>
            <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php
        $start = max(1, $page_number - 2);
        $end = min($total_pages, $page_number + 2);
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?php echo ($page_number == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?page_number=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&caseFilter=<?php echo urlencode($caseFilter); ?>&dateFilter=<?php echo urlencode($dateFilter); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Last Page -->
        <?php if ($page_number < $total_pages - 2): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
            <li class="page-item">
                <a class="page-link" href="?page_number=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&caseFilter=<?php echo urlencode($caseFilter); ?>&dateFilter=<?php echo urlencode($dateFilter); ?>"><?php echo $total_pages; ?></a>
            </li>
        <?php endif; ?>

        <!-- Next Page -->
        <li class="page-item <?php echo ($page_number >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_number=<?php echo min($total_pages, $page_number + 1); ?>&search=<?php echo urlencode($search); ?>&caseFilter=<?php echo urlencode($caseFilter); ?>&dateFilter=<?php echo urlencode($dateFilter); ?>">التالي</a>
        </li>
    </ul>
</nav>



                    </div>
                </div>
            </div>
        </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">إضافة إشعار/مهمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;left: 10px;"></button>
            </div>
            <form id="addTaskForm" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="row mb-3">
                    <input type="hidden" name="lawyer_id" id="lawers" value="<?=$lawyer_id?>">
                    <input type="hidden" name="client_id" id="client_name" value="<?=$user_id?>">

                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="task_title" class="form-label">عنوان المهمة</label>
                        <input class="form-control" type="text" name="task_title" id="task_title">
                    </div>
                    <div class="col-md-6">
                        <label for="task_attach" class="form-label">المرفق</label>
                        <input class="form-control" type="file" name="task_attach" id="task_attach">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="title" class="form-label">وصف المهمة</label>
                    <textarea class="form-control" id="title" name="title" rows="3" placeholder="وصف المهمة"></textarea>
                </div>
            </div>
            <div class="progress m-3" style="height: 25px; display: none;direction:ltr;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;height: 25px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary">إضافة</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تعديل المهمة -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content" style="max-height:80vh;overflow:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">تعديل إشعار/مهمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;left: 10px;"></button>
            </div>
            <form id="editTaskForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="edit_task_id" name="task_id">
                    <div class="row mb-3">
                        <input type="hidden" name="lawyer_id" id="edit_lawers" value="<?=$lawyer_id?>">
                        <input type="hidden" name="client_id" id="edit_client_name" value="<?=$user_id?>">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="edit_priority" class="form-label">الأولوية</label>
                            <select class="form-select" name="priority" id="edit_priority">
                                <option value="" disabled selected>اختر الأولوية...</option>
                                <option value="بأسرع وقت">بأسرع وقت</option>
                                <option value="خلال يوم" selected>خلال يوم</option>
                                <option value="خلال يومين">خلال يومين</option>
                                <option value="خلال ثلاثة">خلال ثلاثة أيام</option>
                                <option value="خلال اسبوع">خلال اسبوع</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_task_title" class="form-label">عنوان المهمة</label>
                            <input class="form-control" type="text" name="task_title" id="edit_task_title">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_task_attach" class="form-label">المرفق</label>
                            <input class="form-control" type="file" name="task_attach" id="edit_task_attach">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">وصف المهمة</label>
                        <textarea class="form-control" id="edit_title" name="title" rows="3" placeholder="وصف المهمة"></textarea>
                    </div>
                </div>
                <div class="progress m-3" style="height: 25px; display: none;direction:ltr;">
                    <div id="edit_progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;height: 25px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script src="../js/jquery-3.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
    // فتح نموذج التعديل وملء البيانات
    $('.edit-to-do').click(function() {
        var taskId = $(this).data('todo-id');
        $.get('req/fetch_task.php', { task_id: taskId }, function(data) {
            try {
                // تحقق من أن البيانات هي JSON صحيح
                var task = (typeof data === 'object') ? data : JSON.parse(data);
                
                if (task.error) {
                    alert(task.error);
                } else {
                    $('#edit_task_id').val(task.id);
                    $('#edit_lawers').val(task.lawyer_id);
                    $('#edit_helper_name').val(task.helper_id);
                    $('#edit_client_name').val(task.client_id);
                    $('#edit_priority').val(task.priority);
                    $('#edit_task_title').val(task.task_title);
                    $('#edit_title').val(task.title);

                    // التحقق مما إذا كان النموذج يُعرض بالفعل
                    console.log('Opening modal with data:', task);

                    $('#editTaskModal').modal('show');
                }
            } catch (e) {
                console.error('Failed to parse JSON', e);
                console.error('Response:', data);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Failed to fetch task data', textStatus, errorThrown);
        });
    });

    // إرسال نموذج التعديل
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        $.ajax({
            url: 'req/update_task.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total * 100;
                        $('#edit_progressBar').width(percentComplete + '%');
                        $('#edit_progressBar').html(Math.round(percentComplete) + '%');
                    }
                }, false);
                return xhr;
            },
            beforeSend: function() {
                $('#edit_progressBar').width('0%');
                $('.progress').show();
            },
            success: function(response) {
                try {
                    var res = (typeof response === 'object') ? response : JSON.parse(response);
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم تحديث المهمة بنجاح',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#editTaskModal').modal('hide');
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: res.error
                        });
                    }
                } catch (e) {
                    console.error('Failed to parse JSON', e);
                    console.error('Response:', response);
                }
            },
            complete: function() {
                $('.progress').hide();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء تحديث المهمة'
                });
                console.error('Update error', textStatus, errorThrown);
            }
        });
    });
});

</script>


<script>
   

   $(".remove-to-do").click(function(e) {
        const id = $(this).attr('data-todo-id');
        
        // عرض رسالة تأكيد باستخدام Swal
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "هل أنت متأكد أنك تريد حذف هذه المهمة؟",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفها!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إذا تم النقر على زر الحذف
                $.post('req/remove_todo.php', { id: id }, function(data) {
                    if(data !== 'error') {
                        // إعادة تحميل الصفحة بعد حذف الرسالة بنجاح
                        location.reload();
                    }
                });
            }
        });
    });

            $(".read-icon, .unread-icon").click(function(e) {
                const id = $(this).attr('data-todo-id');
                const checked = $(this).hasClass('read-icon'); // true إذا كانت الرسالة قد قرأت، وfalse إذا لم تقرأ
                $.post('req/check_todo.php', { id: id, checked: checked }, function(data) {
                    if(data !== 'error') {
                        location.reload();
                    }
                });
            });
    
</script>


<script>
    $(document).ready(function() {
        $('#addTaskForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var progressBar = $('#progressBar');
            var progressContainer = $('.progress');

            $.ajax({
                url: 'req/add_todo.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total * 100;
                            progressBar.width(percentComplete + '%');
                            progressBar.html(Math.round(percentComplete) + '%');
                        }
                    }, false);
                    return xhr;
                },
                beforeSend: function() {
                    progressContainer.show();
                    progressBar.width('0%');
                    progressBar.html('0%');
                },
                success: function(response) {
                    if (response === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم إضافة المهمة بنجاح',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#addTaskModal').modal('hide');
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'يرجى ملء وصف المهمة '
                        });
                    }
                },
                complete: function() {
                    progressContainer.hide();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء رفع الملف.'
                    });
                }
            });
        });
    });
</script>
<script>
$(document).ready(function() {
    // Function to get filter values and update the URL
    function updateFilters() {
        var dateFilter = $('#dateFilter').val();
        var caseFilter = $('#caseFilter').val();
        var search = $('input[name="search"]').val();

        var url = new URL(window.location.href);
        url.searchParams.set('dateFilter', dateFilter);
        url.searchParams.set('caseFilter', caseFilter);
        url.searchParams.set('search', search);
        window.location.href = url.toString();
    }

    // Attach change event to the filters
    $('#dateFilter').change(function() {
        updateFilters();
    });

    $('#caseFilter').change(function() {
        updateFilters();
    });

    // Handle search form submission
    $('#searchForm').submit(function(event) {
        event.preventDefault(); // Prevent form from submitting traditionally
        updateFilters();
    });
});

</script>




</body>
</html>
<?php
    } else {
        header("Location: ../../login.php");
        exit;
    } 
} else {
    header("Location: ../../logout.php");
    exit;
} 
?>
