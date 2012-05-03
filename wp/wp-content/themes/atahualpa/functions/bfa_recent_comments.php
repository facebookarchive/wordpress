<?php
/*
Plugin Name: BFA Recent Comments Widget
Plugin URI: http://wordpress.bytesforall.com/
Description: Highly configurable WordPress widget that shows a list of recent comments.
Version: 1.0
Author: BFA Webdesign
Author URI: http://www.bytesforall.com/
*/

/*
Based on v0.2.4 of Recent Comments Widget by Mika Perälä
http://mika.kfib.org/

Based on v0.1.1 of the Simple Recent Comments-plugin by Raoul
http://www.raoul.shacknet.nu/

License: GPL
Compatibility: WordPress 2.2 or newer.

Installation:
Place the widget_simple_recent_comments.php file in your /wp-content/plugins/widgets/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright BFA Webdesign - http://wordpress.bytesforall.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog
* Fri Aug 22 2008 - v1.0 
- Initial release
*/



	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// This is the function that outputs our little widget
	function widget_simple_recent_comments($args) {
	  extract($args);

	  // Fetch our parameters
	  $bfa_rc_options = get_option('widget_simple_recent_comments');
	  $bfa_rc_title = $bfa_rc_options['bfa_rc_title'];
	  $bfa_rc_src_count = $bfa_rc_options['bfa_rc_src_count'];
	  $bfa_rc_src_length = $bfa_rc_options['bfa_rc_src_length'];
	  $bfa_rc_linking_scheme = $bfa_rc_options['bfa_rc_linking_scheme'];
	  $point_first_link = $bfa_rc_options['point_first_link'];
	  $point_second_link = $bfa_rc_options['point_second_link'];
	  $add_dots = $bfa_rc_options['add_dots'];
	  $limit_by = $bfa_rc_options['limit_by'];
	  $author_bold = $bfa_rc_options['author_bold'];
	  $author_em = $bfa_rc_options['author_em'];	  
	  $comment_bold = $bfa_rc_options['comment_bold'];
	  $comment_em = $bfa_rc_options['comment_em'];	
	  $post_bold = $bfa_rc_options['post_bold'];
	  $post_em = $bfa_rc_options['post_em'];
	  $author_nofollow = $bfa_rc_options['author_nofollow'];	
	  if(isset($bfa_rc_options['bfa_rc_pre_HTML'])) $bfa_rc_pre_HTML = $bfa_rc_options['bfa_rc_pre_HTML'];
		else $bfa_rc_pre_HTML = '';
	  if(isset($bfa_rc_options['bfa_rc_post_HTML'])) $bfa_rc_post_HTML = $bfa_rc_options['bfa_rc_post_HTML'];
		else $bfa_rc_post_HTML = '';
	  $bfa_rc_display_homepage = $bfa_rc_options['bfa_rc_display_homepage'];
	  $bfa_rc_display_category = $bfa_rc_options['bfa_rc_display_category'];
	  $bfa_rc_display_post = $bfa_rc_options['bfa_rc_display_post'];
	  $bfa_rc_display_page = $bfa_rc_options['bfa_rc_display_page'];
	  $bfa_rc_display_archive = $bfa_rc_options['bfa_rc_display_archive'];
	  $bfa_rc_display_tag = $bfa_rc_options['bfa_rc_display_tag'];
	  $bfa_rc_display_search = $bfa_rc_options['bfa_rc_display_search'];
	  $bfa_rc_display_author = $bfa_rc_options['bfa_rc_display_author'];
	  $bfa_rc_display_404 = $bfa_rc_options['bfa_rc_display_404'];
	  	  
	  global $wpdb;

	  if ( (is_home() && $bfa_rc_display_homepage == "on") OR 
	  (is_category() && $bfa_rc_display_category == "on") OR 
	  (is_single() && $bfa_rc_display_post == "on") OR 
	  (is_page() && $bfa_rc_display_page == "on") OR 
	  (is_date() && $bfa_rc_display_archive == "on") OR 	  
	  (is_tag() && $bfa_rc_display_tag == "on") OR 
	  (is_search() && $bfa_rc_display_search == "on") OR 
	  (is_author() && $bfa_rc_display_author == "on") OR 
	  (is_404() && $bfa_rc_display_404 == "on")) { 

	  // Build the query and fetch the results
	  $sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_author_url, comment_content, comment_date_gmt, comment_approved, comment_type, ";

	  if ($limit_by == "letters") {
	  $sql .= "SUBSTRING(comment_content,1,$bfa_rc_src_length) AS com_excerpt "; }
	  elseif ($limit_by == "words") {
	  $sql .= "SUBSTRING_INDEX(comment_content,' ',$bfa_rc_src_length) AS com_excerpt "; }
	  $sql .= "FROM $wpdb->comments 
		LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) 
		WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' 
		ORDER BY comment_date_gmt DESC 
		LIMIT $bfa_rc_src_count";
	  $comments = $wpdb->get_results($sql);

	  // Generate the output string, prepend and append the HTML specified
	  $output = $bfa_rc_pre_HTML;
	  $output .= "\n<ul id=\"bfarecentcomments\">";
	  if (!empty($comments)) {
	    foreach ($comments as $comment) {
	      // Make a check if we need to print out '...' after the selected
	      // comment text. This needs to be done if the text is longer than
	      // the specified length.
	      $dots = '';
#	      if ( $bfa_rc_src_length <= strlen(strip_tags($comment->com_excerpt)) ) $dots = "...";
              if ($limit_by == "letters") {
	      if ( $bfa_rc_src_length <= strlen(strip_tags($comment->comment_content)) ) {$dots = "...";}
	      } 
	      elseif ($limit_by == "words") {
	      if ( $bfa_rc_src_length <= count(explode(" ", strip_tags($comment->comment_content))) ) {$dots = "...";}
	      }
		// different comment link for WP 2.7 and newer / WP 2.6 and older
		 if (function_exists('wp_list_comments')) { $comment_link = get_comment_link($comment->comment_ID); } else {
	      $comment_link = get_permalink($comment->ID) . "#comment-" . $comment->comment_ID; }
	      $post_link = get_permalink($comment->ID);
	      $author_link = $comment->comment_author_url;
	      if ($author_nofollow == "on") {$author_link = $author_link . '" rel="nofollow'; }
	      
	      if ($point_first_link == "comment") {$first_link = $comment_link; }
	      elseif ($point_first_link == "post") {$first_link = $post_link; }
	      elseif ($point_first_link == "author") {$first_link = $author_link; }

	      if ($point_second_link == "comment") {$second_link = $comment_link; }
	      elseif ($point_second_link == "post") {$second_link = $post_link; }
	      elseif ($point_second_link == "author") {$second_link = $author_link; }	      
	      
	      $comment_text = strip_tags($comment->com_excerpt);
	      if ($add_dots == "on") {$comment_text = $comment_text . $dots; }
	      if ($comment_bold == "on") {$comment_text2 = "<strong>$comment_text</strong>"; } else {$comment_text2 = $comment_text; }
	      if ($comment_em == "on") {$comment_text2 = "<em>$comment_text2</em>"; }
	      $post_text = apply_filters('the_title_rss', $comment->post_title);
	      if ($post_bold == "on") {$post_text2 = "<strong>$post_text</strong>"; } else {$post_text2 = $post_text; }
	      if ($post_em == "on") {$post_text2 = "<em>$post_text2</em>"; }     
	      $author_text = $comment->comment_author;
	      if ($author_bold == "on") {$author_text2 = "<strong>$author_text</strong>"; } else {$author_text2 = $author_text; }
	      if ($author_em == "on") {$author_text2 = "<em>$author_text2</em>"; }     

	      $output .= "\n\t<li class=\"bfarecentcomments\">";
	      
	      if ( $bfa_rc_linking_scheme == "Author Comment link-1" ) {
	      $output .= "<a href=\"$first_link\" title=\"" . __('On: ','atahualpa') . "$post_text\">$author_text2</a>: $comment_text2";
	      } elseif ( $bfa_rc_linking_scheme == "Author Comment link-2" ) {
              $output .= "$author_text2: <a href=\"$first_link\" title=\"" . __('On: ','atahualpa') . "$post_text\">$comment_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Author Comment link-1 link-2" ) {
              $output .= "<a href=\"$first_link\">$author_text2</a>: <a href=\"$second_link\" title=\"" . __('On: ','atahualpa') . "$post_text\">$comment_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Author Comment link-all" ) {
              $output .= "<a href=\"$first_link\" title=\"" . __('On: ','atahualpa') . "$post_text\">$author_text2: $comment_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Author on Post link-1" ) {
              $output .= "<a href=\"$first_link\" title=\"$comment_text\">$author_text2</a>" . __(' on: ','atahualpa') . "$post_text2";
	      } elseif ( $bfa_rc_linking_scheme == "Author on Post link-2" ) {
              $output .= "$author_text2" . __(' on: ','atahualpa') . "<a href=\"$first_link\" title=\"$comment_text\">$post_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Author on Post link-1 link-2" ) {
              $output .= "<a href=\"$first_link\" title=\"$comment_text\">$author_text2</a>" . __(' on: ','atahualpa') . "<a href=\"$second_link\">$post_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Author on Post link-all" ) {
              $output .= "<a href=\"$first_link\" title=\"$comment_text\">$author_text2" . __(' on: ','atahualpa') . "$post_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Post Comment link-1" ) {
              $output .= "<a href=\"$first_link\" title=\"$author_text\">$post_text2</a>: $comment_text2";
	      } elseif ( $bfa_rc_linking_scheme == "Post Comment link-2" ) {
              $output .= "$post_text2: <a href=\"$first_link\" title=\"$author_text\">$comment_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Post Comment link-1 link-2" ) {
              $output .= "<a href=\"$first_link\">$post_text2</a>: <a href=\"$second_link\" title=\"$author_text\">$comment_text2</a>";
	      } elseif ( $bfa_rc_linking_scheme == "Post Comment link-all" ) {
              $output .= "<a href=\"$first_link\" title=\"$author_text\">$post_text2: $comment_text2</a>";
              }

	      $output .= "</li>";

	    }
	  } else {
	    $output .= 'No comments.';
	  }
	  $output .= "\n</ul>";
	  $output .= $bfa_rc_post_HTML;

	  // remove empty author links
          $output = preg_replace("/<a href=\"\"(.*?)>(.*?)<\/a>/i","\\2",$output);
          $output = preg_replace("/<a href=\"http:\/\/\"(.*?)>(.*?)<\/a>/i","\\2",$output);
	  
	  // These lines generate the output

	  echo $before_widget . $before_title . $bfa_rc_title . $after_title;
	  echo $output;  
	  echo $after_widget;
	}
	}


	// This is the function that outputs the form to let the users edit
	// the widget's parameters.
	function widget_simple_recent_comments_control() {

	  // Fetch the options, check them and if need be, update the options array
	  $bfa_rc_options = $bfa_rc_newoptions = get_option('widget_simple_recent_comments');
	  if ( isset($_POST["bfa_rc_src-submit"]) ) {
	    $bfa_rc_newoptions['bfa_rc_title'] = strip_tags(stripslashes($_POST["bfa_rc_src-title"]));
	    $bfa_rc_newoptions['bfa_rc_src_count'] = (int) $_POST["bfa_rc_src_count"];
	    $bfa_rc_newoptions['bfa_rc_src_length'] = (int) $_POST["bfa_rc_src_length"];
	    $bfa_rc_newoptions['bfa_rc_linking_scheme'] = strip_tags(stripslashes($_POST["bfa_rc_linking_scheme"]));	
	    $bfa_rc_newoptions['point_first_link'] = $_POST["point_first_link"];	
	    $bfa_rc_newoptions['point_second_link'] = $_POST["point_second_link"];
#	    $bfa_rc_newoptions['add_dots'] = $_POST["add_dots"];
	    $bfa_rc_newoptions['add_dots'] = !isset($_POST["add_dots"]) ? NULL : $_POST["add_dots"];
	    $bfa_rc_newoptions['limit_by'] = $_POST["limit_by"];	
	    $bfa_rc_newoptions['author_bold'] = !isset($_POST["author_bold"]) ? NULL : $_POST["author_bold"];	        	    	    
	    $bfa_rc_newoptions['author_em'] = !isset($_POST["author_em"]) ? NULL : $_POST["author_em"];
	    $bfa_rc_newoptions['comment_bold'] = !isset($_POST["comment_bold"]) ? NULL : $_POST["comment_bold"];
	    $bfa_rc_newoptions['comment_em'] = !isset($_POST["comment_em"]) ? NULL : $_POST["comment_em"];	    	    
	    $bfa_rc_newoptions['post_bold'] = !isset($_POST["post_bold"]) ? NULL : $_POST["post_bold"];
	    $bfa_rc_newoptions['post_em'] = !isset($_POST["post_em"]) ? NULL : $_POST["post_em"];	    	    
	    $bfa_rc_newoptions['author_nofollow'] = !isset($_POST["author_nofollow"]) ? NULL : $_POST["author_nofollow"];	    	    
	    $bfa_rc_newoptions['bfa_rc_display_homepage'] = !isset($_POST["bfa_rc_display_homepage"]) ? NULL : $_POST["bfa_rc_display_homepage"];
	    $bfa_rc_newoptions['bfa_rc_display_category'] = !isset($_POST["bfa_rc_display_category"]) ? NULL : $_POST["bfa_rc_display_category"];
	    $bfa_rc_newoptions['bfa_rc_display_post'] = !isset($_POST["bfa_rc_display_post"]) ? NULL : $_POST["bfa_rc_display_post"];
	    $bfa_rc_newoptions['bfa_rc_display_page'] = !isset($_POST["bfa_rc_display_page"]) ? NULL : $_POST["bfa_rc_display_page"];
	    $bfa_rc_newoptions['bfa_rc_display_archive'] = !isset($_POST["bfa_rc_display_archive"]) ? NULL : $_POST["bfa_rc_display_archive"];
	    $bfa_rc_newoptions['bfa_rc_display_tag'] = !isset($_POST["bfa_rc_display_tag"]) ? NULL : $_POST["bfa_rc_display_tag"];
	    $bfa_rc_newoptions['bfa_rc_display_search'] = !isset($_POST["bfa_rc_display_search"]) ? NULL : $_POST["bfa_rc_display_search"];
	    $bfa_rc_newoptions['bfa_rc_display_author'] = !isset($_POST["bfa_rc_display_author"]) ? NULL : $_POST["bfa_rc_display_author"];
	    $bfa_rc_newoptions['bfa_rc_display_404'] = !isset($_POST["bfa_rc_display_404"]) ? NULL : $_POST["bfa_rc_display_404"];
	    	    	    	    
	  }
	  if ( $bfa_rc_options != $bfa_rc_newoptions ) {
	    $bfa_rc_options = $bfa_rc_newoptions;
	    update_option('widget_simple_recent_comments', $bfa_rc_options);
	  }

	  // Default options to the parameters
	  if ( !$bfa_rc_options['bfa_rc_src_count'] ) $bfa_rc_options['bfa_rc_src_count'] = 7;
	  if ( !isset($bfa_rc_options['bfa_rc_src_length']) ) $bfa_rc_options['bfa_rc_src_length'] = 60;
	  if ( !isset($bfa_rc_options['bfa_rc_linking_scheme']) ) $bfa_rc_options['bfa_rc_linking_scheme'] = "Author Comment link-all";
	  if ( !isset($bfa_rc_options['point_first_link']) ) $bfa_rc_options['point_first_link'] = "author";
	  if ( !isset($bfa_rc_options['point_second_link']) ) $bfa_rc_options['point_second_link'] = "comment";
	  if ( !isset($bfa_rc_options['limit_by']) ) $bfa_rc_options['limit_by'] = "letters";
	  if ( !isset($bfa_rc_options['author_nofollow']) ) $bfa_rc_options['author_nofollow'] = "on";
	  
	  $bfa_rc_src_count = $bfa_rc_options['bfa_rc_src_count'];
	  $bfa_rc_src_length = $bfa_rc_options['bfa_rc_src_length'];
	  $bfa_rc_linking_scheme = $bfa_rc_options['bfa_rc_linking_scheme'];
	  $point_first_link = $bfa_rc_options['point_first_link'];
	  $point_second_link = $bfa_rc_options['point_second_link'];
	  $add_dots = $bfa_rc_options['add_dots'];
	  $limit_by = $bfa_rc_options['limit_by'];
	  $author_bold = $bfa_rc_options['author_bold'];	  
	  $author_em = $bfa_rc_options['author_em'];	  
	  $comment_bold = $bfa_rc_options['comment_bold'];	  
	  $comment_em = $bfa_rc_options['comment_em'];	  
	  $post_bold = $bfa_rc_options['post_bold'];	  
	  $post_em = $bfa_rc_options['post_em'];	  
	  $author_nofollow = $bfa_rc_options['author_nofollow'];	  
	  $bfa_rc_display_homepage = $bfa_rc_options['bfa_rc_display_homepage'];
	  $bfa_rc_display_category = $bfa_rc_options['bfa_rc_display_category'];
	  $bfa_rc_display_post = $bfa_rc_options['bfa_rc_display_post'];
	  $bfa_rc_display_page = $bfa_rc_options['bfa_rc_display_page'];
	  $bfa_rc_display_archive = $bfa_rc_options['bfa_rc_display_archive'];
	  $bfa_rc_display_tag = $bfa_rc_options['bfa_rc_display_tag'];	  
	  $bfa_rc_display_search = $bfa_rc_options['bfa_rc_display_search'];
	  $bfa_rc_display_author = $bfa_rc_options['bfa_rc_display_author'];
	  $bfa_rc_display_404 = $bfa_rc_options['bfa_rc_display_404'];
	  	  
	  // Deal with HTML in the parameters
	  if(isset($bfa_rc_options['bfa_rc_pre_HTML'])) $bfa_rc_pre_HTML = htmlspecialchars($bfa_rc_options['bfa_rc_pre_HTML'], ENT_QUOTES);
	  if(isset($bfa_rc_options['bfa_rc_post_HTML'])) $bfa_rc_post_HTML = htmlspecialchars($bfa_rc_options['bfa_rc_post_HTML'], ENT_QUOTES);
	  $bfa_rc_title = htmlspecialchars($bfa_rc_options['bfa_rc_title'], ENT_QUOTES);

?>
	    Title: <input style="width: 450px;" id="bfa_rc_src-title" name="bfa_rc_src-title" type="text" value="<?php echo $bfa_rc_title; ?>" />
	    <hr noshade size="1" style="clear:left; color: #ccc">
            <p style="text-align: left;">Show <input style="width: 40px;" id="bfa_rc_src_count" name="bfa_rc_src_count" type="text" value="<?php echo $bfa_rc_src_count; ?>" /> comments like this:</p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author Comment link-1" <?php if($bfa_rc_linking_scheme == "Author Comment link-1"){echo " CHECKED";}?> /> <a href="#" title="On: Post Title">Author Name</a>: Comment Text</p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author Comment link-2" <?php if($bfa_rc_linking_scheme == "Author Comment link-2"){echo " CHECKED";}?> /> Author Name: <a href="#" title="On: Post Title">Comment Text</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author Comment link-1 link-2" <?php if($bfa_rc_linking_scheme == "Author Comment link-1 link-2"){echo " CHECKED";}?> /> <a href="#">Author Name</a>: <a href="#" title="On: Post Title">Comment Text</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author Comment link-all" <?php if($bfa_rc_linking_scheme == "Author Comment link-all"){echo " CHECKED";}?> /> <a href="#" title="On: Post Title">Author Name: Comment Text</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author on Post link-1" <?php if($bfa_rc_linking_scheme == "Author on Post link-1"){echo " CHECKED";}?> /> <a href="#" title="Comment Text">Author Name</a> on: Post Title</p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author on Post link-2" <?php if($bfa_rc_linking_scheme == "Author on Post link-2"){echo " CHECKED";}?> /> Author Name on: <a href="#" title="Comment Text">Post Title</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author on Post link-1 link-2" <?php if($bfa_rc_linking_scheme == "Author on Post link-1 link-2"){echo " CHECKED";}?> /> <a href="#" title="Comment Text">Author Name</a> on: <a href="#">Post Title</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Author on Post link-all" <?php if($bfa_rc_linking_scheme == "Author on Post link-all"){echo " CHECKED";}?> /> <a href="#" title="Comment Text">Author Name on: Post Title</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Post Comment link-1" <?php if($bfa_rc_linking_scheme == "Post Comment link-1"){echo " CHECKED";}?> /> <a href="#" title="Author Name">Post Title</a>: Comment Text</p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Post Comment link-2" <?php if($bfa_rc_linking_scheme == "Post Comment link-2"){echo " CHECKED";}?> /> Post Title: <a href="#" title="Author Name">Comment Text</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Post Comment link-1 link-2" <?php if($bfa_rc_linking_scheme == "Post Comment link-1 link-2"){echo " CHECKED";}?> /> <a href="#">Post Title</a>: <a href="#" title="Author Name">Comment Text</a></p>
            <p style="float: left; width: 195px; text-align: left;"><input id="bfa_rc_linking_scheme" name="bfa_rc_linking_scheme" type="radio" value="Post Comment link-all" <?php if($bfa_rc_linking_scheme == "Post Comment link-all"){echo " CHECKED";}?> /> <a href="#" title="Author Name">Post Title: Comment Text</a></p>
	    <hr noshade size="1" style="clear:left; color: #ccc">
            <p style="clear:left">Point the first link to:</p> 
            <p style="float: left; text-align: left;"><input id="point_first_link" name="point_first_link" type="radio" value="author" <?php if($point_first_link == "author"){echo " CHECKED";}?> /> the author's homepage (if any)</p>
            <p style="float: left; margin-left: 10px; text-align: left;"><input id="point_first_link" name="point_first_link" type="radio" value="comment" <?php if($point_first_link == "comment"){echo " CHECKED";}?> /> the comments</p>            
            <p style="float: left; margin-left: 10px; text-align: left;"><input id="point_first_link" name="point_first_link" type="radio" value="post" <?php if($point_first_link == "post"){echo " CHECKED";}?> /> the post</p>              
            <p style="clear:left">Point the second link (if any) to:</p>
            <p style="float: left; text-align: left;"><input id="point_second_link" name="point_second_link" type="radio" value="author" <?php if($point_second_link == "author"){echo " CHECKED";}?> /> the author's homepage (if any)</p>
            <p style="float: left; margin-left: 10px; text-align: left;"><input id="point_second_link" name="point_second_link" type="radio" value="comment" <?php if($point_second_link == "comment"){echo " CHECKED";}?> /> the comments</p>            
            <p style="float: left; margin-left: 10px; text-align: left;"><input id="point_second_link" name="point_second_link" type="radio" value="post" <?php if($point_second_link == "post"){echo " CHECKED";}?> /> the post</p>              
	    <hr noshade size="1" style="clear:left; color: #ccc">
	    <p><input id="author_nofollow" name="author_nofollow" type="checkbox" <?php if($author_nofollow == "on"){echo " CHECKED";}?> /> Set the link to the Author Homepage to "Nofollow"</p>
	    <hr noshade size="1" style="clear:left; color: #ccc">
   	    Limit the comment text to <input style="width: 40px;" id="bfa_rc_src_length" name="bfa_rc_src_length" type="text" value="<?php echo $bfa_rc_src_length; ?>" /> <input id="limit_by" name="limit_by" type="radio" value="letters" <?php if($limit_by == "letters"){echo " CHECKED";}?> /> letters <input id="limit_by" name="limit_by" type="radio" value="words" <?php if($limit_by == "words"){echo " CHECKED";}?> /> words.&nbsp;&nbsp;&nbsp;<input id="add_dots" name="add_dots" type="checkbox" <?php if($add_dots == "on"){echo " CHECKED";}?> /> add "..." if the actual comment is longer than that.</p>
	    <hr noshade size="1" style="clear:left; color: #ccc">
	    <p>Make the Author Name <input id="author_bold" name="author_bold" type="checkbox" <?php if($author_bold == "on"){echo " CHECKED";}?> /> <strong>Bold</strong>  <input id="author_em" name="author_em" type="checkbox" <?php if($author_em == "on"){echo " CHECKED";}?> /> <em>Emphasized</em></p>
	    <p>Make the Comment Text <input id="comment_bold" name="comment_bold" type="checkbox" <?php if($comment_bold == "on"){echo " CHECKED";}?> /> <strong>Bold</strong>  <input id="comment_em" name="comment_em" type="checkbox" <?php if($comment_em == "on"){echo " CHECKED";}?> /> <em>Emphasized</em></p>
	    <p>Make the Post Title <input id="post_bold" name="post_bold" type="checkbox" <?php if($post_bold == "on"){echo " CHECKED";}?> /> <strong>Bold</strong>  <input id="post_em" name="post_em" type="checkbox" <?php if($post_em == "on"){echo " CHECKED";}?> /> <em>Emphasized</em></p>
	    <hr noshade size="1" style="clear:left; color: #ccc">
            <p>Display the list on:</p>            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_homepage" name="bfa_rc_display_homepage" type="checkbox" <?php if($bfa_rc_display_homepage == "on"){echo " CHECKED";}?> /> Homepage</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_category" name="bfa_rc_display_category" type="checkbox" <?php if($bfa_rc_display_category == "on"){echo " CHECKED";}?> /> Category Pages</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_post" name="bfa_rc_display_post" type="checkbox" <?php if($bfa_rc_display_post == "on"){echo " CHECKED";}?> /> Single Post Pages</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_page" name="bfa_rc_display_page" type="checkbox" <?php if($bfa_rc_display_page == "on"){echo " CHECKED";}?> /> "Page" Pages</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_archive" name="bfa_rc_display_archive" type="checkbox" <?php if($bfa_rc_display_archive == "on"){echo " CHECKED";}?> /> Archive Pages</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_tag" name="bfa_rc_display_tag" type="checkbox" <?php if($bfa_rc_display_tag == "on"){echo " CHECKED";}?> /> Tag Pages</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_search" name="bfa_rc_display_search" type="checkbox" <?php if($bfa_rc_display_search == "on"){echo " CHECKED";}?> /> Search Result Pages</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_author" name="bfa_rc_display_author" type="checkbox" <?php if($bfa_rc_display_author == "on"){echo " CHECKED";}?> /> Author Pages</p>
            <p style="float: left; width: 160px; text-align: left;"><input id="bfa_rc_display_404" name="bfa_rc_display_404" type="checkbox" <?php if($bfa_rc_display_404 == "on"){echo " CHECKED";}?> /> 404 Not Found Pages</p>
  	    <input type="hidden" id="bfa_rc_src-submit" name="bfa_rc_src-submit" value="1" />
<?php
	 }

	$widget_ops = array('classname' => 'widget_recent_comments', 'description' => __("Lists the most recent comments","atahualpa") );
	$control_ops = array('width' => 600, 'height' => 500);
	wp_register_sidebar_widget('recent_comments', __('BFA Recent Comments','atahualpa'), 'widget_simple_recent_comments', $widget_ops);
	wp_register_widget_control('recent_comments',  __('BFA Recent Comments','atahualpa'), 'widget_simple_recent_comments_control', $control_ops);	
?>