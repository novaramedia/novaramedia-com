/* global tinymce */
(function() {
  tinymce.PluginManager.add('extlinks', function(editor) {
    editor.addButton('extlinks', {
      title: 'Make Links External',
      cmd: 'extlinks'
    }); // Add Button to Visual Editor Toolbar

    jQuery('.mce-i-extlinks').addClass('dashicons-before dashicons-external');

    editor.addCommand('extlinks', function() { // Add Command when Button Clicked
      var text = editor.getContent({
        'format': 'html'
      }); // Check we have selected some text selected

      text = text.replace(/ href=/g, ' target="_blank" href=');
      editor.setContent(text);

      return alert('All links made External');
    });
  });
})();