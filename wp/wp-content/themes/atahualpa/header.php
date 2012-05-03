<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php global $templateURI,  $homeURL; ?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php // if index.php or another page template (copied from index.php) was not used
if (!isset($bfa_ata))  
list($bfa_ata, $cols, $left_col, $left_col2, $right_col, $right_col2, $bfa_ata['h_blogtitle'], $bfa_ata['h_posttitle']) = bfa_get_options(); ?>
<?php global $post_id; ?>
<?php if ( isset($bfa_ata['IEDocType']) ) { 
switch ( $bfa_ata['IEDocType'] ) { 
	case "None":
		break;
	case "EmulateIE7":
		?><meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
		<?php
		break;
	case "EmulateIE8":
		?><meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
		<?php
		break;
	case "IE8":
		?><meta http-equiv="X-UA-Compatible" content="IE=8"/>
		<?php
		break;
	case "IE9":
		?><meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<?php
		break;
	case "Edge":
		?><meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
		<?php
		break;
	default:
		break;
}} ?><?php bfa_meta_tags(); ?>
<?php if ($bfa_ata['favicon_file'] != "") { ?><link rel="shortcut icon" href="<?php echo $templateURI; ?>/images/favicon/<?php echo $bfa_ata['favicon_file']; ?>" />
<?php } ?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if ( $bfa_ata['css_external'] == "External" ) { ?><link rel="stylesheet" href="<?php echo $homeURL; ?>/?bfa_ata_file=css" type="text/css" media="all" /><?php } ?>
<?php if ( function_exists('wp_list_comments') AND is_singular() AND (comments_open( $post_id ))) { 	wp_enqueue_script( 'comment-reply' ); } ?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?><?php bfa_incl('html_inserts_body_tag'); ?>>
<?php bfa_incl('html_inserts_body_top'); ?>
<div id="wrapper">
<div id="container">
<table id="layout" border="0" cellspacing="0" cellpadding="0">
<colgroup>
<?php if ( $left_col == "on" ) { ?><col class="colone" /><?php } ?>
<?php if ( $left_col2 == "on" ) { ?><col class="colone-inner" /><?php } ?>
<col class="coltwo" />
<?php if ( $right_col2 == "on" ) { ?><col class="colthree-inner" /><?php } ?>
<?php if ( $right_col == "on" ) { ?><col class="colthree" /><?php } ?>
</colgroup> 
	<tr>

		<!-- Header -->
		<td id="header" colspan="<?php echo $cols; ?>">

		<?php echo bfa_header_config(); ?>

		</td>
		<!-- / Header -->

	</tr>

	<!-- Main Body -->	
	<tr id="bodyrow">

		<?php if ( $left_col == "on" ) { ?>
		<!-- Left Sidebar -->
		<td id="left">

			<?php // Widgetize the Left Sidebar 
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Left Sidebar') ) : ?>
		
				<div class="widget widget_categories"><div class="widget-title">
				<h3><?php _e('Categories','atahualpa'); ?></h3>
				</div>
				<ul><?php wp_list_categories('show_count=1&title_li='); ?></ul>
				</div>
				
				<div class="widget widget_archive"><div class="widget-title">
				<h3><?php _e('Archives','atahualpa'); ?></h3>
				</div>
				<ul><?php wp_get_archives('type=monthly'); ?></ul>
				</div>

				<div class="widget widget_text"><div class="widget-title">
				<h3>A sample text widget</h3></div>
				<div class="textwidget">
				<p>Etiam pulvinar consectetur dolor sed malesuada. Ut convallis 
				<a href="http://wordpress.org/">euismod dolor nec</a> pretium. Nunc ut tristique massa. </p>
				<p>Nam sodales mi vitae dolor <em>ullamcorper et vulputate enim accumsan</em>. 
				Morbi orci magna, tincidunt vitae molestie nec, molestie at mi. <strong>Nulla nulla lorem</strong>, 
				suscipit in posuere in, interdum non magna. </p>
				</div>
				
			<?php endif; ?>

		</td>
		<!-- / Left Sidebar -->
		<?php } ?>

		<?php if ( $left_col2 == "on" ) { ?>
		<!-- Left INNER Sidebar -->
		<td id="left-inner">

			<?php // Widgetize the Left Inner Sidebar 
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Left Inner Sidebar') ) : ?>
		
					<!-- no default content for the LEFT INNER sidebar -->
									
			<?php endif; ?>

		</td>
		<!-- / Left INNER Sidebar -->
		<?php } ?>
		

		<!-- Main Column -->
		<td id="middle">