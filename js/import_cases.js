
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmInsert').addEventListener('click', function() {
        var formData = [];
        $('tbody tr').each(function(){
            var row = [];
            $(this).find('input').each(function(){
                row.push($(this).val());
            });
            formData.push(row);
        });


        // التحقق من وجود بيانات
        if(formData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'لا توجد بيانات',
                text: 'لا توجد بيانات للاستيراد!',
                confirmButtonText: 'موافق'
            });
            return;
        }

        $('input.required-field').each(function() {
            if ($(this).val().trim() === "") {
                if ($(this).attr('name').includes('[' + primaryKey + ']')) {
                    $(this).val('new');
                } else {
                    $(this).css("border-color", "red");
                }
            } else {
                $(this).css("border-color", "");
            }
        });

        var formData = new FormData(document.getElementById('updateCases'));
        formData.append('table_name', document.querySelector('select[name="table_name"]').value);

        var xhr = new XMLHttpRequest();

        // Define the progress response for loading
        xhr.upload.onprogress = function(event) {
            document.getElementById('progressBar').style.display = 'block';
            var percent = (event.loaded / event.total) * 100;
            document.getElementById('progress').style.width = percent + '%'; // تحديث عرض شريط التقدم
        };

        // Handle the server's response after completing the request
        xhr.onload = function() {
            // Hide the progress bar when the update process is complete
            document.getElementById('progressBar').style.display = 'none';

            if (xhr.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم',
                    html: xhr.responseText,
                    confirmButtonText: 'موافق'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Clear the table
                        document.querySelector('table').innerHTML = '';
                        // $(".btn-primary").addClass("disabled");
                        document.getElementById('clearButton').click();                       
                    }
                });

                // Unset session
                var xhrSession = new XMLHttpRequest();
                xhrSession.open("GET", "req/unset_session.php", true);
                xhrSession.send();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ غير متوقع تأكد من اختيار الملف واستيراده ليظهر بالجدول !!',
                    confirmButtonText: 'موافق'
                });
            }
        };

        // Send data to the server page
        xhr.open('POST', 'update.php', true);
        xhr.send(formData);
    });
});

$(document).ready(function() {
    $(".required-field").blur(function() {
        if ($(this).val().trim() === "") {
            if ($(this).attr('name').includes('[' + primaryKey + ']')) {
                $(this).val('new');
            } else {
                $(this).css("border-color", "red");
            }
        } else {
            $(this).css("border-color", "");
        }
        enableButton();
    });

    function enableButton() {
        var valid = true;
        $(".required-field").each(function() {
            if ($(this).val().trim() === "") {
                valid = false;
                $(this).css("border-color", "red");
            } else {
                $(this).css("border-color", "");
            }
        });

        if (valid) {
            $(".btn-primary").removeClass("disabled");
        } else {
            $(".btn-primary").addClass("disabled");
        }
    }

    enableButton();
});


document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('export-file').addEventListener('click', function() {
        const tableName = document.querySelector('select[name="table_name"]').value;
        const selectedColumns = document.querySelector('select[name="columns[]"]').selectedOptions;

        if (!tableName || selectedColumns.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'يرجى اختيار جدول وأعمدة للتصدير!',
                confirmButtonText: 'موافق'
            });
            return;
        }

        document.querySelector('#export_modal input[name="selected_table"]').value = tableName;
        const columns = Array.from(selectedColumns).map(option => option.value);
        document.querySelector('#export_modal input[name="selected_columns"]').value = columns.join(',');

        new bootstrap.Modal(document.getElementById('export_modal')).show();
    });
});

function toggleInstructions() {
    var instructions = document.querySelector('.instructions');
    if (instructions.style.display === 'none' || instructions.style.display === '') {
        instructions.style.display = 'block';
    } else {
        instructions.style.display = 'none';
    }
}