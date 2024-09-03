<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    if(!isset($_GET['id'])){
        header('Location: powers.php');
    }
    include "../DB_connection.php";
    include "logo.php";

    try {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            // جلب البيانات من قاعدة البيانات
            $sql = "
            SELECT
                powers.power_id,
                powers.role,
                powers.office_id,
                powers.lawyer_id,
                powers.default_role,
                powers.default_role_client,
                powers.default_role_lawyer,
                powers.default_role_helper,
                powers.default_role_manager,
                offices.office_name,
                page_permissions.page_name,
                page_permissions.can_read AS page_read,
                page_permissions.can_write AS page_write,
                page_permissions.can_add AS page_add,
                page_permissions.can_delete AS page_delete
            FROM
                powers
            JOIN offices ON powers.office_id = offices.office_id
            LEFT JOIN page_permissions ON powers.power_id = page_permissions.role_id
            WHERE powers.power_id = ?
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            // تخزين النتائج في مصفوفة
            $permissions = [];
            $role_id = '';
            $role = '';
            $office_id = '';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $permissions[$row['page_name']] = [
                    'read' => $row['page_read'],
                    'write' => $row['page_write'],
                    'add' => $row['page_add'],
                    'delete' => $row['page_delete']
                ];
                $role = $row['role'];
                $office_id = $row['office_id'];
                $role_id = $row['power_id'];
                $selected_lawyers  = $row['lawyer_id'];
                $default_role = $row['default_role'];
                $default_role_client = $row['default_role_client'];
                $default_role_lawyer = $row['default_role_lawyer'];
                $default_role_helper = $row['default_role_helper'];
                $default_role_manager = $row['default_role_manager'];
            }
        } else {
            throw new Exception("ID is missing from the URL.");
        }
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // قائمة الصفحات الثابتة
    $pages = [
        'control' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'cases' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'sessions' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'calendar' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'events' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'add_old_session' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'expenses' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'expenses_sessions' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'payments' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'attachments' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'lawyers' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'clients' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'assistants' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'expense_types' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'offices' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'courts' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'case_types' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'judicial_circuits' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'documents' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'notifications' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'message_customization' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'inbox' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'outbox' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'join_requests' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'roles' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'logo_contact' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'user_management' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'import' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'adversaries' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'managers' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        'profiles' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
        
        
    ];

    // خريطة أسماء الصفحات من الإنجليزية إلى العربية
    $page_names = [
        'control' => 'لوحة التحكم',
        'cases' => 'القضايا',
        'sessions' => 'الجلسات',
        'add_old_session' => 'إضافة جلسة بتاريخ قديم',
        'expenses' => 'المصاريف',
        'expenses_sessions' => 'مصاريف الجلسات',
        'payments' => 'المدفوعات',
        'attachments' => 'المرفقات',
        'lawyers' => 'المحامين',
        'adversaries' => 'الخصوم',
        'clients' => 'الموكلين',
        'assistants' => 'الإداريين',
        'expense_types' => 'أنواع المصروفات',
        'offices' => 'المكاتب',
        'profiles' => 'صفحات المكاتب',
        'managers' => 'مدراء المكاتب',
        'courts' => 'المحاكم',
        'case_types' => 'أنواع القضايا',
        'judicial_circuits' => 'الدوائر القضائية',
        'documents' => 'الوثائق / العقود',
        'notifications' => 'الاشعارات',
        'message_customization' => 'تخصيص الرسائل',
        'inbox' => 'صندوق الوارد',
        'outbox' => 'صندوق الصادر',
        'join_requests' => 'طلبات الانضمام',
        'roles' => 'الأدوار',
        'logo_contact' => 'اعدادات الاتصال',
        'import' => 'استيراد البيانات',
        'user_management' => 'إدارة المستخدمين',
        'calendar' => 'التقويم',
        'events' => 'الأحداث',
    ];

    // دمج الأذونات المخزنة مع قائمة الصفحات الثابتة
    foreach ($permissions as $page => $perms) {
        if (isset($pages[$page])) {
            $pages[$page] = array_merge($pages[$page], $perms);
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="ar">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- <link href="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/css/selectize.default.min.css" rel="stylesheet"> -->
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
        <?php include "inc/navbar.php"; ?>
        <!-- End of NavBar -->

        <div class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr !important;">        
            <a href="users.php" class="btn btn-secondary">إدارة المستخدمين </a>  
            <a href="powers.php" class="btn btn-dark">الأدوار</a>
        </div>
        <hr>
            <form id="roleForm" method="POST">
            <div class="form-group row">
                    <div class="col-md-6">
                        <label class="mb-2" for="role_name">الاسم</label>
                        <input type="text" class="form-control" id="role_name" name="role_name" value="<?php echo htmlspecialchars($role); ?>" required>
                        <input type="hidden" class="form-control" id="role_id" name="role_id" value="<?php echo htmlspecialchars($role_id); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="mb-2" for="office_id">المكتب</label>
                        <select id="office_id" class="" name="office_id">
                            <option value="" selected>اختر المكتب</option>
                            <?php
                                $sql = "SELECT `office_id`, `office_name` FROM offices";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($row['office_id'] == $office_id) ? 'selected' : '';
                                        echo "<option value='{$row['office_id']}' {$selected}>{$row['office_name']}</option>";
                                    }
                                }
                            ?>
                            </select>
                    </div>
                    <div class="col-md-12">
                    
                        <label class="form-label mt-3" for="lawyer_id">المحامي</label>
                        <select id="lawyer_id" class="" name="lawyer_id[]" data-selected-lawyer="<?=$selected_lawyers?>" multiple>
                            <option value="" disabled>اختر المحامي</option>
                            <!-- خيارات المحامين ستُضاف هنا بواسطة JavaScript -->
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <div class="col-md-6">
                        
                        <div class="form-check custom-checkbox mb-3">
                            <input type="checkbox" class="form-check-input" id="default_role" name="default_role" value="1" <?php echo $default_role ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="default_role">دور افتراضي لطلبات الانضمام</label>
                        </div>
                        <div class="form-check custom-checkbox mb-3">
                            <input type="checkbox" class="form-check-input" id="default_role_client" name="default_role_client" value="1" <?php echo $default_role_client ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="default_role_client">دور افتراضي لإضافة عميل</label>
                        </div>
                        <div class="form-check custom-checkbox mb-3">
                            <input type="checkbox" class="form-check-input" id="default_role_lawyer" name="default_role_lawyer" value="1" <?php echo $default_role_lawyer ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="default_role_lawyer">دور افتراضي لإضافة محامي</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check custom-checkbox mb-3">
                            <input type="checkbox" class="form-check-input" id="default_role_manager" name="default_role_manager" value="1" <?php echo $default_role_manager ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="default_role_manager">دور افتراضي لإضافة مدير مكتب</label>
                        </div>
                        <div class="form-check custom-checkbox mb-3">
                            <input type="checkbox" class="form-check-input" id="default_role_helper" name="default_role_helper" value="1" <?php echo $default_role_helper ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="default_role_helper">دور افتراضي لإضافة إداري</label>
                        </div>
                    </div>
                </div>


                
                <div style="display: ruby-text;" class="mt-3">
                    <h5 style="float:right;">صلاحيات الصفحات</h5>
                    <button type="button" id="selectAll" class="btn btn-link" style="font-size:smaller;float:left;text-decoration:none;box-shadow: none;">تحديد الكل</button>
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
                            <?php foreach ($pages as $page_name => $perms): ?>
                            <tr>
                                <td><?php echo $page_names[$page_name]; ?></td>
                                <td><input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][read]" value="1" <?php echo $perms['read'] ? 'checked' : ''; ?>></td>
                                <td><input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][write]" value="1" <?php echo $perms['write'] ? 'checked' : ''; ?>></td>
                                <td><input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][add]" value="1" <?php echo $perms['add'] ? 'checked' : ''; ?>></td>
                                <td><input type="checkbox" class="form-check-input" name="permissions[<?php echo $page_name; ?>][delete]" value="1" <?php echo $perms['delete'] ? 'checked' : ''; ?>></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button id="editRole" type="submit" class="btn btn-primary">حفظ</button>
            </form>
        </div>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/js/standalone/selectize.min.js"></script>
         

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

        var formData = new FormData(document.getElementById('roleForm'));

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
      $('input[name="permissions[import][delete]"]').hide(); 
      
    });
  </script>
