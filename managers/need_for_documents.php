<script type="text/javascript" src="../documents/js/plugins/image.min.js"></script>
        <script src="../documents/js/plugins/align.min.js"></script>
        <script src="../documents/js/plugins/char_counter.min.js"></script>
        <script src="../documents/js/plugins/code_beautifier.min.js"></script>
        <script src="../documents/js/plugins/code_view.min.js"></script>
        <script src="../documents/js/plugins/colors.min.js"></script>
        <script src="../documents/js/plugins/draggable.min.js"></script>
        <script src="../documents/js/plugins/edit_in_popup.min.js"></script>
        <script src="../documents/js/plugins/emoticons.min.js"></script>
        <script src="../documents/js/plugins/entities.min.js"></script>
        <script src="../documents/js/plugins/font_size.min.js"></script>
        <script src="../documents/js/plugins/forms.min.js"></script>
        <script src="../documents/js/plugins/fullscreen.min.js"></script>
        <script src="../documents/js/plugins/inline_class.min.js"></script>
        <script src="../documents/js/plugins/inline_style.min.js"></script>
        <script src="../documents/js/plugins/line_height.min.js"></script>
        <script src="../documents/js/plugins/link.min.js"></script>
        <script src="../documents/js/plugins/lists.min.js"></script>
        <script src="../documents/js/plugins/markdown.min.js"></script>
        <script src="../documents/js/plugins/paragraph_format.min.js"></script>
        <script src="../documents/js/plugins/special_characters.min.js"></script>
        <script src="../documents/js/plugins/track_changes.min.js"></script>
        <script src="../documents/js/plugins/word_paste.min.js"></script>
<script>
    
 new FroalaEditor('#edit', {
    
    imageUploadURL: 'upload_image.php',
    
    fileUploadParams: {
    id: 'my_editor'
    }
    })

    var editor = new FroalaEditor('#edit');

editor.opts.events['image.removed'] = function (e, editor, $img) {
  $.ajax({
    // Request method.
    method: 'POST',

    // Request URL.
    url: '/delete_image.php',

    // Request params.
    data: {
      src: $img.attr('src')
    }
  })
  .done(function(data) {
  try {
    const response = JSON.parse(data);
    if (response.message === 'Success') {
      console.log('Image was deleted successfully!');
    } else {
      console.error('Server response:', response);
    }
  } catch (error) {
    console.error('Invalid JSON response:', error);
  }
})
.fail(function(err) {
  console.log('Image delete problem:', err);
  console.error('AJAX request failed!'); // إضافة رسالة خطأ
});
}
</script>
