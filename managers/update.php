<?php 
function usernamelIsUnique($uname, $conn, $excludeId = null, $excludeTable = null, $primaryKey = 'id') {
    $sql = "SELECT username FROM `admin` WHERE username = ? 
            UNION
            SELECT username FROM lawyer WHERE username = ? 
            UNION
            SELECT username FROM helpers WHERE username = ? 
            UNION
            SELECT username FROM clients WHERE username = ? 
            UNION
            SELECT username FROM managers_office WHERE username = ? 
            UNION
            SELECT username FROM ask_join WHERE username = ?";
    
    $params = [$uname, $uname, $uname, $uname, $uname, $uname];
    
    if ($excludeId !== null && $excludeTable !== null) {
        $sql = "SELECT * FROM ($sql) AS all_users WHERE username = ? AND NOT EXISTS (
                    SELECT 1 FROM $excludeTable WHERE $excludeTable.username = ? AND $excludeTable.$primaryKey = ?)";
        $params[] = $uname;
        $params[] = $uname;
        $params[] = $excludeId;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->rowCount() > 0 ? 0 : 1;
}
?>

<?php

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Managers') {

        include_once '../DB_connection.php';
        include 'permissions_script.php';
        if ($pages['import']['write'] == 0) {
            header("Location: home.php");
            exit();
        }

        if (isset($_POST['data']) && !empty($_POST['data']) && isset($_POST['table_name'])) {
            $data = $_POST['data'];
            $table_name = $_POST['table_name'];

            // تحديد العمود المعرّف للجدول المختار
            $primaryKeys = [
                'cases' => 'case_id',
                'admin' => 'admin_id',
                'ask_join' => 'user_id',
                'documents' => 'document_id',
                'events' => 'event_id',
                'message' => 'message_id',
                'offices' => 'office_id',
                'powers' => 'power_id',
                'sessions' => 'sessions_id',
                'clients' => 'client_id',
                'lawyer' => 'lawyer_id',
                
                // أضف المزيد حسب الحاجة
            ];

            // تحديد أسماء أعمدة كلمات السر
            $passwordFields = [
                'admin' => 'password',
                'clients' => 'password',
                'lawyer' => 'lawyer_password',
                'helpers' => 'pass',
                'managers_office' => 'manager_password',
                
                // أضف المزيد حسب الحاجة
            ];
            
            $primaryKey = isset($primaryKeys[$table_name]) ? $primaryKeys[$table_name] : 'id';
            $passwordField = isset($passwordFields[$table_name]) ? $passwordFields[$table_name] : 'password';

            echo "إجمالي الصفوف المستلمة: " . count($data) . "<br>";

            try {
                $updatedRowsCount = 0; // متغير لتتبع عدد الصفوف المحدثة
                $addedRowsCount = 0; // متغير لتتبع عدد الصفوف المضافة

                foreach ($data as $key => $row) {
                    // تحقق من وجود الحقول المطلوبة في كل سجل
                    if (!isset($row['columns'])) {
                        echo "Missing required fields for record $key:<br>";
                        if (!isset($row['columns'])) {
                            echo "- Missing 'columns' field<br>";
                        }
                        continue;
                    }

                    // استرجاع قيمة الـ ID
                    $id = isset($row[$primaryKey]) ? $row[$primaryKey] : 'new';

                    // استرجاع أسماء الأعمدة التي ستتم تحديثها أو إضافتها
                    $columns = $row['columns'];

                    // التحقق مما إذا كان السجل جديدًا أو موجودًا
                    if ($id === 'new') {
                        // التحقق من أن اسم المستخدم فريد
                        if (isset($row['username']) && usernamelIsUnique($row['username'], $conn) === 0) {
                            $key = $key +1 ;
                            echo "Username already exists for record $key .<br>";
                            continue;
                        }

                        // تشفير كلمة المرور إذا كانت موجودة
                        if (isset($row[$passwordField])) {
                            $row[$passwordField] = password_hash($row[$passwordField], PASSWORD_DEFAULT);
                        }

                        // إعداد مصفوفات الأعمدة والقيم للإضافة
                        $insertColumns = [];
                        $insertValues = [];
                        foreach ($columns as $column) {
                            if ($column !== 'columns') {
                                $insertColumns[] = $column;
                                $insertValues[] = $row[$column];
                            }
                        }

                        // إعداد الاستعلام لإضافة السجل الجديد
                        $insertColumnsStr = implode(", ", $insertColumns);
                        $placeholders = rtrim(str_repeat('?, ', count($insertColumns)), ', ');
                        $sql = "INSERT INTO $table_name ($insertColumnsStr) VALUES ($placeholders)";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute($insertValues);

                        $addedRowsCount++;
                    } else {
                        // التحقق من أن اسم المستخدم فريد باستثناء المستخدم الحالي
                        if (isset($row['username']) && usernamelIsUnique($row['username'], $conn, $id, $table_name, $primaryKey) === 0) {
                            echo "Username already exists for record $key.<br>";
                            continue;
                        }

                        // تشفير كلمة المرور إذا كانت موجودة
                        if (isset($row[$passwordField])) {
                            $row[$passwordField] = password_hash($row[$passwordField], PASSWORD_DEFAULT);
                        }

                        // التحقق مما إذا كان السجل موجودًا في الجدول
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM $table_name WHERE $primaryKey = ?");
                        $stmt->execute([$id]);
                        $rowCount = $stmt->fetchColumn();

                        // إذا كان السجل موجودًا، قم بتحديث السجل
                        if ($rowCount > 0) {
                            // إعداد مصفوفات الأعمدة والقيم للتحديث
                            $updateColumns = [];
                            $params = [];
                            foreach ($columns as $column) {
                                if ($column !== $primaryKey && $column !== 'columns') {
                                    $updateColumns[] = "$column = ?";
                                    $params[] = $row[$column];
                                }
                            }
                            $params[] = $id;

                            // إعداد الاستعلام لتحديث السجل
                            $updatesStr = implode(", ", $updateColumns);
                            $sql = "UPDATE $table_name SET $updatesStr WHERE $primaryKey = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($params);

                            $updatedRowsCount++;
                        } else {
                            echo "Record with $primaryKey = $id not found for updating.<br>";
                        }
                    }

                    // إضافة تأخير زمني لتجنب الضغط على الخادم
                    usleep(100000); // 100 ميلي ثانية
                }

                // إرسال استجابة بنجاح مع عدد الصفوف المحدثة والمضافة
                unset($_SESSION['imported_data']);
                http_response_code(200);
                exit("تم تحديث $updatedRowsCount صف بنجاح وإضافة $addedRowsCount صف جديد.");
            } catch (PDOException $e) {
                // طباعة الأخطاء التشخيصية
                echo "Error: " . $e->getMessage() . "<br>";
                http_response_code(500);
            }
        } else {
            // إذا لم يتم إرسال البيانات بشكل صحيح
            http_response_code(400);
            exit("Bad Request");
        }
    } else {
        header("Location: ../cases.php");
        exit;
    }
} else {
    header("Location: ../cases.php");
    exit;
}
?>
