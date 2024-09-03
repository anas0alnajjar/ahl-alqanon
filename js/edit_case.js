$(document).ready(function () {
    $('select').selectize({
        sortField: 'text'
    });
});

$(document).ready(function(){
    $("#navLinks li:nth-child(2) a").addClass('active');
});

$(document).ready(function() {
    $('#updateCase').click(function(event) {
        event.preventDefault(); // Prevent the default form submission

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

        var phone1 = $('#phonern1').val();
        var phone2 = $('#phonern2').val();
        if (phone1 === phone2 && (phone1 !== '' && phone2 !== '')) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الهواتف متشابهة',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }
        
        var idrn1 = $('#idrn1').val();
        var idrn2 = $('#idrn2').val();
        if ((idrn1.length < 11 && idrn1 !== '') || (idrn2.length < 11 && idrn2 !== '')) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الأرقام الوطنية يجب أن تكون 11 محرفًا على الأقل اضف فراغات بحال كانت أقل',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }
        
        var relativerelation = $('#relativerelation').val();
        var mothername = $('#mothername').val();
        var recipientname1 = $('#recipientname1').val();
        if (relativerelation === "Mother/الأم" && mothername !== recipientname1) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'اسم الأم غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var relativerelation = $('#relativerelation').val();
        var idmother = $('#idmother').val();
        var idr1 = $('#idrn1').val();
        if (relativerelation === "Mother/الأم" && idmother !== idr1) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'وطني الأم غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var relativerelation = $('#relativerelation').val();
        var idfather = $('#idfather').val();
        var idr1 = $('#idrn1').val();
        if (relativerelation === "Father/ الاب" && idfather !== idr1) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'وطني الأب غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var relativerelation = $('#relativerelation').val();
        var fathername = $('#fathername').val();
        var recipientname1 = $('#recipientname1').val();
        if (relativerelation === "Father/ الاب" && fathername !== recipientname1) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'اسم الأب غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

// المستلم الثاني

        var relative_relation2 = $('#relative_relation2').val();
        var mothername = $('#mothername').val();
        var recipientname2 = $('#recipientname2').val();
        if (relative_relation2 === "Mother/الأم" && mothername !== recipientname2) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'اسم الأم غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var relative_relation2 = $('#relative_relation2').val();
        var idmother = $('#idmother').val();
        var idr2 = $('#idrn2').val();
        if (relative_relation2 === "Mother/الأم" && idmother !== idr2) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'وطني الأم غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var relative_relation2 = $('#relative_relation2').val();
        var idfather = $('#idfather').val();
        var idr2 = $('#idrn2').val();
        if (relative_relation2 === "Father/ الاب" && idfather !== idr2) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'وطني الأب غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var relative_relation2 = $('#relative_relation2').val();
        var fathername = $('#fathername').val();
        var recipientname2 = $('#recipientname2').val();
        if (relative_relation2 === "Father/ الاب" && fathername !== recipientname2) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'اسم الأب غير صحيح! طابقه مع حقل الاستلام رجاءً',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }


        var educationStatutsValue = $('#educationstatuts').val();
        var atAnyClassValue = $('#atanyclass').val();
        if (educationStatutsValue !== 'go to school/يذهب الى المدرسة' && atAnyClassValue !== '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الطفل غير مسجل بالمدرسة أزل الصف!',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }
        
        var placestatus = $('#placestatus').val();
        var hisplace = $('#hisplace').val();
        if (placestatus !== "IDP/نازحة" && hisplace !== '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: ' السكن يوضع فقط بحالة النزوح!',
                confirmButtonColor: '#dc3545'
            });
            return; // Stop further execution
        }

        var hascard = $('#hascard').val();
        var idcard = $('#idcard').val();
        var evidencedisabilty = $('#evidencedisabilty').val();

        // التحقق مما إذا كانت الإعاقة مثبتة ولا توجد بطاقة إعاقة
        if (evidencedisabilty === "Disability Card" && (hascard !== "Yes/ نعم" || idcard === '')) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'تأكد من تعبئة حقول بطاقة الإعاقة بشكل صحيح!',
                confirmButtonColor: '#dc3545'
            });
            return; // التوقف عن متابعة التنفيذ
        }

        var fatherName = $('#fathername').val();
        var motherName = $('#mothername').val();
        var childName = $('#childname').val();
        var recipientName1 = $('#recipientname1').val();
        var recipientName2 = $('#recipientname2').val();

        // التحقق من أن الحقول غير فارغة قبل التحقق من صحة الأسماء
        if (fatherName !== '' && motherName !== '' && childName !== '' && recipientName1 !== '' && recipientName2 !== '') {
            // التحقق مما إذا كانت الأسماء ثلاثية وإذا لم تكن تظهر رسالة خطأ
            if (!isTripletName(fatherName) || !isTripletName(motherName) || !isTripletName(childName) || !isTripletName(recipientName1) || !isTripletName(recipientName2)) {
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
                text: 'يرجى تعبئة اسم الطفل، والأب والأم، والمستلمين.',
                confirmButtonColor: '#ffc107'
            });
            return; // التوقف عن متابعة التنفيذ
        }

        // شرط للتحقق مما إذا كانت الاسم ثلاثية
        function isTripletName(name) {
            var nameParts = name.trim().split(" ");
            return nameParts.length >= 3;
        }


        

        var fieldIds = ['#childname', '#fathername', '#mothername', '#datebirthchild', '#recipientname1', '#recipientname2', '#relativerelation', '#idrn1', '#phonern1', '#phonern2', '#idrn2'];

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

        // Serialize form data
        var formData = new FormData($('#myForm')[0]);

        // Send form data via AJAX
        $.ajax({
            type: 'POST',
            url: 'req/update_case.php', // Replace 'process_form.php' with the URL of your server-side script
            data: formData,
            processData: false, // Tell jQuery not to process the data
            contentType: false, // Tell jQuery not to set contentType
            success: function(response) {
                // Handle the response from the server
                console.log(response);
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم حفظ البيانات بنجاح في قاعدة البيانات.',
                    showConfirmButton: false,
                    timer: 2000, // يختفي الإشعار بعد 2000 مللي ثانية (2 ثانية)
                    willClose: function() {
                        // Redirect to test.php after successful submission
                        // window.location.href = 'test.php';
                    }
                });

            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(xhr.responseText);
                alert('Error occurred while submitting the form.');
            }
        });
    });
});


