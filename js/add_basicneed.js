$(document).ready(function () {
    $('#type_ill').selectize({
        sortField: 'text'
    });
});
$(document).ready(function () {
    $('#community_ar').selectize({
        sortField: 'text'
    });
});

$(document).ready(function(){
    $("#navLinks li:nth-child(2) a").addClass('active');
});




$(document).ready(function() {
    $('#addCase').click(function(event) {
        event.preventDefault(); // منع التصرف الافتراضي لإرسال النموذج

        var casemanagerValue = $('#social_worker').val();
        if (casemanagerValue === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد العامل الاجتماعي',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var phone1 = $('#father_phone').val();
        var phone2 = $('#mother_phone').val();
        if (phone1 === phone2 && (phone1 !== '' && phone2 !== '')) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الهواتف متشابهة',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }
        
        var nat_father = $('#nat_father').val();
        var nat_mother = $('#nat_mother').val();
        var national_num = $('#national_num').val();
        var idrn2 = $('#idrn2').val();
        if ((nat_father.length < 11 && nat_father !== '') || (nat_mother.length < 11 && nat_mother !== '') || (idrn2.length < 11 && idrn2 !== '') || (national_num.length < 11 && national_num !== '') ) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الأرقام الوطنية يجب أن تكون 11 محرفًا على الأقل اضف فراغات بحال كانت أقل',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }
        


        var fatherName = $('#father_name').val();
        var motherName = $('#mother_name').val();
        var name_cus = $('#name_cus').val();
        var recipientName2 = $('#recipientname2').val();

        // التحقق من أن الحقول غير فارغة قبل التحقق من صحة الأسماء
        if (fatherName !== '' && motherName !== '' && name_cus !== '' && recipientName2 !== '') {
            // التحقق مما إذا كانت الأسماء ثلاثية وإذا لم تكن تظهر رسالة خطأ
            if (!isTripletName(fatherName) || !isTripletName(motherName) || !isTripletName(name_cus) || !isTripletName(recipientName2)) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى التأكد من أن الأسماء مكونة من الاسم الأول والاسم الأوسط واسم العائلة.',
                    confirmButtonColor: '#dc3545'
                });
                return; // التوقف عن متابعة التنفيذ
            }
        } else {
            // عرض رسالة تنبيه في حالة عدم ملء جميع الحقول المطلوبة
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'يرجى تعبئة اسم الأب والأم، المستفيدين.',
                confirmButtonColor: '#ffc107'
            });
            return; // التوقف عن متابعة التنفيذ
        }

        // شرط للتحقق مما إذا كانت الاسم ثلاثية
        function isTripletName(name) {
            var nameParts = name.trim().split(" ");
            return nameParts.length >= 3;
        }

        var fieldIds = ['#father_name', '#father_phone', '#nat_father', '#mother_name', '#nat_mother', '#mother_phone', '#n_family_book', '#dis_ex', '#hedead_by_female', '#type_of_headed', '#address'];

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
        var formData = new FormData($('#myForm')[0]);
        var father_name = $('#father_name').val();
        var mother_name = $('#mother_name').val();
        var nat_mother = $('#nat_mother').val();
        var nat_father = $('#nat_father').val();
        var n_family_book = $('#n_family_book').val();
        var national_num = $('#national_num').val();
        var recipientname2 = $('#recipientname2').val();
        var name_cus = $('#name_cus').val();
        var idrn2 = $('#idrn2').val();

        // بحث في قاعدة البيانات عن اسم الطفل
        $.ajax({
            type: 'POST',
            url: 'req/search_basicneed.php',
            data: {
                father_name: father_name,
                mother_name: mother_name,
                nat_mother: nat_mother,
                nat_father: nat_father,
                n_family_book: n_family_book,
                national_num: national_num,
                idrn2: idrn2,
                recipientname2: recipientname2,
                name_cus: name_cus
            },
            dataType: 'json', // تحديد نوع البيانات المُرجعة من السكربت
            success: function(response) {
                if (response.status === 'exists') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه!',
                        html: response.message,
                        showCancelButton: true,
                        confirmButtonText: 'موافق',
                        cancelButtonText: 'إلغاء',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            continueSaving(formData);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = 'basic_needs.php';
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
            url: 'req/process_form_basicneed.php',
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
                        window.location.href = 'basic_needs.php';
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





