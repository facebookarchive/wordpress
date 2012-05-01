<?php 

// Cusom Title and meta description

$metabox_seo = array(

				'natty_meta_title' => array(
					'name' => 'natty_title',
					'type' => 'text',
					'title' => __('Custom Post Title', 'natty'),
					'description' => __('By default, natty uses the title of your post as the contents of the <code>&lt;title&gt;</code> tag. You can override this and further extend your on-page <acronym title="Search Engine Optimization">SEO</acronym> by entering your own <code>&lt;title&gt;</code> tag below.', 'natty'),
					'label' => __('Value for <code>&lt;title&gt;</code> tag', 'natty')				
				),
				'natty_meta_description' => array(
					'name' => 'natty_description',
					'type' => 'textarea',
					'title' => __('Meta Description', 'natty'),
					'description' => '',
					'label' => __('Value for <code>&lt;meta&gt;</code> description', 'natty')
				),
				'natty_meta_keywords' => array(
					'name' => 'natty_keywords',
					'type' => 'text',	
					'title' => __('Meta Keywords', 'natty'),
					'description' => __('Like the <code>&lt;meta&gt;</code> description, <code>&lt;meta&gt;</code> keywords are yet another on-page <acronym title="Search Engine Optimization">SEO</acronym> opportunity. Enter a few keywords that are relevant to your article.', 'natty'),
					'label' => __('Value for <code>&lt;meta&gt;</code> keywords', 'natty')
				)		

    );
    
function seo_meta_box_content() {
    global $post, $metabox_seo;    
    $output = '';	

    foreach ($metabox_seo as $seo_id => $seo_box) {   
		$existing_value = get_post_meta($post->ID, $seo_box['name'], true);
		if ($existing_value != '')
			$value = $existing_value;
		else
			$value = '';			
	
	   $output .='<div id="' . $seo_id . '">' . "\n";
	   $output .='<p><strong>' . $seo_box['title'] . '</strong></p>' . "\n";
	   
		if ($seo_box['type'] == 'text') {			
				$output .= '<p>' . "\n";
				$output .= '	<input type="text" class="text_input" style="width:99%;" name="' .$seo_box['name'] .'" value="' . $value . '" />' . "\n";
				
				$output .= '	<p>' . $seo_box['description'] .'</p>' . "\n";
				$output .= '</p>' . "\n";
				$output .= '<br />' . "\n";
		}
		elseif ($seo_box['type'] == 'textarea') {
				$output .= '<p>' . "\n";
				$output .= '	<textarea style="width:99%;" " name="' . $seo_box['name'] . '">' . $value . '</textarea>' . "\n";
				$output .= '	<label for="' . $seo_box['name'] . '">' . $seo_box['label'] . '</label>' . "\n";
				$output .= '</p>' . "\n";
				$output .= '<br />' . "\n";
		}
	   
	   $output .= '</div>'."\n\n";
    }    
    echo $output;
}

function seo_metabox_insert() {
    global $globals, $post, $metabox_seo;   
    if(isset($post->ID)) {
      $pID = $post->ID;    
      if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return $pID;
    

    $errors = array();  
	
	foreach ($metabox_seo as $seo_id) {
			$new_data = $_POST[$seo_id['name']];
			$current_data = get_post_meta($pID, $seo_id['name'], true);
			
			if ($current_data) {
				if ($new_data == '')
					delete_post_meta($pID, $seo_id['name']);
				elseif ($new_data != $current_data)
					update_post_meta($pID, $seo_id['name'], $new_data);
			}
			elseif ($new_data != '')
				add_post_meta($pID, $seo_id['name'], $new_data, true);
		}
	} 	
	
}

function seo_meta_box() {
    if ( function_exists('add_meta_box') ) {
        add_meta_box('seo-settings','SEO settings','seo_meta_box_content','post','normal');
        add_meta_box('seo-settings','SEO settings','seo_meta_box_content','page','normal');
    }
}
add_action('admin_menu', 'seo_meta_box');
add_action('save_post', 'seo_metabox_insert');
?>