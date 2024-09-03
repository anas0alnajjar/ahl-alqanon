$(document).ready(function() {
    // الحصول على معلمات URL
    var urlParams = new URLSearchParams(window.location.search);
    var tab = urlParams.get('tab');
    var collapseId = urlParams.get('collapse_id');

    // الحصول على معرف التبويب النشط من LocalStorage
    let activeTab = localStorage.getItem('activeTab');

    function openCollapseAndScroll(collapseElementId) {
        if ($(collapseElementId).length) {
            // تأخير فتح الكولابس قليلاً للتأكد من تفعيل التبويب أولاً
            setTimeout(function() {
                $(collapseElementId).addClass('show');
                // Scroll to the element
                $('html, body').animate({
                    scrollTop: $(collapseElementId).offset().top
                }, 1000);
            }, 300); // تأخير لمدة 300 ميلي ثانية
        }
    }

    if (collapseId) {
        // بناء معرف عنصر الكولابس
        var collapseElementId = "#collapseExpense" + collapseId;

        // تفعيل تبويب المصاريف أولاً
        $('#expay-info-tab').tab('show');
        openCollapseAndScroll(collapseElementId);
        // انتظار إظهار التبويب ثم فتح الكولابس
        $('#expay-info').on('shown.bs.tab', function () {
            openCollapseAndScroll(collapseElementId);
        });

        // التحقق مما إذا كان التبويب نشطًا بالفعل
        if ($('#expay-info').hasClass('active show')) {
            openCollapseAndScroll(collapseElementId);
        }
    } else if (tab) {
        // تفعيل التبويب بناءً على معلمة URL
        $('#' + tab + '-tab').tab('show');
        // تخزين التبويب النشط في LocalStorage
        localStorage.setItem('activeTab', tab + '-tab');
        // إزالة المعامل tab بعد تحميل الصفحة بدون إعادة تحميل الصفحة
        var url = new URL(window.location.href);
        url.searchParams.delete('tab');
        window.history.replaceState(null, null, url.toString());
    } else if (activeTab) {
        // تفعيل التبويب المخزن في LocalStorage إذا لم يكن هناك معلمة tab في الرابط
        $('#' + activeTab).tab('show');
    }

    // الاستماع لنقرات التبويب لحفظ التبويب النشط
    $('.nav-link').on('click', function() {
        // حفظ معرف التبويب النشط في LocalStorage
        localStorage.setItem('activeTab', $(this).attr('id')); 
    });
});

function reloadWithoutCollapseId() {
    // الحصول على الرابط الحالي
    var url = new URL(window.location.href);
    
    // حذف المعامل collapse_id من الرابط
    url.searchParams.delete('collapse_id');
    
    // إعادة تحميل الصفحة بالرابط الجديد
    window.location.href = url.toString();
}

$(document).ready(function() {
    $(document).on('keydown', function(event) {
        if ((event.ctrlKey || event.metaKey) && (event.key === 's' || event.key === 'س')) {
            event.preventDefault();
            $('#editCase').click();
        }
    });
});


