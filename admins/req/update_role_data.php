<?php 
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        require '../../DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $role_name = $_POST['role_name'];
    $canread = 1;
    $canwrite = $_POST['edit_canwrite'];
    $candelete = $_POST['edit_candelete'];
    $canadd = $_POST['edit_canadd'];
    $home = $_POST['home'];
    $control = $_POST['control'];
    $cases = $_POST['cases'];
    $sessions = $_POST['sessions'];
    $lawyers = $_POST['lawyers'];
    $clients = $_POST['clients'];
    $helpers = $_POST['helpers'];
    $types = $_POST['types'];
    $documents = $_POST['documents'];
    $tasks = $_POST['tasks'];
    $message = $_POST['message'];
    $outbox = $_POST['outbox'];
    $requests = $_POST['requests'];
    $powers = $_POST['powers'];
    $case_info = $_POST['case_info'];
    $claimantsInformation = $_POST['claimantsInformation'];
    $sessionsInfo = $_POST['sessionsInfo'];
    $expensesInfo = $_POST['expensesInfo'];
    $paymentInfo = $_POST['paymentInfo'];
    $attachmentsInfo = $_POST['attachmentsInfo'];
    $descriptionInfo = $_POST['descriptionInfo'];
    $actionInfo = $_POST['actionInfo'];
    $power_id = $_POST['power_id'];
    
    


    if(isset($_POST['office_id'])) {
    $office_id = $_POST['office_id'];
    $sql = "UPDATE powers SET 
                `role` = :role_name, 
                `read` = :canread, 
                `write` = :canadd, 
                edit = :canwrite, 
                `delete` = :candelete,  
                home = :home, 
                `control` = :control, 
                cases = :cases, 
                `sessions` = :sessions, 
                lawyers = :lawyers, 
                clients = :clients, 
                helpers = :helpers, 
                types = :types, 
                documents = :documents, 
                tasks = :tasks, 
                `message` = :message, 
                outbox = :outbox, 
                requests = :requests, 
                powers = :powers, 
                case_info = :case_info, 
                cliam_info = :claimantsInformation, 
                session_info = :sessionsInfo, 
                exp_info = :expensesInfo, 
                pay_info = :paymentInfo, 
                attach_info = :attachmentsInfo, 
                desc_info = :descriptionInfo, 
                action_info = :actionInfo,
                office_id = :office_id 
                        WHERE power_id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':role_name', $role_name, PDO::PARAM_STR);
        $stmt->bindParam(':canread', $canread, PDO::PARAM_INT);
        $stmt->bindParam(':canwrite', $canwrite, PDO::PARAM_INT);
        $stmt->bindParam(':candelete', $candelete, PDO::PARAM_INT);
        $stmt->bindParam(':canadd', $canadd, PDO::PARAM_INT);
        $stmt->bindParam(':home', $home, PDO::PARAM_INT);
        $stmt->bindParam(':control', $control, PDO::PARAM_INT);
        $stmt->bindParam(':cases', $cases, PDO::PARAM_INT);
        $stmt->bindParam(':sessions', $sessions, PDO::PARAM_INT);
        $stmt->bindParam(':lawyers', $lawyers, PDO::PARAM_INT);
        $stmt->bindParam(':clients', $clients, PDO::PARAM_INT);
        $stmt->bindParam(':helpers', $helpers, PDO::PARAM_INT);
        $stmt->bindParam(':types', $types, PDO::PARAM_INT);
        $stmt->bindParam(':documents', $documents, PDO::PARAM_INT);
        $stmt->bindParam(':tasks', $tasks, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_INT);
        $stmt->bindParam(':outbox', $outbox, PDO::PARAM_INT);
        $stmt->bindParam(':requests', $requests, PDO::PARAM_INT);
        $stmt->bindParam(':powers', $powers, PDO::PARAM_INT);
        $stmt->bindParam(':case_info', $case_info, PDO::PARAM_INT);
        $stmt->bindParam(':claimantsInformation', $claimantsInformation, PDO::PARAM_INT);
        $stmt->bindParam(':sessionsInfo', $sessionsInfo, PDO::PARAM_INT);
        $stmt->bindParam(':expensesInfo', $expensesInfo, PDO::PARAM_INT);
        $stmt->bindParam(':paymentInfo', $paymentInfo, PDO::PARAM_INT);
        $stmt->bindParam(':attachmentsInfo', $attachmentsInfo, PDO::PARAM_INT);
        $stmt->bindParam(':descriptionInfo', $descriptionInfo, PDO::PARAM_INT);
        $stmt->bindParam(':actionInfo', $actionInfo, PDO::PARAM_INT);
        $stmt->bindParam(':office_id', $office_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $power_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "تم تحديث البيانات بنجاح";
    } catch (PDOException $e) {
        echo "خطأ في تحديث البيانات: " . $e->getMessage();
    }
}else {

    $sql = "UPDATE powers SET 
            `role` = :role_name, 
            `read` = :canread, 
            `write` = :canadd, 
            edit = :canwrite, 
            `delete` = :candelete,  
            home = :home, 
            `control` = :control, 
            cases = :cases, 
            `sessions` = :sessions, 
            lawyers = :lawyers, 
            clients = :clients, 
            helpers = :helpers, 
            types = :types, 
            documents = :documents, 
            tasks = :tasks, 
            `message` = :message, 
            outbox = :outbox, 
            requests = :requests, 
            powers = :powers, 
            case_info = :case_info, 
            cliam_info = :claimantsInformation, 
            session_info = :sessionsInfo, 
            exp_info = :expensesInfo, 
            pay_info = :paymentInfo, 
            attach_info = :attachmentsInfo, 
            desc_info = :descriptionInfo, 
            action_info = :actionInfo
            WHERE power_id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':role_name', $role_name, PDO::PARAM_STR);
        $stmt->bindParam(':canread', $canread, PDO::PARAM_INT);
        $stmt->bindParam(':canwrite', $canwrite, PDO::PARAM_INT);
        $stmt->bindParam(':candelete', $candelete, PDO::PARAM_INT);
        $stmt->bindParam(':canadd', $canadd, PDO::PARAM_INT);
        $stmt->bindParam(':home', $home, PDO::PARAM_INT);
        $stmt->bindParam(':control', $control, PDO::PARAM_INT);
        $stmt->bindParam(':cases', $cases, PDO::PARAM_INT);
        $stmt->bindParam(':sessions', $sessions, PDO::PARAM_INT);
        $stmt->bindParam(':lawyers', $lawyers, PDO::PARAM_INT);
        $stmt->bindParam(':clients', $clients, PDO::PARAM_INT);
        $stmt->bindParam(':helpers', $helpers, PDO::PARAM_INT);
        $stmt->bindParam(':types', $types, PDO::PARAM_INT);
        $stmt->bindParam(':documents', $documents, PDO::PARAM_INT);
        $stmt->bindParam(':tasks', $tasks, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_INT);
        $stmt->bindParam(':outbox', $outbox, PDO::PARAM_INT);
        $stmt->bindParam(':requests', $requests, PDO::PARAM_INT);
        $stmt->bindParam(':powers', $powers, PDO::PARAM_INT);
        $stmt->bindParam(':case_info', $case_info, PDO::PARAM_INT);
        $stmt->bindParam(':claimantsInformation', $claimantsInformation, PDO::PARAM_INT);
        $stmt->bindParam(':sessionsInfo', $sessionsInfo, PDO::PARAM_INT);
        $stmt->bindParam(':expensesInfo', $expensesInfo, PDO::PARAM_INT);
        $stmt->bindParam(':paymentInfo', $paymentInfo, PDO::PARAM_INT);
        $stmt->bindParam(':attachmentsInfo', $attachmentsInfo, PDO::PARAM_INT);
        $stmt->bindParam(':descriptionInfo', $descriptionInfo, PDO::PARAM_INT);
        $stmt->bindParam(':actionInfo', $actionInfo, PDO::PARAM_INT);
        $stmt->bindParam(':id', $power_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "تم تحديث البيانات بنجاح";
    } catch (PDOException $e) {
        echo "خطأ في تحديث البيانات: " . $e->getMessage();
    }
}
}
?>

<?php 

} else {
    header("Location: ../../logout.php"); // إعادة التوجيه لتسجيل الخروج
    exit;}
 
} else {
header("Location: ../../logout.php"); // إعادة التوجيه لتسجيل الخروج
exit;
}
?>