document.addEventListener("DOMContentLoaded", function() {
    var addButton = document.getElementById('addRowBtn');
    var tableBody = document.querySelector('#dynamic_table1 tbody');

    // إضافة صف جديد عند النقر على الزر
    addButton.addEventListener('click', function() {
        var newRow = document.createElement('tr');
        newRow.innerHTML = `
<td>
    <select class="form-select" name="payment_batch[]" id="payment_batch" required>
        <option value="الدفعة الأولى">الدفعة الأولى</option>
        <option value="الدفعة الثانية">الدفعة الثانية</option>
        <option value="الدفعة الثالثة">الدفعة الثالثة</option>
        <option value="الدفعة الرابعة">الدفعة الرابعة</option>
        <option value="الدفعة الخامسة">الدفعة الخامسة</option>
        <option value="الدفعة السادسة">الدفعة السادسة</option>
        <option value="الدفعة السابعة">الدفعة السابعة</option>
        <option value="الدفعة الثامنة">الدفعة الثامنة</option>
    </select>
</td>
<td>
    <select class="form-select" name="payment_status[]" id ="payment_status" required>
        <option value="received">received the payment</option>
        <option value="not_received">didn't received the payment</option>
    </select>
</td>
<td><input type="text" class="form-control" name="reason[]" id="reason"></td>
<td><input type="text" class="form-control" name="value[]" id="value" required></td>
<td><input type="date" class="form-control" name="receipt_date[]" id="receipt_date" required></td>
<td><input type="text" class="form-control" name="receipt_number[]" id="receipt_number" required></td>
<td><button type="button" class="btn btn-danger deleteRowBtn">حذف</button></td>
`;

        tableBody.appendChild(newRow);
    });

    // حذف الصف عند النقر على زر الحذف
    tableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('deleteRowBtn')) {
            var row = event.target.closest('tr');
            row.remove();
        }
    });
});
$(document).ready(function() {
      // فتح المودال عند الضغط على الزر
      $('#editClientBtn').click(function() {
          $('#edit_client_modal').modal('show');
      }); }); 
      $(document).ready(function() {
    $('.close-modal2').click(function() {
        $('#edit_client_modal').modal('hide');
    }); });

    $(document).ready(function() {
        $("#money1").submit(function(event) {
            event.preventDefault(); // منع النموذج من إرسال البيانات عبر الطريقة التقليدية
    
            var formData = $(this).serialize(); // جمع بيانات النموذج
    
            var payment_status = $('#payment_status').val();
            var reason = $('#reason').val();
    
            // التحقق مما إذا كانت الإعاقة مثبتة ولا توجد بطاقة إعاقة
            if (payment_status === "not_received" && (reason === '' )) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'ما سبب عدم الاستلام؟!',
                    confirmButtonColor: '#dc3545'
                });
                return; // التوقف عن متابعة التنفيذ
            }
    
            $.ajax({
                type: "POST",
                url: "req/money.php", 
                
                data: formData,
                
                success: function(response) {
                    // التعامل مع الاستجابة بعد الإرسال بنجاح
                    console.log(response);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ!',
                        text: 'تم تحديث بيانات الحوالة بنجاح.',
                        showConfirmButton: false,
                        timer: 2000, 
                        willClose: function() {
                            $(document).ready(function() {
                             $('#edit_client_modal').modal('hide');
                             location.reload();
                            });
                        }
                    }); // يمكنك تغيير هذا لتناسب احتياجاتك
                },
                error: function(xhr, status, error) {
                    // التعامل مع الأخطاء في حالة فشل الإرسال
                    console.error(xhr.responseText);
                }
            });
        });
    });


    $(document).ready(function() {
        $(".delete-row").click(function() {
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "req/delete_money.php", 
                data: {delete_id: id},
                success: function(response) {
                    // إعادة تحميل الجدول بعد الحذف بنجاح
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // التعامل مع الأخطاء
                    console.error(xhr.responseText);
                }
            });
        });
    });