$(document).on('click', '#editCase, .save-sessions', function(event) {
    event.preventDefault();
    var formData = new FormData($('#formEdit')[0]);

    var caseTitleValue = $('#case_title').val();
    if (caseTitleValue === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد عنوان القضية',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }

    var lawyerIdValue = $('#lawyer_id').val();
    if (lawyerIdValue === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد المحامي',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }

    // تحقق من جميع حقول الجلسات
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
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب ملء جميع حقول الجلسات إذا تم إضافتها',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }
    }

    // جمع القيم الجديدة للجلسات
    var newSessionNumbers = $('input[name="new_session_number[]"]').map(function() {
        return $(this).val();
    }).get();
    var newSessionDates = $('input[name="new_session_date[]"]').map(function() {
        return $(this).val();
    }).get();
    var newSessionHours = $('input[name="new_session_hour[]"]').map(function() {
        return $(this).val();
    }).get();

    // إضافة القيم الجديدة إلى formData
    for (var i = 0; i < newSessionNumbers.length; i++) {
        if (newSessionNumbers[i] === '' || newSessionDates[i] === '' || newSessionHours[i] === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب ملء جميع حقول الجلسات إذا تم إضافتها',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }
    }

    // جمع القيم الجديدة للمصاريف

    var newPay = $('input[name="newPay[]"]').map(function() {
        return $(this).val();
    }).get();
    var newAmount = $('input[name="newAmount[]"]').map(function() {
        return $(this).val();
    }).get();
    var newNotes = $('textarea[name="NewNotes[]"]').map(function() {
        return $(this).val();
    }).get();

    // إضافة القيم الجديدة إلى formData
    for (var i = 0; i < newAmount.length; i++) {
        if (newPay[i] === '' || newAmount[i] === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب ملء حقول المبلغ والتاريخ للمصروفات',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }
    }

    // جمع القيم الجديدة للدفعات
    var newMethod = $('select[name="newMethod[]"]').map(function() {
        return $(this).val();
    }).get();
    var newDate = $('input[name="newDate[]"]').map(function() {
        return $(this).val();
    }).get();
    var newAmountPaid = $('input[name="newAmountPaid[]"]').map(function() {
        return $(this).val();
    }).get();

    // إضافة القيم الجديدة إلى formData
    for (var i = 0; i < newAmountPaid.length; i++) {
        if (newAmountPaid[i] === '' || newMethod[i] === '' || newDate[i] === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب ملء حقول طريقة الدفع، والمبلغ والتاريخ',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }
    }

    // إرسال النموذج عبر Ajax
    $.ajax({
        type: 'POST',
        url: 'req/editCases.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // console.log(response);
            Swal.fire({
                icon: 'success',
                title: 'تم الحفظ بنجاح!',
                text: 'تم حفظ البيانات بنجاح في قاعدة البيانات.',
                showConfirmButton: false,
                timer: 2000,
                willClose: function() {
                    reloadWithoutCollapseId();
                    
                }
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('حدث خطأ أثناء إرسال النموذج.');
        }
    });
});




$(document).ready(function() {
    $(".deleteCardBtn").click(function() {
        var id = $(this).data('session-id');
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف الجلسة نهائياً!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم, قم بالحذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إذا قام المستخدم بالموافقة على الحذف
                $.ajax({
                    type: "POST",
                    url: "req/delete_session.php",
                    data: {sessions_id: id},
                    success: function(response) {
                        // إعادة تحميل الجدول بعد الحذف بنجاح
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // التعامل مع الأخطاء
                        console.error(xhr.responseText);
                    }
                });
            }else{
                location.reload();
            }
        });
    });
});


// For Edit Client
$(document).ready(function() {
    $('#editClient').click(function(event) {
        event.preventDefault(); // منع التصرف الافتراضي لإرسال النموذج
        var formData = new FormData($('#clientEdit')[0]);

        var client_nameValue = $('#fname').val();
        if (client_nameValue === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد الاسم الأول للعميل',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }

        var caseTitleValue = $('#lname').val();
        if (caseTitleValue === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد العائلة للعميل',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }

        var lawyerIdValue = $('#email_address').val();
        if (lawyerIdValue === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد البريد الإلكتروني للعميل',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }

        $.ajax({
            type: 'POST',
            url: 'req/clientEdit.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var responseData = JSON.parse(response);

                if (responseData.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم تحديث البيانات بنجاح .',
                        showConfirmButton: false,
                        timer: 2000,
                        willClose: function() {
                            // Use window.location.reload() instead of window.reload
                            window.location.reload(); 
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

$(document).ready(function() {
    // فتح المودال عند الضغط على الزر
    $('#addDocBtn').click(function() {
        $('#add_doc_modal').modal('show');
    });
    $('.close-modal1').click(function() {
        $('#add_doc_modal').modal('hide');
    });
    $('.closeDocumentation').click(function() {
        $('#add_doc_modal').modal('hide');
    });
});


$(document).ready(function() {
    $(".delete-doc").click(function() {
        var id = $(this).data('document_id');

        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من استعادة هذا العقد بعد الحذف!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "req/delete_document.php",
                    data: { document_id: id },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم !',
                            text: 'تم حذف العقد بنجاح وبشكل لا يمكن استرجاعه.',
                            showConfirmButton: false,
                            timer: 2000,
                            willClose: function() {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء محاولة حذف العقد.',
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    });
});



    $(document).ready(function(){
        $('#addReport').click(function(e){
            e.preventDefault();
            
            var formData = new FormData();
            formData.append('content', $('#hiddenTextarea').val());
            formData.append('client_id_doc', $('#client_id_doc').val());
            formData.append('lawyer_id_doc', $('#lawyer_id_doc').val());
            formData.append('case_id_doc', $('#case_id_doc').val());
            formData.append('office_id_doc', $('#office_id_doc').val());
            formData.append('document_title', $('#document_title').val());
            formData.append('notes_for_doc', $('#notes_for_doc').val());
            formData.append('attachments', $('#attachments')[0].files[0]);

            
            $.ajax({
                url: 'req/addContract.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    response = JSON.parse(response); // تحويل الاستجابة إلى كائن JavaScript
                    console.log(response);
                    if(response.hasOwnProperty('error')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ !',
                            text: 'املء حقل العنوان الوثيقة رجاءً',
                            showConfirmButton: true,
                            willClose: function() {
                            }
                        });
                    } else if(response.hasOwnProperty('success')) {
                        Swal.fire({
                            icon: 'success',
                            title: 'نجاح !',
                            text: 'تم حفظ العقد بنجاح',
                            showConfirmButton: false,
                            timer: 2000,
                            willClose: function() {
                                location.reload();
                            }
                        });
                    }
                },
                error: function(xhr, status, error){
                }
            });
        });
    });


    $(document).ready(function() {
        $('#documentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var documentId = button.data('id');
            
            $.ajax({
                url: 'req/showContract.php',
                type: 'GET',
                data: { document_id: documentId },
                success: function(response) {
                    $('#documentModalBody').html(response);
                }
            });
        });

        $('#documentModal').on('hidden.bs.modal', function () {
            $('#documentModalBody').html(''); // إزالة المحتوى عند إغلاق المودال
        });
    });

//    For Delete expensses
    $(document).ready(function() {
        $(".deleteExpenses").click(function() {
            var id = $(this).data('expenses-id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف البند نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم, قم بالحذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // إذا قام المستخدم بالموافقة على الحذف
                    $.ajax({
                        type: "POST",
                        url: "req/deleteExpenses.php",
                        data: {expenses_id: id},
                        success: function(response) {
                            // إعادة تحميل الجدول بعد الحذف بنجاح
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            // التعامل مع الأخطاء
                            console.error(xhr.responseText);
                        }
                    });
                }else{
                    location.reload();
                }
            });
        });
    });
    

    
    document.addEventListener("DOMContentLoaded", function() {
        var addButton = document.getElementById('addPayment');
        var cardsContainer = document.getElementById('dynamic_cards_payment');
        var rowIndex = 0;
    
        // دالة لتحويل التاريخ الميلادي إلى هجري
        function convertToHijri(gregorianDate) {
            if (gregorianDate) {
                return moment(gregorianDate, 'YYYY-MM-DD').format('iYYYY-iMM-iDD');
            }
            return '';
        }
    
        // دالة لتحويل التاريخ الهجري إلى ميلادي
        function convertToGregorian(hijriDate) {
            if (hijriDate) {
                return moment(hijriDate, 'iYYYY-iMM-iDD').format('YYYY-MM-DD');
            }
            return '';
        }
    
        // مراقبة التغيرات في حقول التواريخ
        function attachDateChangeEvents(card) {
            var gregorianInput = card.querySelector('.geo-data-input');
            var hijriInput = card.querySelector('.hijri-date-input');
    
            gregorianInput.addEventListener('input', function() {
                hijriInput.value = convertToHijri(gregorianInput.value);
            });
    
            hijriInput.addEventListener('change', function() {
                gregorianInput.value = convertToGregorian(hijriInput.value);
            });
    
            $(hijriInput).hijriDatePicker({
                locale: "ar-sa",
                format: "DD-MM-YYYY",
                hijriFormat: "iYYYY-iMM-iDD",
                dayViewHeaderFormat: "MMMM YYYY",
                hijriDayViewHeaderFormat: "iMMMM iYYYY",
                showSwitcher: true,
                allowInputToggle: true,
                useCurrent: false,
                isRTL: true,
                viewMode: 'days',
                keepOpen: false,
                hijri: true,
                debug: false,
                showClear: true,
                showClose: true
            }).on('dp.change', function(e) {
                gregorianInput.value = convertToGregorian(e.date.format('iYYYY-iMM-iDD'));
            });
        }
    
        // تطبيق الأحداث للعناصر الموجودة بالفعل
        var existingCards = document.querySelectorAll('#dynamic_cards_payment .col-sm-12.col-md-6.col-lg-4');
        existingCards.forEach(function(card) {
            attachDateChangeEvents(card);
        });
    
        addButton.addEventListener('click', function() {
            rowIndex++;
    
            var newCard = document.createElement('div');
            newCard.className = 'col-sm-12 col-md-6 col-lg-4 mb-3';
            newCard.innerHTML = `
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="mb-2">طريقة الدفع</label>
                            <select class="form-control form-control-sm" name="newMethod[]" required>
                                <option value="" disabled selected>اختر الطريقة...</option>
                                <option value="كاش" >كاش</option>
                                <option value="تحويل نقدي" >تحويل نقدي</option>
                                <option value="أخرى" >أخرى</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="mb-2">التاريخ ميلادي</label>
                            <input type="date" class="form-control form-control-sm geo-data-input" name="newDate[]" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2">التاريخ هجري</label>
                            <input type="text" class="form-control form-control-sm hijri-date-input" name="newDateHiri[]" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2">المبلغ</label>
                            <input type="number" class="form-control form-control-sm" name="newAmountPaid[]" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2">الملاحظات</label>
                            <textarea class="form-control form-control-sm" name="new_payments_notes[]" rows="2"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="button" style="min-width:49%" class="btn btn-warning btn-sm btn-block save-sessions">حفظ</button>
                            <button type="button" style="min-width:49%" class="btn btn-danger btn-sm btn-block deletePayment">حذف</button>
                        </div>
                    </div>
                </div>
            `;
            newCard.dataset.rowIndex = rowIndex;
            cardsContainer.appendChild(newCard);
            attachDateChangeEvents(newCard);
    
            // التركيز على البطاقة الجديدة وجعلها مرئية بالكامل للمستخدم
            newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    
        cardsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('deletePayment')) {
                var card = event.target.closest('.col-sm-12.col-md-6.col-lg-4');
                var rowIndex = card.dataset.rowIndex;
                card.remove();
            }
        });
    });
    
    
    
    $(document).ready(function() {
        $(".deletePayment").click(function() {
            var id = $(this).data('payment-id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف الدفعة نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم, قم بالحذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // إذا قام المستخدم بالموافقة على الحذف
                    $.ajax({
                        type: "POST",
                        url: "req/deletePayment.php",
                        data: {payment_id: id},
                        success: function(response) {
                            // إعادة تحميل الجدول بعد الحذف بنجاح
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            // التعامل مع الأخطاء
                            console.error(xhr.responseText);
                        }
                    });
                }else{
                    location.reload();
                }
            });
        });
    });


// To show upload files modal
$('#addFiles1').click(function() {
    $('#upload_files_modal').modal('show');
}); 
$('#addHelper').click(function() {
    $('#helperModal').modal('show');
}); 

$('.close-modal2').click(function() {
    $('#upload_files_modal').modal('hide');
}); 
$('.close').click(function() {
    $('#helperModal').modal('hide');
}); 



// To download Files
function downloadFile(fileName) {
    // إنشاء عنصر <a> مؤقت لتنفيذ التحميل
    var link = document.createElement("a");
    link.href = "../../Lawyer/files/" + fileName;
    link.download = fileName; // اسم الملف المحمّل
    
    // إضافة العنصر إلى DOM وتنفيذ التحميل
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function deleteFile(button) {
    var cardBody = button.closest('.card-body');
    var fileId = cardBody.querySelector('input[type="hidden"]').value;

    if (!fileId) {
        Swal.fire(
            'خطأ!',
            'لم يتم العثور على معرف الملف.',
            'error'
        );
        return;
    }

    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "لن تتمكن من التراجع عن هذا!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذفه!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('req/deleteFile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: fileId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var card = button.closest('.col-sm-12.col-md-6.col-lg-4');
                    card.remove();
                    Swal.fire(
                        'تم الحذف!',
                        'تم حذف الملف بنجاح.',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'خطأ!',
                        data.message || 'حدث خطأ أثناء محاولة حذف الملف.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'خطأ!',
                    'حدث خطأ أثناء محاولة حذف الملف.',
                    'error'
                );
            });
        }
    });
}

$('#saveHelper').on('click', function(){
    var userName = $('#usernameHelper').val();
    var helperName = $('#helper_nameModal').val();
    var pass = $('#pass').val();
    var lawyer_id = $('#lawyer_id555').val();
    var role_id = $('#role_idHelper').val();
    if (userName === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد اسم المستخدم',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (pass === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد كلمة السر',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (helperName === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد اسم المساعد',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (lawyer_id === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد المحامي',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (role_id === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد الدور',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }

    $.ajax({
        url: 'req/save_helper.php',
        type: 'POST',
        data: $('#helperForm').serialize(),
        success: function(response){
            var jsonResponse = JSON.parse(response);
            if (jsonResponse.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح',
                    text: jsonResponse.message
                }).then(function(){
                    $('#helperModal').modal('hide');
                    $('#helperForm')[0].reset();
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: jsonResponse.message
                });
            }
        },
        error: function(){
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء حفظ البيانات'
            });
        }
    });
});
    

