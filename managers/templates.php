<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Managers') {
    include "logo.php";
    include "../DB_connection.php";

    include 'permissions_script.php';
    if ($pages['message_customization']['read'] == 0) {
        header("Location: home.php");
        exit();
    }


    include "get_office.php";
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);
    ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="X-UA-Compatible" content="ie=edge"> -->
    <title>Templates</title>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    
    



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    
    

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_cases.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

    
    




    <style>
        td, th {
            text-align: center;
        }
        div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:last-child {
            padding-right: unset !important;
            max-height: 500px;
            overflow: auto;
            scroll-behavior: smooth;
            scrollbar-width: unset !important;
            scrollbar-color: black !important;
        }
        .dataTables_length{
            margin-bottom: 2%;
        }

        .modal-header, .modal-footer {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-top: 1px solid #e9ecef;
}

.modal-title {
    color: #007bff;
}

.form-group label {
    font-weight: bold;
}

.variable, .variable-add, .variable-add-dues, .variableDues, .variable-Phone, .variable-Dues-Phone, .variableSessionsPhone, .variable-Dues {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 5px 10px;
    margin: 5px;
    border-radius: 4px;
    cursor: grab;
}

.variable:hover {
    background-color: #0056b3;
}

textarea {
    min-height: 150px;
    resize: auto;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.ck-editor__editable[role="textbox"] {
    /* Editing area */
    min-height: 100px;
    max-height: 200px;
    overflow: auto;
    text-align: right; /* Ensure text is aligned to the right */
    direction: rtl; /* Ensure text direction is right-to-left */
    }
    .ck-content .image {
        /* Block images */
        max-width: 80%;
        margin: 20px auto;
    }
    .ck.ck-toolbar.ck-toolbar_grouping>.ck-toolbar__items {
        flex-wrap: wrap;
    }
    .ck.ck-button.ck-off.ck-file-dialog-button {
        display: none !important;
    }
    .ck.ck-editor__editable_inline[dir=ltr] {
        text-align: right;
    }
    /* إضافة تنسيقات للقوائم لتظهر من اليمين إلى اليسار */
    .ck-content ol, .ck-content ul {
        text-align: right;
        direction: rtl;
        padding-right: 40px; /* Ensure padding on the right for RTL */
    }
    .ck-content ol {
        list-style-type: decimal;
    }
    .ck-content ul {
        list-style-type: disc;
    }
    #records_paginate {
        direction: ltr;
    }

       .table-responsive {
            scrollbar-width: none !important;
        }
        #records_filter {
            display:none !important;
        }

    </style>
    
</head>

<body>



<!-- Modal Add For Sessions by Email-->
<div class="modal fade" id="addMessageModal" tabindex="-1" aria-labelledby="addMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="addMessageModalLabel">إضافة رسالة</h5>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="messageForm" method="post">
                    <input type="hidden" value="<?=$OfficeId?>" name="office_id" id="office_id">
                    <div class="form-group">
                        <label for="for_whom" class="form-label">موجهة لمن</label>
                        <select name="for_whom" id="for_whom" class="form-control">
                            <option value="" selected>اختر الفئة المستهدفة</option>
                            <option value="1">للعميل</option>
                            <option value="2">للمحامي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editor" class="form-label">نص الرسالة</label>
                        <textarea name="message_text" id="editor" class="form-control" ondrop="drop(event)" ondragover="allowDrop(event)"></textarea>
                    </div>
                    <h5>المتغيرات:</h5>
                    <div class="variable-add" draggable="true" ondragstart="drag(event)">الاسم الأول للعميل</div>
                    <div class="variable-add" draggable="true" ondragstart="drag(event)">اسم العائلة للعميل</div>
                    <div class="variable-add" draggable="true" ondragstart="drag(event)">عنوان القضية</div>
                    <div class="variable-add" draggable="true" ondragstart="drag(event)">تاريخ الجلسة</div>
                    <div class="variable-add" draggable="true" ondragstart="drag(event)">ساعة الجلسة</div>
                    <div class="variable-add" draggable="true" ondragstart="drag(event)">اسم المحامي</div>
                    
                
            </div>
            <div class="modal-footer" style="">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <button type="" id="saveMsgSessionsByEmails" class="btn btn-success btn-block">حفظ </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit For Sessions by Email-->
