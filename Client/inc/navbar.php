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
                WHERE read_by_client != 1 OR read_by_client IS NULL
                AND (client_id = :client_id 
                    OR case_id IN (SELECT case_id FROM cases WHERE client_id = :client_id OR FIND_IN_SET(:client_id, plaintiff)))";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':client_id', $user_id, PDO::PARAM_INT);
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
                <?php if ($pages['adversaries']['read'] || $pages['cases']['read'] || $pages['sessions']['read'] || $pages['clients']['read'] || $pages['assistants']['read']) : ?>
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
                            <?php if ($pages['adversaries']['read']) : ?>
                                <li><a class="dropdown-item nav-link" href="adversaries.php"><i class="fa fa-user-times"></i> الخصوم</a></li>
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
    