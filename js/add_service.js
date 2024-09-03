$(document).ready(function () {
    $('select').selectize({
        sortField: 'text'
    });
});

$(document).ready(function(){
    $("#navLinks li:nth-child(2) a").addClass('active');
});



$(document).ready(function() {
    $('#addService').click(function(event) {
        event.preventDefault(); // منع التصرف الافتراضي لإرسال النموذج

        var casemanagerValue = $('#casemanager').val();
        if (casemanagerValue === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد مدير الحالة',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var phone1 = $('#phone').val();
        var phone2 = $('#phone2').val();
        if (phone1 === phone2 && (phone1 !== '' && phone2 !== '')) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الهواتف متشابهة',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }


        var fieldIds = ['#service_provider', '#service_type', '#city22', '#phone', '#phone2', '#address', '#who_response', '#temporary_or_permanent', '#explain', '#evidence'];

        // Flag to track if all fields are valid
        var allFieldsValid = true;

        // Loop through each field and validate
        fieldIds.forEach(function(fieldId) {
            var fieldValue = $(fieldId).val();
            if (fieldValue === '') {
                $(fieldId).addClass('invalid');
                allFieldsValid = false;
            } else {
                $(fieldId).removeClass('invalid');
            }
        });

        // Check if any required field is empty
        if (!allFieldsValid) {
            // Show error message using Swal
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب ملء جميع الحقول المطلوبة',
                confirmButtonColor: '#dc3545'
            });
            
            return; // Stop further execution
        }


        // تسلسل بيانات النموذج
        var formData = new FormData($('#serviceForm')[0]);
        var phone1 = $('#phone').val();
        var phone2 = $('#phone2').val();
        var serviceProvider = $('#service_provider').val();
        var responsePerson = $('#who_response').val();

        
        $.ajax({
            type: 'POST',
            url: 'req/search_service.php',
            data: {
                phone: phone1,
                phone2: phone2,
                service_provider: serviceProvider,
                who_response: responsePerson,
            },
            dataType: 'json', // تحديد نوع البيانات المُرجعة من السكربت
            success: function(response) {
                if (response.status === 'exists') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه!',
                        html: response.message,
                        showCancelButton: true,
                        confirmButtonText: 'إضافة على كل حال',
                        cancelButtonText: 'إلغاء',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            continueSaving(formData);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = 'build.php';
                        }
                    });
                } else {
                    continueSaving(formData);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('حدث خطأ أثناء البحث عن السجل.');
            }
        });
    });

    // دالة لمواصلة عملية الحفظ
    function continueSaving(formData) {
        $.ajax({
            type: 'POST',
            url: 'req/save_service.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // التعامل مع الرد من الخادم
                console.log(response);
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم حفظ البيانات بنجاح في قاعدة البيانات.',
                    showConfirmButton: false,
                    timer: 2000,
                    onClose: function() {
                        window.location.href = 'build.php';
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('حدث خطأ أثناء إرسال النموذج.');
            }
        });
    }
});



$('input, textarea').on('copy', function(event) {
    event.preventDefault();
    return false;
});

// منع عملية اللصق لجميع العناصر
$('input, textarea').on('paste', function(event) {
    event.preventDefault();
    return false;
});


