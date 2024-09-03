var langOptions = {
    "sProcessing": "جارٍ التحميل...",
    "sLengthMenu": "أظهر _MENU_ مدخلات",
    "sZeroRecords": "لم يعثر على أية سجلات",
    "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
    "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
    "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
    "sInfoPostFix": "",
    "sSearch": "ابحث هنا...",
    "sUrl": "",
    "oPaginate": {
        "sFirst": "الأول",
        "sPrevious": "السابق",
        "sNext": "التالي",
        "sLast": "الأخير"
    },
    "oAria": {
        "sSortAscending": ": تفعيل لترتيب العمود تصاعدياً",
        "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
    }
};

function fetch(start_date, end_date) {
    $.ajax({
        url: "req/fetch_templates.php",
        type: "POST",
        dataType: "json",
        success: function(data) {
            $('#records').DataTable({
                "data": data,
                "destroy": true,
                "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    // 'csv', 'excel', 'pdf', 'print'
                ],
                "responsive": true,
                "language": langOptions,
                "orderCellsTop": true,
                "fixedHeader": true,
                "processing": true,
                "order": [[0, "desc"]],
                "columns": [
                    { "data": "id", "title": "ID" },
                    {
                        "data": "type_template",
                        "title": "النوع",
                    },
                    {
                        "data": "for_whom_translated",
                        "title": "موجهة لمن",
                    },
                    {
                        "data": null,
                        "title": "الاجراءات",
                        "render": function(data, type, row, meta) {
                            var buttons = '';
                            var editButtonClass = "btn btn-primary btn-sm edit-button";
                            var editButtonText = "تعديل";
                    
                            // تحقق من قيمة type_template
                            if (row.type_template === 'تذكير بالمستحقات (إيميل)') {
                                editButtonClass = "btn btn-primary btn-sm edit-btn-dues";
                            } else if (row.type_template === 'تذكير بالجلسات (واتساب)') {
                                editButtonClass = "btn btn-primary btn-sm edit-btn-whatsapp";
                            } else if (row.type_template === 'تذكير بالمستحقات (واتساب)') {
                                editButtonClass = "btn btn-primary btn-sm edit-btn-dues-whatsapp";
                            }
                    
                            // تحقق من صلاحيات القراءة والكتابة لتحديد نص الزر
                            if (pages.message_customization.read == 1 && pages.message_customization.write == 0) {
                                editButtonText = "عرض";
                            }
                    
                            // أضف زر التعديل إذا كان المستخدم لديه صلاحية الكتابة
                            if (pages.message_customization.write == 1 || pages.message_customization.read == 1) {
                                buttons += '<button class="' + editButtonClass + '" data-id="' + row.id + '">' + editButtonText + '</button>';
                            }
                    
                            // أضف زر الحذف إذا كان المستخدم لديه صلاحية الحذف
                            if (pages.message_customization.delete == 1) {
                                buttons += ' <button class="btn btn-danger btn-sm delete-button" data-id="' + row.id + '">حذف</button>';
                            }
                    
                            // إذا لم يكن هناك أزرار لإظهارها، اعرض رسالة أو عنصر فارغ
                            if (buttons === '') {
                                return ''; // أو أي محتوى آخر تود عرضه
                            }
                    
                            return buttons;
                        },
                        "visible": pages.message_customization.write == 1 || pages.message_customization.read == 1 || pages.message_customization.delete == 1 // عرض العمود فقط إذا كانت الصلاحيات موجودة
                    }
                    
                ]
            });

            // البحث الفوري
            $('#searchInput').on('keyup', function() {
                $('#records').DataTable().search(this.value).draw();
            });
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

fetch();

// For Add Sessions by Email
$(document).ready(function() {
$('#messageForm').on('submit', function(event) {
event.preventDefault();

var office_id = $('#office_id').val();
var for_whom = $('#for_whom').val();
var editor = $('#editor').val();

if (office_id === '' || for_whom === '' || editor === '') {
    Swal.fire({
        icon: 'error',
        title: 'خطأ',
        text: 'الرجاء تعبئة جميع الحقول',
        confirmButtonColor: '#dc3545'
    });
    return; // إيقاف التنفيذ
}

// اجمع البيانات من النموذج
var formData = $(this).serialize();



// إرسال البيانات عبر AJAX
$.ajax({
    url: 'req/save_message.php',
    type: 'POST',
    data: formData,
    success: function(response) {
        if (response.status === 'success') {
            Swal.fire(
                'تم الحفظ!',
                'تم حفظ الرسالة بنجاح.',
                'success'
            ).then(() => {
                // إغلاق المودال
                $('#addMessageModal').modal('hide');
                // إعادة تحميل الصفحة أو تحديث البيانات كما يلزم
                location.reload();
            });
        } else if (response.status === 'error') {
            Swal.fire(
                'خطأ!',
                response.message,
                'error'
            );
        }
    },
    error: function() {
        Swal.fire(
            'خطأ!',
            'حدث خطأ غير متوقع. يرجى المحاولة لاحقًا.',
            'error'
        );
    }
});
});
});


// For Add Dues by Email
$(document).ready(function() {
$('#messageFormDues').on('submit', function(event) {
event.preventDefault();

var office_id = $('#office_idDues').val();
var for_whom = $('#for_whomDues').val();
var editor = $('#editorDues').val();

if (office_id === '' || for_whom === '' || editor === '') {
    Swal.fire({
        icon: 'error',
        title: 'خطأ',
        text: 'الرجاء تعبئة جميع الحقول',
        confirmButtonColor: '#dc3545'
    });
    return; // إيقاف التنفيذ
}

// اجمع البيانات من النموذج
var formData = $(this).serialize();



// إرسال البيانات عبر AJAX
$.ajax({
    url: 'req/save_messageDues.php',
    type: 'POST',
    data: formData,
    success: function(response) {
        if (response.status === 'success') {
            Swal.fire(
                'تم الحفظ!',
                'تم حفظ الرسالة بنجاح.',
                'success'
            ).then(() => {
                // إغلاق المودال
                $('#addMessageModalDues').modal('hide');
                // إعادة تحميل الصفحة أو تحديث البيانات كما يلزم
                location.reload();
            });
        } else if (response.status === 'error') {
            Swal.fire(
                'خطأ!',
                response.message,
                'error'
            );
        }
    },
    error: function() {
        Swal.fire(
            'خطأ!',
            'حدث خطأ غير متوقع. يرجى المحاولة لاحقًا.',
            'error'
        );
    }
});
});
});

// For Edit Sessions by Email

$(document).ready(function() {
let editor;

// وظيفة للتحقق من وجود CKEditor مسبقاً
function loadCKEditor(selector) {
if (typeof ClassicEditor !== 'undefined' && !editor) {
    return ClassicEditor
        .create(document.querySelector(selector))
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}
return Promise.resolve();
}

// التحقق مما إذا كان CKEditor محملاً مسبقاً واستخدامه
loadCKEditor('#editEditor');

function drag(ev) {
ev.dataTransfer.setData("text", ev.target.innerText);
}

function drop(ev) {
ev.preventDefault();
var data = ev.dataTransfer.getData("text");
editor.model.change(writer => {
    const insertPosition = editor.model.document.selection.getFirstPosition();
    writer.insertText(data, { bold: true, color: '#007bff' }, insertPosition);
});
}

function allowDrop(ev) {
ev.preventDefault();
}

function insertText(text) {
editor.model.change(writer => {
    const insertPosition = editor.model.document.selection.getFirstPosition();
    writer.insertText(text, { bold: true, color: '#007bff' }, insertPosition);
});
}

document.querySelectorAll('.variable').forEach(function(el) {
el.addEventListener('dragstart', drag);
el.addEventListener('click', function() {
    insertText(el.innerText);
});
});

document.querySelector('#editEditor').addEventListener('drop', drop);
document.querySelector('#editEditor').addEventListener('dragover', allowDrop);

// فتح المودال وجلب البيانات
$(document).on('click', '.edit-button', function() {
var messageId = $(this).data('id');
$.ajax({
    url: 'req/get_message.php',
    method: 'GET',
    data: { id: messageId },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            $('#messageId').val(response.data.id);
            $('#officeId').val(response.data.office_id);
            $('#forWhom').val(response.data.for_whom);
            loadCKEditor('#editEditor').then(() => {
                editor.setData(response.data.message_text);
            });
            $('#editMessageModal').modal('show');
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء جلب البيانات.', 'error');
    }
});
});

// AJAX لزر حفظ التعديلات
$('#editMsgSessionsByEmails').click(function() {
var messageText = editor.getData();
var officeId = $('#officeId').val();
var forWhom = $('#forWhom').val();
var messageId = $('#messageId').val();

$.ajax({
    url: 'req/edit_message.php',
    method: 'POST',
    data: {
        message_text: messageText,
        office_id: officeId,
        for_whom: forWhom,
        id: messageId
    },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            Swal.fire('تم التعديل!', 'تم تعديل الرسالة بنجاح.', 'success');
            $('#editMessageModal').modal('hide');
            // تحديث الجدول أو إعادة تحميل البيانات
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء تعديل الرسالة.', 'error');
    }
});
});
});


let editorDues, editorMain;

// التحقق مما إذا كان CKEditor محملاً بالفعل للمحرر الأول
if (typeof ClassicEditor !== 'undefined') {
if (!editorDues) {
ClassicEditor
    .create(document.querySelector('#editorDues'))
    .then(newEditor => {
        editorDues = newEditor;
    })
    .catch(error => {
        console.error(error);
    });
}
if (!editorMain) {
ClassicEditor
    .create(document.querySelector('#editor'))
    .then(newEditor => {
        editorMain = newEditor;
    })
    .catch(error => {
        console.error(error);
    });
}
}

function drag(ev) {
ev.dataTransfer.setData("text", ev.target.innerText);
}

function drop(ev, editor) {
ev.preventDefault();
var data = ev.dataTransfer.getData("text");
editor.model.change(writer => {
const insertPosition = editor.model.document.selection.getFirstPosition();
writer.insertText(data, { bold: true, color: '#007bff' }, insertPosition);
});
}

function allowDrop(ev) {
ev.preventDefault();
}

function insertText(text, editor) {
editor.model.change(writer => {
const insertPosition = editor.model.document.selection.getFirstPosition();
writer.insertText(text, { bold: true, color: '#007bff' }, insertPosition);
});
}

// إعداد الأحداث لمحرر #editorDues
document.querySelectorAll('.variable-add-dues').forEach(function(el) {
el.addEventListener('dragstart', drag);
el.addEventListener('click', function() {
insertText(el.innerText, editorDues);
});
});

document.querySelector('#editorDues').addEventListener('drop', function(ev) {
drop(ev, editorDues);
});
document.querySelector('#editorDues').addEventListener('dragover', allowDrop);

// إعداد الأحداث لمحرر #editor
document.querySelectorAll('.variable-add').forEach(function(el) {
el.addEventListener('dragstart', drag);
el.addEventListener('click', function() {
insertText(el.innerText, editorMain);
});
});

document.querySelector('#editor').addEventListener('drop', function(ev) {
drop(ev, editorMain);
});
document.querySelector('#editor').addEventListener('dragover', allowDrop);



// For Edit Dues
$(document).ready(function() {
let editEditorDues;

// وظيفة للتحقق من وجود CKEditor مسبقاً
function loadCKEditor(selector) {
if (typeof ClassicEditor !== 'undefined' && !editEditorDues) {
    return ClassicEditor
        .create(document.querySelector(selector))
        .then(newEditor => {
            editEditorDues = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}
return Promise.resolve();
}

// التحقق مما إذا كان CKEditor محملاً مسبقاً واستخدامه
loadCKEditor('#editEditorDues');

function drag(ev) {
ev.dataTransfer.setData("text", ev.target.innerText);
}

function drop(ev) {
ev.preventDefault();
var data = ev.dataTransfer.getData("text");
editEditorDues.model.change(writer => {
    const insertPosition = editEditorDues.model.document.selection.getFirstPosition();
    writer.insertText(data, { bold: true, color: '#007bff' }, insertPosition);
});
}

function allowDrop(ev) {
ev.preventDefault();
}

function insertText(text) {
editEditorDues.model.change(writer => {
    const insertPosition = editEditorDues.model.document.selection.getFirstPosition();
    writer.insertText(text, { bold: true, color: '#007bff' }, insertPosition);
});
}

document.querySelectorAll('.variableDues').forEach(function(el) {
el.addEventListener('dragstart', drag);
el.addEventListener('click', function() {
    insertText(el.innerText);
});
});

document.querySelector('#editEditorDues').addEventListener('drop', drop);
document.querySelector('#editEditorDues').addEventListener('dragover', allowDrop);

// فتح المودال وجلب البيانات
$(document).on('click', '.edit-btn-dues', function() {
var messageId = $(this).data('id');
$.ajax({
    url: 'req/get_message.php',
    method: 'GET',
    data: { id: messageId },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            $('#messageIdDues').val(response.data.id);
            $('#officeIdDues').val(response.data.office_id);
            $('#forWhomDues').val(response.data.for_whom);
            loadCKEditor('#editEditorDues').then(() => {
                editEditorDues.setData(response.data.message_text);
            });
            $('#editMessageModalDues').modal('show');
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء جلب البيانات.', 'error');
    }
});
});

// AJAX لزر حفظ التعديلات
$('#editMsgSessionsByEmailsDues').click(function() {
var messageText = editEditorDues.getData();
var officeId = $('#officeIdDues').val();
var forWhom = $('#forWhomDues').val();
var messageId = $('#messageIdDues').val();

$.ajax({
    url: 'req/edit_messageDues.php',
    method: 'POST',
    data: {
        message_text: messageText,
        office_id: officeId,
        for_whom: forWhom,
        id: messageId
    },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            Swal.fire('تم التعديل!', 'تم تعديل الرسالة بنجاح.', 'success');
            $('#editMessageModalDues').modal('hide');
            // تحديث الجدول أو إعادة تحميل البيانات
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء تعديل الرسالة.', 'error');
    }
});
});
});



function drag(ev) {
ev.dataTransfer.setData("text", ev.target.innerText);
}

function drop(ev) {
ev.preventDefault();
var data = ev.dataTransfer.getData("text");
var textarea = document.getElementById("editorPhone");
textarea.value += data;
}

function dropDues(ev) {
ev.preventDefault();
var data = ev.dataTransfer.getData("text");
var textarea = document.getElementById("editorDuesPhone");
textarea.value += data;
}
function dropEdit(ev) {
ev.preventDefault();
var data = ev.dataTransfer.getData("text");
var textarea = document.getElementById("editEditorPhone");
textarea.value += data;
}
function dropEditDues(ev) {
ev.preventDefault();
var data = ev.dataTransfer.getData("text");
var textarea = document.getElementById("editEditorDuesPhones");
textarea.value += data;
}

function insertVariable(variableText) {
var textarea = document.getElementById("editorPhone");
textarea.value += variableText;
}
function insertVariableEditDues(variableText) {
var textarea = document.getElementById("editEditorDuesPhones");
textarea.value += variableText;
}
function insertVariableEdit(variableText) {
var textarea = document.getElementById("editEditorPhone");
textarea.value += variableText;
}
function insertVariableDues(variableText) {
var textarea = document.getElementById("editorDuesPhone");
textarea.value += variableText;
}


// For Add Sessions by Phone
$(document).ready(function() {
$('#messageFormPhone').on('submit', function(event) {
event.preventDefault();

var office_idPhone = $('#office_idPhone').val();
var for_whomPhone = $('#for_whomPhone').val();
var editorPhone = $('#editorPhone').val();

if (office_idPhone === '' || for_whomPhone === '' || editorPhone === '') {
    Swal.fire({
        icon: 'error',
        title: 'خطأ',
        text: 'الرجاء تعبئة جميع الحقول',
        confirmButtonColor: '#dc3545'
    });
    return; // إيقاف التنفيذ
}

// اجمع البيانات من النموذج
var formData = $(this).serialize();



// إرسال البيانات عبر AJAX
$.ajax({
    url: 'req/save_messagePhone.php',
    type: 'POST',
    data: formData,
    success: function(response) {
        if (response.status === 'success') {
            Swal.fire(
                'تم الحفظ!',
                'تم حفظ الرسالة بنجاح.',
                'success'
            ).then(() => {
                // إغلاق المودال
                $('#addMessageModalPhone').modal('hide');
                // إعادة تحميل الصفحة أو تحديث البيانات كما يلزم
                location.reload();
            });
        } else if (response.status === 'error') {
            Swal.fire(
                'خطأ!',
                response.message,
                'error'
            );
        }
    },
    error: function() {
        Swal.fire(
            'خطأ!',
            'حدث خطأ غير متوقع. يرجى المحاولة لاحقًا.',
            'error'
        );
    }
});
});
});



// For Add Dues by Email
$(document).ready(function() {
$('#messageFormDuesPhone').on('submit', function(event) {
event.preventDefault();

var office_id = $('#office_idDuesPhone').val();
var editor = $('#editorDuesPhone').val();

if (office_id === '' || editor === '') {
    Swal.fire({
        icon: 'error',
        title: 'خطأ',
        text: 'الرجاء تعبئة جميع الحقول',
        confirmButtonColor: '#dc3545'
    });
    return; // إيقاف التنفيذ
}

// اجمع البيانات من النموذج
var formData = $(this).serialize();



// إرسال البيانات عبر AJAX
$.ajax({
    url: 'req/save_messageDuesPhone.php',
    type: 'POST',
    data: formData,
    success: function(response) {
        if (response.status === 'success') {
            Swal.fire(
                'تم الحفظ!',
                'تم حفظ الرسالة بنجاح.',
                'success'
            ).then(() => {
                // إغلاق المودال
                $('#addMessageModalDuesPhone').modal('hide');
                // إعادة تحميل الصفحة أو تحديث البيانات كما يلزم
                location.reload();
            });
        } else if (response.status === 'error') {
            Swal.fire(
                'خطأ!',
                response.message,
                'error'
            );
        }
    },
    error: function() {
        Swal.fire(
            'خطأ!',
            'حدث خطأ غير متوقع. يرجى المحاولة لاحقًا.',
            'error'
        );
    }
});
});
});


// لتعديل بيانات الرسالة للجلسات واتساب فتح المودال وجلب البيانات
$(document).on('click', '.edit-btn-whatsapp', function() {
var messageId = $(this).data('id');
$.ajax({
    url: 'req/get_message.php',
    method: 'GET',
    data: { id: messageId },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            $('#messageIdPhoneEdit').val(response.data.id);
            $('#officeIdPhoneEdit').val(response.data.office_id);
            $('#forWhomPhoneEdit').val(response.data.for_whom);
            $('#editEditorPhone').val(response.data.message_text);
            $('#editMessageModalPhone').modal('show');
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء جلب البيانات.', 'error');
    }
});
});
// AJAX لزر حفظ التعديلات
$('#editMsgSessionsByPhone').click(function() {
var messageText = $('#editEditorPhone').val();
var officeId = $('#officeIdPhoneEdit').val();
var forWhom = $('#forWhomPhoneEdit').val();
var messageId = $('#messageIdPhoneEdit').val();

$.ajax({
    url: 'req/edit_message.php',
    method: 'POST',
    data: {
        message_text: messageText,
        office_id: officeId,
        for_whom: forWhom,
        id: messageId
    },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            Swal.fire('تم التعديل!', 'تم تعديل الرسالة بنجاح.', 'success');
            $('#editMessageModalPhone').modal('hide');
            // تحديث الجدول أو إعادة تحميل البيانات
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء تعديل الرسالة.', 'error');
    }
});
});

