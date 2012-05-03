/* Handles the theme's shortcode buttons in the TinyMCE editor */
(function() {  
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('grapheneshortcodes');

	tinymce.create('tinymce.plugins.grapheneShortCodes', {  
		init : function(ed, url) {  
			
			ed.addButton('warning', {  
				title : ed.getLang('grapheneshortcodes.warningtitle', 'Add a warning message block'),  
				image : url+'/buttons/warning.png',  
				onclick : function() {  
					 ed.selection.setContent('[warning]' + ed.selection.getContent() + '[/warning]');  
				}  
			});
			
			ed.addButton('error', {  
				title : ed.getLang('grapheneshortcodes.errortitle', 'Add an error message block'), 
				image : url+'/buttons/error.png',  
				onclick : function() {  
					 ed.selection.setContent('[error]' + ed.selection.getContent() + '[/error]');  
				}  
			});
			
			ed.addButton('notice', {  
				title : ed.getLang('grapheneshortcodes.noticetitle', 'Add a notice message block'), 
				image : url+'/buttons/notice.png',  
				onclick : function() {  
					 ed.selection.setContent('[notice]' + ed.selection.getContent() + '[/notice]');  
				}  
			});
			
			ed.addButton('important', {  
				title : ed.getLang('grapheneshortcodes.importanttitle', 'Add an important message block'), 
				image : url+'/buttons/important.png',  
				onclick : function() {  
					 ed.selection.setContent('[important]' + ed.selection.getContent() + '[/important]');  
				}  
			});
		},  
		createControl : function(n, cm) {  
			return null;  
		},  
	});  
	tinymce.PluginManager.add('grapheneshortcodes', tinymce.plugins.grapheneShortCodes);  
})();