<div class="modal fade" id="editMessageModal" tabindex="-1" aria-labelledby="editMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
            <?php if ($pages['message_customization']['write']) : ?>
                <h5 class="modal-title" id="editMessageModalLabel">تعديل الرسالة</h5>
            <?php endif; ?>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="EditmessageForm" method="post">
                    <div class="form-group">
                        <label for="editEditor" class="form-label">نص الرسالة</label>
                        <textarea name="message_text" id="editEditor" class="form-control" ondrop="drop(event)" ondragover="allowDrop(event)"></textarea>
                    </div>
                    <input type="hidden" id="messageId" name="messageId">
                    <input type="hidden" id="officeId" name="office_id">
                    <input type="hidden" id="forWhom" name="for_whom">
                    <?php if ($pages['message_customization']['write']) : ?>
                    <h5>المتغيرات:</h5>
                    <div class="variable" draggable="true" ondragstart="drag(event)">الاسم الأول للعميل</div>
                    <div class="variable" draggable="true" ondragstart="drag(event)">اسم العائلة للعميل</div>
                    <div class="variable" draggable="true" ondragstart="drag(event)">عنوان القضية</div>
                    <div class="variable" draggable="true" ondragstart="drag(event)">تاريخ الجلسة</div>
                    <div class="variable" draggable="true" ondragstart="drag(event)">ساعة الجلسة</div>
                    <div class="variable" draggable="true" ondragstart="drag(event)">اسم المحامي</div>
                    <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <?php if ($pages['message_customization']['write']) : ?>
                    <button type="button" id="editMsgSessionsByEmails" class="btn btn-success btn-block">حفظ التعديلات</button>
                <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add For Dues by Email-->
<div class="modal fade" id="addMessageModalDues" tabindex="-1" aria-labelledby="addMessageModalLabelDues" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="addMessageModalLabelDues">إضافة رسالة</h5>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="messageFormDues" method="post">
                <input type="hidden" value="<?=$OfficeId?>" id="office_idDues" name="office_id">    
                    <input type="hidden" value="1" name="for_whom" id="for_whomDues">
                    <div class="form-group">
                        <label for="editorDues" class="form-label">نص الرسالة</label>
                        <textarea name="message_text" id="editorDues" class="form-control" ondrop="drop(event)" ondragover="allowDrop(event)"></textarea>
                    </div>
                    <h5>المتغيرات:</h5>
                    <div class="variable-add-dues" draggable="true" ondragstart="drag(event)">الاسم الأول للعميل</div>
                    <div class="variable-add-dues" draggable="true" ondragstart="drag(event)">اسم العائلة للعميل</div>
                    <div class="variable-add-dues" draggable="true" ondragstart="drag(event)">مبلغ الدفعة</div>
                    <div class="variable-add-dues" draggable="true" ondragstart="drag(event)">تاريخ الدفع ميلادي</div>
                    <div class="variable-add-dues" draggable="true" ondragstart="drag(event)">تاريخ الدفع هجري</div>
                    <div class="variable-add-dues" draggable="true" ondragstart="drag(event)">عنوان القضية</div>
                    
                    
                    
                
            </div>
            <div class="modal-footer" style="">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <button type="" id="saveMsgDuesByEmails" class="btn btn-success btn-block">حفظ </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit For Dues by Email-->
<div class="modal fade" id="editMessageModalDues" tabindex="-1" aria-labelledby="editMessageModalLabelDues" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
                
                <?php if ($pages['message_customization']['write']) : ?>
                <h5 class="modal-title" id="editMessageModalLabelDues">تعديل الرسالة</h5>
            <?php endif; ?>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="EditmessageFormDues" method="post">
                    <div class="form-group">
                        <label for="editEditorDues" class="form-label">نص الرسالة</label>
                        <textarea name="message_text" id="editEditorDues" class="form-control" ondrop="drop(event)" ondragover="allowDrop(event)"></textarea>
                    </div>
                    <input type="hidden" id="messageIdDues" name="messageId">
                    <input type="hidden" id="officeIdDues" name="office_id">
                    <input type="hidden" id="forWhomDues" name="for_whom">
                    <?php if ($pages['message_customization']['write']) : ?>
                    <h5>المتغيرات:</h5>
                    <div class="variableDues" draggable="true" ondragstart="drag(event)">الاسم الأول للعميل</div>
                    <div class="variableDues" draggable="true" ondragstart="drag(event)">اسم العائلة للعميل</div>
                    <div class="variableDues" draggable="true" ondragstart="drag(event)">عنوان القضية</div>
                    <div class="variableDues" draggable="true" ondragstart="drag(event)">مبلغ الدفعة</div>
                    <div class="variableDues" draggable="true" ondragstart="drag(event)">تاريخ الدفع ميلادي</div>
                    <div class="variableDues" draggable="true" ondragstart="drag(event)">تاريخ الدفع هجري</div>
                    <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <?php if ($pages['message_customization']['write']) : ?>
                <button type="button" id="editMsgSessionsByEmailsDues" class="btn btn-success btn-block">حفظ التعديلات</button>
                <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add For Sessions by Phone-->
