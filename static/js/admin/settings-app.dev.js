jQuery(function() {
var app_id_el = jQuery("#facebook-app-id");
if ( app_id_el.length > 0 ) {
	app_id_el.blur(function(){
		var app_id = app_id_el.val();
		if ( app_id.length > 5 && jQuery("#facebook-app-namespace").val() === "" ) {
			jQuery.getJSON( "https://graph.facebook.com/" + app_id ).success(function(app_data){
				if ( app_data.namespace !== undefined ) {
					jQuery("#facebook-app-namespace").val(app_data.namespace);
				}
			});
		}
	});
}
});