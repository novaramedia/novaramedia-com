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
      // Only add attributes to <a> tags that don't already specify target OR
      // rel, so running the command twice (or on a link that already carries a
      // rel, e.g. rel="nofollow") doesn't produce duplicate target/rel attributes.
      const updated = html.replace(/<a (?![^>]*\b(?:target|rel)=)/gi, '<a target="_blank" rel="noopener noreferrer" ');
      editor.selection.setContent(updated);
    });
  });
})();
