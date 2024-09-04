<?php
    include "get_office.php";

    // الحصول على معرف المستخدم من الجلسة
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);

    // إذا لم يكن هناك مكاتب، تعيين معرف غير موجود لتجنب الخطأ
    if (empty($OfficeId)) {
        $OfficeId = 0; // تعيين قيمة 0 لتجنب الأخطاء
    }

    // استعلام لجلب عدد المهام غير المقروءة المرتبطة بالآدمن باستخدام استعلام مُحَضّر
    $query = "SELECT COUNT(*) AS count 
                FROM todos 
                WHERE read_by_lawyer IS NULL
                AND (lawyer_id = :lawyer_id 
                    OR case_id IN (SELECT case_id FROM cases WHERE lawyer_id = :lawyer_id))";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $unread_count = $row['count']; // تغيير اسم الحقل هنا للوصول إلى الحقل الصحيح
?>

 <?php include 'footer.php'; ?>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid" style="direction: rtl;text-wrap: nowrap !important;">
        <a class="navbar-brand" href="home.php">
            <img src="../img/<?=$setting['logo']?>" width="40" alt="OurLogo" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0" id="navLinks">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="home.php">
                            <i class="fa fa-home"></i> الرئيسية
                        </a>
                    </li>
                <?php if ($pages['control']['read']) : ?>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="control.php">
                            <i class="fa fa-tachometer-alt"></i> لوحة التحكم
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($pages['adversaries']['read'] || $pages['cases']['read'] || $pages['sessions']['read'] || $pages['clients']['read'] || $pages['assistants']['read'] || $pages['calendar']['read'] || $pages['events']['read']) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-briefcase"></i> إدارة 
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php if ($pages['cases']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="cases.php"><i class="fa fa-gavel"></i> القضايا</a></li>
                            <?php endif; ?>
                            <?php if ($pages['sessions']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="sessions.php"><i class="fa fa-calendar-alt"></i> الجلسات</a></li>
                            <?php endif; ?>
                            <?php if ($pages['calendar']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="calendar.php"><i class="fa fa-calendar-check"></i> الأجندة</a></li>
                            <?php endif; ?>
                            <?php if ($pages['events']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="events.php"><i class="fa fa-calendar-day"></i> الإجراءات</a></li>
                            <?php endif; ?>
                            <?php if ($pages['clients']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="clients.php"><i class="fa fa-users"></i> الموكلين</a></li>
                            <?php endif; ?>
                            <?php if ($pages['assistants']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="helpers.php"><i class="fa fa-user-friends"></i> الإداريين</a></li>
                            <?php endif; ?>
                            <?php if ($pages['adversaries']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="adversaries.php"><i class="fa fa-user-times"></i> الخصوم</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($pages['courts']['read'] || $pages['case_types']['read'] || $pages['judicial_circuits']['read'] || $pages['expense_types']['read'] || $pages['expenses']['read']) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle basic_info" href="#" id="navbarDropdownTables" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-table"></i> بيانات المكتب
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownTables">
                            <?php if ($pages['courts']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="courts.php"><i class="fa fa-balance-scale"></i> المحاكم</a></li>
                            <?php endif; ?>
                            <?php if ($pages['case_types']['read']) : ?>                                
                                <li><a class="dropdown-item nav-link" href="types_case.php"><i class="fa fa-file-text"></i> أنواع القضايا</a></li>
                            <?php endif; ?>
                            <?php if ($pages['judicial_circuits']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="departments_types.php"><i class="fa fa-university"></i> الدوائر القضائية</a></li>
                            <?php endif; ?>
                            <?php if ($pages['expense_types']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="types.php"><i class="fa fa-list"></i> أنواع المصروفات</a></li>
                            <?php endif; ?>
                            <?php if ($pages['expenses']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="genral_expenses.php"><i class="fa fa-calculator"></i> المصاريف</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($pages['documents']['read']) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="documents.php"><i class="fa fa-file-alt"></i> الوثائق/ العقود</a>
                    </li>
                <?php endif; ?>
                <?php if ($pages['notifications']['read']) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="tasks.php" style="position: relative; display: inline-flex; align-items: center;">
                            <i class="fa fa-tasks"></i> الإشعارات
                            <?php if ($unread_count > 0): ?>
                                <span id="notification-badge"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($pages['outbox']['read'] || $pages['message_customization']['read']) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="message.php" id="navbarDropdownMessages" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-envelope"></i> الرسائل
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMessages">
                            <?php if ($pages['outbox']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="outbox.php"><i class="fa fa-paper-plane"></i> الصادر</a></li>
                            <?php endif; ?>
                            <?php if ($pages['message_customization']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="templates.php"><i class="fa fa-pencil-alt"></i> تخصيص الرسائل</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($pages['logo_contact']['read'] || $pages['user_management']['read'] || $pages['roles']['read']) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="settings.php" id="navbarDropdownSettings" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-cog"></i> الاعدادات
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownSettings">
                            <?php if ($pages['logo_contact']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="settings.php"><i class="fa fa-cogs"></i> الشعار والاتصال</a></li>
                            <?php endif; ?>
                            <?php if ($pages['profiles']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="profiles.php"><i class="fa fa-globe"></i> صفحات المكاتب</a></li>
                            <?php endif; ?>
                            <?php if ($pages['user_management']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="users.php"><i class="fa fa-users-cog"></i> إدارة المستخدمين</a></li>
                            <?php endif; ?>
                            <?php if ($pages['roles']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="powers.php"><i class="fa fa-shield-alt"></i> الأدوار</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0" id="navLinks">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLanguages" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-language"></i> اللغة
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownLanguages">
                        <li><a class="dropdown-item" href="#" onclick="changeLanguage('en')">English</a></li>
                        <li><a class="dropdown-item" href="#" onclick="changeLanguage('ar')">العربية</a></li>
                        <li><a class="dropdown-item" href="#" onclick="changeLanguage('fr')">Français</a></li>
                    </ul>
                </li>
            </ul>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
function changeLanguage(lang) {
    // إرسال طلب AJAX لتحديث اللغة في قاعدة البيانات
    $.ajax({
        url: '../update_language.php', // ملف PHP لتحديث اللغة
        type: 'POST',
        data: { language: lang },
        success: function(response) {
            // تحديث الصفحة بعد تغيير اللغة
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error updating language:', error);
        }
    });
}
</script>



  