<?php
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Sidebar 1',
'id' => 'sidebar-1',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
));

$options = get_option('evolve'); if (($options['evl_sidebar_num'] == "two"))  
{
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Sidebar 2',
'id' => 'sidebar-2',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
));
}


function evlheader1() {
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Header 1',
'id' => 'header-1',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
)); }
function evlheader2() { if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Header 2',
'id' => 'header-2',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
)); }
function evlheader3() { if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Header 3',
'id' => 'header-3',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
)); }
function evlheader4() { if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Header 4',
'id' => 'header-4',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
));
}

function evlfooter1() {
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Footer 1',
'id' => 'footer-1',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
)); }
function evlfooter2() { if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Footer 2',
'id' => 'footer-2',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
)); }
function evlfooter3() { if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Footer 3',
'id' => 'footer-3',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
)); }
function evlfooter4() { if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => 'Footer 4',
'id' => 'footer-4',
'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
'after_widget' => '</div></div>',
'before_title' => '<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">',
'after_title' => '</h3></div>',
));
}

$options = get_option('evolve');

// Header widgets

  if (($options['evl_widgets_header'] == "one"))  
{
evlheader1();
}
  if (($options['evl_widgets_header'] == "two"))  
{
evlheader1();
evlheader2();
}
  if (($options['evl_widgets_header'] == "three"))  
{
evlheader1();
evlheader2();
evlheader3();
}
  if (($options['evl_widgets_header'] == "four"))  
{
evlheader1();
evlheader2();
evlheader3();
evlheader4();
} else {}

// Footer widgets

  if (($options['evl_widgets_num'] == "one"))  
{
evlfooter1();
}
  if (($options['evl_widgets_num'] == "two"))  
{
evlfooter1();
evlfooter2();
}
  if (($options['evl_widgets_num'] == "three"))  
{
evlfooter1();
evlfooter2();
evlfooter3();
}
  if (($options['evl_widgets_num'] == "four"))  
{
evlfooter1();
evlfooter2();
evlfooter3();
evlfooter4();
} else {}

function evlwidget_area_active( $index ) {
	global $wp_registered_sidebars;
	
	$widgetarea = wp_get_sidebars_widgets();
	if ( isset($widgetarea[$index]) ) return true;
	
	return false;
}

function evolve_widget_area( $name = false ) {
	if ( !isset($name) ) {
		$widget[] = "widget.php";
	} else {
		$widget[] = "widget-{$name}.php";
	}
	locate_template( $widget, true );
}




function evlwidget_before_title() { ?>

<div style="position:relative;top:-10px;"><div class="widget-title-background"></div><h3 class="widget-title">

<?php }

function evlwidget_after_title() { ?>

</h3></div>

<?php }

function evlwidget_before_widget() { ?>

<div class="widget"><div class="widget-content">

<?php }

function evlwidget_after_widget() { ?>

</div></div>

<?php }


function evlwidget_text($args, $number = 1) {
extract($args);
$options = get_option('evlwidget_text');
$title = $options[$number]['title'];
if ( empty($title) )
$title = '';  }

?>