$(document).ready(function() {
    
    $('.close-modal1').click(function() {
        $('#edit_doc_modal').modal('hide');
    }); });
    $('.close-modal2').click(function() {
        $('#edit_service_modal').modal('hide');
    }); 

        // فتح المودال عند الضغط على الزر
    $('#editDocBtn').click(function() {
        $('#edit_doc_modal').modal('show');
    });
    $('#editService').click(function() {
        $('#edit_service_modal').modal('show');
    });


$(document).ready(function() {
    $(".delete-doc").click(function() {
        
        var id = $(this).data('document_id');
        $.ajax({
            type: "POST",
            url: "req/delete_document.php", 
            data: {document_id: id},
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم !',
                    text: 'تم حذف التقرير بنجاح وبشكل لا يمكن استرجاعه.',
                    showConfirmButton: false,
                    timer: 2000, // يختفي الإشعار بعد 2000 مللي ثانية (2 ثانية)
                    willClose: function() {
                        location.reload();
                        // Redirect to test.php after successful submission
                        // window.location.href = 'test.php';
                    }
                });
                
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء
                console.error(xhr.responseText);
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
    });});


    $(document).ready(function(){
        $('#addReport').click(function(e){
            e.preventDefault();
            
            var formData = new FormData();
            formData.append('content', $('#edit').val());
            formData.append('supervisor_id', $('#supervisor_id').val());
            formData.append('casemanager_id', $('#casemanager_id').val());
            formData.append('case_id', $('#case_id').val());
            formData.append('document_title', $('#document_title').val());
            formData.append('attachments', $('#attachments')[0].files[0]);

            
            $.ajax({
                url: 'req/add_report.php',
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
                            text: 'حدث خطأ، حاول مرة أخرى، أو تأكد من ملء الحقول',
                            showConfirmButton: true,
                            willClose: function() {
                            }
                        });
                    } else if(response.hasOwnProperty('success')) {
                        Swal.fire({
                            icon: 'success',
                            title: 'نجاح !',
                            text: 'تم حفظ التقرير بنجاح',
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
    


// Here For the Ref


$(document).ready(function() {
    $("#service").submit(function(event) {
        event.preventDefault(); // منع النموذج من إرسال البيانات عبر الطريقة التقليدية

        var formData = $(this).serialize(); // جمع بيانات النموذج

        var ser_ref = $('#ser_ref').val();
        var spill_it = $('#spill_it').val();

        // التحقق مما إذا كانت الإعاقة مثبتة ولا توجد بطاقة إعاقة
        if (ser_ref === "اخرى" && (spill_it === '' )) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'ما هو نوع الإحالة؟!',
                confirmButtonColor: '#dc3545'
            });
            return; // التوقف عن متابعة التنفيذ
        }
        if (ser_ref !== "اخرى" && (spill_it !== '' )) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: ' حقل (ما هي؟) يوضع فقط بحالة كان نوع الإحالة أُخرى!',
                confirmButtonColor: '#dc3545'
            });
            return; // التوقف عن متابعة التنفيذ
        }

        $.ajax({
            type: "POST",
            url: "req/ref-add.php", 
            
            data: formData,
            
            success: function(response) {
                // التعامل مع الاستجابة بعد الإرسال بنجاح
                console.log(response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ!',
                    text: 'تم تحديث بيانات الإحالة بنجاح.',
                    showConfirmButton: false,
                    timer: 2000, 
                    willClose: function() {
                        $(document).ready(function() {
                         $('#edit_service_modal').modal('hide');
                         location.reload();
                        });
                    }
                }); // يمكنك تغيير هذا لتناسب احتياجاتك
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء في حالة فشل الإرسال
                // console.error(xhr.responseText);
            }
        });
    });
});