<div class="modal fade" id="addMessageModalPhone" tabindex="-1" aria-labelledby="addMessageModalLabelPhone" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="addMessageModalLabelPhone">إضافة رسالة</h5>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="messageFormPhone" method="post">
                <input type="hidden" value="<?=$OfficeId?>" name="office_id" id="office_idPhone">        
                    <div class="form-group">
                        <label for="for_whomPhone" class="form-label">موجهة لمن</label>
                        <select name="for_whom" id="for_whomPhone" class="form-control">
                            <option value="" selected>اختر الفئة المستهدفة</option>
                            <option value="1">للعميل</option>
                            <option value="2">للمحامي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editorPhone" class="form-label">نص الرسالة</label>
                        <textarea style="resize: vertical;" name="message_text" id="editorPhone" class="form-control" ondrop="drop(event)" ondragover="allowDrop(event)" rows="2"></textarea>
                    </div>
                    <h5>المتغيرات:</h5>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariable('الاسم الأول للعميل')">الاسم الأول للعميل</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariable('اسم العائلة للعميل')">اسم العائلة للعميل</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariable('عنوان القضية')">عنوان القضية</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariable('تاريخ الجلسة')">تاريخ الجلسة</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariable('ساعة الجلسة')">ساعة الجلسة</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariable('اسم المحامي')">اسم المحامي</div>

                    
                
            </div>
            <div class="modal-footer" style="">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <button type="" id="saveMsgSessionsByPhone" class="btn btn-success btn-block">حفظ </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add For Dues by Phone-->
<div class="modal fade" id="addMessageModalDuesPhone" tabindex="-1" aria-labelledby="addMessageModalLabelDuesPhone" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="addMessageModalLabelDuesPhone">إضافة رسالة</h5>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="messageFormDuesPhone" method="post">
                <input type="hidden" value="<?=$OfficeId?>" id="office_idDuesPhone" name="office_id">    
                    <div class="form-group">
                        <label for="editorDuesPhone" class="form-label">نص الرسالة</label>
                        <textarea style="resize: vertical;" name="message_text" id="editorDuesPhone" class="form-control" ondrop="dropDues(event)" ondragover="allowDrop(event)" rows="2"></textarea>
                    </div>
                    <h5>المتغيرات:</h5>
                    <div class="variable-Dues-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableDues('الاسم الأول للعميل')">الاسم الأول للعميل</div>
                    <div class="variable-Dues-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableDues('اسم العائلة للعميل')">اسم العائلة للعميل</div>
                    <div class="variable-Dues-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableDues('عنوان القضية')">عنوان القضية</div>
                    <div class="variable-Dues-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableDues('مبلغ الدفعة')">مبلغ الدفعة</div>
                    <div class="variable-Dues-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableDues('تاريخ الدفع ميلادي')">تاريخ الدفع ميلادي</div>
                    <div class="variable-Dues-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableDues('تاريخ الدفع هجري')">تاريخ الدفع هجري</div>

                    
                
            </div>
            <div class="modal-footer" style="">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <button type="" id="saveMsgSessionsByDuesPhone" class="btn btn-success btn-block">حفظ </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit For Sessions by Phone-->
<div class="modal fade" id="editMessageModalPhone" tabindex="-1" aria-labelledby="editMessageModalLabelPhone" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
            <?php if ($pages['message_customization']['write']) : ?>
                <h5 class="modal-title" id="editMessageModalLabelPhone">تعديل الرسالة</h5>
            <?php endif; ?>
                
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="EditmessageForm2" method="post">
                    <div class="form-group">
                        <label for="editEditorPhone" class="form-label">نص الرسالة</label>
                        <textarea name="message_text" id="editEditorPhone" class="form-control" ondrop="dropEdit(event)" ondragover="allowDrop(event)"></textarea>
                    </div>
                    <input type="hidden" id="messageIdPhoneEdit" name="messageId">
                    <input type="hidden" id="officeIdPhoneEdit" name="office_id">
                    <input type="hidden" id="forWhomPhoneEdit" name="for_whom">
                    <?php if ($pages['message_customization']['write']) : ?>
                    <h5>المتغيرات:</h5>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableEdit('الاسم الأول للعميل')">الاسم الأول للعميل</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableEdit('اسم العائلة للعميل')">اسم العائلة للعميل</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableEdit('عنوان القضية')">عنوان القضية</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableEdit('تاريخ الجلسة')">تاريخ الجلسة</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableEdit('ساعة الجلسة')">ساعة الجلسة</div>
                    <div class="variable-Phone" draggable="true" ondragstart="drag(event)" onclick="insertVariableEdit('اسم المحامي')">اسم المحامي</div>
                    <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <?php if ($pages['message_customization']['write']) : ?>
                    <button type="button" id="editMsgSessionsByPhone" class="btn btn-success btn-block">حفظ التعديلات</button>
                <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit For Dues by Dues-->
