<?php
function natty_sidebar_init() {
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Sidebar (Main)',
		'before_widget' => '<li id="%2$s" class="widget png_scale">',
    	'after_widget' => '</li>',
    	'before_title' => '<h2 class="blocktitle"><span>',
        'after_title' => '</span></h2>'
    ));	
	register_sidebar(array(
		'name' => 'Sidebar (Inner Page/Post)',
		'description'   => 'Add widgets for single Page and Post. Displaying Sidebar (Main) if empty.',
		'before_widget' => '<li id="%2$s" class="widget png_scale">',
    	'after_widget' => '</li>',
    	'before_title' => '<h2 class="blocktitle"><span>',
        'after_title' => '</span></h2>'
    ));

	}
}

add_action( 'widgets_init', 'natty_sidebar_init' );

?>