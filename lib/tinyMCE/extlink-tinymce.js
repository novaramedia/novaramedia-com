/* global jQuery, tinymce */
(function () {
  tinymce.PluginManager.add('extlinks', function (editor) {
    editor.addButton('extlinks', {
      title: 'Make Links External',
      cmd: 'extlinks',
    }); // Add Button to Visual Editor Toolbar

    jQuery('.mce-i-extlinks').addClass('dashicons-before dashicons-external');

    editor.addCommand('extlinks', function () {
      const html = editor.selection.getContent({ format: 'html' });
      const updated = html.replace(/ href=/g, ' target="_blank" rel="noopener noreferrer" href=');
      editor.selection.setContent(updated);
    });
  });
})();