<div class="modal fade" id="editMessageModalDuesPhones" tabindex="-1" aria-labelledby="editMessageModalLabelDues" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
            <?php if ($pages['message_customization']['write']) : ?>
                <h5 class="modal-title" id="editMessageModalLabelDues">تعديل الرسالة</h5>
            <?php endif; ?>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="EditmessageFormDues2" method="post">
                    <div class="form-group">
                        <label for="editEditorDuesPhones" class="form-label">نص الرسالة</label>
                        <textarea name="message_text" id="editEditorDuesPhones" class="form-control" ondrop="dropEditDues(event)" ondragover="allowDrop(event)"></textarea>
                    </div>
                    
                    <input type="hidden" id="messageIdDuesEditPhones" name="messageId">
                    <input type="hidden" id="officeIdDuesEditPhones" name="office_id">
                    <input type="hidden" id="forWhomDuesEditPhones" name="for_whom">
                    <?php if ($pages['message_customization']['write']) : ?>
                    <h5>المتغيرات:</h5>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('الاسم الأول للعميل')">الاسم الأول للعميل</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('اسم العائلة للعميل')">اسم العائلة للعميل</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('عنوان القضية')">عنوان القضية</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('مبلغ الدفعة')">مبلغ الدفعة</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('تاريخ الدفع ميلادي')">تاريخ الدفع ميلادي</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('تاريخ الدفع هجري')">تاريخ الدفع هجري</div>
                    <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <?php if ($pages['message_customization']['write']) : ?>
                    <button type="button" id="editMsgSessionsByDues" class="btn btn-success btn-block">حفظ التعديلات</button>
                <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>



    <?php include "inc/navbar.php"; ?>
    <main class="container mt-5">
    <?php if ($pages['message_customization']['add']) : ?>
    <div class="btn-group" style="direction:ltr;">
    <button id="add_template_btn"  class="btn btn-dark ">اضافة رسالة</button>

    
    <select name="template_type" id="template_type" class="form-control-sm" style="border-left:none;border-radius: 0px 5px 5px 0;">
        <option value="" selected disabled>اختر نوع الرسالة</option>
        <option value="1">تذكير بالجلسات (إيميل)</option>
        <option value="2">تذكير بالجلسات (واتساب)</option>
        <option value="3">تذكير بالمستحقات (إيميل)</option>
        <option value="4">تذكير بالمستحقات (واتساب)</option>
        <!-- <option value="5">إخطار بالمهام (واتساب)</option> -->
    </select>
    </div>
    <?php endif; ?>
        <div class="input-group mt-3 text-center" style="max-width: 100%; min-width: 80%; direction: ltr;">
        <input type="text" class="form-control" id="searchInput" placeholder="ابحث هنا..." onkeyup="filterTable()">
        <div class="input-group-append">
            <button style="border-radius: 0;" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
        </div>
    </div>
    
    <div style="direction:rtl !important;" class="mt-2">
        <a href="home.php" class="btn btn-light w-100">الرئيسية</a>
    </div>

    <hr>
    <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-info mt-3 n-table" role="alert" style="max-width:100% !important;">
                <?php 
                if (isset($_GET['success'])) {
                    echo $_GET['success'];
                } 
                ?>
            </div>
        <?php } ?>
        <div class="row justify-content-center">
            <div class="col-md-12">
            <div class="container">
        <div class="row">
            <div class="col-md-12" style="padding:0;">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <!-- Table -->
                        <div class="table-responsive" style="padding:0;">
                        <table id="records" class="display responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>
            </div>
        </div>
    </main>




       <!-- تضمين ملفات JavaScript -->

       
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    

    
    <script>var pages = <?php echo $permissions_json; ?>;</script>
    <script src="../js/script_templates_offices.js"></script>
    
    






    <script>
        document.getElementById('add_template_btn').addEventListener('click', function() {
            
        });
    </script>

<script>
        document.getElementById('add_template_btn').addEventListener('click', function() {
            var templateType = document.getElementById('template_type').value;
            if (templateType == 1) {
                $('#addMessageModal').modal('show');
            } else if (templateType == 2) {
                $('#addMessageModalPhone').modal('show');
            } else if (templateType == 3) {
                $('#addMessageModalDues').modal('show');
            } else if (templateType == 4) {
                $('#addMessageModalDuesPhone').modal('show');
            } else if (templateType == 5) {
                $('#addUserModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى اختيار نوع الرسالة التي ترغب في إضافتها.'
                });
            }
        });


    </script>
        <script>
        $(document).ready(function(){
        $('.close-modal').on('click', function(){
                $('#addMessageModal, #editMessageModal, #addMessageModalDues, #editMessageModalDues, #addMessageModalPhone, #editMessageModalPhone, #addMessageModalDuesPhone, #editMessageModalDuesPhones').modal('hide');
            });
        });        
    </script>



</body>

</html>
<?php 
  } else {
    header("Location: ../login.php");
    exit;
  } 

?>





        
        
        
        
        