document.getElementById('printCon').addEventListener('click', function() {
    var caseId = document.getElementById('idForPrint').value; // افترض أن لديك حقل إدخال يحتوي على معرف القضية
    window.location.href = 'print_report.php?id=' + caseId;
});

document.getElementById('printInfo').addEventListener('click', function() {
    var caseId = document.getElementById('idForPrint').value; // افترض أن لديك حقل إدخال يحتوي على معرف القضية
    window.location.href = 'print_case_info.php?id=' + caseId;
});

document.getElementById('printClient').addEventListener('click', function() {
    var caseId = document.getElementById('ClientID').value; // افترض أن لديك حقل إدخال يحتوي على معرف القضية
    window.location.href = 'print_client_info.php?id=' + caseId;
});



document.getElementById('id_picture').addEventListener('click', function() {
    var src = this.src;
    window.open(src, '_blank');
});


function sendReminder(dues, phoneNumber, clientName, caseTitle) {
    var formattedDues = numberWithCommas(dues);
    var message = encodeURIComponent("مرحبًا " + clientName + "،\nنود تذكيرك بأن هناك دفعة لم يتم تسديدها بعد بمبلغ " + formattedDues + "  مرتبطة القضية: " + caseTitle + ".");
    var whatsappLink = "https://api.whatsapp.com/send?phone=" + phoneNumber + "&text=" + message;
    window.open(whatsappLink);
}

// Function to add commas to numbers for better formatting
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


