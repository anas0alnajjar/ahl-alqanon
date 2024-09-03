<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $source = $_POST['source'];
            $success = false;
            $errorMessage = '';

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

                            // حذف ملف اللوغو إذا كان موجودًا
                            if (file_exists($logoPath)) {
                                if (!unlink($logoPath)) {
                                    throw new Exception('Failed to delete logo file');
                                }
                            } else {
                                error_log('Logo not found for admin');
                            }
                        }

                        // حذف السجل من جدول settings
                        $sql = "DELETE FROM setting WHERE admin_id = :id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        if (!$stmt->execute()) {
                            throw new Exception('Failed to delete settings for admin');
                        }

                        // حذف السجل من جدول admin
                        $sql = "DELETE FROM admin WHERE admin_id = :id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        if (!$stmt->execute()) {
                            throw new Exception('Failed to delete admin');
                        }
                        break;
                    case 'مدير مكتب':
                        $sql = "DELETE FROM managers_office WHERE id = :id";
                        break;
                    default:
                        throw new Exception('Invalid source');
                }

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $conn->commit();
                    $success = true;
                } else {
                    throw new Exception('Failed to execute deletion query');
                }
            } catch (Exception $e) {
                $conn->rollBack();
                error_log($e->getMessage());
                $errorMessage = $e->getMessage();
                $success = false;
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'error' => $errorMessage]);
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
