<?php 
/* 
Plugin Name: Otto test

*/

add_action('admin_menu', 'otto_admin_add_page');
function otto_admin_add_page() {
	add_options_page("Otto's Options", 'Otto Test Options', 'manage_options', 'otto', 'otto_options_page');
}


add_action('admin_init', 'otto_admin_add_settings');
function otto_admin_add_settings() {

	register_setting( 'otto_options', 'otto_db_options', 'otto_options_validate' );

	add_settings_section( 'section_one', 'First Section', 'otto_first_section', 'otto_options' );
	
	// second section needs no description, thus __return_false for the callback
	add_settings_section( 'section_two', 'Second Section', '__return_false', 'otto_options' );
	
	add_settings_field('otto_field_one', 'First Field', 'otto_first_field', 'otto_options', 'section_one');
	add_settings_field('otto_field_two', 'Second Field', 'otto_second_field', 'otto_options', 'section_one');
	
	add_settings_field('otto_field_three', 'Third Field', 'otto_third_field', 'otto_options', 'section_two');
	add_settings_field('otto_field_four', 'Fourth Field', 'otto_fourth_field', 'otto_options', 'section_two');
}

function otto_first_section() {
	echo '<p>This is the first section.</p>';
}


function otto_options_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Otto's Options Demo</h2>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'otto_options' );
			do_settings_sections( 'otto_options' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

function otto_first_field() {
	$options = get_option('otto_options');
	echo "<input id='otto_first_field' name='otto_options[first_field]' size='40' type='text' value='{$options['first_field']}' />";
}

function otto_second_field() {
	$options = get_option('otto_options');
	echo "<input id='otto_second_field' name='otto_options[second_field]' size='40' type='text' value='{$options['second_field']}' />";
}

function otto_third_field() {
	$options = get_option('otto_options');
	echo "<input id='otto_third_field' name='otto_options[third_field]' size='40' type='text' value='{$options['third_field']}' />";
}

function otto_fourth_field() {
	$options = get_option('otto_options');
	echo "<input id='otto_fourth_field' name='otto_options[fourth_field]' size='40' type='text' value='{$options['fourth_field']}' />";
}


// validate our options
function otto_options_validate($input) {
	// TODO: actual validation of the inputs here...
	$newinput['first_field'] = trim($input['first_field']);
	$newinput['second_field'] = trim($input['second_field']);
	$newinput['third_field'] = trim($input['third_field']);
	$newinput['fourth_field'] = trim($input['fourth_field']);

	return $newinput;
}
