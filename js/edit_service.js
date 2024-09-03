$(document).ready(function () {
    $('select').selectize({
        sortField: 'text'
    });
});


$(document).ready(function(){
    $("#navLinks li:nth-child(2) a").addClass('active');
});


$(document).ready(function() {
    $('#updateService').click(function(event) {
        event.preventDefault(); // Prevent the default form submission

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

        // Serialize form data
        var formData = new FormData($('#serviceForm')[0]);

        // Send form data via AJAX
        $.ajax({
            type: 'POST',
            url: 'req/update_service.php', 
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
