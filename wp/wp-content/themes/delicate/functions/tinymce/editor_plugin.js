(function() {   
tinymce.create('tinymce.plugins.NattyShortcodes', {
 createControl : function(n, cm) {
    switch (n) {
    case 'mygallery_button':
      var c = cm.createMenuButton('mygallery_button', {
        title : 'My menu button',
        icons : false
       });
       
     c.onRenderMenu.add(function(c, m) {
        var sub;
        m.add({title : 'Shortcodes', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
        
        sub = m.addMenu({title : 'Text features'});
        sub.add({title : 'Add Button', onclick : function() {
           tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[button link="#"]'+tinyMCE.activeEditor.selection.getContent()+'[/button]');            
        }});
        sub.add({title : 'DropCap', onclick : function() {
           tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[dropcap]'+tinyMCE.activeEditor.selection.getContent()+'[/dropcap]');             
        }});
        sub.add({title : 'Toggle', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[toggle title="Toggle Title"]'+tinyMCE.activeEditor.selection.getContent()+'[/toggle]');
        }});
        sub.add({title : 'Highlight', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[highlight]'+tinyMCE.activeEditor.selection.getContent()+'[/highlight]');  
        }});    
        sub.add({title : 'Highlight Bold', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[highlightbold]'+tinyMCE.activeEditor.selection.getContent()+'[/highlightbold]');  
        }});  
        sub.add({title : 'Green', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[green]'+tinyMCE.activeEditor.selection.getContent()+'[/green]'); 
        }});
        sub.add({title : 'Red', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[red]'+tinyMCE.activeEditor.selection.getContent()+'[/red]');
        }});
        sub.add({title : 'Yellow', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[yellow]'+tinyMCE.activeEditor.selection.getContent()+'[/yellow]'); 
        }});
        sub.add({title : 'Blue', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[blue]'+tinyMCE.activeEditor.selection.getContent()+'[/blue]');             
        }});
        
        sub = m.addMenu({title : 'Messages'});
        sub.add({title : 'Notice', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[notice]'+tinyMCE.activeEditor.selection.getContent()+'[/notice]'); 
         }});
        sub.add({title : 'Alert', onclick : function() {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[alert]'+tinyMCE.activeEditor.selection.getContent()+'[/alert]'); 
        }});
        
        sub = m.addMenu({title : 'Text boxes'});
        sub.add({title : 'Inset Left', onclick : function() {
          tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[insetleft title="help"]'+tinyMCE.activeEditor.selection.getContent()+'[/insetleft]'); 
        }});
        sub.add({title : 'Inset Right', onclick : function() {
          tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[insetright title="help"]'+tinyMCE.activeEditor.selection.getContent()+'[/insetright]'); 
        }});
        sub.add({title : 'Important', onclick : function() {
          tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[important title="help"]'+tinyMCE.activeEditor.selection.getContent()+'[/important]'); 
        }});
        
          
        sub = m.addMenu({title : 'Layout'});
        sub.add({title : '2 column', onclick : function() {
         tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[col2]'+tinyMCE.activeEditor.selection.getContent()+'[/col2]'); 
        }});
        sub.add({title : '3 column', onclick : function() {
         tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[col3]'+tinyMCE.activeEditor.selection.getContent()+'[/col3]'); 
        }});
        sub.add({title : '4 column', onclick : function() {
         tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[col4]'+tinyMCE.activeEditor.selection.getContent()+'[/col4]'); 
        }});
          
        sub = m.addMenu({title : 'Misc'});
        sub.add({title : 'Clear', onclick : function() {
         tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[clear]'); 
        }});
        sub.add({title : 'Divider', onclick : function() {
         tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[divider]'); 
        }});
    });
    
            return c;
        }
    return null;
    }
}); 

    tinymce.PluginManager.add('mygallery', tinymce.plugins.NattyShortcodes);  

})();