<script>
$(document).ready(function() {
    // تهيئة Selectize للمكتب
    $('#office_id').selectize({
        create: false,
        sortField: 'text'
    });

    // تهيئة Selectize للمحامي
    var lawyerSelect = $('#lawyer_id').selectize({
        create: false,
        sortField: 'text',
        plugins: ['remove_button']
    });

    $('#office_id').on('change', function() {
        fetchLawyers();
    });

    function fetchLawyers() {
        var officeId = $('#office_id').val();
        var lawyerSelectElement = $('#lawyer_id')[0].selectize;
        
        if (officeId) {
            // قم بعمل طلب AJAX للحصول على المحامين بناءً على office_id
            $.ajax({
                url: 'api/fetch_lawyers.php',
                type: 'GET',
                data: { office_id: officeId },
                success: function(data) {
                    var response = JSON.parse(data);
                    lawyerSelectElement.clearOptions(); // مسح الخيارات الحالية
                    lawyerSelectElement.clear(); // مسح التحديد الحالي
                    response.forEach(function(lawyer) {
                        lawyerSelectElement.addOption({value: lawyer.lawyer_id, text: lawyer.lawyer_name});
                    });

                    // إعادة تعيين القيم المختارة إذا كانت تتطابق مع المكتب الجديد
                    var selectedLawyers = lawyerSelectElement.$input.attr('data-selected-lawyer');
                    if (selectedLawyers) {
                        selectedLawyers = selectedLawyers.split(',');
                        selectedLawyers.forEach(function(lawyer_id) {
                            if (lawyerSelectElement.options.hasOwnProperty(lawyer_id)) {
                                lawyerSelectElement.addItem(lawyer_id, false);
                            }
                        });
                    }
                }
            });
        } else {
            lawyerSelectElement.clearOptions();
            lawyerSelectElement.clear();
        }
    }

    // استدعاء الدالة عند تحميل الصفحة إذا كان هناك مكتب محدد
    var officeId = $('#office_id').val();
    if (officeId) {
        fetchLawyers();
    } else {
        // إعادة تعيين القيم المختارة عند تحميل الصفحة
        var lawyerSelectElement = $('#lawyer_id')[0].selectize;
        var selectedLawyers = lawyerSelectElement.$input.attr('data-selected-lawyer');
        if (selectedLawyers) {
            selectedLawyers = selectedLawyers.split(',');
            selectedLawyers.forEach(function(lawyer_id) {
                lawyerSelectElement.addItem(lawyer_id, false);
            });
        }
    }
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
