(function() {
  tinymce.PluginManager.add( 'extlinks', function( editor, url ) {

    // Add Button to Visual Editor Toolbar
    editor.addButton('extlinks', {
      title: 'Make Links External',
      cmd: 'extlinks'
    });

    jQuery('.mce-i-extlinks').addClass('dashicons-before dashicons-external');

    // Add Command when Button Clicked
    editor.addCommand('extlinks', function() {

      // Check we have selected some text selected
      var text = editor.getContent({
        'format': 'html'
      });

      text = text.replace(/ href=/g, ' target="_blank" href=');

      editor.setContent(text);

      return alert('All links made External');

    });

  });
})();