$(document).ready(function() {
    $(".delete-service").click(function() {
        var id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "req/delete_ref.php", // 
            data: {ref_id: id},
            success: function(response) {
                // إعادة تحميل الجدول بعد الحذف بنجاح
                location.reload();
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء
                console.error(xhr.responseText);
            }
        });
    });
});


$('.close-modal2').click(function() {
    $('#edit_visit_modal').modal('hide');
}); 

    
$('#editVisitBtn').click(function() {
    $('#edit_visit_modal').modal('show');
});
$('.close-modal2').click(function() {
    $('#add_visit_modal').modal('hide');
}); 

    
$('#addVisit').click(function() {
    $('#add_visit_modal').modal('show');
});
    
$('#addVisit').click(function() {
    $('#edit_visit_modal').modal('hide');
});



$('#pro2').click(function() {
    $('#protectionModal').modal('show');
});
$('#pro3').click(function() {
    $('#protectionModal3').modal('show');
});






// document.getElementById('getLocationButton').addEventListener('click', function() {
//     if (navigator.geolocation) {
//         navigator.geolocation.getCurrentPosition(function(position) {
//             var latitude = position.coords.latitude;
//             var longitude = position.coords.longitude;
//             fetch('https://api.ipgeolocation.io/ipgeo?apiKey=8bf824b3878144378db056989bd88890')
//                 .then(response => response.json())
//                 .then(data => {
//                     // إنشاء رابط يحتوي على الإحداثيات الجغرافية
//                     var mapLink = "https://www.google.com/maps/place/" + latitude + "," + longitude;
//                     // عرض الرابط في حقل الإدخال
//                     document.getElementById('location').value = mapLink;
//                     // إظهار حقل الإدخال بعد وصول المعلومات
//                     document.getElementById('location').style.display = "block";
//                     // إذا كانت المدينة ليست سوريا، عرض رسالة تنبيه للمستخدم
//                     if (data.country_name !== "Syria") {
//                         alert("تنبيه: يبدو أن موقعك ليس في سوريا، يُرجى التحقق من إيقاف أي برامج تغيير المواقع.");
//                     }
//                 })
//                 .catch(error => {
//                     console.error('خطأ في الحصول على الموقع:', error);
//                 });
//         }, function(error) {
//             console.error("Error getting location:", error);
//         });
//     } else {
//         console.error("Geolocation is not supported by this browser.");
//     }
// });

