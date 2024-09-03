<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
    if(!isset($_GET['id'])){
        header('Location: powers.php');
    }
    include "../DB_connection.php";
    include "get_office.php";
    include 'permissions_script.php';
    if ($pages['roles']['read'] == 0) {
        header("Location: home.php");
        exit();
    }
    $allow = $pages['roles']['write'];
    include "logo.php";

    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);

    try {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            $sql = "
            SELECT
                powers.power_id,
                powers.role,
                powers.office_id,
                offices.office_name,
                page_permissions.page_name,
                page_permissions.can_read AS page_read,
                page_permissions.can_write AS page_write,
                page_permissions.can_add AS page_add,
                page_permissions.can_delete AS page_delete,
                page_permissions.id AS permission_id
            FROM
                powers
            JOIN offices ON powers.office_id = offices.office_id
            LEFT JOIN page_permissions ON powers.power_id = page_permissions.role_id
            WHERE powers.power_id = ?
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            $editPermissions = [];
            $role_id = '';
            $role = '';
            $office_id = '';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $editPermissions[$row['page_name']] = [
                    'read' => $row['page_read'],
                    'write' => $row['page_write'],
                    'add' => $row['page_add'],
                    'delete' => $row['page_delete'],
                    'permission_id' => $row['permission_id']
                ];
                $role = $row['role'];
                $office_id = $row['office_id'];
                $role_id = $row['power_id'];
            }
        } else {
            throw new Exception("ID is missing from the URL.");
        }
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    $editPages = [
        'control' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'cases' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'sessions' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'add_old_session' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'expenses_sessions' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'payments' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'attachments' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'clients' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'documents' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'notifications' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
        'adversaries' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0, 'permission_id' => null],
    ];

        // خريطة أسماء الصفحات من الإنجليزية إلى العربية
        $page_names = [
            'control' => 'لوحة التحكم',
            'cases' => 'القضايا',
            'sessions' => 'الجلسات',
            'add_old_session' => 'إضافة جلسة بتاريخ قديم',
            'expenses_sessions' => 'مصاريف الجلسات',
            'payments' => 'المدفوعات',
            'attachments' => 'المرفقات',
            'adversaries' => 'الخصوم',
            'clients' => 'الموكلين',
            'documents' => 'الوثائق / العقود',
            'notifications' => 'الاشعارات',
        ];

    foreach ($editPermissions as $editPage => $editPerms) {
        if (isset($editPages[$editPage])) {
            $editPages[$editPage] = array_merge($editPages[$editPage], $editPerms);
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="ar">
    <head>
        <meta charset="UTF-8">
        <title>Admin - Edit Powers</title>
        <link rel="icon" href="../img/<?=$setting['logo']?>">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

        <link rel="stylesheet" href="../css/style.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <style>
            * {
                direction: rtl !important;
            }
            input[type="checkbox"] {
                appearance: none;
                width: 20px;
                height: 20px;
                background-color: #fff;
                border: 1px solid #ced4da;
                border-radius: 3px;
                cursor: pointer;
                vertical-align: middle;
            }

            input[type="checkbox"]:checked {
                background-color: #007bff;
            }

            thead th {
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <!-- Nav bar -->
        <?php include "inc/footer.php"; ?>
        <?php include "inc/navbar.php"; ?>
        
        <!-- End of NavBar -->

        <div class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr !important;">      
        <?php if ($pages['user_management']['read']) : ?>  
            <a href="users.php" class="btn btn-secondary">إدارة المستخدمين </a>  
        <?php endif; ?>
            <a href="powers.php" class="btn btn-dark">الأدوار</a>
        </div>
        <hr>
            <form id="roleFormEdit" method="POST">
                <div class="form-group row">
                    <div class="col-md-12">
                    <label class="mb-2" for="role_name">الاسم</label>
                    <input type="text" class="form-control" id="role_name" name="role_name" value="<?php echo htmlspecialchars($role); ?>" required>
                    <input type="hidden" class="form-control" id="role_id" name="role_id" value="<?php echo htmlspecialchars($role_id); ?>" required>
                    <input type="hidden" class="form-control" id="office_id" name="office_id" value="<?php echo htmlspecialchars($OfficeId); ?>" required>
                    </div>
                </div>
                
                <div style="display: ruby-text;" class="mt-3">
                    <h5 style="float:right;">صلاحيات الصفحات</h5>
                    <?php if ($allow) : ?>
                        <button type="button" id="selectAll" class="btn btn-link" style="font-size:smaller;float:left;text-decoration:none;box-shadow: none;">تحديد الكل</button>
                    <?php endif; ?>
                </div>
                <div class="table-responsive" style="position:relative;max-height: 400px;overflow-y:auto;scrollbar-width: thin;">
                    <table class="table table-hover">
                        <thead style="position: sticky; top: -1px; background: #eee; z-index: 1;">
                                        <tr>
                                            <th>الصفحة</th>
                                            <th>
                                                <input type="checkbox" id="checkAllRead" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 1)">
                                                قراءة
                                                
                                            </th>
                                            <th>
                                                <input type="checkbox" id="checkAllEdit" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 2)">
                                                تعديل
                                                
                                            </th>
                                            <th>
                                                <input type="checkbox" id="checkAllCreate" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 3)">
                                                إنشاء
                                                
                                            </th>
                                            <th>
                                                <input type="checkbox" id="checkAllDelete" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 4)">
                                                حذف
                                                
                                            </th>
                                        </tr>
                                    </thead>
                    <tbody>
                            <?php foreach ($editPages as $page_name => $editPerms): ?>
                            <tr>
                                <td><?php echo $page_names[$page_name]; ?></td>
                                <td>
                                    <input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][read]" value="1" <?php echo $editPerms['read'] ? 'checked' : ''; ?> id="read_<?php echo $editPerms['permission_id']; ?>">
                                </td>
                                <td>
                                    <input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][write]" value="1" <?php echo $editPerms['write'] ? 'checked' : ''; ?> id="write_<?php echo $editPerms['permission_id']; ?>">
                                </td>
                                <td>
                                    <input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][add]" value="1" <?php echo $editPerms['add'] ? 'checked' : ''; ?> id="add_<?php echo $editPerms['permission_id']; ?>">
                                </td>
                                <td>
                                    <input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][delete]" value="1" <?php echo $editPerms['delete'] ? 'checked' : ''; ?> id="delete_<?php echo $editPerms['permission_id']; ?>">
                                </td>
                                <input type="hidden" name="permissions[<?php echo $page_name; ?>][permission_id]" value="<?php echo $editPerms['permission_id']; ?>">
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($allow) : ?>
                    <button id="editRole" type="submit" class="btn btn-primary">حفظ</button>
                <?php endif; ?>
                
            </form>
        </div>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
    document.addEventListener('DOMContentLoaded', function() {
        // وظيفة لتحديد أو إلغاء تحديد جميع الخانات في عمود معين
        function toggleColumnCheckboxes(headerCheckbox, columnIndex) {
            const checkboxes = document.querySelectorAll(`tbody tr td:nth-child(${columnIndex + 1}) input[type="checkbox"]`);
            const visibleCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.offsetParent !== null); // استبعاد الخانات المخفية
            const allChecked = visibleCheckboxes.every(checkbox => checkbox.checked);
            visibleCheckboxes.forEach(checkbox => {
                if (!checkbox.disabled) { // تجنب تغيير حالة الخانات المؤمنة
                    checkbox.checked = !allChecked;
                }
            });
            updateHeaderCheckbox(columnIndex); // تحديث حالة الـ checkbox في الرأس
        }

        // وظيفة لتحديد جميع الخانات
        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            const visibleCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.offsetParent !== null); // استبعاد الخانات المخفية
            const allChecked = visibleCheckboxes.every(checkbox => checkbox.checked);
            visibleCheckboxes.forEach(checkbox => {
                if (!checkbox.disabled) { // تجنب تغيير حالة الخانات المؤمنة
                    checkbox.checked = !allChecked;
                }
            });
            this.textContent = allChecked ? 'تحديد الكل' : 'إلغاء تحديد الكل';
            updateAllHeaderCheckboxes(); // تحديث حالة جميع الـ checkboxes في الرأس
        });

        // إضافة مستمع للأحداث لكل خانة checkbox في الجسم لتحديث حالة الـ checkbox في الرأس
        document.querySelectorAll('thead input[type="checkbox"]').forEach(headerCheckbox => {
            const columnIndex = headerCheckbox.closest('th').cellIndex;
            headerCheckbox.addEventListener('change', function() {
                toggleColumnCheckboxes(this, columnIndex);
            });
        });

        // إضافة مستمع للأحداث لكل خانة checkbox في الجسم لتحديث حالة الـ checkbox في الرأس عند تغيير حالته
        document.querySelectorAll('tbody input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const columnIndex = this.closest('td').cellIndex;
                updateHeaderCheckbox(columnIndex); // تحديث حالة الـ checkbox في الرأس
            });
        });

        // وظيفة لتحديث حالة الـ checkbox في الرأس بناءً على حالة جميع الخانات في العمود
        function updateHeaderCheckbox(columnIndex) {
            const checkboxes = document.querySelectorAll(`tbody tr td:nth-child(${columnIndex + 1}) input[type="checkbox"]`);
            const visibleCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.offsetParent !== null); // استبعاد الخانات المخفية
            const allChecked = visibleCheckboxes.every(cb => cb.checked);
            const headerCheckbox = document.querySelector(`thead th:nth-child(${columnIndex + 1}) input[type="checkbox"]`);
            headerCheckbox.checked = allChecked;
        }

        // وظيفة لتحديث حالة جميع الـ checkboxes في الرأس
        function updateAllHeaderCheckboxes() {
            document.querySelectorAll('thead th').forEach((th, index) => {
                if (index > 0) { // تخطي العمود الأول لأنه يحتوي على أسماء الصفحات
                    updateHeaderCheckbox(index);
                }
            });
        }

        // تحديث حالة جميع الـ checkboxes في الرأس عند تحميل الصفحة
        updateAllHeaderCheckboxes();
    });
