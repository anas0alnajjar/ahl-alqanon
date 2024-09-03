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
        $('.progress').css('display', 'block');

        $.ajax({
            url: 'req/edit_profile.php',
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
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء معالجة الرد من الخادم.',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

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
                        location.reload();
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
            error: function() {
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

    $('#add-image').click(function() {
        $('<input style="display:none;" type="file" name="new_images[]" multiple>').click().on('change', function() {
            $('#profileForm').append($(this));
        });
    });
});

function deleteImage(imageName) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: 'لن تتمكن من التراجع عن هذا!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذفها!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'req/delete-image.php',
                method: 'POST',
                data: { image: imageName },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'تم الحذف!',
                            'تم حذف الصورة بنجاح.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ أثناء محاولة حذف الصورة.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'خطأ!',
                        'حدث خطأ أثناء محاولة حذف الصورة.',
                        'error'
                    );
                }
            });
        }
    });
}