$(document).ready(function() {
    $('#saveVisit').click(function(event) {
        event.preventDefault(); // منع إرسال النموذج بشكل افتراضي

        var fieldIds = ['#date_visit', '#with_supervisor', '#famiy', '#type_visit'];

        // متغير لتتبع ما إذا كانت جميع الحقول صالحة
        var allFieldsValid = true;

        // الحلقة عبر كل حقل والتحقق من الصحة
        fieldIds.forEach(function(fieldId) {
            var fieldValue = $(fieldId).val();
            if (fieldValue === '') {
                $(fieldId).addClass('invalid');
                allFieldsValid = false;
            } else {
                $(fieldId).removeClass('invalid');
            }
        });

        // التحقق مما إذا كان أي حقل مطلوب فارغًا
        if (!allFieldsValid) {
            // عرض رسالة خطأ باستخدام Swal
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب ملء جميع الحقول المطلوبة',
                confirmButtonColor: '#dc3545'
            });
            return; // توقف التنفيذ الإضافي
        }

        // تسلسل بيانات النموذج
        var formData = new FormData($('#visitForm')[0]);

        $.ajax({
            type: 'POST',
            url: 'req/add_visit.php', 
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم حفظ الزيارة بنجاح في قاعدة البيانات.',
                    showConfirmButton: false,
                    timer: 2000, // يختفي الإشعار بعد 2000 مللي ثانية (2 ثانية)
                    willClose: function() {
                        $('#add_visit_modal').modal('hide');
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء

                alert('Error occurred while submitting the form.');
            }
        });
    });
});

$(document).ready(function() {
    $(".delete-visit").click(function() {
        var id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "req/delete_visit.php", // 
            data: {visit_id: id},
            success: function(response) {
                // إعادة تحميل الجدول بعد الحذف بنجاح
                location.reload();

            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء
                console.error(xhr.responseText);
            }
        });
    });
});





// For Portection
$(document).ready(function() {
    $('#updatePro').click(function(event) {
        event.preventDefault(); // Prevent the default form submission



        

        var fieldIds = ['#date_ref', '#phone_ref', '#name_ref', '#disable', '#who_ref', '#is_he_married', '#orphan', '#what_care', '#pro_explain', '#pro_type'];

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

        // Serialize form data
        var formData = new FormData($('#FormPro')[0]);

        // Send form data via AJAX
        $.ajax({
            type: 'POST',
            url: 'req/update_pro1.php', // Replace 'process_form.php' with the URL of your server-side script
            data: formData,
            processData: false, // Tell jQuery not to process the data
            contentType: false, // Tell jQuery not to set contentType
            success: function(response) {
                // Handle the response from the server
                console.log(response);
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم حفظ بيانات الحماية في قاعدة البيانات.',
                    showConfirmButton: false,
                    timer: 2000, // يختفي الإشعار بعد 2000 مللي ثانية (2 ثانية)
                    willClose: function() {

                    }
                });

            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(xhr.responseText);
                alert('Error occurred while submitting the form.');
            }
        });
    });
});

