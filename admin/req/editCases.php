<?php
session_start();
include "../../DB_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formData = $_POST;
    print_r($formData);
    $formData['last_modified'] = date("Y-m-d");

    // معالجة بيانات الجلسات
    $sessionNumbers = isset($formData['session_number']) ? $formData['session_number'] : [];
    $sessionDates = isset($formData['session_date']) ? $formData['session_date'] : [];
    $sessionDatesHiri = isset($formData['session_date_hjri']) ? $formData['session_date_hjri'] : [];
    $sessionHours = isset($formData['session_hour']) ? $formData['session_hour'] : [];
    $sessionIds = isset($formData['sessions_id']) ? $formData['sessions_id'] : [];
    $sessionNotes = isset($formData['session_notes']) ? $formData['session_notes'] : [];
    $assistant_lawyer = isset($formData['assistant_lawyer']) ? $formData['assistant_lawyer'] : [];
    // معالجة بيانات المصاريف
    $pay_date = isset($formData['pay_date']) ? $formData['pay_date'] : [];
    $pay_date_hijri = isset($formData['pay_date_hijri']) ? $formData['pay_date_hijri'] : [];
    $amount = isset($formData['amount']) ? $formData['amount'] : [];
    $notes_expenses = isset($formData['notes_expenses']) ? $formData['notes_expenses'] : [];
    $exp_session_id = isset($formData['exp_session_id']) ? $formData['exp_session_id'] : [];
    $expensesIds = isset($formData['expenses_id']) ? $formData['expenses_id'] : [];

    // معالجة بيانات الدفعات
    $payment_method = isset($formData['payment_method']) ? $formData['payment_method'] : [];
    $payment_date = isset($formData['payment_date']) ? $formData['payment_date'] : [];
    $payment_date_hiri = isset($formData['payment_date_hiri']) ? $formData['payment_date_hiri'] : [];
    $amount_paid = isset($formData['amount_paid']) ? $formData['amount_paid'] : [];
    $payment_ids = isset($formData['payment_id']) ? $formData['payment_id'] : [];
    $payment_notes = isset($formData['payment_notes']) ? $formData['payment_notes'] : [];
    $received = isset($formData['received']) ? $formData['received'] : [];

    $sessionNumbersNews = isset($formData['new_session_number']) ? $formData['new_session_number'] : [];
    $sessionDatesNews = isset($formData['new_session_date']) ? $formData['new_session_date'] : [];
    $sessionDatesNewsHiri = isset($formData['new_session_date_hjri']) ? $formData['new_session_date_hjri'] : [];
    $sessionHoursNews = isset($formData['new_session_hour']) ? $formData['new_session_hour'] : [];
    $sessionNotesNews = isset($formData['notes_sessions']) ? $formData['notes_sessions'] : [];
    $assistant_lawyerNew = isset($formData['assistant_lawyerNew']) ? $formData['assistant_lawyerNew'] : [];

    $newPay = isset($formData['newPay']) ? $formData['newPay'] : [];
    $newPayHijri = isset($formData['newPayHijri']) ? $formData['newPayHijri'] : [];
    $NewNotes = isset($formData['NewNotes']) ? $formData['NewNotes'] : [];
    $newAmount = isset($formData['newAmount']) ? $formData['newAmount'] : [];
    $new_payments_notes = isset($formData['new_payments_notes']) ? $formData['new_payments_notes'] : [];
    $exp_session_id_new = isset($formData['exp_session_id_new']) ? $formData['exp_session_id_new'] : [];

    $newMethod = isset($formData['newMethod']) ? $formData['newMethod'] : [];
    $newDate = isset($formData['newDate']) ? $formData['newDate'] : [];
    $newDateHiri = isset($formData['newDateHiri']) ? $formData['newDateHiri'] : [];
    $newAmountPaid = isset($formData['newAmountPaid']) ? $formData['newAmountPaid'] : [];

    unset(
        $formData['session_number'], $formData['session_date'], $formData['session_hour'], 
        $formData['sessions_id'], $formData['notes_sessions'], $formData['session_notes'], 
        $formData['assistant_lawyer'], $formData['assistant_lawyerNew'], $formData['new_session_hour'], 
        $formData['new_session_date'], $formData['new_session_number'], $formData['newPay'], 
        $formData['NewNotes'], $formData['newAmount'], $formData['pay_date'], $formData['amount'], 
        $formData['notes_expenses'], $formData['expenses_id'], $formData['payment_method'], 
        $formData['payment_date'], $formData['amount_paid'], $formData['payment_id'], $formData['payment_notes'], 
        $formData['received'], $formData['newMethod'], $formData['newDate'], $formData['newAmountPaid'], 
        $formData['new_payments_notes'], $formData['newDateHiri'], $formData['newPayHijri'], 
        $formData['new_session_date_hjri'], $formData['payment_date_hiri'], $formData['pay_date_hijri'], 
        $formData['session_date_hjri'], $formData['exp_session_id_new'], $formData['exp_session_id']
    );

    $received_values = array_fill(0, count($payment_ids), 0);
    foreach ($received as $key => $value) {
        $received_values[$key] = 1;
    }

    // Check if files are uploaded
    if (!empty($_FILES['id_picture']['tmp_name'])) {
        // Retrieve old picture path if exists
        $oldPictureQuery = $conn->prepare("SELECT id_picture FROM cases WHERE case_id = ?");
        $oldPictureQuery->execute([$formData['id']]);
        $oldPicture = $oldPictureQuery->fetchColumn();

        // Handle uploaded picture file
        $pictureTmpName = $_FILES['id_picture']['tmp_name'];
        $pictureExtension = pathinfo($_FILES['id_picture']['name'], PATHINFO_EXTENSION); // Get the file extension
        $pictureName = uniqid('id_picture', true) . '_' . bin2hex(random_bytes(8)) . '.' . $pictureExtension; // Generate a unique filename
        $picturePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $pictureName;

        if (move_uploaded_file($pictureTmpName, $picturePath)) {
            $formData['id_picture'] = $pictureName; // Store the path in the form data

            // Delete old picture if it exists
            if ($oldPicture && file_exists($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $oldPicture)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $oldPicture);
            }
        } else {
            echo "Error: Failed to move uploaded file.";
            exit();
        }
    } else {
        // If no new file is uploaded, do not update the id_picture field
        unset($formData['id_picture']);
    }

    try {
        $conn->beginTransaction();
        if (isset($_POST['helper_name']) && is_array($_POST['helper_name'])) {
            $selectedValues = implode(',', $_POST['helper_name']);
        } else {
            $selectedValues = '';
        }
        $formData['helper_name'] = $selectedValues;

        if (isset($_POST['plaintiff']) && is_array($_POST['plaintiff'])) {
            $selectedValuesPlaintiff = implode(',', $_POST['plaintiff']);
        } else {
            $selectedValuesPlaintiff = '';
        }
        $formData['plaintiff'] = $selectedValuesPlaintiff;

        if (isset($_POST['defendant']) && is_array($_POST['defendant'])) {
            $selectedValuesDefendant = implode(',', $_POST['defendant']);
        } else {
            $selectedValuesDefendant = '';
        }
        $formData['defendant'] = $selectedValuesDefendant;

        $checkBoxes = ['agency'];
        foreach ($checkBoxes as $notCheck) {
            $formData[$notCheck] = isset($_POST[$notCheck]) ? 'on' : 0;
        }

        // تحديث بيانات القضية
        $caseId = $formData['id'];
        unset($formData['id']);
        $updateCaseSql = "UPDATE cases SET " . implode(" = ?, ", array_keys($formData)) . " = ? WHERE case_id = ?";
        $updateCaseStmt = $conn->prepare($updateCaseSql);
        $updateCaseStmt->execute(array_merge(array_values($formData), [$caseId]));

        // تحديث الجلسات الحالية
        if (!empty($sessionIds)) {
            $updateSessionSql = "UPDATE sessions SET session_number = ?, session_date = ?, session_hour = ?, session_date_hjri = ?, notes=?, assistant_lawyer = ? WHERE sessions_id = ?";
            $updateSessionStmt = $conn->prepare($updateSessionSql);

            for ($i = 0; $i < count($sessionIds); $i++) {
                $updateSessionStmt->execute([$sessionNumbers[$i], $sessionDates[$i], $sessionHours[$i], $sessionDatesHiri[$i], $sessionNotes[$i], $assistant_lawyer[$i], $sessionIds[$i]]);
            }
        }

        // إضافة جلسات جديدة
        if (!empty($sessionNumbersNews)) {
            $insertSessionSql = "INSERT INTO sessions (case_id, session_number, session_date, session_date_hjri, session_hour, notes, assistant_lawyer) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertSessionStmt = $conn->prepare($insertSessionSql);

            for ($i = 0; $i < count($sessionNumbersNews); $i++) {
                if (!empty($sessionDatesNews[$i]) && !empty($sessionHoursNews[$i])) {
                    $insertSessionStmt->execute([
                        $caseId,
                        $sessionNumbersNews[$i],
                        $sessionDatesNews[$i],
                        $sessionDatesNewsHiri[$i],
                        $sessionHoursNews[$i],
                        $sessionNotesNews[$i],
                        $assistant_lawyerNew[$i]
                    ]);
                } else {
                    echo "Error: Missing session information for new session " . ($i + 1) . ".";
                }
            }
        }

        // تحديث المصروفات الحالية
        if (!empty($expensesIds)) {
            $updateExpensesSql = "UPDATE expenses SET pay_date = ?, amount = ?, notes_expenses = ?, pay_date_hijri = ?, session_id = ? WHERE id = ?";
            $updateExpensesStmt = $conn->prepare($updateExpensesSql);

            for ($i = 0; $i < count($expensesIds); $i++) {
                $updateExpensesStmt->execute([$pay_date[$i], $amount[$i], $notes_expenses[$i], $pay_date_hijri[$i], $exp_session_id[$i], $expensesIds[$i]]);
            }
        }

        // إضافة مصروفات جديدة
        if (!empty($newAmount)) {
            $insertExpensesSql = "INSERT INTO expenses (case_id, pay_date, amount, pay_date_hijri, notes_expenses, session_id) VALUES (?, ?, ?, ?, ?, ?)";
            $insertExpensesStmt = $conn->prepare($insertExpensesSql);

            for ($i = 0; $i < count($newAmount); $i++) {
                if (!empty($newPay[$i])) {
                    $insertExpensesStmt->execute([
                        $caseId,
                        $newPay[$i],
                        $newAmount[$i],
                        $newPayHijri[$i],
                        $NewNotes[$i],
                        $exp_session_id_new[$i]
                    ]);
                } else {
                    echo "Error: Missing session information for new expenses " . ($i + 1) . ".";
                }
            }
        }

        // تحديث الدفعات الحالية
        if (!empty($payment_ids)) {
            $updatePaymentSql = "UPDATE payments SET amount_paid = ?, payment_date = ?, payment_method = ?, payment_date_hiri = ?, payment_notes = ?, received = ? WHERE id = ?";
            $updatePaymentStmt = $conn->prepare($updatePaymentSql);

            for ($i = 0; $i < count($payment_ids); $i++) {
                $updatePaymentStmt->execute([$amount_paid[$i], $payment_date[$i], $payment_method[$i], $payment_date_hiri[$i], $payment_notes[$i], $received_values[$i], $payment_ids[$i]]);
            }
        }

        // إضافة دفعات جديدة
        if (!empty($newAmountPaid)) {
            $insertPaymentSql = "INSERT INTO payments (case_id, amount_paid, payment_date, payment_method, payment_date_hiri, payment_notes) VALUES (?, ?, ?, ?, ?, ?)";
            $insertPaymentStmt = $conn->prepare($insertPaymentSql);

            for ($i = 0; $i < count($newAmountPaid); $i++) {
                if (!empty($newAmountPaid[$i]) && !empty($newDate[$i])) {
                    $insertPaymentStmt->execute([
                        $caseId,
                        $newAmountPaid[$i],
                        $newDate[$i],
                        $newMethod[$i],
                        $newDateHiri[$i],
                        $new_payments_notes[$i]
                    ]);
                } else {
                    echo "Error: Missing session information for new Payment " . ($i + 1) . ".";
                }
            }
        }

        $conn->commit();
        echo "Record updated successfully.";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: Unable to update records. " . $e->getMessage();
    }
} else {
    header("Location: cases.php");
    exit();
}
?>

