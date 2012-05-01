<?php
function widget_bfa_subscribe($args) {
	global $bfa_ata, $templateURI;
	extract($args);
	$options = get_option('widget_bfa_subscribe');
	$title = apply_filters('widget_title', $options['title']);
	$email_text = apply_filters('widget_text', $options['email-text']);
	$field_text = apply_filters('widget_title', $options['field-text']);
	$submit_text = apply_filters('widget_title', $options['submit-text']);
	$posts_text = apply_filters('widget_text', $options['posts-text']);
	$comments_text = apply_filters('widget_text', $options['comments-text']);
	$id = apply_filters('widget_title', $options['id']);
	$google_or_feedburner = apply_filters('widget_title', $options['google-or-feedburner']);
	echo $before_widget;
	if ( !empty($title) ) { echo $before_title . $title . $after_title; }
	// feedburner or google:
	if ( $google_or_feedburner == "feedburner" ) {
		$action_url = "www.feedburner.com/fb/a/emailverify";
		$window_url = "www.feedburner.com/fb/a/emailverifySubmit?feedId=";
		$hidden_value_url = "http://feeds.feedburner.com/~e?ffid=";
		$hidden_uri = "url";
		$hidden_input = '<input type="hidden" value="' . get_bloginfo('name') . '" name="title"/>';
	} else {
		$action_url = "feedburner.google.com/fb/a/mailverify";
		$window_url = "feedburner.google.com/fb/a/mailverify?uri=";
		$hidden_value_url = "";
		$hidden_uri = "uri";
		$hidden_input = "";
	}
	// replace URL placeholders:
	$email_text = str_replace("%email-url", "http://" . $window_url . $id . "&amp;loc=" . get_locale(), $email_text);
	$posts_text = str_replace("%posts-url",  get_bloginfo('rss2_url'), $posts_text);
	$comments_text = str_replace("%comments-url",  get_bloginfo('comments_rss2_url'), $comments_text);
?>
<form class="feedburner-email-form" 
action="http://<?php echo $action_url; ?>" method="post" target="popupwindow" 
onsubmit="window.open('http://<?php echo $window_url . $id; ?>', 
'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
<table class="subscribe" cellpadding="0" cellspacing="0" border="0"><tr>
<td class="email-text" colspan="2"><p>
<a href="http://<?php echo $window_url . $id; ?>&amp;loc=<?php echo get_locale() . 
($bfa_ata_nofollow == "Yes" ? ' rel="nofollow"' : ''); ?>">
<img src="<?php echo $templateURI; ?>/images/feedburner-email.gif" style="float:left; margin: 0 7px 3px 0" alt="" /></a><?php echo $email_text; ?></p>
</td></tr><tr><td class="email-field"><input type="text" name="email" class="text inputblur" value="<?php echo $field_text; ?>" 
onfocus="this.value='';" />
<input type="hidden" value="<?php echo $hidden_value_url . $id; ?>" name="<?php echo $hidden_uri; ?>"/>
<?php echo $hidden_input ?>
<input type="hidden" name="loc" value="<?php echo get_locale(); ?>"/>
</td><td class="email-button">
<input type="submit" class="button"  value="<?php echo $submit_text; ?>" />
</td></tr>
<tr>
<td  class="post-text" colspan="2"><p>
<a href="<?php echo get_bloginfo('rss2_url'); ?>"<?php if ($bfa_ata['nofollow'] == "Yes") { ?> rel="nofollow"<?php } ?>>
<img src="<?php echo $templateURI; ?>/images/post-feed.gif" style="float:left; margin: 0 7px 3px 0" alt="" /></a><?php echo $posts_text; ?></p>
</td>
</tr>
<tr>
<td class="comment-text" colspan="2"><p>
<a href="<?php echo get_bloginfo('comments_rss2_url'); ?>"<?php if ($bfa_ata['nofollow'] == "Yes") { ?> rel="nofollow"<?php } ?>>
<img src="<?php echo $templateURI; ?>/images/comment-feed.gif" style="float:left; margin: 0 7px 3px 0" alt="" /></a><?php echo $comments_text; ?></p>
</td>
</tr>
</table>
</form>
<?php
	echo $after_widget;
}
function widget_bfa_subscribe_control() {
	$options = $newoptions = get_option('widget_bfa_subscribe');
	if ( isset($_POST["subscribe-submit"]) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["subscribe-title"]));
		
		$newoptions['field-text'] = strip_tags(stripslashes($_POST["feedburner-email-field-text"]));
		$newoptions['submit-text'] = strip_tags(stripslashes($_POST["feedburner-email-submit-text"]));
		
		if ( current_user_can('unfiltered_html') ) {
		$newoptions['email-text'] = stripslashes($_POST["subscribe-email-text"]); 
		$newoptions['posts-text'] = stripslashes($_POST["subscribe-posts-text"]);
		$newoptions['comments-text'] = stripslashes($_POST["subscribe-comments-text"]);
		} else { 
		$newoptions['email-text'] = stripslashes(wp_filter_post_kses($_POST["subscribe-email-text"]));
		$newoptions['posts-text'] = stripslashes(wp_filter_post_kses($_POST["subscribe-posts-text"]));
		$newoptions['comments-text'] = stripslashes(wp_filter_post_kses($_POST["subscribe-comments-text"]));
		}
		
		$newoptions['id'] = strip_tags(stripslashes($_POST["feedburner-email-id"]));
		$newoptions['google-or-feedburner'] = strip_tags(stripslashes($_POST["google-or-feedburner"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_bfa_subscribe', $options);
	}
	$title = esc_attr($options['title']);
	$email_text = format_to_edit($options['email-text']);
	$field_text = esc_attr($options['field-text']);
	$submit_text = esc_attr($options['submit-text']);
	$posts_text = format_to_edit($options['posts-text']);
	$comments_text = format_to_edit($options['comments-text']);
	$id = esc_attr($options['id']);
	$google_or_feedburner = esc_attr($options['google-or-feedburner']);
	if ( $google_or_feedburner == "" ) { $google_or_feedburner = "google"; }
?>
<p><label for="subscribe-title">Optional: Title:</label>
<input class="widefat" id="subscribe-title" name="subscribe-title" type="text" value="<?php echo $title; ?>" /></p>

<p><label for="subscribe-email-text"></label>
Text for Email section. <?php if ( current_user_can('unfiltered_html')) echo' (HTML allowed. Email subscribe URL = %email-url)'; ?>
<textarea class="widefat" style="width: 98%" rows="3" cols="20" id="subscribe-email-text" name="subscribe-email-text">
<?php echo $email_text; ?></textarea></p>

<p style="float: left; width: 69%; display: block">
<label for="feedburner-email-field-text">Optional: Text inside Email input field:</label> 
<input class="widefat" id="feedburner-email-field-text" name="feedburner-email-field-text" type="text" value="<?php echo $field_text; ?>" /></p>

<p style="float: right; width: 29%; display: block">
<label for="feedburner-email-submit-text">Text for Email submit button:</label>
<input class="widefat" id="feedburner-email-submit-text" name="feedburner-email-submit-text" type="text" value="<?php echo $submit_text; ?>" /></p>
<div style="clear: both"></div>

<p><label for="subscribe-posts-text"></label>
Text for Posts RSS section <?php if ( current_user_can('unfiltered_html')) echo ' (HTML allowed. Posts feed URL = %posts-url)'; ?>
<textarea class="widefat" style="width: 98%" rows="3" cols="20" id="subscribe-posts-text" name="subscribe-posts-text">
<?php echo $posts_text; ?></textarea></p>

<p><label for="subscribe-comments-text"></label>
Text for Comments RSS section <?php if ( current_user_can('unfiltered_html')) echo ' (HTML allowed. Comments feed URL = %comments-url)'; ?>
<textarea class="widefat" style="width: 98%" rows="3" cols="20" id="subscribe-comments-text" name="subscribe-comments-text">
<?php echo $comments_text; ?></textarea></p>

<p><label for="feedburner-email-id">Feedburner Email ID:</label>
<input class="widefat" id="feedburner-email-id" name="feedburner-email-id" type="text" value="<?php echo $id; ?>" /></p>

<p><strong>How to find the feed ID for this site at Feedburner:</strong><br />
Login to your Feedburner account, click "My Feeds" -> Title of the feed in question -> Publicize -> Email Subscriptions -> Check out the two textareas. 
In the bigger one of the two textareas your ID appears as: <br />1) If you have an <strong>old feedburner.com</strong> account:  
www.feedburner.com/fb/a/emailverifySubmit?feedId=<strong style="color:green">1234567</strong>&amp;loc=en_US<br />2) if you have a <strong>new feedburner.google.com</strong> account: 
feedburner.google.com/fb/a/mailverify?uri=<strong style="color:green">bytesforall/lzoG</strong>&amp;loc=en_US<br />The green part is the ID that you need to put here.</p>

<p>Is this a feedburner.google.com account or an old feedburner.com account?		
<p><input id="google-or-feedburner" name="google-or-feedburner" type="radio" value="google" <?php 
if($google_or_feedburner == "google"){echo " CHECKED";}?> /> New feedburner.google.com account</p>
<p><input id="google-or-feedburner" name="google-or-feedburner" type="radio" value="feedburner" <?php 
if($google_or_feedburner == "feedburner"){echo " CHECKED";}?> /> Old feedburner.com account</p>

			<input type="hidden" id="subscribe-submit" name="subscribe-submit" value="1" />

<?php
}
// register feedburner email widget
	$widget_ops = array('classname' => 'widget_bfa_subscribe', 'description' => 'Subscribe widget for RSS and Email' );
	$control_ops = array('width' => 600, 'height' => 500);
	wp_register_sidebar_widget('bfa_subscribe', 'BFA Subscribe', 'widget_bfa_subscribe', $widget_ops);
	wp_register_widget_control('bfa_subscribe', 'BFA Subscribe', 'widget_bfa_subscribe_control', $control_ops);		
?>