// لتعديل بيانات الرسالة للمستحقات واتساب فتح المودال وجلب البيانات
$(document).on('click', '.edit-btn-dues-whatsapp', function() {
var messageId = $(this).data('id');
$.ajax({
    url: 'req/get_message.php',
    method: 'GET',
    data: { id: messageId },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            $('#messageIdDuesEditPhones').val(response.data.id);
            $('#officeIdDuesEditPhones').val(response.data.office_id);
            $('#forWhomDuesEditPhones').val(response.data.for_whom);
            $('#editEditorDuesPhones').val(response.data.message_text);
            $('#editMessageModalDuesPhones').modal('show');
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء جلب البيانات.', 'error');
    }
});
});
// AJAX لزر حفظ التعديلات
$('#editMsgSessionsByDues').click(function() {
var messageText = $('#editEditorDuesPhones').val();
var officeId = $('#officeIdDuesEditPhones').val();
var forWhom = $('#forWhomDuesEditPhones').val();
var messageId = $('#messageIdDuesEditPhones').val();

$.ajax({
    url: 'req/edit_messageDues.php',
    method: 'POST',
    data: {
        message_text: messageText,
        office_id: officeId,
        for_whom: forWhom,
        id: messageId
    },
    success: function(response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.error("Parsing error:", e);
                Swal.fire('خطأ!', 'حدث خطأ أثناء معالجة البيانات.', 'error');
                return;
            }
        }

        if (response.status === 'success') {
            Swal.fire('تم التعديل!', 'تم تعديل الرسالة بنجاح.', 'success');
            $('#editMessageModalDuesPhones').modal('hide');
            // تحديث الجدول أو إعادة تحميل البيانات
        } else {
            Swal.fire('خطأ!', response.message, 'error');
        }
    },
    error: function() {
        Swal.fire('خطأ!', 'حدث خطأ أثناء تعديل الرسالة.', 'error');
    }
});
});




$(document).on('click', '.delete-button', function() {
    document.addEventListener('touchmove', function() {}, { passive: true });
    var id = $(this).data('id');
    
    // عرض نافذة تأكيد باستخدام SweetAlert2
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سوف يتم حذف الرسالة بشكل نهائي!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم, احذفه!',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // في حالة الموافقة على الحذف، نقوم بإرسال طلب AJAX
            $.ajax({
                type: "POST",
                url: "req/delete_template.php", 
                data: {id: id},
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم !',
                        text: 'تم حذف الرسالة بنجاح.',
                        showConfirmButton: false,
                        timer: 2000,
                        willClose: function() {
                            // إعادة تحميل الصفحة بعد إغلاق النافذة
                            location.reload();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
});





