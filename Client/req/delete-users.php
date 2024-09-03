<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {
        include "../../DB_connection.php";
        include '../permissions_script.php';
        if ($pages['user_management']['delete'] == 0) {
            header("Location: ../home.php");
            exit();
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $source = $_POST['source'];
            $success = false;

            try {
                $conn->beginTransaction();

                switch ($source) {
                    case 'محامي':
                        $sql = "DELETE FROM lawyer WHERE lawyer_id = :id";
                        break;
                    case 'موكل':
                        $sql = "DELETE FROM clients WHERE client_id = :id";
                        break;
                    case 'إداري':
                        $sql = "DELETE FROM helpers WHERE id = :id";
                        break;
                    case 'آدمن':
                        // الحصول على اسم ملف اللوغو
                        $sql = "SELECT logo FROM setting WHERE admin_id = :id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $logo = $stmt->fetchColumn();

                        if ($logo) {
                            $logoPath = "../../img/" . $logo;

                            // حذف السجل من جدول settings
                            $sql = "DELETE FROM setting WHERE admin_id = :id";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            if ($stmt->execute()) {
                                // حذف السجل من جدول admin
                                $sql = "DELETE FROM admin WHERE admin_id = :id";
                            } else {
                                throw new Exception('Failed to delete settings for admin');
                            }

                            // حذف ملف اللوغو
                            if (file_exists($logoPath)) {
                                unlink($logoPath);
                            }
                        } else {
                            throw new Exception('Logo not found for admin');
                        }
                        break;
                    case 'مدير مكتب':
                        $sql = "DELETE FROM managers_office WHERE id = :id";
                        break;
                    default:
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false]);
                        exit;
                }

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $conn->commit();
                    $success = true;
                } else {
                    $conn->rollBack();
                }
            } catch (Exception $e) {
                $conn->rollBack();
                $success = false;
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
        }
    } else {
        header("Location: ../managers.php");
        exit;
    }
} else {
    header("Location: ../managers.php");
    exit;
}
?>
