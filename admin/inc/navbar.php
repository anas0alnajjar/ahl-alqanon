<?php
$query = "SELECT COUNT(*) as unread_count FROM todos WHERE checked != 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$unread_count = $row['unread_count'];
?>

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
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="control.php">
                        <i class="fa fa-tachometer-alt"></i> لوحة التحكم
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-briefcase"></i> إدارة 
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item nav-link" href="offices.php"><i class="fa fa-building"></i> المكاتب</a></li>
                        <li><a class="dropdown-item nav-link" href="managers.php"><i class="fa fa-briefcase"></i> مدراء المكاتب</a></li>
                        <li><a class="dropdown-item nav-link" href="cases.php"><i class="fa fa-gavel"></i> القضايا</a></li>
                        <li><a class="dropdown-item nav-link" href="genral_expenses.php"><i class="fa fa-calculator"></i> المصاريف</a></li>
                        <li><a class="dropdown-item nav-link" href="sessions.php"><i class="fa fa-calendar-alt"></i> الجلسات</a></li>
                        <li><a class="dropdown-item nav-link" href="events.php"><i class="fa fa-calendar-day"></i> الإجراءات</a></li>
                        <li><a class="dropdown-item nav-link" href="calendar.php"><i class="fa fa-calendar-check"></i> الأجندة</a></li>
                        <li><a class="dropdown-item nav-link" href="clients.php"><i class="fa fa-users"></i> الموكلين</a></li>
                        <li><a class="dropdown-item nav-link" href="lawyers.php"><i class="fa fa-user"></i> المحامين</a></li>
                        <li><a class="dropdown-item nav-link" href="helpers.php"><i class="fa fa-user-friends"></i> الإداريين</a></li>
                        <li style="display:none;"><button id="chat_ai" class="dropdown-item nav-link"><i class="fa "></i> استشر الذكاء الصنعي</button></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownTables" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-table"></i> البيانات الأساسية
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownTables">
                        <li><a class="dropdown-item nav-link" href="courts.php"><i class="fa fa-balance-scale"></i> المحاكم</a></li>
                        <li><a class="dropdown-item nav-link" href="adversaries.php"><i class="fa fa-user-times"></i> الخصوم</a></li>
                        <li><a class="dropdown-item nav-link" href="types_case.php"><i class="fa fa-file-text"></i> أنواع القضايا</a></li>
                        <li><a class="dropdown-item nav-link" href="departments_types.php"><i class="fa fa-university"></i> الدوائر القضائية</a></li>
                        <li><a class="dropdown-item nav-link" href="types.php"><i class="fa fa-list"></i> أنواع المصروفات</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="documents.php"><i class="fa fa-file-alt"></i> الوثائق/ العقود</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tasks.php" style="position: relative; display: inline-flex; align-items: center;">
                        <i class="fa fa-tasks"></i> الإشعارات
                        <?php if ($unread_count > 0): ?>
                            <span id="notification-badge"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="message.php" id="navbarDropdownMessages" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-envelope"></i> الرسائل
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMessages">
                        <li><a class="dropdown-item nav-link" href="message.php"><i class="fa fa-inbox"></i> الوارد</a></li>
                        <li><a class="dropdown-item nav-link" href="outbox.php"><i class="fa fa-paper-plane"></i> الصادر</a></li>
                        <li><a class="dropdown-item nav-link" href="templates.php"><i class="fa fa-pencil-alt"></i> تخصيص الرسائل</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="settings.php" id="navbarDropdownSettings" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-cog"></i> الاعدادات
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownSettings">
                        <li><a class="dropdown-item nav-link" href="settings.php"><i class="fa fa-cogs"></i> الشعار والاتصال</a></li>
                        <li><a class="dropdown-item nav-link" href="import_cases.php"><i class="fa fa-upload"></i> استيراد البيانات</a></li>
                        <li><a class="dropdown-item nav-link" href="profiles.php"><i class="fa fa-globe"></i> صفحات المكاتب</a></li>
                        <li><a class="dropdown-item nav-link" href="requests.php"><i class="fa fa-user-plus"></i> طلبات الانضمام</a></li>
                        <li><a class="dropdown-item nav-link" href="users.php"><i class="fa fa-users-cog"></i> إدارة المستخدمين</a></li>
                        <li><a class="dropdown-item nav-link" href="powers.php"><i class="fa fa-shield-alt"></i> الأدوار</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

  