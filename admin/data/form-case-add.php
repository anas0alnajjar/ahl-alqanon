<?php 
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
?>
<form id="myForm" class="shadow p-3 mt-5" action="" enctype="multipart/form-data" method="post">
    <h3>إضافة قضية</h3>
    <hr />
    <ul id="myTabs" class="nav nav-tabs">
        <li class="nav-item">
            <button id="case-info-tab" class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#case-info">المعلومات الرئيسية</button>
        </li>
    </ul>
    <div id="myTabsContent" class="tab-content mt-1">
        <div id="case-info" class="tab-pane fade show active">
            <div id="accordionMain">
                <div class="card">
                    <div id="heading1" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse55">القضية/الموكل</button>
                        </h2>
                    </div>
                    <div id="collapse55" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المكتب</label>
                                        <select id="office_id" class="" name="office_id" required>
                                            <option value="">اختر المكتب...</option>
                                            <?php
                                            $sql = "SELECT * FROM offices ORDER BY office_id DESC";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $office_id = $row["office_id"];
                                                    $office_name = $row["office_name"];
                                                    echo "<option value='$office_id'>$office_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم المحامي</label>
                                        <select id="lawyer_id" class="" name="lawyer_id" required>
                                            <option value="">اختر المحامي...</option>
                                            <?php
                                            $lawyer_id_session = isset($_SESSION['lawyer_id']) ? $_SESSION['lawyer_id'] : '';
                                            $sql = "SELECT * FROM lawyer ORDER BY lawyer_id";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $lawyer_id = $row["lawyer_id"];
                                                    $lawyer_name = $row["lawyer_name"];
                                                    $lawyer_email = $row["lawyer_email"];
                                                    $selected = ($lawyer_id == $lawyer_id_session) ? 'selected' : '';
                                                    echo "<option value='$lawyer_id' data-client-email='$lawyer_email' $selected>$lawyer_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم الموكل</label>
                                        <select id="client_id" class="" name="client_id" required>
                                            <option value="">اختر موكلاً...</option>
                                            <?php
                                            $client_id_session = isset($_SESSION['client_name']) ? $_SESSION['client_name'] : '';
                                            $sql = "SELECT * FROM clients ORDER BY clients.client_id";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $client_id = $row["client_id"];
                                                    $client_name = $row["first_name"] . " " . $row["last_name"];
                                                    $client_email = $row["email"];
                                                    $selected = ($client_id == $client_id_session) ? 'selected' : '';
                                                    echo "<option value='$client_id' data-client-email='$client_email' $selected>$client_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <button  style="font-size: smaller;float: left;text-decoration: none;padding: 0;" id="editClientBtn" type="button" class="btn btn-link mt-2">إضافة موكل</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">رقم ملف القضية</label>
                                        <input type="text" class="form-control" name="case_title" id="case_title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" style="position: relative;left:75%;" name="agency" <?php echo isset($_SESSION['agency']) && $_SESSION['agency'] === 'on' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" style="cursor:pointer;" for="flexSwitchCheckChecked">وكالة؟</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label id="legal-number" class="form-label">رقم القضية</label>
                                        <input type="text" class="form-control" name="case_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">نوع القضية</label>
                                        <select id="case_type" class="" name="case_type">
                                            <option value="" selected disabled >اختر النوع...</option>
                                            <?php
                                            $sql = "SELECT * FROM types_of_cases ORDER BY id DESC";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $type_id = $row["id"];
                                                    $type_case = $row["type_case"];
                                                    echo "<option value='$type_id'>$type_case</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="cursor: pointer;" for="id_picture">صورة هوية الموكل</label>
                                        <input id="id_picture" class="form-control" name="id_picture" type="file" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="heading2" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse2">المدعي/المدعى عليه</button>
                        </h2>
                    </div>
                    <div id="collapse2" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المدعي</label>   
                                        <select id="plaintiff" class="" name="plaintiff[]" multiple>
                                            <option value="" selected disabled>اختر موكلاً...</option>
                                            <?php
                                            $sql = "SELECT * FROM clients ORDER BY clients.client_id DESC";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $client_id = $row["client_id"];
                                                    $client_name = $row["first_name"] . " " . $row["last_name"];
                                                    echo "<option value='$client_id'>$client_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الخصم</label>
                                        <select id="defendant" class="" name="defendant[]" multiple>
                                            <option value="" selected disabled>اختر...</option>
                                            <?php
                                            $sql = "SELECT * FROM adversaries ORDER BY id DESC";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $adver_id = $row["id"];
                                                    $adver_name = $row["fname"] . " " . $row["lname"];
                                                    echo "<option value='$adver_id'>$adver_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم المحكمة</label>
                                        <select id="court_name" class="" name="court_name">
                                            <option value="" selected disabled> اختر المحكمة...</option>
                                            <?php
                                            $sql = "SELECT * FROM courts ORDER BY id DESC";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $court_id = $row["id"];
                                                    $court_name = $row["court_name"];
                                                    echo "<option value='$court_id'>$court_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الدائرة</label>
                                        <select id="department" class="" name="department">
                                            <option value="" selected disabled> اختر الدائرة...</option>
                                            <?php
                                            $sql = "SELECT * FROM departments ORDER BY id DESC";
                                            $result = $conn->query($sql);
                                            if ($result->rowCount() > 0) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $department_id = $row["id"];
                                                    $department_name = $row["type"];
                                                    echo "<option value='$department_id'>$department_name</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم القاضي</label>
                                        <input type="text" class="form-control" name="judge_name">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="heading3" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse3">الجلسات</button>
                            
                        </h2>
                    </div>
                    <div id="collapse3" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3" style="max-width: 100%; overflow-x: auto; scrollbar-width: thin; max-height: 600px;">
                                        <div id="dynamic_cards" class="row">
                                            <!-- البطاقات ستتم إضافتها هنا بواسطة JavaScript -->
                                        </div>
                                        <div class="text-left">
                                            
                                        </div>
                                    </div>
                                    <button type="button" id="addRowBtn" class="btn btn-dark btn-sm m-2" style="float: left;">إضافة جلسة</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="heading4" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse4">الملاحظات/وصف القضية</button>
                        </h2>
                    </div>
                    <div id="collapse4" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ملاحظات</label>
                                        <textarea class="form-control" name="notes" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">وصف القضية</label>
                                        <textarea id="editor" class="form-control" rows="3" name="case_description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="floating-button">
        <button type="submit" class="btn btn-primary" id="addCase">
            <i class="fas fa-save"></i> <!-- استخدام أيقونة من Font Awesome على سبيل المثال -->
        </button>
    </div>
    
</form>


<?php 

} else {
    header("Location: ../../login.php");
    exit;
}
} else {
header("Location: ../../login.php");
exit;
} 
?>