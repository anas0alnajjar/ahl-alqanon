$(document).ready(function() {
    let editor;

    function loadCKEditor(selector) {
        if (typeof ClassicEditor !== 'undefined') {
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
    loadCKEditor('#editor');

    $('#profileForm').submit(function(e) {
        e.preventDefault();

        // تحديث محتوى حقل النص من CKEditor
        if (editor) {
            $('textarea[name="desc2"]').val(editor.getData());
        }

        var formData = new FormData(this);

        // إظهار شريط التقدم عند بدء التحميل
        console.log('Setting progress bar to display block');
        $('.progress').css('display', 'block');
        console.log('Progress bar should be visible now.');

        $.ajax({
            url: 'req/save_profile.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var percentComplete = Math.ceil((e.loaded / e.total) * 100);
                        $('#progressBar').css('width', percentComplete + '%').text(percentComplete + '%');
                        console.log('Progress: ' + percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                // طباعة الرد في وحدة التحكم للتحقق
                console.log("Response from server: ", response);

                // معالجة الاستجابة
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء معالجة الرد من الخادم.',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                // إخفاء شريط التقدم بعد انتهاء التحميل
                console.log('Hiding progress bar');
                $('.progress').css('display', 'none');
                $('#progressBar').css('width', '0%').text('0%');

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم حفظ البيانات بنجاح.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // تفريغ النموذج بعد الحفظ بنجاح
                        $('#profileForm')[0].reset();
                        if (editor) {
                            editor.setData('');
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(response) {
                // طباعة الرد في وحدة التحكم للتحقق
                console.log("Error response from server: ", response);

                // إخفاء شريط التقدم بعد انتهاء التحميل
                console.log('Hiding progress bar');
                $('.progress').css('display', 'none');
                $('#progressBar').css('width', '0%').text('0%');
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء الحفظ، حاول مرة أخرى.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});
