// avoid collisions
var FB_WP = FB_WP || {};
FB_WP.admin = FB_WP.admin || {};
FB_WP.admin.login = FB_WP.admin.login || {
	accounts: {
		viewer: {
			// track the Facebook account viewing the page
			id: "",
			// track which Facebook app permissions exist for the current Facebook user
			permissions: {
				connected: false,
				manage_pages: false,
				publish_actions: false,
				publish_stream: false
			},
			pages:{}
		}
	},
	// overwrite messages with text specific to the current locale
	messages: {
		form_submit_prompt: "Please save your edits by submitting the form"
	},
	// style an element similar to default anchor styles
	link_style: {cursor:"pointer",color:"#21759B","text-decoration":"underline"},
	// Facebook login status handler
	status_change: function( response ) {
		if ( response.authResponse !== undefined ) {
			if ( response.authResponse.userID !== undefined ) {
				FB_WP.admin.login.accounts.viewer.id = response.authResponse.userID;
			}
			jQuery(document).trigger("facebook-logged-in");
		} else {
			jQuery(document).trigger("facebook-not-logged-in");
		}
	},
	// request a list of permissions granted by the current visitor for our Facebook application
	permissions_check: function() {
		FB.api( "/me/permissions", function(response) {
			if ( response.data !== undefined && jQuery.isArray( response.data ) ) {
				if ( response.data[0].installed === 1 ) {
					FB_WP.admin.login.accounts.viewer.permissions.connected = true;
				}
				if ( response.data[0].manage_pages === 1 ) {
					FB_WP.admin.login.accounts.viewer.permissions.manage_pages = true;
				}
				if ( response.data[0].publish_actions === 1 ) {
					FB_WP.admin.login.accounts.viewer.permissions.publish_actions = true;
				}
				if ( response.data[0].publish_stream === 1 ) {
					FB_WP.admin.login.accounts.viewer.permissions.publish_stream = true;
				}
			}
			jQuery(document).trigger("facebook-permissions-check");
		} );
	},
	// request a fresh status from Facebook
	get_status: function() {
		FB.getLoginStatus( FB_WP.admin.login.status_change );
	},
	// call FB.login with an optional scope parameter
	trigger_login: function( callback, scope ) {
		// enforce scope as object only
		if ( jQuery.isPlainObject( scope ) ) {
			var permissions = [];
			jQuery.each( scope, function( permission ) {
				permissions.push( permission );
			} );
			if ( permissions.length > 0 ) {
				FB.login( callback, {scope: permissions.join(",")} );
				return;
			}
		}
		FB.login( callback );
	},
	init: function() {
		jQuery(document).one( "facebook-logged-in", FB_WP.admin.login.permissions_check );
		FB_WP.admin.login.get_status();
	}
};
FB_WP.admin.login.page = FB_WP.admin.login.page || {
	accounts: {
		page: {
			id: "", // track the stored Facebook page
			name: ""
		}
	},
	// overwrite messages with text specific to the current locale
	messages: {
		add_manage_pages: "Allow new posts to a Facebook Page",
		delete_stored_page: "None: remove %s",
		select_new: "Select a new page:",
		no_create_content_pages: "No new published pages with create content permission found"
	},
	// request a list of published pages where the current Facebook user has permission to create new content
	get_publishable_pages: function() {
		FB_WP.admin.login.accounts.viewer.pages.create_content = [];
		FB.api( "/me/accounts", "GET", {fields:"id,name,is_published,perms",limit:20,ref:"fbwpp"}, function(response){
			if ( response.data === undefined ) {
				return;
			}
			var pages = [];
			jQuery.each( response.data, function(i,page){
				if ( page.is_published === true && page.name !== "" && page.perms !== undefined && jQuery.inArray("CREATE_CONTENT", page.perms) ) {
					pages.push({id:page.id,name:page.name});
				}
			} );
			FB_WP.admin.login.accounts.viewer.pages.create_content = pages;
			jQuery(document).trigger("facebook-pages-check");
		} );
	},
	// allow the viewer to associate a Facebook Page with the WordPress site
	display_publishable_pages: function() {
		if ( FB_WP.admin.login.accounts.viewer.pages.create_content === undefined ) {
			return;
		}

		var container = jQuery("#facebook-login");
		if ( container.length === 0 ) {
			return;
		}
		var field_name = container.data("option");
		if ( field_name === undefined ) {
			return;
		}
		container.empty();

		if ( FB_WP.admin.login.accounts.viewer.pages.create_content.length === 0 ) {
			container.append( jQuery("<p>").text( FB_WP.admin.login.page.messages.no_create_content_pages ) );
			return;
		}

		var field_container = jQuery("<div>"), pages = FB_WP.admin.login.accounts.viewer.pages.create_content.slice(0);
		field_container.append( jQuery("<p>").text( FB_WP.admin.login.page.messages.select_new ) );

		if ( FB_WP.admin.login.page.accounts.page.id !== "" && FB_WP.admin.login.page.accounts.page.name !== "" ) {
			pages.unshift({id:"delete",name: FB_WP.admin.login.page.messages.delete_stored_page.replace( /%s/i, FB_WP.admin.login.page.accounts.page.name)});
		}

		if ( pages.length < 6 ) {
			// show all options as input[type=radio] if not many options
			jQuery.each( pages, function(i,page) {
				// hide already selected page
				if ( FB_WP.admin.login.page.accounts.page.id == page.id ) {
					return;
				}

				var input = jQuery("<input>").attr({type:"radio",name:field_name}).val(page.id);
				field_container.append( jQuery("<div>").append( jQuery("<label>").text(page.name).prepend( input ) ) );
				input=null;
			} );
		} else {
			// handle longer lists in a select
			var select = jQuery("<select>").attr("name",field_name);
			// provide a default selected page value
			pages.unshift({id:"",name:" "});
			jQuery.each( pages, function(i,page) {
				// hide already selected page
				if ( FB_WP.admin.login.page.accounts.page.id == page.id ) {
					return;
				}

				var option = jQuery("<option>").val(page.id).text(page.name);
				select.append( option );
				option=null;
			} );
			field_container.append(select);
			select=null;
		}

		container.append( field_container );
	},
	// refresh the page when a FB.login response received
	handle_login: function(response) {
		if ( response.authResponse === undefined || response.authResponse.userID === undefined ) {
			return;
		}
		jQuery("#facebook-login").empty().append( jQuery("<p>").text(FB_WP.admin.login.messages.form_submit_prompt) );
		// refresh the page while saving any work in progress
		jQuery("#facebook-page").closest("form").find("input[type=submit]").click();
	},
	// prompt the viewer to grant manage_pages permission to the application
	// display publishable pages if manage_pages exists
	prompt_to_log_in: function() {
		var container = jQuery("#facebook-login");
		if ( container.length === 0 ) {
			return;
		}
		var section = jQuery("#facebook-page");
		var existing_page = {
			id: section.data("fbid"),
			name: section.data("name")
		};
		if ( existing_page.id !== undefined && existing_page.name !== undefined ) {
			FB_WP.admin.login.page.accounts.page = existing_page;
		}
		existing_page=null;

		if ( FB_WP.admin.login.accounts.viewer.permissions.manage_pages === true && FB_WP.admin.login.accounts.viewer.permissions.publish_stream === true ) {
			jQuery(document).one("facebook-pages-check",FB_WP.admin.login.page.display_publishable_pages);
			FB_WP.admin.login.page.get_publishable_pages();
		} else {
			container.append( jQuery("<p>").addClass("facebook-login-prompt").css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.page.messages.add_manage_pages).click( function(){FB_WP.admin.login.trigger_login(FB_WP.admin.login.page.handle_login, {manage_pages:true,publish_stream:true})} ) );
		}
	},
	init: function() {
		jQuery(document).one( "facebook-permissions-check", FB_WP.admin.login.page.prompt_to_log_in );
		FB_WP.admin.login.init();
	}
};
FB_WP.admin.login.person = {
	accounts: {
		profile: {
			id: "" // track the stored user for the profile separately from the current viewer
		}
	},
	// overwrite messages with text specific to the current locale
	messages: {
		associate_account: "Associate my WordPress account with my Facebook account",
		associate_account_publish: "Associate my WordPress account with my Facebook account and allow new posts to my Facebook Timeline",
		add_publish_actions: "Allow new posts to my Facebook Timeline",
		edit_permissions: "Manage app permissions and visibility"
	},
	// FB.login handler
	handle_login: function(response) {
		if ( response.authResponse === undefined || response.authResponse.userID === undefined ) {
			return;
		}
		var fb_section = jQuery("#facebook-info");
		if ( fb_section.length === 0 ) {
			return;
		}

		fb_section.append( jQuery("<input>").attr({type:"hidden",name:"facebook_fbid"}).val(response.authResponse.userID) );

		var login_prompts_container = jQuery("#facebook-login");
		if ( login_prompts_container.length > 0 ) {
			login_prompts_container.find(".facebook-login-prompt").remove();
			login_prompts_container.append( jQuery("<p>").text( FB_WP.admin.login.messages.form_submit_prompt ) );
		}

		// submit the form to refresh the page while saving any new edits
		fb_section.closest("form").find("input[type=submit]").click();
	},
	prompt_to_log_in: function() {
		var section = jQuery("#facebook-info"), container = jQuery("#facebook-login");
		if ( section.length === 0 || container.length === 0 ) {
			return;
		}

		// identify the currently stored account if one exists
		var existing_account = section.data("fbid");
		if ( existing_account === undefined ) {
			if ( FB_WP.admin.login.accounts.viewer.permissions.connected === false ) {
				container.append( jQuery( "<p>" ).addClass("facebook-login-prompt").css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.person.messages.associate_account).click( function(){FB_WP.admin.login.trigger_login(FB_WP.admin.login.person.handle_login)} ) );
				container.append( jQuery( "<p>" ).addClass("facebook-login-prompt").css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.person.messages.associate_account_publish).click( function(){FB_WP.admin.login.trigger_login(FB_WP.admin.login.person.handle_login, {publish_actions:true})} ) );
			} else if ( FB_WP.admin.login.accounts.viewer.permissions.publish_actions === false ) {
				container.append( jQuery( "<p>" ).addClass("facebook-login-prompt").css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.person.messages.add_publish_actions).click( function(){FB_WP.admin.login.trigger_login(FB_WP.admin.login.person.handle_login, {publish_actions:true})} ) );
			} else {
				// logged in to Facebook, permissions granted, but not associated with the WordPress user
				container.append( jQuery( "<p>" ).addClass("facebook-login-prompt").css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.person.messages.associate_account).click( function(){FB_WP.admin.login.person.handle_login({authResponse:{userID:FB_WP.admin.login.accounts.viewer.id}})} ) );
			}
		} else {
			FB_WP.admin.login.person.accounts.profile.id = existing_account;
			// only provide option to modify if viewer logged in to Facebook and has the same Facebook ID as the currently viewed WordPress profile
			if ( FB_WP.admin.login.accounts.viewer.id !== "" && ( FB_WP.admin.login.accounts.viewer.id == FB_WP.admin.login.person.accounts.profile.id ) ) {
				if ( FB_WP.admin.login.accounts.viewer.permissions.connected === false ) {
					container.append( jQuery( "<p>" ).css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.person.messages.associate_account).click( function(){FB_WP.admin.login.trigger_login(FB_WP.admin.login.person.handle_login)} ) );
					container.append( jQuery( "<p>" ).css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.person.messages.associate_account_publish).click( function(){FB_WP.admin.login.trigger_login(FB_WP.admin.login.person.handle_login, {publish_actions:true})} ) );
				} else if ( FB_WP.admin.login.accounts.viewer.permissions.publish_actions === false ) {
					container.append( jQuery( "<p>" ).css(FB_WP.admin.login.link_style).text(FB_WP.admin.login.person.messages.add_publish_actions).click( function(){FB_WP.admin.login.trigger_login(FB_WP.admin.login.person.handle_login, {publish_actions:true})} ) );
				}

				var app_id = section.data("appid");
				if ( app_id !== undefined ) {
					container.append( jQuery( "<p>" ).append( jQuery("<a>").attr( "href", "https://www.facebook.com/settings?tab=applications#application-li-" + app_id ).text(FB_WP.admin.login.person.messages.edit_permissions) ) );
				}
				app_id=null;
			}
		}
	},
	init: function() {
		jQuery(document).one( "facebook-not-logged-in", FB_WP.admin.login.person.prompt_to_log_in ).one( "facebook-permissions-check", FB_WP.admin.login.person.prompt_to_log_in );
		FB_WP.admin.login.init();
	}
};
jQuery(document).trigger("facebook-login-load");