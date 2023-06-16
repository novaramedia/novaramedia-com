/* global jQuery, tinymce */
(function () {
  tinymce.PluginManager.add('videocaptionshortcode', function (editor) {
    editor.addButton('videocaptionshortcode', {
      title: 'Video caption',
      cmd: 'videocaptionshortcode',
    });

    jQuery('.mce-i-videocaptionshortcode').addClass(
      'dashicons-before dashicons-admin-comments'
    );

    editor.addCommand('videocaptionshortcode', function () {
      let text = editor.getContent({
        format: 'html',
      }); // get all the text in editor

      const selectedText = editor.selection.getContent({ format: 'text' }); // get selected text

      text = text.replace(
        selectedText,
        `[video-caption]${selectedText}[/video-caption]`
      ); // replace selected text with shortcode

      editor.setContent(text); // set content with updated text

      return;
    });
  });
})();
