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
      // Only add attributes to <a> tags that don't already specify target, so
      // running the command twice (or on an already-external link) doesn't
      // duplicate target/rel.
      const updated = html.replace(/<a (?![^>]*\btarget=)/gi, '<a target="_blank" rel="noopener noreferrer" ');
      editor.selection.setContent(updated);
    });
  });
})();
