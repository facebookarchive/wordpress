<?php 
function shortcodes_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_shortcodes_tinymce_plugin");
     add_filter('mce_buttons', 'register_shortcodes_button');
   }
}
 
function register_shortcodes_button($buttons) {
   array_push($buttons, "|", "mygallery_button");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js
function add_shortcodes_tinymce_plugin($plugin_array) {
   $plugin_array['mygallery'] = get_template_directory_uri() .'/functions/tinymce/editor_plugin.js';
   return $plugin_array;
}

function my_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}

add_filter( 'tiny_mce_version', 'my_refresh_mce');

// init process for button control
add_action('init', 'shortcodes_addbuttons');
?>