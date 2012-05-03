<?php
/*
Plugin Name: BFA Popular in Cat
Plugin URI: http://wordpress.bytesforall.com/
Description: Configurable WordPress widget that displays the most popular posts in a given category based on the number of comments 
Version: 1.0
Author: BFA Webdesign
Author URI: http://www.bytesforall.com/
*/
/*
Based on Plugin "Most Commented" by Nick Momrik http://mtdewvirus.com/ Version 1.5
and the modification Last X days by DJ Chuang www.djchuang.com 
*/


	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// This is the function that outputs our little widget
	function widget_mdv_most_commented_per_cat($args) {
	  extract($args);

	if (is_category() ) { 
		
		$cat_id = get_query_var('cat');

	  // Fetch our parameters
	  $bfa_pic_options = get_option('widget_mdv_most_commented_per_cat');
	  $bfa_pic_title = $bfa_pic_options['bfa_pic_title'];
	  $bfa_pic_no_posts = $bfa_pic_options['bfa_pic_no_posts'];
	  $bfa_pic_duration = $bfa_pic_options['bfa_pic_duration'];
	  $bfa_pic_min_amount_comments = $bfa_pic_options['bfa_pic_min_amount_comments'];
	  $bfa_pic_prepend_cat_title = $bfa_pic_options['bfa_pic_prepend_cat_title'];  
	  $bfa_pic_append_cat_title = $bfa_pic_options['bfa_pic_append_cat_title'];
	  
	  $current_cat_title = htmlentities(single_cat_title('', false),ENT_QUOTES);
	  if ($bfa_pic_prepend_cat_title == "on" ) { $bfa_pic_title = $current_cat_title . " " . $bfa_pic_title; }
	  if ($bfa_pic_append_cat_title == "on" ) { $bfa_pic_title = $bfa_pic_title . " " . $current_cat_title; }	  
	  	  
	  global $wpdb;


		$bfa_pic_request = "SELECT DISTINCT ID, post_title, comment_count FROM $wpdb->posts as p";
		$bfa_pic_request .= " INNER JOIN $wpdb->term_relationships AS tr ON";
		$bfa_pic_request .= " (p.ID = tr.object_id AND";
		$bfa_pic_request .= " tr.term_taxonomy_id = $cat_id )";
		$bfa_pic_request .= " INNER JOIN $wpdb->term_taxonomy AS tt ON";
		$bfa_pic_request .= " (tr.term_taxonomy_id = tt.term_taxonomy_id AND";
		$bfa_pic_request .= " taxonomy = 'category')";
		$bfa_pic_request .= " WHERE post_status = 'publish' AND comment_count >= $bfa_pic_min_amount_comments";
		$bfa_pic_request .= " AND post_password =''";
	
		if ($bfa_pic_duration !="") $bfa_pic_request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$bfa_pic_duration." DAY) < post_date ";
	
		$bfa_pic_request .= " ORDER BY comment_count DESC LIMIT $bfa_pic_no_posts";
		$bfa_pic_posts = $wpdb->get_results($bfa_pic_request);

		if ($bfa_pic_posts) {
			$widget_mdv_most_commented_per_cat = '';
			foreach ($bfa_pic_posts as $bfa_pic_post) {
				$bfa_pic_post_title = stripslashes($bfa_pic_post->post_title);
				$bfa_pic_comment_count = $bfa_pic_post->comment_count;
				$bfa_pic_permalink = get_permalink($bfa_pic_post->ID);
				$widget_mdv_most_commented_per_cat .= '<li><a href="' . $bfa_pic_permalink . '" title="' . $bfa_pic_post_title.'">' . $bfa_pic_post_title . ' (' . $bfa_pic_comment_count . ')</a></li>';
			}
		} else {
			$widget_mdv_most_commented_per_cat = "None found";
		}
	

    if ($widget_mdv_most_commented_per_cat != "None found") {
    echo $before_widget . $before_title . $bfa_pic_title . $after_title;	
    echo "<ul>" . $widget_mdv_most_commented_per_cat . "</ul>";
    echo $after_widget;
    } else { return $widget_mdv_most_commented_per_cat; }
}
}


	// This is the function that outputs the form to let the users edit
	// the widget's parameters.
	function widget_mdv_most_commented_per_cat_control() {

	  // Fetch the options, check them and if need be, update the options array
	  $bfa_pic_options = $bfa_pic_newoptions = get_option('widget_mdv_most_commented_per_cat');
	  if ( isset($_POST["bfa_pic_src-submit"]) ) {
	    $bfa_pic_newoptions['bfa_pic_title'] = strip_tags(stripslashes($_POST["bfa_pic_src-title"]));
	    $bfa_pic_newoptions['bfa_pic_no_posts'] = (int) $_POST["bfa_pic_no_posts"];
	    $bfa_pic_newoptions['bfa_pic_duration'] = (int) $_POST["bfa_pic_duration"];
	    $bfa_pic_newoptions['bfa_pic_min_amount_comments'] = (int) $_POST["bfa_pic_min_amount_comments"];
	    $bfa_pic_newoptions['bfa_pic_append_cat_title'] = !isset($_POST["bfa_pic_append_cat_title"]) ? NULL : $_POST["bfa_pic_append_cat_title"];
	    $bfa_pic_newoptions['bfa_pic_prepend_cat_title'] = !isset($_POST["bfa_pic_prepend_cat_title"]) ? NULL : $_POST["bfa_pic_prepend_cat_title"];
	    	    	    	    
	  }
	  if ( $bfa_pic_options != $bfa_pic_newoptions ) {
	    $bfa_pic_options = $bfa_pic_newoptions;
	    update_option('widget_mdv_most_commented_per_cat', $bfa_pic_options);
	  }

	  // Default options to the parameters
	  if ( !isset($bfa_pic_options['bfa_pic_no_posts']) ) $bfa_pic_options['bfa_pic_no_posts'] = 10;
	  if ( !isset($bfa_pic_options['bfa_pic_min_amount_comments']) ) $bfa_pic_options['bfa_pic_min_amount_comments'] = 1;

	  $bfa_pic_no_posts = $bfa_pic_options['bfa_pic_no_posts'];
	  $bfa_pic_duration = $bfa_pic_options['bfa_pic_duration'];
	  $bfa_pic_min_amount_comments = $bfa_pic_options['bfa_pic_min_amount_comments'];
	  $bfa_pic_append_cat_title = $bfa_pic_options['bfa_pic_append_cat_title'];
	  $bfa_pic_prepend_cat_title = $bfa_pic_options['bfa_pic_prepend_cat_title'];
	  	  
	  // Deal with HTML in the parameters
	  $bfa_pic_title = htmlspecialchars($bfa_pic_options['bfa_pic_title'], ENT_QUOTES);

?>
	    Title: <input style="width: 450px;" id="bfa_pic_src-title" name="bfa_pic_src-title" type="text" value="<?php echo $bfa_pic_title; ?>" />
            <hr noshade size="1" style="clear:left; color: #ccc">
            <p style="text-align: left;"><input id="bfa_pic_prepend_cat_title" name="bfa_pic_prepend_cat_title" type="checkbox" <?php if($bfa_pic_prepend_cat_title == "on"){echo " CHECKED";}?> />Prepend &nbsp; 
            <p style="text-align: left;"><input id="bfa_pic_append_cat_title" name="bfa_pic_append_cat_title" type="checkbox" <?php if($bfa_pic_append_cat_title == "on"){echo " CHECKED";}?> /> Append &nbsp;&nbsp;&nbsp;Category Name to Title
            <hr noshade size="1" style="clear:left; color: #ccc">
            <p style="text-align: left;">Show <input style="width: 40px;" id="bfa_pic_no_posts" name="bfa_pic_no_posts" type="text" value="<?php echo $bfa_pic_no_posts; ?>" /> 
            posts not older than <input style="width: 60px;" id="bfa_pic_duration" name="bfa_pic_duration" type="text" value="<?php echo $bfa_pic_duration; ?>" /> days and with at least&nbsp;
   	    <input style="width: 40px;" id="bfa_pic_min_amount_comments" name="bfa_pic_min_amount_comments" type="text" value="<?php echo $bfa_pic_min_amount_comments; ?>" /> comments
   	    </p>
            <div style="clear:left"></div>
  	    <input type="hidden" id="bfa_pic_src-submit" name="bfa_pic_src-submit" value="1" />
<?php
	 }
	
	$widget_ops = array('classname' => 'widget_popular_in_cat', 'description' => __("Lists most commented posts of given category","atahualpa") );
	$control_ops = array('width' => 600, 'height' => 500);
	wp_register_sidebar_widget('popular_in_cat', __('BFA Popular in Cat','atahualpa'), 'widget_mdv_most_commented_per_cat', $widget_ops);
	wp_register_widget_control('popular_in_cat',  __('BFA Popular in Cat','atahualpa'), 'widget_mdv_most_commented_per_cat_control', $control_ops);	
?>