// Table Pro1
document.addEventListener("DOMContentLoaded", function() {
    var addButton = document.getElementById('addButtonPro1');
    var tableBody = document.querySelector('#protectionTable tbody');

    // إضافة صف جديد عند النقر على الزر
    addButton.addEventListener('click', function() {
        var newRow = document.createElement('tr');
        newRow.innerHTML = `
<td><input type="date" class="form-control" name="date_v[]" id="date_v" required></td>
<td>
    <select class="form-select" name="type_v[]" id="type_v" required>
        <option value="" disable></option>
        <option value="إهمال">إهمال</option>
        <option value="تسرب">تسرب</option>
        <option value="عمالة">عمالة</option>
        <option value="غير مسجل">غير مسجل</option>
        <option value="أطفال شوارع">أطفال شوارع</option>
        <option value="تجارة بالأطفال">تجارة بالأطفال</option>
        <option value="عنف/سوء معاملة نفسية">عنف/سوء معاملة نفسية</option>
        <option value="اغتصاب">اغتصاب</option>
        <option value="فقر">فقر</option>
        <option value="عنف/سوء معاملة جنسية">عنف/سوء معاملة جنسية</option>
        <option value="طفل ناج من الذخائر المتفجرة">طفل ناج من الذخائر المتفجرة</option>
        <option value="زواج أطفال">زواج أطفال</option>
        <option value="تنمر">تنمر</option>
        <option value="حمل الطفل نتيجة الاعتداء">حمل الطفل نتيجة الاعتداء</option>
        <option value="عنف/سوء معاملة جسدية">عنف/سوء معاملة جسدية</option>
    </select>
</td>
<td>
    <select class="form-select" name="place_v[]" id="place_v" required>
        <option value="" disable></option>
        <option value="المنزل">المنزل</option>
        <option value="المدرسة">المدرسة</option>
        <option value="المجتمع المحلي">المجتمع المحلي</option>
        <option value="غير ذلك">غير ذلك</option>
    </select>
</td>
<td>
    <select class="form-select" name="who_v[]" id="who_v" required>
        <option value="" disable></option>
        <option value="أب">أب</option>
        <option value="أم">أم</option>
        <option value="أخ">أخ</option>
        <option value="أخت">أخت</option>
        <option value="جد">جد</option>
        <option value="جدة">جدة</option>
        <option value="معلم/معلمة">معلم/معلمة</option>
        <option value="عم/خال">عم/خال</option>
        <option value="زميل/زميلة في المدرسة">زميل/زميلة في المدرسة</option>
        <option value="عمة/خالة">عمة/خالة</option>
        <option value="غير ذلك">غير ذلك</option>
        <option value="غير معروف">غير معروف</option>
    </select>
</td>
<td>
    <select class="form-select" name="v_relation[]" id="v_relation" required>
        <option value="" disable></option>
        <option value="نعم">نعم</option>
        <option value="لا">لا</option>
    </select>
</td>
<td><button type="button" class="btn btn-danger deleteRowPro1">حذف</button></td>
`;

        tableBody.appendChild(newRow);
    });

    // حذف الصف عند النقر على زر الحذف
    tableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('deleteRowPro1')) {
            var row = event.target.closest('tr');
            row.remove();
        }
    });
});



$(document).ready(function() {
    $("#protectionForm").submit(function(event) {
        event.preventDefault(); // منع النموذج من إرسال البيانات عبر الطريقة التقليدية

        var formData = $(this).serialize(); // جمع بيانات النموذج

        $.ajax({
            type: "POST",
            url: "req/protection2.php", 
            
            data: formData,
            
            success: function(response) {
                // التعامل مع الاستجابة بعد الإرسال بنجاح
                // console.log(response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ!',
                    text: 'تم إضافة البيانات بنجاح.',
                    showConfirmButton: false,
                    timer: 2000, 
                    willClose: function() {
                        $(document).ready(function() {
                         $('#protectionModal').modal('hide');
                        });
                    }
                }); // يمكنك تغيير هذا لتناسب احتياجاتك
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء في حالة فشل الإرسال
                console.error(xhr.responseText);
            }
        });
    });
});