</script>

    <script>
    $(document).ready(function(){
    $('#editRole').on('click', function(event){
        event.preventDefault(); // منع إعادة تحميل الصفحة

        var roleName = $('#role_name').val();
        if (roleName === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد اسم الدور',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }
       
        var office_id = $('#office_id').val();
        if (office_id === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد مكتب',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }

        var formData = new FormData(document.getElementById('roleFormEdit'));

        $.ajax({
            url: 'req/edit_role.php',
            type: 'POST',
            data: formData,
            processData: false, // عدم معالجة البيانات (تجعلها سلسلة استعلام)
            contentType: false, // عدم تعيين نوع المحتوى
            success: function(response){
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح',
                        text: jsonResponse.message
                    }).then(function(){
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: jsonResponse.message
                    });
                }
            },
            error: function(){
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء حفظ البيانات'
                });
            }
        });
    });
});

    </script>
    
  <script>
    $(document).ready(function() {
      // إخفاء جميع العناصر التي لها name = عنصر_مخفي
      $('input[name="permissions[control][write]"]').hide(); 
      $('input[name="permissions[control][add]"]').hide(); 
      $('input[name="permissions[control][delete]"]').hide(); 
      $('input[name="permissions[inbox][write]"]').hide(); 
      $('input[name="permissions[inbox][add]"]').hide(); 
      $('input[name="permissions[inbox][delete]"]').hide(); 
      $('input[name="permissions[outbox][write]"]').hide(); 
      $('input[name="permissions[outbox][add]"]').hide(); 
      $('input[name="permissions[outbox][delete]"]').hide(); 
      $('input[name="permissions[add_old_session][write]"]').hide(); 
      $('input[name="permissions[add_old_session][delete]"]').hide(); 
      $('input[name="permissions[add_old_session][read]"]').hide(); 
      $('input[name="permissions[attachments][write]"]').hide(); 
      $('input[name="permissions[join_requests][write]"]').hide(); 
      $('input[name="permissions[logo_contact][add]"]').hide(); 
      $('input[name="permissions[logo_contact][delete]"]').hide(); 
      
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
