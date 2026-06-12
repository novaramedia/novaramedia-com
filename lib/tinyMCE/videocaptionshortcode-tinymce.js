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
      const selected = editor.selection.getContent({ format: 'html' });
      editor.selection.setContent(`[video-caption]${selected}[/video-caption]`);
    });
  });
})();
