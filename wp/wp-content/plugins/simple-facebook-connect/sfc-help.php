<?php
// help content

function sfc_plugin_help() {
	if (!class_exists('WP_Screen')) return;
	
	global $sfc_options_page;
	
	$screen = get_current_screen();
	
	if ($screen->id != $sfc_options_page) 
		return;
		
	$home = home_url('/');
	$sfc_help_base = __("
		<p>To connect your site to Facebook, you will need a Facebook Application.
		If you have already created one, please insert your Application Secret and Application ID below.</p>
		<p><strong>Can't find your key?</strong></p>
		<ol>
		<li>Get a list of your applications from here: <a target='_blank' href='https://developers.facebook.com/apps'>Facebook Application List</a></li>
		<li>Select the application you want, then copy and paste the Application Secret and Application ID from there.</li>
		</ol>

		<p><strong>Haven't created an application yet?</strong> Don't worry, it's easy!</p>
		<ol>
		<li>Go to this link to create your application: <a target='_blank' href='https://developers.facebook.com/apps'>Facebook Application List</a></li>
		<li>After creating the application, put <strong>%s</strong> in as the Site URL in the Website section.</li>
		<li>You can get the information from the application on the
		<a target='_blank' href='https://developers.facebook.com/apps'>Facebook Application List</a> page.</li>
		<li>Select the application you created, then copy and paste the Application Secret, and Application ID from there.</li>
		</ol>
	", 'sfc');

	$sfc_help_base = sprintf( $sfc_help_base, $home );

	$screen->add_help_tab( array(
		'id'      => 'sfc-base',
		'title'   => __('Connecting to Facebook', 'sfc'),
		'content' => $sfc_help_base,
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-modules',
		'title'   => __('SFC Modules', 'sfc'),
		'content' => __("<p>Each separate module can be enabled or disabled using the checkboxes below. 
			Only enable the modules you want to use, and the rest will not run at all!</p> 
			<p>This is how SFC remains quick and fast. Non-activated modules never get loaded into memory, 
			and so will take no extra time or processing power.</p>
			<p>You can see more information about each module in its individual help tab.</p>"
			, 'sfc'),
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-login',
		'title'   => __('Login and Register', 'sfc'),
		'content' => __("<p>The Login module will allow you to log into the WordPress site using your Facebook credentials.</p>
			<p>Each existing user can activate their Facebook credentials for login by visiting their User Profile page.</p>
			<p>If you also enable the Register module, then SFC will modify the default registration screen to allow new users to register 
			using the Facebook registration plugin. This plugin will also allow non-Facebook users to register.</p>
			<p>The Login module has one option, which is to enable Facebook avatars in preference to Gravatars. If this is turned on, any 
			user with Facebook credentials attached to their WordPress account will show their Facebook avatar instead of the normal Gravatar.</p>"
			,'sfc'),
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-like',
		'title'   => __('Like and Share', 'sfc'),
		'content' => __("<p>The Like module will allow you to automatically or manually add Facebook like buttons to your posts.</p>
			<p>The automatic option will add Like buttons to all posts, pages, and other forms of content on your site.</p>
			<p>If you want to be more selective, you can edit your theme to have the <code>sfc_like_button();</code> function call where you 
			want the Like buttons to appear.</p>
			<p>You can also use the <code>[fb-like]</code> shortcode in your posts, for more specific usage.</p>
			<p>Facebook also used to have an option known as the Share button. They have deprecated this button, and it will no longer
			work with their newer codebase.</p>
			<p>Therefore, the Share module will allow you to automatically or manually add Facebook like buttons to your posts which are styled 
			so as to be approximately the same look and feel of the former Share button.</p>
			<p>The automatic option will add Like buttons to all posts, pages, and other forms of content on your site.</p>
			<p>If you want to be more selective, you can edit your theme to have the <code>sfc_share_button();</code> function call where you 
			want the Like buttons to appear.</p>
			<p>You can also use the <code>[fb-share]</code> shortcode in your posts, for more specific usage.</p>"
			,'sfc'),
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-publish',
		'title'   => __('Publish', 'sfc'),
		'content' => __("<p>The Publish module will allow you to automatically or manually send posts to your Facebook Profile or Page Walls.</p>
			<p>The automatic option can be configured on the main SFC Settings screen, and publishing happens transparently, with 
			very little configuration. You will need to grant the proper permissions and Save the settings page before this will work.</p>
			<p>The manual option is performed through the Edit Post screen. A new meta box will exist allowing you to publish to your Profile 
			or Page using a popup box. The post must be published, and public, for this option to appear.</p>"
			,'sfc'),
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-widgets',
		'title'   => __('Widgets', 'sfc'),
		'content' => __("<p>The Widgets module adds several widgets to the Appearance->Widgets screen, allowing for various widgets to be used in your
			theme's sidebar or other widget areas. Most of these widgets are duplicating Facebook widgets, and thus will have their own configuration.</p>
			<p>The Fan Box widget is a special case, as it can be independently styled using CSS. The Fan Box CSS box on the main SFC Settings page will
			allow you to add custom CSS for this widget to use.</p>"
			,'sfc'),
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-comments',
		'title'   => __('Comments', 'sfc'),
		'content' => __("<p>The Comments module will let your users use Facebook credentials to make comments, and also offer those users 
			the option to share their comments, and your post, on Facebook. This basically eliminates the need for users to type in 
			their Names and Email addresses.</p>
			<p>For newer themes that use the <code>comment_form()</code> function in WordPress, this is completely automatic. For older themes, 
			you may need to edit your theme's comments form to contain the necessary hooks to make the module work. Please see the 
			<a href='http://wordpress.org/extend/plugins/simple-facebook-connect/faq/'>FAQ</a> for more information on this.</p>
			<p>Note that some themes do checking for 'required' elements via Javascript. Because Facebook Comments get these fields 
			filled on the back end, the theme may need to be modified to both display the button or to eliminate the javascript checks.</p>"
			,'sfc'),
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-getcomm',
		'title'   => __('Comments Integration', 'sfc'),
		'content' => __("<p>The Comment Integration module uses the data saved from the automatic Publishing module to check the published stories on 
			Facebook for comments. It will then periodically poll Facebook to get those comments, and integrate them back into the normally 
			displayed comments stream on your post.</p>
			<p>Note that this is not guaranteed to work in all cases. It polls on a 6 hour basis, and sometimes Facebook is non-repsonsive, and so
			the comments won't be available. This is a 'best-effort' operation.</p>
			<p>Because these comments are not actually on your site, the module also removes the 'Reply' link from them, so as to prevent people from
			replying to comments made elsewhere, and which the original author will not be able to see.</p>"
			,'sfc'),
	));

	$screen->add_help_tab( array(
		'id'      => 'sfc-photos',
		'title'   => __('Photos','sfc'),
		'content' => __("<p>The Photos module adds a new tab to the Media Uploader on the Edit Post pages, which will show your Facebook photo 
			albums and let you easily embed pictures from Facebook into your posts.</p>
			<p>This module is new and considered to be 'alpha' quality, so don't count on it to work for all cases at present.</p>"
			,'sfc'),
	));
}