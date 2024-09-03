$(document).ready(function () {
    $('select').selectize({
        sortField: 'text'
    });
});


$(document).ready(function() {
    // فتح المودال عند الضغط على الزر
    $('#editClientBtn').click(function() {
        $('#edit_client_modal').modal('show');
    });
});

$(document).ready(function() {
    $('.close-modal2').click(function() {
        $('#edit_client_modal').modal('hide');
}); });

$(document).ready(function() {
    $('#addCase').click(function(event) {
        event.preventDefault(); // منع التصرف الافتراضي لإرسال النموذج
        var formData = new FormData($('#myForm')[0]);

        var requiredFields = [
            { name: 'lawyer_id', message: 'يرجى اختيار اسم المحامي.' },
            { name: 'case_title', message: 'يرجى ملء حقل عنوان القضية.' },
            { name: 'client_id', message: 'يرجى اختيار الموكل.' }
        ];

        var isValid = true;
        var firstInvalidField = null;
        var firstInvalidMessage = '';

        requiredFields.forEach(function(field) {
            var input = $(`[name="${field.name}"]`);
            if (input.length > 0) {
                if (input[0].tagName.toLowerCase() === 'select' && input[0].selectize) {
                    // التحقق من حقول selectize
                    var selectizeElement = input[0].selectize;
                    if (!selectizeElement.getValue()) {
                        $(selectizeElement.$control).addClass('error');
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = selectizeElement.$control[0];
                            firstInvalidMessage = field.message;
                        }
                    } else {
                        $(selectizeElement.$control).removeClass('error');
                    }
                } else {
                    // التحقق من الحقول العادية
                    if (!input.val().trim()) {
                        input.addClass('error');
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = input[0];
                            firstInvalidMessage = field.message;
                        }
                    } else {
                        input.removeClass('error');
                    }
                }
            }
        });

        // التحقق من جميع حقول الجلسات
        var sessionNumbers = $('input[name="session_number[]"]').map(function() {
            return $(this).val();
        }).get();
        var sessionDates = $('input[name="session_date[]"]').map(function() {
            return $(this).val();
        }).get();
        var sessionHours = $('input[name="session_hour[]"]').map(function() {
            return $(this).val();
        }).get();

        // التحقق من أن جميع حقول الجلسات ممتلئة إذا كانت موجودة
        for (var i = 0; i < sessionNumbers.length; i++) {
            if (sessionNumbers[i] === '' || sessionDates[i] === '' || sessionHours[i] === '') {
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = $('input[name="session_number[]"]')[i];
                    firstInvalidMessage = 'يجب ملء جميع حقول الجلسات إذا تم إضافتها';
                }
                $('input[name="session_number[]"]')[i].classList.add('error');
                $('input[name="session_date[]"]')[i].classList.add('error');
                $('input[name="session_hour[]"]')[i].classList.add('error');
                break;
            } else {
                $('input[name="session_number[]"]')[i].classList.remove('error');
                $('input[name="session_date[]"]')[i].classList.remove('error');
                $('input[name="session_hour[]"]')[i].classList.remove('error');
            }
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: firstInvalidMessage,
                confirmButtonColor: '#dc3545'
            }).then(function() {
                if (firstInvalidField) {
                    $('html, body').animate({
                        scrollTop: $(firstInvalidField).offset().top - 100
                    }, 500);
                    firstInvalidField.focus();
                }
            });
        } else {
            $.ajax({
                type: 'POST',
                url: 'req/process_form.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم حفظ البيانات بنجاح في قاعدة البيانات.',
                        showConfirmButton: false,
                        timer: 2000,
                        willClose: function() {
                            window.location.href = 'cases.php';
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء إرسال النموذج.'
                    });
                }
            });
        }
    });
});




// For Add Client
$(document).ready(function() {
    $('#addClient').click(function(event) {
        event.preventDefault(); // منع التصرف الافتراضي لإرسال النموذج
        var formData = new FormData($('#clientAdd')[0]);

        var valid = true;

        // وظيفة للتحقق من الحقول المطلوبة
        function checkField(selector, errorMessage) {
            var field = $(selector);
            if (field.val().trim() === '') {
                field.addClass('error'); // إضافة كلاس error
                field.focus(); // التركيز على العنصر
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: errorMessage,
                    confirmButtonColor: '#dc3545'
                });
                valid = false;
                return false;
            } else {
                field.removeClass('error'); // إزالة كلاس error إذا كان العنصر مليء
                return true;
            }
        }

        // التحقق من الحقول المطلوبة
        if (!checkField('#fname', 'يجب تحديد الاسم الأول للعميل')) return;
        if (!checkField('#lname', 'يجب تحديد العائلة للعميل')) return;
        if (!checkField('#email_address', 'يجب تحديد البريد الإلكتروني للعميل')) return;
        if (!checkField('#office_idModal', 'يجب تحديد المكتب للعميل')) return;
        if (!checkField('#phone', 'يجب تحديد هاتف الموكل')) return;
        if (!checkField('#date_of_birth', 'يجب تحديد مواليد الموكل')) return;
        if (!checkField('#city', 'يجب تحديد مدينة الموكل')) return;

        if (!valid) return;

        $.ajax({
            type: 'POST',
            url: 'req/clientAdd.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var responseData = JSON.parse(response);

                if (responseData.clientId) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم حفظ البيانات بنجاح في قاعدة البيانات.',
                        showConfirmButton: false,
                        timer: 2000,
                        willClose: function() {
                            window.location.href = 'add_case.php';
                            $('#client_id').val(responseData.clientId); // تحديث حقل client_id بالقيمة الجديدة
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: responseData.error || 'حدث خطأ أثناء حفظ البيانات',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء إرسال النموذج.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});



