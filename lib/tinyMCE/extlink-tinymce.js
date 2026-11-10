/* global jQuery, tinymce */
(function () {
  tinymce.PluginManager.add('extlinks', function (editor) {
    editor.addButton('extlinks', {
      title: 'Make Links External',
      cmd: 'extlinks',
    }); // Add Button to Visual Editor Toolbar

    jQuery('.mce-i-extlinks').addClass('dashicons-before dashicons-external');

    editor.addCommand('extlinks', function () {
      const selection = editor.selection;
      const range = selection.getRng();
      const links = Array.prototype.slice.call(editor.getBody().querySelectorAll('a[href]'));

      // With text selected, only touch links the selection overlaps; with no
      // selection (cursor only), apply to every link in the post.
      const targets = selection.isCollapsed()
        ? links
        : links.filter(function (link) {
            return range.intersectsNode(link);
          });

      editor.undoManager.transact(function () {
        targets.forEach(function (link) {
          // Merge into any existing rel (e.g. nofollow) rather than replacing
          // or skipping it, so repeat runs never duplicate tokens.
          const rel = (editor.dom.getAttrib(link, 'rel') || '').split(/\s+/).filter(Boolean);

          ['noopener', 'noreferrer'].forEach(function (token) {
            if (rel.indexOf(token) === -1) {
              rel.push(token);
            }
          });

          editor.dom.setAttribs(link, { target: '_blank', rel: rel.join(' ') });
        });
      });
    });
  });
})();