$(document).ready(function() {
    $(".deleteRowPro1").click(function() {
        var id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "req/delete_pro2.php", 
            data: {delete_id: id},
            success: function(response) {
                // إعادة تحميل الجدول بعد الحذف بنجاح
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: 'تم حذف البيانات بنجاح.',
                    showConfirmButton: false,
                    timer: 2000, 
                    willClose: function() {
                        $(document).ready(function() {
                         $('#protectionModal').modal('hide');
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء
                console.error(xhr.responseText);
            }
        });
    });
});


// protection3

// Table Pro1
document.addEventListener("DOMContentLoaded", function() {
    var addButton = document.getElementById('addButtonPro3');
    var tableBody = document.querySelector('#protectionTable3 tbody');

    // إضافة صف جديد عند النقر على الزر
    addButton.addEventListener('click', function() {
        var newRow = document.createElement('tr');
        newRow.innerHTML = `
<td>
    <select class="form-select" name="does_help[]" id="does_help" required>
        <option value="" disable></option>
        <option value="نعم">نعم</option>
        <option value="لا">لا</option>
    </select>
</td>
<td>
    <select class="form-select" name="type_help[]" id="type_help">
        <option value="" disable></option>
        <option value="الصحة الجسدية">الصحة الجسدية</option>
        <option value="الصحة العقلية">الصحة العقلية</option>
        <option value="الحماية">الحماية</option>
        <option value="المأوى">المأوى</option>
        <option value="الطعام">الطعام</option>
        <option value="اللباس">اللباس</option>
        <option value="العلاج">العلاج</option>
        <option value="العلاج">العلاج</option>
        <option value="غير ذلك">غير ذلك</option>
    </select>
</td>
<td>
    <select class="form-select" name="formal_referral[]" id="formal_referral" required>
        <option value="" disable></option>
        <option value="نعم">نعم</option>
        <option value="لا">لا</option>
    </select>
</td>
<td>
    <input class="form-control" type="text" name="who_gave[] id="who_gave"/>
</td>
<td>
    <input class="form-control" type="date" name="referral_date[] id="referral_date"/>
</td>
<td><button type="button" class="btn btn-danger deleteRowPro3">حذف</button></td>
`;

        tableBody.appendChild(newRow);
    });

    // حذف الصف عند النقر على زر الحذف
    tableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('deleteRowPro3')) {
            var row = event.target.closest('tr');
            row.remove();
        }
    });
});



$(document).ready(function() {
    $("#protectionForm3").submit(function(event) {
        event.preventDefault(); // منع النموذج من إرسال البيانات عبر الطريقة التقليدية


        var formData = $(this).serialize(); // جمع بيانات النموذج

        $.ajax({
            type: "POST",
            url: "req/protection3.php", 
        
            
            data: formData,
            
            
            success: function(response) {
                // التعامل مع الاستجابة بعد الإرسال بنجاح
                // console.log(response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ!',
                    text: 'تم إضافة البيانات بنجاح.',
                    showConfirmButton: false,
                    timer: 2000, 
                    willClose: function() {
                        $(document).ready(function() {
                         $('#protectionModal3').modal('hide');
                        });
                    }
                }); // يمكنك تغيير هذا لتناسب احتياجاتك
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء في حالة فشل الإرسال
                console.error(xhr.responseText);
            }
        });
    });
});


$(document).ready(function() {
    $(".deleteRowPro3").click(function() {
        var id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "req/delete_pro3.php", 
            data: {delete_id: id},
            success: function(response) {
                // إعادة تحميل الجدول بعد الحذف بنجاح
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: 'تم حذف البيانات بنجاح.',
                    showConfirmButton: false,
                    timer: 2000, 
                    willClose: function() {
                        $(document).ready(function() {
                         $('#protectionModal3').modal('hide');
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                // التعامل مع الأخطاء
                console.error(xhr.responseText);
            }
        });
    });
});


