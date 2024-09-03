$(document).ready(function () {
    $('#patient_full_name, #social_worker,#community_ar,#type_ill, #social_worker2 ').selectize({
        sortField: 'text'
    });
});

$(document).ready(function(){
    $("#navLinks li:nth-child(2) a").addClass('active');
});

$(document).ready(function() {
    $('#updateCase').click(function(event) {
        event.preventDefault(); // Prevent the default form submission
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

        // Serialize form data
        var formData = new FormData($('#myForm')[0]);

        // Send form data via AJAX
        $.ajax({
            type: 'POST',
            url: 'req/update_basicneed.php', // Replace 'process_form.php' with the URL of your server-side script
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
                url: "req/money_basicneeds.php", 
                
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
                url: "req/delete_money_basic.php", 
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
            formData.append('socail_worker_id', $('#socail_worker_id').val());
            formData.append('case_id', $('#case_id').val());
            formData.append('document_title', $('#document_title').val());
            formData.append('attachments', $('#attachments')[0].files[0]);

            
            $.ajax({
                url: 'req/add_reportBasic.php',
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
            url: "req/ref-add_basic.php", 
            
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
            url: "req/delete_ref_basic.php", // 
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
    $('#edit_children_modal').modal('hide');
}); 

    
$('#editChildernBtn').click(function() {
    $('#edit_children_modal').modal('show');
});

$('.close-modal2').click(function() {
    $('#add_visit_modal').modal('hide');
}); 

    
$('#addVisit').click(function() {
    $('#add_visit_modal').modal('show');
});
$('.close-modal2').click(function() {
    $('#add_child_modal').modal('hide');
}); 
$('#closeEditChild, #closeChild').click(function() {
    $('#edit_child_modal').modal('hide');
}); 

$('#closeEditChild, #closeChild').click(function() {
    $('#edit_children_modal').modal('show');
}); 

    
$('#addChild').click(function() {
    $('#add_child_modal').modal('show');
});
$('#addChild').click(function() {
    $('#edit_children_modal').modal('hide');
});
    

    
$('#addVisit').click(function() {
    $('#edit_visit_modal').modal('hide');
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
            url: 'req/add_visit_basic.php', 
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
            url: "req/delete_visit_basic.php", // 
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








$(document).ready(function() {
    $("#FormService").submit(function(event) {
        event.preventDefault(); // منع النموذج من إرسال البيانات عبر الطريقة التقليدية

        var formData = $(this).serialize(); // جمع بيانات النموذج

        $.ajax({
            type: "POST",
            url: "req/service_add.php", 
            
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
    $(".deleteService").click(function() {
        var id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "req/delete_service_basicneed.php", 
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




// Table servises
document.addEventListener("DOMContentLoaded", function() {
    var addButton = document.getElementById('addservice');
    var tableBody = document.querySelector('#servises tbody');

    // إضافة صف جديد عند النقر على الزر
    addButton.addEventListener('click', function() {
        var newRow = document.createElement('tr');
        newRow.innerHTML = `
<td>
    <input class="form-control" type="text" name="service_name[] id="service_name"/>
</td>
<td>
    <input class="form-control" type="number" name="number_of_recipients[] id="number_of_recipients"/>
</td>
<td>
    <input class="form-control" type="date" name="its_date[] id="its_date"/>
</td>
<td>
    <select class="form-select" name="for_whom[]" id="for_whom" required>
        <option value="" disable></option>
        <option value="جمعية">جمعية</option>
        <option value="مشروع">مشروع</option>
    </select>
</td>
<td>
    <input class="form-control" type="text" name="project_name[] id="project_name"/>
</td>
<td>
    <select class="form-select" name="type[]" id="type" required>
        <option value="" disable></option>
        <option value="طبية">طبية</option>
        <option value="تعليمية">تعليمية</option>
        <option value="عينية">عينية</option>
    </select>
</td>
<td><button type="button" class="btn btn-danger deleteService">حذف</button></td>
`;

        tableBody.appendChild(newRow);
    });

    // حذف الصف عند النقر على زر الحذف
    tableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('deleteService')) {
            var row = event.target.closest('tr');
            row.remove();
        }
    });
});





        document.addEventListener('DOMContentLoaded', function() {
            var chlidFemales = document.getElementById('chlid_females');
            var chlidMales = document.getElementById('chlid_males');
            var adultsMale = document.getElementById('adults_male');
            var adultsFemale = document.getElementById('adults_female');
            var sumChildren = document.getElementById('sum_children');
            var totalFamily = document.getElementById('total_family');


            function updateSum() {
                var females = parseInt(chlidFemales.value) || 0;
                var males = parseInt(chlidMales.value) || 0;
                var adultMales = parseInt(adultsMale.value) || 0;
                var adultFemales = parseInt(adultsFemale.value) || 0;

                var sum = females + males;
                sumChildren.value = sum;

                var total = sum + adultMales + adultFemales;
                totalFamily.value = total;
            }

            chlidFemales.addEventListener('input', updateSum);
            chlidMales.addEventListener('input', updateSum);
            adultsMale.addEventListener('input', updateSum);
            adultsFemale.addEventListener('input', updateSum);
        });

        document.addEventListener('DOMContentLoaded', function () {
            var fields = [
                {
                    relationshipField: document.getElementById('relationship_with_child'),
                    nationalNumField: document.getElementById('national_num'),
                    nameCusField: document.getElementById('name_cus'),
                    genderField: document.getElementById('gender_of_the_first_recipient'),
                    relationToFather: 'Father/ الاب',
                    relationToMother: 'Mother/الأم',
                    genderMale: 'Male/ ذكر',
                    genderFemale: 'Female/ أنثى'
                },
                {
                    relationshipField: document.getElementById('relative_relation2'),
                    nationalNumField: document.getElementById('idrn2'),
                    nameCusField: document.getElementById('recipientname2'),
                    genderField: null, // لا يوجد حقل للجنس في المجموعة الثانية
                    relationToFather: 'Father/ الاب',
                    relationToMother: 'Mother/الأم',
                    genderMale: null, // لا يتم تحديث الجنس في المجموعة الثانية
                    genderFemale: null // لا يتم تحديث الجنس في المجموعة الثانية
                }
            ];
    
            var fatherNameField = document.getElementById('father_name');
            var motherNameField = document.getElementById('mother_name');
            var natMotherField = document.getElementById('nat_mother');
            var natFatherField = document.getElementById('nat_father');
    
            function updateFields() {
                fields.forEach(function (field) {
                    var selectedRelation = field.relationshipField.value;
    
                    if (selectedRelation === field.relationToFather) {
                        field.nationalNumField.value = natFatherField.value;
                        field.nameCusField.value = fatherNameField.value;
                        if (field.genderField) field.genderField.value = field.genderMale;
                        field.nationalNumField.readOnly = true;
                        field.nameCusField.readOnly = true;
                        if (field.genderField) field.genderField.readOnly = true;
                    } else if (selectedRelation === field.relationToMother) {
                        field.nationalNumField.value = natMotherField.value;
                        field.nameCusField.value = motherNameField.value;
                        if (field.genderField) field.genderField.value = field.genderFemale;
                        field.nationalNumField.readOnly = true;
                        field.nameCusField.readOnly = true;
                        if (field.genderField) field.genderField.readOnly = true;
                    } else {

                        field.nationalNumField.readOnly = false;
                        field.nameCusField.readOnly = false;
                    }
                });
            }
    
            // إضافة المستمع للأحداث على الحقول المتعددة
            fields.forEach(function (field) {
                field.relationshipField.addEventListener('change', updateFields);
            });
            fatherNameField.addEventListener('change', updateFields);
            motherNameField.addEventListener('change', updateFields);
            natMotherField.addEventListener('change', updateFields);
            natFatherField.addEventListener('change', updateFields);
    
            // Initialize on page load
            updateFields();
        });


        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-child').forEach(button => {
                button.addEventListener('click', function() {
                    let childId = this.getAttribute('data-id');
                    
                    // قم بإجراء استعلام AJAX لجلب بيانات الطفل
                    fetch(`get_child_data.php?id=${childId}`)
                        .then(response => response.json())
                        .then(data => {
                            // املأ الحقول بالبيانات المسترجعة
                            document.getElementById('child_nameEdit').value = data.child_name;
                            document.getElementById('id_childEdit').value = data.id_child;
                            document.getElementById('genderEdit').value = data.gender;
                            document.getElementById('relationshipEdit').value = data.relationship;
                            document.getElementById('child_birthEdit').value = data.child_birth;
                            document.getElementById('child_eduEdit').value = data.child_edu;
                            document.getElementById('disabilityEdit').value = data.disability;
                            document.getElementById('proof_of_disabilityEdit').value = data.proof_of_disability;
                            document.getElementById('has_idEdit').value = data.has_id;
                            document.getElementById('note_managerEdit').value = data.note_manager;
                            document.getElementById('id').value = data.id;
        
                            // قم بملء الاحتياجات الصحية
                            let healthNeeds = data.health_need_child ? data.health_need_child.split(',') : [];
                            document.querySelectorAll('#health_need_childEdit option').forEach(option => {
                                option.selected = healthNeeds.includes(option.value);
                            });
                            // فتح النموذج
                            $('#edit_child_modal').modal('show');
                            $('#edit_children_modal').modal('hide');

                            
                        });
                });
            });
            
            document.getElementById('editChild').addEventListener('click', function() {
                let formData = new FormData(document.getElementById('childFormEdit'));
                
                fetch('req/update_child_data.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحفظ بنجاح!',
                            text: 'تم حفظ البيانات بنجاح في قاعدة البيانات.',
                            showConfirmButton: false,
                            timer: 2000, 
                            willClose: function() {
                                $('#edit_child_modal').modal('hide');
                                $('#edit_children_modal').modal('show');
                                
                            }
                        });
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ راجع الدعم الفني رجاءً.',
                            showConfirmButton: true,
                            timer: 2000, 
                            willClose: function() {

                            }
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            document.getElementById('closeChild').addEventListener('click', function() {
                $('#edit_child_modal').modal('hide');
            });
        });
        


        $(document).ready(function() {
            $(".delete-child").click(function() {
                var id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: "req/delete_child.php", // 
                    data: {child_id: id},
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
            $('#saveChild').click(function(event) {
                event.preventDefault(); // منع إرسال النموذج بشكل افتراضي
        
                var fieldIds = ['#health_need_child', '#child_name', '#gender', '#relationship', '#child_birth', '#child_edu', '#disability', '#has_id'];
        
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
                var formData = new FormData($('#childForm')[0]);
        
                $.ajax({
                    type: 'POST',
                    url: 'req/add_child_basic.php', 
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحفظ بنجاح!',
                            text: 'تم حفظ الطفل بنجاح في قاعدة البيانات.',
                            showConfirmButton: false,
                            timer: 2000, // يختفي الإشعار بعد 2000 مللي ثانية (2 ثانية)
                            willClose: function() {
                                $('#add_child_modal').modal('hide');
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