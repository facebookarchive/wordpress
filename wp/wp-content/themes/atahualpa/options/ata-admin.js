jQuery(document).ready(function($){   

	$.ajaxSetup({
		type:	'post',
		url: 	'admin-ajax.php'
	});

	var atahualpacookie=new RegExp("atahualpatabposition=[^;]+", "i"); 
	if( document.cookie.match(atahualpacookie) ) {
		var curTab = document.cookie.match(atahualpacookie)[0].split("=")[1];
		$("div.tabcontent").css("display", "none");
		$("div#" + curTab).css("display", "block");
		$("ul#bfaoptiontabs li a").removeClass("selected");
		$("ul#bfaoptiontabs li a[rel=" + curTab +"]").addClass("selected");
	}
	
	$("ul#bfaoptiontabs li a").live("click", function() {
		var newTab = $(this).attr('rel');
		$("div.tabcontent").css("display", "none");
		$("div#" + newTab).css("display", "block");
		$("ul#bfaoptiontabs li a").removeClass("selected");
		$(this).addClass("selected");
		document.cookie = 'atahualpatabposition=' + newTab + ';path=/';
	});

	$("a#import-settings").live("click", function() { 
		var dataString = encodeURIComponent($("textarea#import-textarea").val());
		$.ajax({
			data: 'action=import_settings&ataoptions=' + dataString + '&_ajax_nonce=' + nonce3,
			success: function(html){ 
				setTimeout(function() {
					window.location = window.location;					
				}, 3000);
				$("#settingsimported").html( html ).fadeIn("fast").fadeOut(3000); 
			}
		}); 
		return false;
	});

	$("a#reset_widget_areas").bind("click", function() { 
		var delWidgetAreas = "";
		$("input[type='checkbox'][name='delete_widget_areas']").each(function(){
			if(this.checked){
				delWidgetAreas += "&delete_areas[]=" + this.value;
			}
		});
		$.ajax({
			data: 'action=reset_bfa_ata_widget_areas' + delWidgetAreas + '&_ajax_nonce=' + nonce,
			success: function(html){ 
				$("#formstatus").html( html ).fadeIn("fast").fadeOut(3000); 
			}
		}); 
		return false;
	});

	$("a#delete_bfa_ata4").bind("click", function() { 
		$.ajax({
			data: 'action=bfa_delete_bfa_ata4&_ajax_nonce=' + $nonce2,
			success: function(html){ 
				$("#bfa_ata4_deleted").html( html ).fadeIn("fast").fadeOut(3000); 
				window.location = window.location;
			}
		}); 			
		return false;
	});

	var textareawidth = $(document).width() - 430; 		
	$("div.mooarea, textarea.growing").css({width: textareawidth}); 
});

function confirmPageReset() { 
	var agree=confirm("This will reset ALL the options on this page. Are you sure?"); 
	if (agree) return true ; 
	else return false ; 
} 

function confirmSubmit() { 
	var agree=confirm("Are you sure? This will reset ALL theme options."); 
	if (agree) return true ; 
	else return false ; 
} 

new UvumiTextarea({
	selector: 'textarea.growing',
	maxChar: 0
});

			
