(function() {
   /* tinymce.create('tinymce.plugins.NattyShortcodes', {
        init : function(ed, url) {
            ed.addButton('mygallery_button', {
                title : 'brettsyoutube.youtube',
                image : url+'/youtube.png',
                onclick : function() {
                    idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
                    var vidId = prompt("YouTube Video", "Enter the id or url for your video");
                    var m = idPattern.exec(vidId);
                    if (m != null && m != 'undefined')
                        ed.execCommand('mceInsertContent', false, '[youtube id="'+m[1]+'"]');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
        
    });*/
    
tinymce.create('tinymce.plugins.NattyShortcodes', {
 createControl : function(n, cm) {
    switch (n) {
    case 'mygallery_button':
      var c = cm.createMenuButton('mygallery_button', {
        title : 'My menu button',
        image : 'etc.gif',
        icons : false
       });
       
     c.onRenderMenu.add(function(c, m) {
        var sub;
        m.add({title : 'Some item 1', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, 'Some item 1');
        }});
        m.add({title : 'Some item 2', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, 'Some item 2');
        }});
    });
    
            return c;
        }
    return null;
    }
});

    tinymce.PluginManager.add('mygallery', tinymce.plugins.NattyShortcodes);  

})();
