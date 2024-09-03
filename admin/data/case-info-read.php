<?php

if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
 ?>
<?php
    // Fetch all the columns from the database table
    $query = "SELECT cases.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.phone, clients.last_name AS client_last_name 
                FROM cases 
                LEFT JOIN lawyer ON cases.lawyer_id = lawyer.lawyer_id 
                LEFT JOIN clients ON cases.client_id = clients.client_id
                WHERE case_id= :id";
                $id = $_GET['id'];

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Fetch data as associative array
    $caseData = $stmt->fetch(PDO::FETCH_ASSOC);

    $queryExp = "SELECT SUM(amount) AS total_amount FROM expenses WHERE case_id = :id";
                $id = $_GET['id'];

    $stmtExp = $conn->prepare($queryExp);
    $stmtExp->bindParam(':id', $id);
    $stmtExp->execute();

    // Fetch data as associative array
    $expData = $stmtExp->fetch(PDO::FETCH_ASSOC);



    $client_name = $caseData['client_first_name'] . ' ' . $caseData['client_last_name'];

?>



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
                            <select id="office_id" class="form-control" name="office_id" required>
                                <option value="">اختر المكتب...</option>
                                <?php
                                $sql = "SELECT * FROM offices ORDER BY office_id DESC";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $office_id = $row["office_id"];
                                        $office_name = $row["office_name"];
                                        $selected = ($office_id == $caseData['office_id'])? "selected" : "";
                                        echo "<option value='$office_id' $selected>$office_name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اسم المحامي</label>
                            <select id="lawyer_id" class="form-control" name="lawyer_id">
                                <option disabled="disabled" selected="selected"></option>
                                <?php
                                $sql = "SELECT `lawyer_id`, lawyer_name FROM lawyer";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $id = $row["lawyer_id"];
                                        $lawyer = $row["lawyer_name"];
                                        $selected = ($id == $caseData['lawyer_id']) ? "selected" : "";
                                        echo "<option value='$id' $selected>$lawyer</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اسم الموكل</label>
                            <input type="text" class="form-control" value="<?=$caseData['client_first_name'] . ' ' . $caseData['client_last_name']?>" disabled>
                            <input type="hidden" value="<?=$caseData['client_id']?>" name="client_id">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">رقم ملف القضية</label>
                            <input type="text" class="form-control" value="<?=$caseData['case_title']?>" name="case_title" id="case_title">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" style="position: relative; left:75%;" name="agency" <?php echo isset($caseData['agency']) && $caseData['agency'] === 'on' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="flexSwitchCheckChecked" style="cursor: pointer;">وكالة؟</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" id="legal-number">رقم القضية</label>
                            <input type="text" class="form-control" value="<?=$caseData['case_number']?>" name="case_number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نوع القضية</label>
                            <select id="case_type" class="form-control" name="case_type">
                                <option value="" selected disabled>اختر النوع...</option>
                                <?php
                                $sql = "SELECT * FROM types_of_cases ORDER BY id DESC";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $type_id = $row["id"];
                                        $type_case = $row["type_case"];
                                        $selected = ($type_id == $caseData['case_type'])? "selected" : "";
                                        echo "<option value='$type_id' $selected>$type_case</option>";
                                    }
                                }
                                ?>
                            </select>
                            <input type="hidden" class="form-control" value="<?=$caseData['case_id']?>" id="caseId" name="id">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" style="cursor: pointer;" for="helper_name">الإداريين</label>
                            <select id="helper_name" class="form-control" name="helper_name[]" multiple>
                                <option disabled="disabled" selected="selected"></option>
                                <?php
                                $sql = "SELECT `id`, helper_name FROM helpers";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $id = $row["id"];
                                        $helper_name = $row["helper_name"];
                                        $selectedValues = explode(',', $caseData['helper_name']);
                                        $selected = (in_array($id, $selectedValues)) ? "selected" : "";
                                        echo "<option value='$id' $selected>$helper_name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php if (!empty($caseData['id_picture'])) { ?>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" style="cursor: pointer;" for="id_picture">صورة هوية الموكل</label>
                            <img id="id_picture" class="img-fluid" src="../uploads/<?php echo $caseData['id_picture']; ?>" alt="<?php echo $caseData['id_picture']; ?>">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" style="cursor: pointer;" for="id_pictureMain">
                                <?php if (!empty($caseData['id_picture'])) { ?>
                                    تغيير صورة هوية الموكل
                                <?php } else { ?>
                                    صورة هوية الموكل
                                <?php } ?>
                            </label>
                            <input id="id_pictureMain" class="form-control" name="id_picture" type="file" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="claimantsInformation">
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
                            <select id="plaintiff" class="form-control" name="plaintiff[]" multiple>
                                <option disabled="disabled" selected="selected"></option>
                                <?php
                                $sql = "SELECT * FROM clients ORDER BY clients.client_id DESC";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $client_id = $row["client_id"];
                                        $client_name = $row["first_name"] . " " . $row["last_name"];
                                        $selectedValues = explode(',', $caseData['plaintiff']);
                                        $selected = (in_array($client_id, $selectedValues)) ? "selected" : "";
                                        echo "<option value='$client_id' $selected>$client_name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الخصم</label>
                            <select id="defendant" class="form-control" name="defendant[]" multiple>
                                <option disabled="disabled" selected="selected"></option>
                                <?php
                                $sql = "SELECT * FROM adversaries ORDER BY id DESC";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $adver_id = $row["id"];
                                        $adver_name = $row["fname"] . " " . $row["lname"];
                                        $selectedValues2 = explode(',', $caseData['defendant']);
                                        $selected2 = (in_array($adver_id, $selectedValues2)) ? "selected" : "";
                                        echo "<option value='$adver_id' $selected2>$adver_name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اسم المحكمة</label>
                            <select id="court_name" class="form-control" name="court_name">
                                <option value="" selected disabled>اختر المحكمة...</option>
                                <?php
                                $sql = "SELECT * FROM courts ORDER BY id DESC";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $court_id = $row["id"];
                                        $court_name = $row["court_name"];
                                        $selected = ($court_id == $caseData['court_name'])? "selected" : "";
                                        echo "<option value='$court_id' $selected>$court_name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الدائرة</label>
                            <select id="department" class="form-control" name="department">
                                <option value="" selected disabled>اختر الدائرة...</option>
                                <?php
                                $sql = "SELECT * FROM departments ORDER BY id DESC";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $department_id = $row["id"];
                                        $department_name = $row["type"];
                                        $selected = ($department_id == $caseData['department'])? "selected" : "";
                                        echo "<option value='$department_id' $selected>$department_name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اسم القاضي</label>
                            <input type="text" class="form-control" name="judge_name" value="<?=$caseData['judge_name']?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    
    

<?php 

} else {
    header("Location: ../cases.php");
    exit;
}
} else {
header("Location: ../cases.php");
exit;
} 
?>