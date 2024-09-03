  <!-- <script>
        $(document).ready(function() {
            $('#chat_ai').click(function() {
                $('#chatModal').modal('show');
            });

            $('#chatForm').submit(function(event) {
                event.preventDefault();
                var message = $('#message').val();
                $.ajax({
                    url: 'req/chat_ai.php',
                    method: 'POST',
                    data: { message: message },
                    success: function(response) {
                        $('#response').html(response).show();
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 429) {
                            $('#response').html('<p class="text-danger">تم تجاوز الحد المسموح به للطلبات. حاول مرة أخرى لاحقاً.</p>').show();
                        } else {
                            $('#response').html('<p class="text-danger">حدث خطأ أثناء إرسال الرسالة. حاول مرة أخرى.</p>' + 
                                                '<p>' + xhr.responseText + '</p>').show();
                        }
                    }
                });
            });
        });
    </script> -->