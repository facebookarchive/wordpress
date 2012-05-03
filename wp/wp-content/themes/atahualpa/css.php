<?php # error_reporting(E_ALL & ~E_NOTICE);
#include_once (TEMPLATEPATH . '/functions.php'); 
global $templateURI,  $homeURL; 
if ( isset($bfa_ata_preview) OR $bfa_ata['css_external'] == "Inline" OR 
( isset($bfa_ata_debug) AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	echo '<style type="text/css">'; 
} else { 
	header("Content-type: text/css"); 
}
if ( $bfa_ata['css_compress'] == "Yes" AND 
!( $bfa_ata_debug == 1 AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	ob_start("bfa_compress_css");
}

function bfa_compress_css($buffer) {
	
	/* remove comments */
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	
	/* remove tabs, spaces, newlines, etc. */
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
	$buffer = str_replace(array('  ', '   ', '    ', '     '), ' ', $buffer);
	$buffer = str_replace(array(": ", " :"), ':', $buffer);
	$buffer = str_replace(array(" {", "{ "), '{', $buffer);
	$buffer = str_replace(';}','}', $buffer);
	$buffer = str_replace(', ', ',', $buffer);
	$buffer = str_replace('; ', ';', $buffer);
	
	return $buffer;
  
}

?>
/* ------------------------------------------------------------------
---------- BASE LAYOUT ----------------------------------------------
------------------------------------------------------------------ */

body {
	text-align: center;  /* centering the page container, 
							text-align will be reset to left 
							inside the container */
	margin: 0;
	padding: 0;
	<?php echo $bfa_ata['body_style']; ?>
	}

a:link, a:visited, a:active {
	color: #<?php echo $bfa_ata['link_color']; ?>; 
	font-weight: <?php echo $bfa_ata['link_weight']; ?>; 
	text-decoration: <?php echo $bfa_ata['link_default_decoration']; ?>; 
	}
	
a:hover {
	color: #<?php echo $bfa_ata['link_hover_color']; ?>;
	font-weight: <?php echo $bfa_ata['link_weight']; ?>; 
	text-decoration: <?php echo $bfa_ata['link_hover_decoration']; ?>;
	}

ul, ol, dl, p, h1, h2, h3, h4, h5, h6 {
	margin-top: 10px;
	margin-bottom: 10px;
	padding-top: 0;
	padding-bottom: 0; 	
	}

/* remove margins on sub-lists */
ul ul, ul ol, ol ul, ol ol {
	margin-top: 0;
	margin-bottom: 0;
	}
/*
h1 { font-size: 34px; line-height: 1.2; margin: 0.3em 0 10px; }
h2 { font-size: 28px; line-height: 1.3; margin: 1em 0 .2em; }
h3 { font-size: 24px; line-height: 1.3; margin: 1em 0 .2em; }
h4 { font-size: 19px; margin: 1.33em 0 .2em; }
h5 { font-size: 1.3em; margin: 1.67em 0; font-weight: bold; }
h6 { font-size: 1.15em; margin: 1.67em 0; font-weight: bold; }
*/
code, pre {
	font-family: "Courier New", Courier, monospace;
	font-size: 1em;
	}

pre {
	overflow: auto;
	word-wrap: normal;
	padding-bottom: 1.5em;
	overflow-y: hidden;
	width: 99%;
	}

abbr[title], acronym[title] {
	border-bottom: 1px dotted;
	}
	
hr {
	display: block;
	height: 2px;
	border: none;
	margin: 0.5em auto;
	color: #cccccc;
	background-color: #cccccc;
	}

/* use the body's font size in tables, too: */

table {
	font-size: 1em; 
	}	


/* ------------------------------------------------------------------
---------- BREAK LONG STRINGS ---------------------------------------
------------------------------------------------------------------ */

/* break long strings in IE6+ and Safari2+ in posts and comments: */

div.post, ul.commentlist li, ol.commentlist li {
	word-wrap: break-word; 
	}

/* reset "break-word" for pre & wp-syntax: */

pre, .wp_syntax {
	word-wrap: normal; 
	}

	
/* ------------------------------------------------------------------
---------- WRAPPER, CONTAINER & LAYOUT ------------------------------
------------------------------------------------------------------ */
	
<?php if  ( $bfa_ata['layout_style_leftright_padding'] == "" ) { 
	$bfa_ata['layout_style_leftright_padding'] = "0"; }
	if ( $bfa_ata['layout_style_leftright_padding'] != "0" ) { 
	$bfa_ata['layout_min'] = $bfa_ata['layout_min_width'] + ( $bfa_ata['layout_style_leftright_padding'] * 2 );
	$bfa_ata['layout_max'] = $bfa_ata['layout_max_width'] + ( $bfa_ata['layout_style_leftright_padding'] * 2 );	
	} else {
	$bfa_ata['layout_min'] = $bfa_ata['layout_min_width'];
	$bfa_ata['layout_max'] = $bfa_ata['layout_max_width'];
	}
	?>

/*-------------------- WRAPPER for MIN / MAX width --------*/

div#wrapper {
	text-align: center;  
	margin-left: auto;
	margin-right: auto;
	display: block;
	width: <?php echo $bfa_ata['layout_width']; ?>;
	<?php // if layout is fluid, set min/max width, if defined:
	if(stristr($bfa_ata['layout_width'], 'px') === FALSE) { 
	echo ($bfa_ata['layout_min_width'] == "" ? "" : "min-width: " . $bfa_ata['layout_min'] . "px;\n"); 
	echo ($bfa_ata['layout_max_width'] == "" ? "" : "max-width: " . $bfa_ata['layout_max'] . "px;\n");	
	} ?>
	}

<?php // min/max width for IE6:
if(stristr($bfa_ata['layout_width'], 'px') === FALSE && ($bfa_ata['layout_min'] != "" OR $bfa_ata['layout_max'] != "" )) { ?>
* html div#wrapper {
<!--
	width:expression<?php if($bfa_ata['layout_max_width'] != "") { ?>(((document.compatMode && 
	document.compatMode=='CSS1Compat') ? 
	document.documentElement.clientWidth : 
	document.body.clientWidth) 
	> <?php echo $bfa_ata['layout_max'] +1; ?> ? "<?php echo $bfa_ata['layout_max']; ?>px" : 
	<?php } if($bfa_ata['layout_min_width'] == "") { ?>"<?php echo $bfa_ata['layout_width']; ?>"); -->}<?php } else { ?>
	(((document.compatMode && 
	document.compatMode=='CSS1Compat') ? 
	document.documentElement.clientWidth : 
	document.body.clientWidth) 
	< <?php echo $bfa_ata['layout_min'] + 1; ?> ? "<?php echo $bfa_ata['layout_min']; ?>px" : 
	"<?php echo $bfa_ata['layout_width']; ?>")); 
-->
	}
<?php } } ?>

/*-------------------- CONTAINER for VISUAL styles --------*/

div#container {
	<?php echo $bfa_ata['layout_style']; ?>
	<?php if ( $bfa_ata['layout_style_leftright_padding'] != "0" ) { ?>
	padding-left: <?php echo $bfa_ata['layout_style_leftright_padding']; ?>px;
	padding-right: <?php echo $bfa_ata['layout_style_leftright_padding']; ?>px;
	<?php } ?>
	width: auto;
	margin-left: auto;
	margin-right: auto;
	text-align: left; /* resetting the "text-align: center" of "wrapper" */
	display: block;
	}

/*-------------------- LAYOUT to keep it all together -----*/
	
table#layout {
	font-size: 100%;
	width: 100%;
	table-layout: fixed;
	}
	
.colone {width: <?php echo $bfa_ata['left_sidebar_width']; ?>px;}
.colone-inner {width: <?php echo $bfa_ata['left_sidebar2_width']; ?>px;}
.coltwo { width: 100% }
.colthree-inner {width: <?php echo $bfa_ata['right_sidebar2_width']; ?>px;}
.colthree {width: <?php echo $bfa_ata['right_sidebar_width']; ?>px;}

/* ------------------------------------------------------------------
---------- HEADER ---------------------------------------------------
------------------------------------------------------------------ */


/*-------------------- HEADER CONTAINER -------------------*/

td#header {
	width: auto;
	padding: 0;
	}


/*-------------------- LOGO AREA --------------------------*/

table#logoarea, 
table#logoarea tr, 
table#logoarea td {
	margin: 0;
	padding: 0;
	background: none;
	border: 0;
	}

table#logoarea {
	width: 100%;
	border-spacing: 0px;
	<?php bfa_incl('logoarea_style') ?>
	}
	
/*-------------------- LOGO -------------------------------*/

img.logo {
	display: block;
	<?php bfa_incl('logo_style') ?>
	}

td.logoarea-logo {
	width: 1%;
	}

	
/*-------------------- BLOG TITLE -------------------------*/

h1.blogtitle,
h2.blogtitle {
    display: block;
	<?php bfa_incl('blog_title_style') ?>
	font-smooth: always;
	}
	
h1.blogtitle a:link, 
h1.blogtitle a:visited, 
h1.blogtitle a:active,
h2.blogtitle a:link, 
h2.blogtitle a:visited, 
h2.blogtitle a:active {
    text-decoration: none;
	color: #<?php echo $bfa_ata['blog_title_color']; ?>;
	font-weight: <?php echo $bfa_ata['blog_title_weight']; ?>;
	font-smooth: always;
	}
	
h1.blogtitle a:hover,
h2.blogtitle a:hover {
    text-decoration: none;
	color: #<?php echo $bfa_ata['blog_title_color_hover']; ?>;
	font-weight: <?php echo $bfa_ata['blog_title_weight']; ?>;
	}

/*-------------------- BLOG TAGLINE -----------------------*/

p.tagline { 
	<?php bfa_incl('blog_tagline_style') ?>
	}

td.feed-icons {
	white-space: nowrap; 
	}

div.rss-box {
	height: 1%; 
	display: block; 
	padding: 10px 0 10px 10px; 
	margin: 0;
	width: <?php echo $bfa_ata['rss_box_width']; ?>px;
	}
	
/*-------------------- COMMENTS FEED ICON -----------------*/

a.comments-icon {
	height: 22px;
	line-height: 22px;
	margin: 0 5px 0 5px;
	padding-left: 22px;
	display: block;
	text-decoration: none;
	float: right;
	white-space: nowrap;
	}

a.comments-icon:link,
a.comments-icon:active,
a.comments-icon:visited {
	background: transparent url(<?php echo $templateURI; ?>/images/comment-gray.png) no-repeat scroll center left;
}

a.comments-icon:hover {
	background: transparent url(<?php echo $templateURI; ?>/images/comment.png) no-repeat scroll center left;
}


/*-------------------- POSTS FEED ICON --------------------*/

a.posts-icon {
	height: 22px;
	line-height: 22px;
	margin: 0 5px 0 0;
	padding-left: 20px;
	display: block;
	text-decoration: none;
	float: right;
	white-space: nowrap;
	}

a.posts-icon:link,
a.posts-icon:active,
a.posts-icon:visited {
	background: transparent url(<?php echo $templateURI; ?>/images/rss-gray.png) no-repeat scroll center left;
}

a.posts-icon:hover {
	background: transparent url(<?php echo $templateURI; ?>/images/rss.png) no-repeat scroll center left;
}

/*-------------------- EMAIL SUBSCRIBE ICON ---------------*/

a.email-icon {
	height: 22px;
	line-height: 22px;
	margin: 0 5px 0 5px;
	padding-left: 24px;
	display: block;
	text-decoration: none;
	float: right;
	white-space: nowrap;
	}
	
a.email-icon:link,
a.email-icon:active,
a.email-icon:visited {
	background: transparent url(<?php echo $templateURI; ?>/images/email-gray.png) no-repeat scroll center left;
}

a.email-icon:hover {
	background: transparent url(<?php echo $templateURI; ?>/images/email.png) no-repeat scroll center left;
}
	
/*-------------------- SEARCH BOX IN HEADER ---------------*/	

td.search-box {
	height: 1%;
	}
	
div.searchbox {
	height: 35px;
	<?php bfa_incl('searchbox_style') ?>
	}

div.searchbox-form {
	margin: 5px 10px 5px 10px;
	}



/*-------------------- HORIZONTAL BARS --------------------*/

div.horbar1, 
div.horbar2 { 
	font-size: 1px;
	clear: both; 
	display: block;
	position: relative;
	padding: 0; 
	margin: 0;
	width: 100%; 
	}

div.horbar1 {
	<?php bfa_incl('horbar1') ?>
	}
	
div.horbar2 { 
	<?php bfa_incl('horbar2') ?>
	}	

<?php if (strpos($bfa_ata['configure_header'],'%image')!==false) { ?>
div.header-image-container {
	position: relative; 
	margin: 0; 
	padding: 0; 
	height: <?php echo $bfa_ata['headerimage_height']; ?>px; 
	}
<?php } ?>
	
<?php if ( $bfa_ata['overlay_blog_title'] == "Yes" OR $bfa_ata['overlay_blog_tagline'] == "Yes" ) { ?>
div.titleoverlay {
	z-index: 4;
	position: relative;
	float: left;
	width: auto;
	<?php echo $bfa_ata['overlay_box_style']; ?>
	}
<?php } ?>

<?php if ( $bfa_ata['header_opacity_left'] != 0 AND $bfa_ata['header_opacity_left'] != '' ) { ?>
/*-------------------- OPACITY LEFT -----------------------*/

div.opacityleft {
	position: absolute; 
	z-index: 2; 
	top: 0; 
	left: 0; 
	background-color: #<?php echo $bfa_ata['header_opacity_left_color']; ?>; 
	height: <?php echo $bfa_ata['headerimage_height']; ?>px;
	width: <?php echo $bfa_ata['header_opacity_left_width']; ?>px; 
	filter: alpha(opacity=<?php echo $bfa_ata['header_opacity_left']; ?>);
	opacity:.<?php echo $bfa_ata['header_opacity_left']; ?>;
	}
<?php } ?>

<?php if ( $bfa_ata['header_opacity_right'] != 0 AND $bfa_ata['header_opacity_right'] != '' ) { ?>
/*-------------------- OPACITY RIGHT ----------------------*/	

div.opacityright {
	position: absolute; 
	z-index: 2; 
	top: 0; 
	right: 0; 
	background-color: #<?php echo $bfa_ata['header_opacity_right_color']; ?>; 
	height: <?php echo $bfa_ata['headerimage_height']; ?>px;
	width: <?php echo $bfa_ata['header_opacity_right_width']; ?>px; 
	filter: alpha(opacity=<?php echo $bfa_ata['header_opacity_right']; ?>);
	opacity:.<?php echo $bfa_ata['header_opacity_right']; ?>;
	}
<?php } ?>


<?php if ($bfa_ata['header_image_clickable'] == "Yes") { ?>
/*-------------------- CLICKABLE HEADER IMAGE -------------*/

div.clickable {
	position:absolute; 
	top:0; 
	left:0; 
	z-index:3; 
	margin: 0; 
	padding: 0; 
	width: 100%;
	height: <?php echo $bfa_ata['headerimage_height']; ?>px; 
	}
<?php } ?>
		
a.divclick:link, 
a.divclick:visited, 
a.divclick:active, 
a.divclick:hover {
	width: 100%; 
	height: 100%; 
	display: block;
	text-decoration: none;
	}

		
/* ------------------------------------------------------------------
---------- LEFT SIDEBARS ---------------------------------------------
------------------------------------------------------------------ */

td#left {
	vertical-align: top;
	<?php bfa_incl('left_sidebar_style') ?>
	}

td#left-inner {
	vertical-align: top;
	<?php bfa_incl('left_sidebar2_style') ?>
	}
	
/* ------------------------------------------------------------------
---------- RIGHT SIDEBARS --------------------------------------------
------------------------------------------------------------------ */

td#right {
	vertical-align: top;
	<?php bfa_incl('right_sidebar_style') ?>
	}

td#right-inner {
	vertical-align: top;
	<?php bfa_incl('right_sidebar2_style') ?>
	}
	
/* ------------------------------------------------------------------
---------- CENTER COLUMN --------------------------------------------
------------------------------------------------------------------ */

td#middle {
	vertical-align: top;
	width: 100%;
	<?php bfa_incl('center_column_style') ?>
	}

	
/* ------------------------------------------------------------------
---------- FOOTER ---------------------------------------------------
------------------------------------------------------------------ */

td#footer {
	width: auto;
	<?php bfa_incl('footer_style') ?>
	}

td#footer a:link, td#footer a:visited, td#footer a:active {
	<?php bfa_incl('footer_style_links') ?>
	}

td#footer a:hover {
	<?php bfa_incl('footer_style_links_hover') ?>
	}
	
	
/* ------------------------------------------------------------------
---------- WIDGETS --------------------------------------------------
------------------------------------------------------------------ */

div.widget {
	display: block;
	width: auto;  /* without this IE will stretch too-wide select 
					menus but not the other widgets. With 100% IE
					will remove sidebar borders if select menu is
					too wide */
	<?php bfa_incl('widget_container') ?>
	}

div.widget-title {
	display: block;
	width: auto;
	<?php bfa_incl('widget_title_box') ?>
	}

div.widget-title h3,
td#left h3.tw-widgettitle,
td#right h3.tw-widgettitle,
td#left ul.tw-nav-list,
td#right ul.tw-nav-list {
	padding:0;
	margin:0;
	<?php bfa_incl('widget_title') ?>
	}

/* Since 3.4 "div-widget-content" is gone for better plugin compatibility. 
Instead we'll try to mimick the feature by putting the styles on the following 
containers: */
div.widget ul,
div.textwidget {
	display: block;
	width: auto;
	<?php bfa_incl('widget_content') ?>
	}

	
/* ------------------------------------------------------------------
---------- Select MENUS INSIDE OF WIDGETS -------------------------
------------------------------------------------------------------ */

/* if a select menu is too wide to fit into the sidebar (because one 
 or several of its option titles are too long) then it will be cut off
 in IE 6 & 7 */

div.widget select { 
	width: 98%; 		/* auto won't work in Safari */
	margin-top: 5px;
	<?php if ( $bfa_ata['select_font_size'] != "Default" ) { 
	echo "font-size: " . $bfa_ata['select_font_size'] . ";\n"; } ?> 
}	


/* ------------------------------------------------------------------
---------- LISTS INSIDE OF WIDGETS ----------------------------------
------------------------------------------------------------------ */

div.widget ul {
	list-style-type: none;
	margin: 0; 
	padding: 0;
	width: auto;
	}

/*------------- base styling for all widgets -----------*/
	
div.widget ul li {
	display: block;
	margin: 2px 0 2px <?php echo $bfa_ata['widget_lists']['li-margin-left']; ?>px;
	padding: 0 0 0 <?php echo $bfa_ata['widget_lists']['link-padding-left']; ?>px; 
	border-left: solid <?php echo $bfa_ata['widget_lists']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists']['link-border-left-color']; ?>;
	}

div.widget ul li:hover,
div.widget ul li.sfhover {
display: block;
width: auto;
	border-left: solid <?php echo $bfa_ata['widget_lists']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists']['link-border-left-hover-color']; ?>; 
	}

div.widget ul li ul li {
	margin: 2px 0 2px <?php echo $bfa_ata['widget_lists2']['li-margin-left']; ?>px;  
	padding: 0 0 0 <?php echo $bfa_ata['widget_lists2']['link-padding-left']; ?>px; 
	border-left: solid <?php echo $bfa_ata['widget_lists2']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists2']['link-border-left-color']; ?>; 
	}

div.widget ul li ul li:hover,
div.widget ul li ul li.sfhover {
	border-left: solid <?php echo $bfa_ata['widget_lists2']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists2']['link-border-left-hover-color']; ?>; 
	}

div.widget ul li ul li ul li {
	margin: 2px 0 2px <?php echo $bfa_ata['widget_lists3']['li-margin-left']; ?>px; 
	padding: 0 0 0 <?php echo $bfa_ata['widget_lists3']['link-padding-left']; ?>px; 	
	border-left: solid <?php echo $bfa_ata['widget_lists3']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists3']['link-border-left-color']; ?>; 
	}

div.widget ul li ul li ul li:hover,
div.widget ul li ul li ul li.sfhover {
	border-left: solid <?php echo $bfa_ata['widget_lists3']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists3']['link-border-left-hover-color']; ?>; 
	}
	
div.widget a:link,
div.widget a:visited,
div.widget a:active,
div.widget td a:link,
div.widget td a:visited,
div.widget td a:active,
div.widget ul li a:link, 
div.widget ul li a:visited, 
div.widget ul li a:active {
	text-decoration: none; 
	font-weight: normal; 
	color: #<?php echo $bfa_ata['widget_lists']['link-color']; ?>; 
	font-weight: <?php echo $bfa_ata['widget_lists']['link-weight']; ?>; 
	}

div.widget ul li ul li a:link, 
div.widget ul li ul li a:visited, 
div.widget ul li ul li a:active {
	color: #<?php echo $bfa_ata['widget_lists2']['link-color']; ?>; 
	font-weight: <?php echo $bfa_ata['widget_lists2']['link-weight']; ?>; 
	}

div.widget ul li ul li ul li a:link, 
div.widget ul li ul li ul li a:visited, 
div.widget ul li ul li ul li a:active {
	color: #<?php echo $bfa_ata['widget_lists3']['link-color']; ?>; 
	font-weight: <?php echo $bfa_ata['widget_lists3']['link-weight']; ?>; 
	}

	
div.widget a:hover,
div.widget ul li a:hover {
	color: #<?php echo $bfa_ata['widget_lists']['link-hover-color']; ?>; 
	}

div.widget ul li ul li a:hover {
	color: #<?php echo $bfa_ata['widget_lists2']['link-hover-color']; ?>; 
	}

div.widget ul li ul li ul li a:hover {
	color: #<?php echo $bfa_ata['widget_lists3']['link-hover-color']; ?>; 
	}
	
div.widget ul li a:link, 
div.widget ul li a:visited, 
div.widget ul li a:active,
div.widget ul li a:hover {
	display: inline;
	}

* html div.widget ul li a:link, 
* html div.widget ul li a:visited, 
* html div.widget ul li a:active,
* html div.widget ul li a:hover {
	height: 1%;   /* IE6 needs this */
	}
	
/*------------- styling for categories and pages widgets -----------*/

/* Because they can have sub items, the categories and the pages 
widgets get the left border and padding on the <A> instead of the <LI>.
Otherwise, sub items would have two left borders - their own left border 
and the left border of their parent (since the parent contains the sub item). 
You may actually like that, it looks interesting. To try it out, comment all the next 
rules up to "BFA SUBSCRIBE WIDGET" */
 
/* First, remove the left border and padding from the <LI>. The margin stays on the <LI>'s 
because if the <A>'s of the cateories widget were set to display:inline (default setting) 
then margin would work there */
div.widget_nav_menu ul li, 
div.widget_pages ul li, 
div.widget_categories ul li {
	border-left: 0 !important;
	padding: 0 !important;
}

/* Then, add left border and padding to the <A> */
div.widget_nav_menu ul li a:link, 
div.widget_nav_menu ul li a:visited, 
div.widget_nav_menu ul li a:active,
div.widget_pages ul li a:link, 
div.widget_pages ul li a:visited, 
div.widget_pages ul li a:active,
div.widget_categories ul li a:link,
div.widget_categories ul li a:visited, 
div.widget_categories ul li a:active {
	padding: 0 0 0 <?php echo $bfa_ata['widget_lists']['link-padding-left']; ?>px; 
	border-left: solid <?php echo $bfa_ata['widget_lists']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists']['link-border-left-color']; ?>;
	}

div.widget_nav_menu ul li a:hover,
div.widget_pages ul li a:hover,
div.widget_categories ul li a:hover {
	border-left: solid <?php echo $bfa_ata['widget_lists']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists']['link-border-left-hover-color']; ?>; 
}

div.widget_nav_menu ul li ul li a:link, 
div.widget_nav_menu ul li ul li a:visited, 
div.widget_nav_menu ul li ul li a:active,
div.widget_pages ul li ul li a:link, 
div.widget_pages ul li ul li a:visited, 
div.widget_pages ul li ul li a:active,
div.widget_categories ul li ul li a:link,
div.widget_categories ul li ul li a:visited, 
div.widget_categories ul li ul li a:active {
	padding: 0 0 0 <?php echo $bfa_ata['widget_lists2']['link-padding-left']; ?>px; 
	border-left: solid <?php echo $bfa_ata['widget_lists2']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists2']['link-border-left-color']; ?>;
	}

div.widget_nav_menu ul li ul li a:hover,
div.widget_pages ul li ul li a:hover,
div.widget_categories ul li ul li a:hover {
	border-left: solid <?php echo $bfa_ata['widget_lists2']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists2']['link-border-left-hover-color']; ?>; 
}

div.widget_nav_menu ul li ul li ul li a:link, 
div.widget_nav_menu ul li ul li ul li a:visited, 
div.widget_nav_menu ul li ul li ul li a:active,
div.widget_pages ul li ul li ul li a:link, 
div.widget_pages ul li ul li ul li a:visited, 
div.widget_pages ul li ul li ul li a:active,
div.widget_categories ul li ul li ul li a:link,
div.widget_categories ul li ul li ul li a:visited, 
div.widget_categories ul li ul li ul li a:active {
	padding: 0 0 0 <?php echo $bfa_ata['widget_lists3']['link-padding-left']; ?>px; 
	border-left: solid <?php echo $bfa_ata['widget_lists3']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists3']['link-border-left-color']; ?>;
	}

div.widget_nav_menu ul li ul li ul li a:hover,
div.widget_pages ul li ul li ul li a:hover,
div.widget_categories ul li ul li ul li a:hover {
	border-left: solid <?php echo $bfa_ata['widget_lists3']['link-border-left-width']; ?>px #<?php echo $bfa_ata['widget_lists3']['link-border-left-hover-color']; ?>; 
}

/* The pages widget gets "block" because it usually has only 
one link per <LI> and no text */
div.widget_nav_menu ul li a:link,
div.widget_nav_menu ul li a:active,
div.widget_nav_menu ul li a:visited,
div.widget_nav_menu ul li a:hover,
div.widget_pages ul li a:link,
div.widget_pages ul li a:active,
div.widget_pages ul li a:visited,
div.widget_pages ul li a:hover {
	display: block !important;
}

/* The category widget gets "inline" per default or otherwise the 
post count would wrap into the next line. If no post count is displayed,
"block" can be chosen at Theme Options -> Style Widgets -> Category Widget Display Type. 
With "block", links that don't fit into one line will align properly (as a block) 
on the left side. */
div.widget_categories ul li a:link,
div.widget_categories ul li a:active,
div.widget_categories ul li a:visited,
div.widget_categories ul li a:hover {
	display: <?php echo $bfa_ata['category_widget_display_type']; ?> !important;
}



/* ------------------------------------------------------------------
---------- BFA SUBSCRIBE WIDGET -------------------------------------
------------------------------------------------------------------ */

table.subscribe {
	width: 100%;
	}
	
table.subscribe td.email-text {
	padding: 0 0 5px 0;
	vertical-align: top;
	}

table.subscribe td.email-field {
	padding: 0;
	width: 100%;
	}
	
table.subscribe td.email-button {
	padding: 0 0 0 5px;
	}
	
table.subscribe td.post-text {
	padding: 7px 0 0 0;
	vertical-align: top;
	}
	
table.subscribe td.comment-text {
	padding: 7px 0 0 0;
	vertical-align: top;
	}
	
	
/* ------------------------------------------------------------------
---------- POSTS ----------------------------------------------------
------------------------------------------------------------------ */

/*-------------------- POST CONTAINER ---------------------*/

div.post, div.page {
	display: block;
	<?php bfa_incl('post_container_style') ?>
	}

/* additonal styles for sticky posts */

div.sticky {
	<?php bfa_incl('post_container_sticky_style') ?>
	}

/*-------------------- POST KICKER ------------------------*/

div.post-kicker {
	<?php bfa_incl('post_kicker_style') ?>
	}

div.post-kicker a:link, 
div.post-kicker a:visited, 
div.post-kicker a:active {
	<?php bfa_incl('post_kicker_style_links') ?>
	}

div.post-kicker a:hover {
	<?php bfa_incl('post_kicker_style_links_hover') ?>
	}

/*-------------------- POST HEADLINE ----------------------*/

div.post-headline {
	<?php bfa_incl('post_headline_style') ?>
	}

div.post-headline h1,
div.post-headline h2 {
    margin: 0;
    padding: 0;
	<?php bfa_incl('post_headline_style_text') ?>
	}

div.post-headline h2 a:link, 
div.post-headline h2 a:visited, 
div.post-headline h2 a:active,
div.post-headline h1 a:link, 
div.post-headline h1 a:visited, 
div.post-headline h1 a:active {
	<?php bfa_incl('post_headline_style_links') ?>
	}

div.post-headline h2 a:hover,
div.post-headline h1 a:hover {
	<?php bfa_incl('post_headline_style_links_hover') ?>
	}


/*-------------------- POST BYLINE ------------------------*/

div.post-byline {
	<?php bfa_incl('post_byline_style') ?>
	}

div.post-byline a:link, 
div.post-byline a:visited, 
div.post-byline a:active {
	<?php bfa_incl('post_byline_style_links') ?>
	}

div.post-byline a:hover {
	<?php bfa_incl('post_byline_style_links_hover') ?>
	}


/*-------------------- POST BODY COPY ---------------------*/
	
div.post-bodycopy {
	<?php bfa_incl('post_bodycopy_style') ?>
	}
	
div.post-bodycopy p {
	margin: 1em 0;
	padding: 0;
	display: block;
	/* The rule below would create hor. scrollbars in Firefox, 
	which would be better than overflowing long strings, but the
	downside is that text won't float around images anymore. 
	Uncomment this if you don't float images anyway */
	/* overflow: auto; */
	}

	
/*-------------------- POST PAGINATION --------------------*/

div.post-pagination {
	/*border: solid 1px brown;*/
	}

	
/*-------------------- POST FOOTER ------------------------*/
	
div.post-footer {
	clear:both; 
	display: block;	
	<?php bfa_incl('post_footer_style') ?>
	}

div.post-footer a:link, 
div.post-footer a:visited, 
div.post-footer a:active {
	<?php bfa_incl('post_footer_style_links') ?>
	}	

div.post-footer a:hover {
	<?php bfa_incl('post_footer_style_links_hover') ?>
	}

/*-------------------- ICONS in KICKER, BYLINE & FOOTER ---*/

div.post-kicker img, 
div.post-byline img, 
div.post-footer img {
	border: 0;
	padding: 0;
	margin: 0 0 -1px 0;
	background: none;
	}
	
span.post-ratings {
	display:inline-block; 	/* postratings set to "span" by the 
							theme, instead of default "div", to 
							make them display inline. Adding 
							inline-block and nowrap to avoid 
							line wrapping of single voting stars. */
	width: auto;
	white-space: nowrap;
	}


/* ------------------------------------------------------------------
---------- PAGE NAVIGATION NEXT/PREVIOUS ----------------------------
------------------------------------------------------------------ */

div.navigation-top {
	<?php bfa_incl('next_prev_style_top') ?>
	}

div.navigation-middle {
	<?php bfa_incl('next_prev_style_middle') ?>
	}
	
div.navigation-bottom {
	<?php bfa_incl('next_prev_style_bottom') ?>
	}

div.navigation-comments-above {
	<?php bfa_incl('next_prev_style_comments_above') ?>
	}
	
div.navigation-comments-below {
	<?php bfa_incl('next_prev_style_comments_below') ?>
	}
	
div.older {
	float: left; 
	width: 48%; 
	text-align: left; 
	margin:0; 
	padding:0;
	}
	
div.newer {
	float:right; 
	width: 48%; 
	text-align: right; 
	margin:0; 
	padding:0; 
	}	

div.older-home {
	float: left; 
	width: 44%; 
	text-align: left; 
	margin:0; 
	padding:0;
	}

div.newer-home {
	float:right; 
	width: 44%; 
	text-align: right; 
	margin:0; 
	padding:0; 
	}	

div.home {
	float: left; 
	width: 8%; 
	text-align: center;  
	margin:0; 
	padding:0;
	}

	
/* ------------------------------------------------------------------
---------- FORMS ----------------------------------------------------
------------------------------------------------------------------ */

form, .feedburner-email-form {
	margin: 0; 
	padding: 0; 
	}

fieldset {
	border: 1px solid #cccccc; 
	width: auto; 
	padding: 0.35em 0.625em 0.75em;
	display: block; 
	}
	
legend { 
	color: #000000; 
	background: #f4f4f4; 
	border: 1px solid #cccccc; 
	padding: 2px 6px; 
	margin-bottom: 15px; 
	}
	
form p {
	margin: 5px 0 0 0; 
	padding: 0; 
	}

div.xhtml-tags p {
margin: 0;
}
	
label {
	margin-right: 0.5em; 
	font-family: arial;
	cursor: pointer; 
	}

/* input.TextField for WP-Email
input.textbox for WPG2 */
input.text, 
input.textbox, 
input.password, 
input.file,
input.TextField, 
textarea {
	padding: 3px;
	<?php echo $bfa_ata['form_input_field_style'] . "\n"; ?>
	<?php if ($bfa_ata['form_input_field_background'] != "") {  
	echo "background: url(" . $templateURI . "/images/" . 
	$bfa_ata['form_input_field_background'] . ") top left no-repeat;"; } ?>
	}

textarea {
	width: 96%; 
	}


input.inputblur {
	color: #777777;
	width: 95%;
	}

input.inputfocus {
	color: #000000;
	width: 95%;
	}	
	
<?php if ($bfa_ata['highlight_forms'] == "Yes") { ?>
input.highlight, textarea.highlight {
	<?php bfa_incl('highlight_forms_style') ?>
	}
<?php } ?>

/* .Button for WP-Email, input[type=submit] for comment submit button since 3.6.1 */
.button, .Button, input[type=submit] {
	padding: 0 2px;
	height: 24px;
	line-height: 16px;
	<?php bfa_incl('button_style') ?>
	}

/* changed from .buttonhover to input.buttonhover in 3.6.1 */
input.buttonhover {
	padding: 0 2px;
	cursor: pointer;
	<?php bfa_incl('button_style_hover') ?>
	}

/* comment submit button */

/* IE button width/padding fix */

form#commentform input#submit {
    padding: 0 .25em;
    /* Since 3.6: Using comment_form() */
	/* width: 0; */
    overflow:visible;
}

form#commentform input#submit[class] { /*ie ignores [class]*/
    width: auto;
}

form#commentform input#submit	{
	<?php bfa_incl('submit_button_style') ?>
	}
	
/* ------------------------------------------------------------------
---------- SEARCH FORM ----------------------------------------------
------------------------------------------------------------------ */

table.searchform {
	width: 100%;
	}

table.searchform td.searchfield {
	padding: 0;
	width: 100%;
	}
	
table.searchform td.searchbutton {
	padding: 0 0 0 5px;
	}

table.searchform td.searchbutton input{
	padding: 0 0 0 5px;
	}
	
/* ------------------------------------------------------------------
---------- BLOCKQUOTES ----------------------------------------------
------------------------------------------------------------------ */

blockquote {
	height: 1%;
	display: block;
	clear: both;
	<?php bfa_incl('blockquote_style') ?>
	}
	
blockquote blockquote {
	height: 1%;
	display: block;
	clear: both;
	<?php bfa_incl('blockquote_style_2nd_level') ?>
	}


/* ------------------------------------------------------------------
---------- TABLES & CALENDAR ----------------------------------------
------------------------------------------------------------------ */

/*-------------------- TABLES IN POSTS --------------------*/

div.post table {
	<?php bfa_incl('table') ?>
	}
	
div.post table caption {
	width: auto;
	margin: 0 auto;
	<?php bfa_incl('table_caption') ?>
	}
	
div.post table th {
	<?php bfa_incl('table_th') ?>
	}
	
div.post table td {
	<?php bfa_incl('table_td') ?>
	}

div.post table tfoot td {
	<?php bfa_incl('table_tfoot_td') ?>
	}
	
div.post table tr.alt td {
	<?php bfa_incl('table_zebra_td') ?>
	}

div.post table tr.over td {
	<?php bfa_incl('table_hover_td') ?>
	}

/*-------------------- CALENDAR WIDGET --------------------*/

#calendar_wrap {
	padding: 0;
	border: none;
	}
	
table#wp-calendar {
	width: 100%; 
	font-size:90%;
	border-collapse: collapse;
	background-color: #ffffff;
	margin: 0 auto;
	}

table#wp-calendar caption {
	width: auto;
	background: #eeeeee;
	border: none;;
	padding: 3px;
	margin: 0 auto;
	font-size: 1em;
	}

table#wp-calendar th {
	border: solid 1px #eeeeee;
	background-color: #999999;
	color: #ffffff;
	font-weight: bold;
	padding: 2px;
	text-align: center;
	}
	
table#wp-calendar td {
	padding: 0;
	line-height: 18px;
	background-color: #ffffff;
	border: 1px solid #dddddd;
	text-align: center;
	}

table#wp-calendar tfoot td {
	border: solid 1px #eeeeee;
	background-color: #eeeeee;
	}
	
table#wp-calendar td a {
	display: block;
	background-color: #eeeeee;
	width: 100%;
	height: 100%;
	padding: 0;
	}


	


	
/* ------------------------------------------------------------------
---------- COMMENTS -------------------------------------------------
------------------------------------------------------------------ */



/* whole respond area */
div#respond {
	<?php bfa_incl('comment_form_style') ?>
	}

p.thesetags {
	margin: 10px 0;
	}

/* Since 3.6.1: added h3#reply-title. class reply cannot be added to new comment_form() without hacks */
h3.reply, h3#reply-title {
	margin: 0;
	padding: 0 0 10px 0;
	}
	
ol.commentlist {
	margin: 15px 0 25px 0;
	list-style-type: none;
	padding: 0;
	display:block;
	border-top: <?php echo $bfa_ata['comment_border']; ?>;
	}
	
ol.commentlist li {
	padding: 15px 10px;
	display: block;
	height: 1%; /* for IE6 */
	margin: 0;
	background-color: #<?php echo $bfa_ata['comment_background_color']; ?>;
	border-bottom: <?php echo $bfa_ata['comment_border']; ?>;
	}

ol.commentlist li.alt {
	display: block;
	height: 1%; /* for IE6 */
	background-color: #<?php echo $bfa_ata['comment_alt_background_color']; ?>;
	border-bottom: <?php echo $bfa_ata['comment_border']; ?>;
	}

ol.commentlist li.authorcomment {
	display: block;
	height: 1%; /* for IE6 */
	background-color: #<?php echo $bfa_ata['author_highlight_color']; ?>;
	}

ol.commentlist span.authorname {
	font-weight: bold;
	font-size: <?php echo $bfa_ata['comment_author_size']; ?>;
	}

ol.commentlist span.commentdate {
	color: #666666;
	font-size: 90%;
	margin-bottom: 5px;
	display: block;
	}

ol.commentlist span.editcomment {
	display: block;
	}
	
ol.commentlist li p {
	margin: 2px 0 5px 0;
	}

div.comment-number {
	float: right; 
	font-size: 2em; 
	line-height: 2em; 
	font-family: georgia, serif; 
	font-weight: bold; 
	color: #ddd; 
	margin: -10px 0 0 0; 
	position: relative; 
	height: 1%
	}

div.comment-number a:link, 
div.comment-number a:visited, 
div.comment-number a:active {
	color: #ccc;
	}

textarea#comment {
	width: 98%; 
	margin: 10px 0; 
	display: block;
	}



/* ------------------------------------------------------------------
---------- COMMENTS WP 2.7 ------------------------------------------
------------------------------------------------------------------ */

ul.commentlist {
	margin: 15px 0 15px 0;
	list-style-type: none;
	padding: 0;
	display:block;
	border-top: <?php echo $bfa_ata['comment_border']; ?>;
	}

ul.commentlist ul {
	margin: 0;
	border: none;
	list-style-type: none;
	padding: 0;
	}

ul.commentlist li {
	padding: 0;
    margin: 0;
	display: block;
	clear: both;
	height: 1%; /* for IE */
}

/* indent children */
ul.commentlist ul.children li {
    margin-left: 30px;
}

/* padding and bottom margin for all commment boxes */
ul.commentlist div.comment-container {
	padding: 10px;
	margin: 0;
}

/* round corners for all children comment boxes */
ul.children div.comment-container {
	background-color: transparent;
	border: dotted 1px #ccc;
	padding: 10px;
	margin: 0 10px 8px 0;
   	border-radius: 5px;
	}

ul.children div.bypostauthor {
	/*margin: 10px 0 0 30px;*/
	/* more  ... */
	}
	
ul.commentlist li.thread-even {
	background-color: #<?php echo $bfa_ata['comment_background_color']; ?>;
	border-bottom: <?php echo $bfa_ata['comment_border']; ?>;
	}

ul.commentlist li.thread-odd {
	background-color: #<?php echo $bfa_ata['comment_alt_background_color']; ?>;
	border-bottom: <?php echo $bfa_ata['comment_border']; ?>;
	}

<?php if ($bfa_ata['author_highlight'] == "Yes") { ?>
ul.commentlist div.bypostauthor {
	background-color: #<?php echo $bfa_ata['author_highlight_color']; ?>;
	}
<?php } ?>
	
<?php if ($bfa_ata['author_highlight'] == "Yes") { ?>
ul.children div.bypostauthor {
	border: dotted 1px #<?php echo $bfa_ata['author_highlight_border_color']; ?>;
	}
<?php } ?>
	
ul.commentlist span.authorname {
	font-size: <?php echo $bfa_ata['comment_author_size']; ?>;
	}

div.comment-meta a:link, 
div.comment-meta a:visited, 
div.comment-meta a:active, 
div.comment-meta a:hover {
	font-weight: normal;
	}

div#cancel-comment-reply {
	margin: -5px 0 10px 0;
	}

div.comment-number {
	float: right; 
	font-size: 2em; 
	line-height: 2em; 
	font-family: georgia, serif; 
	font-weight: bold; 
	color: #ddd; 
	margin: -10px 0 0 0; 
	position: relative; 
	height: 1%
	}

div.comment-number a:link, 
div.comment-number a:visited, 
div.comment-number a:active {
	color: #ccc;
	}

/* paged comments navigation */
.page-numbers {
	padding: 2px 6px;
	border: solid 1px #000000;
	border-radius: 6px;
	}

/* current page number */
span.current {
	background: #ddd;
	}
	
a.prev, a.next {
	border: none;
	}
	
a.page-numbers:link, 
a.page-numbers:visited, 
a.page-numbers:active {
	text-decoration: none;
	color: #<?php echo $bfa_ata['link_color']; ?>; 
	border-color: #<?php echo $bfa_ata['link_color']; ?>;
	}

a.page-numbers:hover {
	text-decoration: none;
	color: #<?php echo $bfa_ata['link_hover_color']; ?>; 
	border-color: #<?php echo $bfa_ata['link_hover_color']; ?>;
	}

	/* "you can use these xhtml tags" initially closed */
div.xhtml-tags {
	display: none;
	}

	
/* ------------------------------------------------------------------
---------- For CommentLuv ----------------------------------------
------------------------------------------------------------------ */

abbr em {
	border: none !important;
	border-top: dashed 1px #aaa !important;
	display: inline-block !important;
	background: url(<?php echo $templateURI; ?>/images/commentluv.gif) 0% 90% no-repeat;
	margin-top: 8px;
	padding:  5px 5px 2px 20px !important;
	font-style: normal;
	}

/* ------------------------------------------------------------------
---------- Subscribe to comments -----------------------------------
------------------------------------------------------------------ */

p.subscribe-to-comments {
	margin-bottom: 10px;
	}

	
/* ------------------------------------------------------------------
---------- For WPG2 Gallery Plugin ----------------------------------
------------------------------------------------------------------ */

/* remove the gallery header with the "Gallery" logo */

div#gsHeader {
	display: none; 
	}

/* change the formatting of the whole gallery container.
Default settings: margin:0 1px 0 12px; width:738px; */

div.g2_column {
	margin: 0 !important;
	width: 100% !important;
	font-size: 1.2em;
	}

div#gsNavBar {
	border-top-width: 0 !important;
	}
	
p.giDescription {
font-size: 1.2em;
line-height: 1 !important;
}

p.giTitle {
margin: 0.3em 0 !important;
font-size: 1em;
font-weight: normal;
color: #666;
}

/* ------------------------------------------------------------------
---------- For WP Email Plugin ----------------------------------
------------------------------------------------------------------ */

div#wp-email img {
  border: 0;
  padding: 0;
}

div#wp-email input, div#wp-email textarea {
  margin-top: 5px;
  margin-bottom: 2px;
}

div#wp-email p {
  margin-bottom: 10px;
}

input#wp-email-submit {
    padding: 0;
    font-size: 30px;
    height: 50px;
    line-height: 50px;
    overflow: visible; /* for IE */
}

/* icon in post footer */
img.WP-EmailIcon {
    vertical-align: text-bottom !important;
}


/* ------------------------------------------------------------------
---------- For Tabbed Widgets Plugin ---------------------------------
------------------------------------------------------------------ */

/* For the accordion */

.tw-accordion .tw-widgettitle,
.tw-accordion .tw-widgettitle:hover,
.tw-accordion .tw-hovered,
.tw-accordion .selected,
.tw-accordion .selected:hover {
    background: transparent !important;
    background-image: none !important;
}

.tw-accordion .tw-widgettitle span {
    padding-left: 0 !important;
}

.tw-accordion h3.tw-widgettitle {
border-bottom: solid 1px #ccc;
}
.tw-accordion h3.selected {
border-bottom: none;
}


/* For  accordion & tabs*/

td#left .without_title,
td#right .without_title {
    margin-top: 0;
	margin-bottom: 0;
}

/* For  tabs*/

ul.tw-nav-list {
	border-bottom: solid 1px #999;
	display: block;
	margin-bottom: 5px !important;
}


td#left ul.tw-nav-list li,
td#right ul.tw-nav-list li { 
	padding: 0 0 1px 0;
	margin: 0 0 -1px 5px;
    border: solid 1px #ccc;
	border-bottom: none;
	border-radius: 5px;
	border-bottom-right-radius: 0;
	border-bottom-left-radius: 0;
	background: #eee;
}

td#left ul.tw-nav-list li.ui-tabs-selected,
td#right ul.tw-nav-list li.ui-tabs-selected {
    background: none;
	border: solid 1px #999;
	border-bottom: solid 1px #fff !important;
}

ul.tw-nav-list li a:link,
ul.tw-nav-list li a:visited,
ul.tw-nav-list li a:active,
ul.tw-nav-list li a:hover {
	padding: 0 8px !important;
	background: none;
	border-left: none !important;
	outline: none;
}


td#left ul.tw-nav-list li.ui-tabs-selected a,
td#left li.ui-tabs-selected a:hover,
td#right ul.tw-nav-list li.ui-tabs-selected a,
td#right li.ui-tabs-selected a:hover {
    color: #000000;
    text-decoration: none; 
	font-weight: bold;
	background: none !important;
	outline: none;
}

td#left .ui-tabs-panel,
td#right .ui-tabs-panel {
    margin: 0;
    padding: 0;
}


/* ------------------------------------------------------------------
---------- IMAGES --------------------------------------------------
------------------------------------------------------------------ */

img { 
	border: 0;
	}

/* For Events manager plugin Google Map */
#dbem-location-map img {
    background: none !important;
}

.post img { 
	<?php bfa_incl('post_image_style') ?>
	}

.post img.size-full {
<?php if(strpos($bfa_ata['layout_width'], 'px') === FALSE) { ?>
	max-width: 96%;		/* 	resize images in the main column if needed.
							97% so images with padding and border don't touch
							the right sidebar while being resized. Change this 
							to 100% if you want, if your images
							don't have padding and a border */
	width: auto;
<?php } ?>
	margin: 5px 0 5px 0;
	}



<?php if(strpos($bfa_ata['layout_width'], 'px') === FALSE) { ?>
/* hiding from IE6 which would stretch the image vertically. 
IE6 will get width and height via jQuery */
div.post img[class~=size-full] { 
	height: auto; /* FF & Safari need auto */
	}	
<?php } ?>

.post img.alignleft {
	float: left; 
	margin: 10px 10px 5px 0; 
	}
	
.post img.alignright {
	float: right; 
	margin: 10px 0 5px 10px; 
	}

.post img.aligncenter {
	display: block;
	margin: 10px auto;
	}

.aligncenter, 
div.aligncenter {
   	display: block;
   	margin-left: auto;
   	margin-right: auto;
	}

.alignleft, 
div.alignleft {
	float: left;
	margin: 10px 10px 5px 0;
	}

.alignright, 
div.alignright {
   	float: right;
   	margin: 10px 0 5px 10px;
	}

/* feed icons on archives page */
div.archives-page img {
	border: 0;
	padding: 0;
	background: none;
	margin-bottom: 0;
	vertical-align: -10%;
	}
	
	
/* ------------------------------------------------------------------
---------- IMAGE CAPTION (WP 2.6 and newer) -----------------------
------------------------------------------------------------------ */

.wp-caption {
	/*max-width: 100% auto;*/
	max-width: 96%;		/* FF2, IE7, Opera9, Safari 3.0/3.1 will 
							resize images in the main column if needed.
							97% so images with padding and border don't touch
							the right sidebar while being resized. Change this 
							to 100% if you want, if your images
							don't have padding and a border */
	width: auto 100%;
	height: auto;  /* FF3 needs "auto", IE6 needs "100%", see next style*/
	display: block;
	<?php bfa_incl('post_image_caption_style') ?>
	}

/* for imges inside a caption container IE6 does not
stretch images vertically as it does with images without
caption so we can leave this rule although it is probably not
required as jQuery sets the height for caption'ed images too */
* html .wp-caption {
	height: 100%; 
	}
	
.wp-caption img {
   	margin: 0 !important;
   	padding: 0 !important;
   	border: 0 none !important;
	}
	
.wp-caption p.wp-caption-text {
	<?php bfa_incl('image_caption_text') ?>
	}

/* ------------------------------------------------------------------
---------- POST THUMBNAILS (WP 2.9 and newer) -----------------------
------------------------------------------------------------------ */

img.wp-post-image {
	<?php bfa_incl('post_thumbnail_css') ?>
}

/* ------------------------------------------------------------------
---------- SMILEYS -------------------------------------------------
------------------------------------------------------------------ */

img.wp-smiley {
    float: none;  
    border: none !important; 
	margin: 0 1px -1px 1px; 
	padding: 0 !important;
	background: none !important;
	}


/* ------------------------------------------------------------------
---------- GRAVATARS ----------------------------------------------
------------------------------------------------------------------ */

img.avatar {
	float: left; 
	display: block;
	<?php bfa_incl('avatar_style') ?>
	}
	

/* ------------------------------------------------------------------
---------- FOR THE QUICKTAGS PLUGIN ------------------------------
------------------------------------------------------------------ */	

/*--------------------COMMENTS QUCIKTAGS ------------------*/

/* Main Span */
#comment_quicktags {
	text-align: left;
	padding: 10px 0 2px 0;
	display: block;
	}

/* Button Style */
#comment_quicktags input.ed_button {
	background: #f4f4f4;
	border: 2px solid #cccccc;
	color: #444444;
	margin: 2px 4px 2px 0;
	width: auto;
	padding: 0 4px;
	height: 24px;
	line-height: 16px;
	}
	
/* Button Style on focus/click */
#comment_quicktags input.ed_button_hover {
	background: #dddddd;
	border: 2px solid #666666;
	color: #000000;
	margin: 2px 4px 2px 0;
	width: auto;
	padding: 0 4px;
	height: 24px;
	line-height: 16px;
	cursor: pointer;
	}

/* Button Lable style */
#comment_quicktags #ed_strong {
	font-weight: bold;
	}
	
/* Button Lable style */
#comment_quicktags #ed_em {
	font-style: italic;
	}

	
<?php 
if (function_exists('sociable_html')) {
# include (WP_PLUGIN_DIR.'/sociable/sociable.css');
?>

/* ------------------------------------------------------------------
---------- FOR THE SOCIABLE PLUGIN --------------------------------
------------------------------------------------------------------ */

div.sociable { 
	margin: 0; 
	width: 200px;
	display:inline;
	}

div.sociable-tagline {
	display: none;
	}
	
.sociable span {
	display: inline-block;
	}
	
.sociable ul {
	display: inline;
	margin: 0 !important;
	padding: 0 !important;
	}
	
.sociable ul li {
	background: none;
	display: inline;
	list-style-type: none;
	margin: 0;
	padding: 1px;
	}
	
.sociable ul li:before { 
	content: ""; 
	}
	
.sociable img {
	float: none;
	width: 16px;
	height: 16px;
	border: 0;
	margin: 0;
	padding: 0;
	}

.sociable-hovers {
	opacity: .4;
	filter: alpha(opacity=40);
	vertical-align: text-bottom;
	}
	
.sociable-hovers:hover {
	opacity: 1;
	filter: alpha(opacity=100);
	}

<?php } ?>


<?php 
if (function_exists('wp_pagenavi')) {
include (WP_PLUGIN_DIR.'/wp-pagenavi/pagenavi-css.css');
?>

/* ------------------------------------------------------------------
---------- FOR THE WP-PAGENAVI PLUGIN ----------------------------
------------------------------------------------------------------ */

.wp-pagenavi a:link, 
.wp-pagenavi a:visited, 
.wp-pagenavi a:active { 
	color: #<?php echo $bfa_ata['link_color']; ?>; 
	border: solid 1px #<?php echo $bfa_ata['link_color']; ?>; 
	}

.wp-pagenavi a:hover { 
	color: #<?php echo $bfa_ata['link_hover_color']; ?>; 
	border: solid 1px #<?php echo $bfa_ata['link_hover_color']; ?>; 
	}

<?php } ?>


/* ------------------------------------------------------------------
---------- PRINT STYLE ----------------------------------------------
------------------------------------------------------------------ */

@media print {

	body { 
		background: white; 
		color: black; 
		margin: 0; 
		font-size: 10pt !important; 
		font-family: arial, sans-serif; 
		}

	div.post-footer {
		line-height: normal !important;
		color: #555 !important;
		font-size: 9pt !important;
		}

	a:link, 
	a:visited, 
	a:active,
	a:hover {
		text-decoration: underline !important; 
		color: #000;
		}
		
	h2 {
		color: #000; 
		font-size: 14pt !important; 
		font-weight: normal !important;
		}
		
	h3 {
		color: #000; 
		font-size: 12pt !important; 
		}
		
	#header, 
	#footer, 
	.colone, 
	.colthree,
	.navigation, 
	.navigation-top,
	.navigation-middle,
	.navigation-bottom,
	.wp-pagenavi-navigation, 
	#comment, 
	#respond,
	.remove-for-print {
		display: none;
		}

	td#left, td#right, td#left-inner, td#right-inner {
		width: 0;
		display: none;
		}

	td#middle {
		width: 100% !important;
		display: block;
		}

	/* 8 hacks for display:none for all sidebars for all browsers except IE. */

	*:lang(en) td#left {
	    display: none;
		}
		
	*:lang(en) td#right {
	    display: none;
		}

	*:lang(en) td#left-inner {
	    display: none;
		}
		
	*:lang(en) td#right-inner {
	    display: none;
		}
		
	td#left:empty {
	    display: none;
		}

	td#right:empty {
	    display: none;
		}

	td#left-inner:empty {
	    display: none;
		}

	td#right-inner:empty {
	    display: none;
		}
		
}	


/* ##################################################################
---------------------------------------------------------------------
---------- DROP DOWN / FLY OUT MENUS --------------------------------
Ruthsarian's rMenu http://webhost.bridgew.edu/etribou/layouts/
modified by Bytes For All http://wordpress.bytesforall.com/
---------------------------------------------------------------------
################################################################## */


/* ------------------------------------------------------------------
---------- GENERAL MENU MECHANICS -----------------------------------
------------------------------------------------------------------ */

ul.rMenu, 
ul.rMenu ul, 
ul.rMenu li, 
ul.rMenu a {
	display: block;		/* make these objects blocks so they're easier  to deal with */
	margin: 0;
	padding: 0;			/* get rid of padding/margin values that these
						elements may have by default */
	}
	
ul.rMenu, ul.rMenu li, ul.rMenu ul {
	list-style: none;	
	}
	
ul.rMenu ul {
	display: none;		/* hide the sub-menus until needed */
	}
	
ul.rMenu li {
	position: relative;	/* so sub-menus position relative to their 
						parent LI element */
	z-index: 1;
	}
	
ul.rMenu li:hover {
	z-index: 999;		/* make sure this and any sub-menus that pop 
						appear above everything else on the page */
	}
	
ul.rMenu li:hover > ul	/* hide from IE5.0 because it gets confused 
						by this selector */
	{
	display: block;		/* show the sub-menu */
	position: absolute;	/* remove the sub-menus from the flow of the
						layout so when they pop they don't cause any
						disfiguration of the layout. */
	}
	
ul.rMenu li:hover {
background-position: 0 0;
}

/* ------------------------------------------------------------------
---------- EXTENDED MENU MECHANICS ----------------------------------
------------------------------------------------------------------ */

/* These rules exist only for specific menu types, such as horizontal 
or vertical menus, right or left aligned menus. */
 
ul.rMenu-hor li {
	float: left;
	width: auto;
	}
	
ul.rMenu-hRight li {
	float: right;		/* horizontal, right menus need their LI
				   elements floated to get them over there */
	}
	
ul.sub-menu li,
ul.rMenu-ver li {
	float: none;		/* clear this so vertical sub-menus that are
				   children of horizontal menus won't have
				   their LI widths set to auto. */
	}


<?php if (strpos($bfa_ata['configure_header'],'%pages')!==FALSE OR 
strpos($bfa_ata['configure_header'],'%page-center')!==FALSE OR 
strpos($bfa_ata['configure_header'],'%page-right')!==FALSE OR 
strpos($bfa_ata['configure_header'],'%cats')!==FALSE OR 
strpos($bfa_ata['configure_header'],'%cat-center')!==FALSE OR 
strpos($bfa_ata['configure_header'],'%cat-right')!==FALSE) { ?>
div#menu1 ul.sub-menu, 
div#menu1 ul.sub-menu ul,
div#menu1 ul.rMenu-ver, 
div#menu1 ul.rMenu-ver ul {
	width: <?php echo $bfa_ata['page_menu_submenu_width']; ?>em;	
	}
div#menu2 ul.sub-menu, 
div#menu2 ul.sub-menu ul,
div#menu2 ul.rMenu-ver, 
div#menu2 ul.rMenu-ver ul {
	width: <?php echo $bfa_ata['cat_menu_submenu_width']; ?>em;	
	}
	
ul.rMenu-wide
	{
	width: 100%;		/* apply this rule if you want the top-level
				   menu to go as wide as possible. this is 
				   something you might want if your top-level
				   is a vertical menu that spans the width
				   of a column which has its width 
				   pre-defined. IE/Win 5 seems to prefer
				   a value of 100% over auto. */
	}
	
ul.rMenu-vRight
	{
	float: right;		/* use this to float a vertical menu right. */
	}
	
ul.rMenu-lFloat
	{
	float: left;		/* use this to float a vertical menu left. */
	}
	
ul.rMenu-noFloat
	{
	float: none;		/* this is to cover those cases where a menu
				   is floated by default and you have a reason
				   to not float it. such as a menu on the
				   right side of the screen that you want 
				   to have drops going left but not floated.
				   to be honest, i don't think this rule is 
				   needed. the clearfix hack will resolve
				   renering issues associated with a floated
				   menu anyways. */
	}


/* ------------------------------------------------------------------
---------- EXTENDED MENU MECHANICS - Center Horizontal Menu ---------
------------------------------------------------------------------ */

div.rMenu-center ul.rMenu {
	float: left;
	position: relative;
	left: 50%;
	}
	
div.rMenu-center ul.rMenu li {
	position: relative;
	left: -50%;
	}
	
div.rMenu-center ul.rMenu li li {
	left: auto;
	}


/* ------------------------------------------------------------------
---------- DROP POSITIONS -------------------------------------------
------------------------------------------------------------------ */

ul.rMenu-hor ul {
	top: auto;		/* a value of 100% creates a problem in IE 5.0 
				   and Opera 7.23 */
	right: auto;
	left: auto;		/* typically want a value of 0 here but set to
				   auto for same reasons detailed above */
	margin-top: -1px;	/* so the top border of the dropdown menu 
				   overlaps the bottom border of its parent
				   horizontal menu. */
	}

ul.rMenu-hor ul ul {
	margin-top: 0;	/* reset the above for fly out menus */
	margin-left: 0px;
	}
	
ul.sub-menu ul,
ul.rMenu-ver ul {
	/*left: 60%;*/
	left: 100%;
	right: auto;
	top: auto;
	/*margin-top: -0.5em;*/	/* i prefer top: 80% but this creates a problem
				   in iCab so negative top margin must be used.
				   salt to taste. */
	top: 0;
	}
	
ul.rMenu-vRight ul, 
ul.rMenu-hRight ul.sub-menu ul,
ul.rMenu-hRight ul.rMenu-ver ul {
	left: -100%;
	right: auto;
	top: auto;
	/*margin-top: -0.5em;*/	/* i prefer top: 80% but this creates a problem
				   in iCab so negative top margin must be used.
				   salt to taste. */
	}
	
ul.rMenu-hRight ul {
	left: auto;
	right: 0;		/* this doesn't work in Opera 7.23 but 7.5 and
				   beyond work fine. this means right-aligned
				   horizontal menus break in Opera 7.23 and
				   earlier. no workaround has been found. */
	top: auto;
	margin-top: -1px;	/* so the top border of the dropdown menu 
				   overlaps the bottom border of its parent
				   horizontal menu. */
	}


/* ------------------------------------------------------------------
---------- PRESENTATION: General ------------------------------------
------------------------------------------------------------------ */

div#menu1 ul.rMenu {
	background: #<?php echo $bfa_ata['page_menu_bar_background_color']; ?>;
	border: <?php echo $bfa_ata['anchor_border_page_menu_bar']; ?>;
	}
div#menu2 ul.rMenu {
	background: #<?php echo $bfa_ata['cat_menu_bar_background_color']; ?>;
	border: <?php echo $bfa_ata['anchor_border_cat_menu_bar']; ?>;
	}

div#menu1 ul.rMenu li a {
	border: <?php echo $bfa_ata['anchor_border_page_menu_bar']; ?>;	
	}
div#menu2 ul.rMenu li a {
	border: <?php echo $bfa_ata['anchor_border_cat_menu_bar']; ?>;	
	}

ul.rMenu-hor li {
	margin-bottom: -1px;	/* this is so if we apply a bottom border to 
				   the UL element it will render behind, but
				   inline with the bottom border of the LI
				   elements. Default: -1px */
	margin-top: -1px;	/* this is so if we apply a top border to 
				   the UL element it will render behind, but
				   inline with the bottom border of the LI
				   elements. Default: -1px */				
	margin-left: -1px;	/* negative borders on LIs to make borders on
				   child A elements overlap. they go here and
				   not on the A element for compatibility
				   reasons (IE6 and earlier). Default: -1px */
	}

ul#rmenu li {	
	/*margin-right: 3px;*/	/* set to 0 to remove the space between single, 
				   horizontal LI elements */
	}
ul#rmenu li ul li {	
	/*margin-right: 0;*/	/* without this, the 2nd level horizontal LI's would get
				   a margin-right, too. This should always be 0 */
	}

ul.rMenu-hor {
	padding-left: 1px ;	/* compensate for the 1px left jog created by
				   the above negative margin. */
	}
	
ul.sub-menu li,
ul.rMenu-ver li {
	margin-left: 0;
	margin-top: -1px;	/* same thing above except for vertical
				   menus */
				   
	}
	
div#menu1 ul.sub-menu,
div#menu1 ul.rMenu-ver {
	border-top: <?php echo $bfa_ata['anchor_border_page_menu_bar']; ?>;	
	}
div#menu2 ul.sub-menu,
div#menu2 ul.rMenu-ver {
	border-top: <?php echo $bfa_ata['anchor_border_cat_menu_bar']; ?>;	
	}

				
div#menu1 ul.rMenu li a {
	padding: 4px 5px;	
	}
div#menu2 ul.rMenu li a {
	padding: 4px 5px;	
	}
		
div#menu1 ul.rMenu li a:link, 
div#menu1 ul.rMenu li a:hover, 
div#menu1 ul.rMenu li a:visited, 
div#menu1 ul.rMenu li a:active {
	text-decoration: none;
	margin: 0;
	color: #<?php echo $bfa_ata['page_menu_bar_link_color']; ?>;
	text-transform: <?php echo $bfa_ata['page_menu_transform']; ?>;
	font: <?php echo $bfa_ata['page_menu_font']; ?>;  
	}
div#menu2 ul.rMenu li a:link, 
div#menu2 ul.rMenu li a:hover, 
div#menu2 ul.rMenu li a:visited, 
div#menu2 ul.rMenu li a:active {
	text-decoration: none;
	margin:0;
	color: #<?php echo $bfa_ata['cat_menu_bar_link_color']; ?>;
	text-transform: <?php echo $bfa_ata['cat_menu_transform']; ?>;
	font: <?php echo $bfa_ata['cat_menu_font']; ?>; 
	}
	
/*
ul.rMenu li.sfhover a:active,
ul.rMenu li:hover a:active
	{
	color: #fff;
	background-color: #c00;
	}
*/

	
div#menu1 ul.rMenu li {
	background-color: #<?php echo $bfa_ata['page_menu_bar_background_color']; ?>;	
	}
div#menu2 ul.rMenu li {
	background-color: #<?php echo $bfa_ata['cat_menu_bar_background_color']; ?>;	
	}
	
div#menu1 ul.rMenu li:hover,
div#menu1 ul.rMenu li.sfhover {
	/* background color for parent menu items of
	the current sub-menu. includes the sfhover
	class which is used in the suckerfish hack
	detailed later in this stylesheet. */
	background: #<?php echo $bfa_ata['page_menu_bar_background_color_parent']; ?>;	
	}
div#menu2 ul.rMenu li:hover,
div#menu2 ul.rMenu li.sfhover {
	/* background color for parent menu items of
	the current sub-menu. includes the sfhover
	class which is used in the suckerfish hack
	detailed later in this stylesheet. */
	background: #<?php echo $bfa_ata['cat_menu_bar_background_color_parent']; ?>;	
	}

/* "current" page and hover, first part old version */
div#menu1 ul.rMenu li.current-menu-item > a:link, 
div#menu1 ul.rMenu li.current-menu-item > a:active, 
div#menu1 ul.rMenu li.current-menu-item > a:hover, 
div#menu1 ul.rMenu li.current-menu-item > a:visited,
div#menu1 ul.rMenu li.current_page_item > a:link, 
div#menu1 ul.rMenu li.current_page_item > a:active, 
div#menu1 ul.rMenu li.current_page_item > a:hover, 
div#menu1 ul.rMenu li.current_page_item > a:visited {
	background-color: #<?php echo $bfa_ata['page_menu_bar_background_color_hover']; ?>;
	color: #<?php echo $bfa_ata['page_menu_bar_link_color_hover']; ?>;
	}
/* First 4 lines For IE6:*/
div#menu1 ul.rMenu li.current-menu-item a:link, 
div#menu1 ul.rMenu li.current-menu-item a:active, 
div#menu1 ul.rMenu li.current-menu-item a:hover, 
div#menu1 ul.rMenu li.current-menu-item a:visited, 
div#menu1 ul.rMenu li.current_page_item a:link, 
div#menu1 ul.rMenu li.current_page_item a:active, 
div#menu1 ul.rMenu li.current_page_item a:hover, 
div#menu1 ul.rMenu li.current_page_item a:visited, 
div#menu1 ul.rMenu li a:hover {
	background-color: #<?php echo $bfa_ata['page_menu_bar_background_color_hover']; ?>;
	color: #<?php echo $bfa_ata['page_menu_bar_link_color_hover']; ?>;
	}
div#menu2 ul.rMenu li.current-menu-item > a:link, 
div#menu2 ul.rMenu li.current-menu-item > a:active, 
div#menu2 ul.rMenu li.current-menu-item > a:hover, 
div#menu2 ul.rMenu li.current-menu-item > a:visited,
div#menu2 ul.rMenu li.current-cat > a:link, 
div#menu2 ul.rMenu li.current-cat > a:active, 
div#menu2 ul.rMenu li.current-cat > a:hover, 
div#menu2 ul.rMenu li.current-cat > a:visited {
	background-color: #<?php echo $bfa_ata['cat_menu_bar_background_color_hover']; ?>;
	color: #<?php echo $bfa_ata['cat_menu_bar_link_color_hover']; ?>;
	}
/* First 4 lines For IE6:*/
div#menu2 ul.rMenu li.current-menu-item a:link, 
div#menu2 ul.rMenu li.current-menu-item a:active, 
div#menu2 ul.rMenu li.current-menu-item a:hover, 
div#menu2 ul.rMenu li.current-menu-item a:visited, 
div#menu2 ul.rMenu li.current-cat a:link, 
div#menu2 ul.rMenu li.current-cat a:active, 
div#menu2 ul.rMenu li.current-cat a:hover, 
div#menu2 ul.rMenu li.current-cat a:visited, 
div#menu2 ul.rMenu li a:hover {
	background-color: #<?php echo $bfa_ata['cat_menu_bar_background_color_hover']; ?>;
	color: #<?php echo $bfa_ata['cat_menu_bar_link_color_hover']; ?>;
	}

/* ------------------------------------------------------------------
---------- PRESENTATION: Expand -------------------------------------
------------------------------------------------------------------ */

div#menu1 ul.rMenu li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a {
	padding-right: 15px;
	padding-left: 5px;
	background-repeat: no-repeat;
	background-position: 100% 50%;
	background-image: url(<?php echo $templateURI; ?>/images/expand-right<?php echo ($bfa_ata['page_menu_arrows'] == "white" ? "-white" : ""); ?>.gif);
	}
div#menu2 ul.rMenu li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a {
	padding-right: 15px;
	padding-left: 5px;
	background-repeat: no-repeat;
	background-position: 100% 50%;
	background-image: url(<?php echo $templateURI; ?>/images/expand-right<?php echo ($bfa_ata['cat_menu_arrows'] == "white" ? "-white" : ""); ?>.gif);
	}
	
ul.rMenu-vRight li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-vRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-hRight li.rMenu-expand a,
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand a,
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a,
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a, 
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a, 
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a, 
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a, 
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a, 
ul.rMenu-hRight li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand a 
	{
	padding-right: 5px;
	padding-left: 20px;
	background-image: url(<?php echo $templateURI; ?>/images/expand-left.gif);
	background-repeat: no-repeat;
	background-position: -5px 50%;
	}

/* divs added for "IE6 & 2 menu styles" */	

div#menu1 ul.rMenu-hor li.rMenu-expand a {
	padding-left: 5px;	/* reset padding */
	padding-right: 15px !important;
	background-position: 100% 50%;
	background-image: url(<?php echo $templateURI; ?>/images/expand-down<?php echo ($bfa_ata['page_menu_arrows'] == "white" ? "-white" : ""); ?>.gif);
	}
div#menu2 ul.rMenu-hor li.rMenu-expand a {
	padding-left: 5px;	/* reset padding */
	padding-right: 15px !important;
	background-position: 100% 50%;
	background-image: url(<?php echo $templateURI; ?>/images/expand-down<?php echo ($bfa_ata['cat_menu_arrows'] == "white" ? "-white" : ""); ?>.gif);
	}
	
	
div#menu1 ul.rMenu li.rMenu-expand li a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li a,
div#menu1 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li a  {
	background-image: none;
	padding-right: 5px;	/* reset padding */
	padding-left: 5px;	/* reset padding */
	}
div#menu2 ul.rMenu li.rMenu-expand li a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li a,
div#menu2 ul.rMenu li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li.rMenu-expand li a {
	background-image: none;
	padding-right: 5px;	/* reset padding */
	padding-left: 5px;	/* reset padding */
	}


<?php if (strpos($bfa_ata['configure_header'],'%page-center')!==FALSE) { ?> 
/* For centered page menu */

ul#rmenu2 {
	border: 0 !important;
}

ul#rmenu2 li a {
	white-space: nowrap; /* IE may wrap the link text of a first level item */
}

ul#rmenu2 li ul li a {
	white-space: normal; /* reset nowrap for sub levels. They should wrap if required */
}

ul#rmenu2 ul.sub-menu,
ul#rmenu2 ul.rMenu-ver {
	text-align: left; /* because the parent container, the table's TD has align="center" */
}

div#menu1 {
	border: dashed 1px #ccc;
}
<?php } ?>


<?php if (strpos($bfa_ata['configure_header'],'%cat-center')!==FALSE) { ?> 
/* For centered category menu */

ul#rmenu {
	border: 0 !important;
}

ul#rmenu li a {
	white-space: nowrap; /* IE may wrap the link text of a first level item */
}

ul#rmenu li ul li a {
	white-space: normal; /* reset nowrap for sub levels. They should wrap if required */
}

ul#rmenu ul.sub-menu,
ul#rmenu ul.rMenu-ver {
	text-align: left; /* because the parent container, the table's TD has align="center" */
}

div#menu2 {
	border: dashed 1px #ccc;
}
<?php } ?>


<?php if (strpos($bfa_ata['configure_header'],'%page-right')!==FALSE) { ?> 
div#menu1 ul.rMenu {
	background: #<?php echo $bfa_ata['page_menu_bar_background_color']; ?>;
	border: <?php echo $bfa_ata['anchor_border_page_menu_bar']; ?>;
	border-right: none;
	}
<?php } ?>

<?php if (strpos($bfa_ata['configure_header'],'%cat-right')!==FALSE) { ?> 
div#menu2 ul.rMenu {
	background: #<?php echo $bfa_ata['cat_menu_bar_background_color']; ?>;
	border: <?php echo $bfa_ata['anchor_border_cat_menu_bar']; ?>;
	border-right: none;
	}
<?php } ?>
	
/*******************************************************************************
 * HACKS : General
 *
 * These are rules specifically targeted to resolve bugs/quirks that some
 * browser exhibit.
 *
 * REFERENCES:
 *	http://www.webdevout.net/css-hacks
 *	http://www.satzansatz.de/cssd/onhavinglayout.html
 *	http://www.communis.co.uk/dithered/css_filters/css_only/index.html
 */
* html ul.rMenu
{
	display: inline-block;	/* this is for IE/Mac. it forces IE/Mac to 
							   expand the element's dimensions to contain 
							   its floating child elements without a 
							   clearing element. */
	/* \*/ display: block;	/* override above rule for every other 
							   browser using IE/Mac backslash hack */
	position: relative;		/* IE 5.0/Mac needs this or it may clip the
							   dropdown menus */
	/* \*/ position: static;/* reset position attribute for IE/Win as it
							   causes z-index problems */
}
* html ul.rMenu ul
{
	float: left;	/* IE/Mac 5.0 needs this, otherwise hidden 
					   menus are not completely removed from the
					   flow of the document. */
	/* \*/ float: none;	/* reset the rule for non-Macs */
}
ul.rMenu ul
{
	background-color: #fff;	/* IE/Win (including 7) needs this on an object 
							   that hasLayout so that it doesn't "look through"
							   the menu and let any object (text) below the 
							   menu to gain focus, causing the menu to 
							   disappear. application of this rule does not
							   cause any rendering problems with other browsers
							   as the background color his covered by the
							   menu itself. */
}
* html ul.sub-menu li,
* html ul.rMenu-ver li,
* html ul.rMenu-hor li ul.sub-menu li,
* html ul.rMenu-hor li ul.rMenu-ver li
{
					/* the second selector above is there 
					   because of problems IE/Mac has with 
					   inheritance and what rules should take
					   precedence. and to serve as a reminder on
					   how to work around the issue if it's 
					   encountered again down the road. */
	width: 100%;
	float: left;
	clear: left;	/* IE likes to stick space below any LI
					   in :hover state with a sub-menu. floating
					   the LIs seems to work around this issue. But
					   note that this also triggers hasLayout 
					   because we need a width of 100% on floats. */
}
*:first-child+html ul.sub-menu > li:hover ul,
*:first-child+html ul.rMenu-ver > li:hover ul /* hide from IE5.0 because it gets confused by this selector */
{
	min-width: 0;	/* this fixes a similar problem as described in the
					   rule set that exists in IE7 (and later?). However
					   the whitespace only appears when the LI element is
					   in a :hover state. */
}
ul.rMenu li a
{
	position: relative;	/* trigger hasLayout for IE on anchor 
						   elements. without hasLayout on anchors
						   they would not expand the full width 
						   of the menu. this rule may not trigger
						   hasLayour in later versions of IE and
						   if you find this system broken in new
						   versions of IE, this is probably the
						   source. */
	min-width: 0;		/* triggers hasLayout for IE 7 */
}
* html ul.rMenu-hor li
{
	width: 6em;	/* IE Mac doesn't do auto widths so specify a width 
				   for the sake of IE/Mac. Salt to taste. */
	/* \*/ width: auto;	/* now undo previous rule for non Macs by using 
						   the IE Mac backslash comment hack */
}
* html div.rMenu-center
{
	position: relative;
	z-index: 1;		/* IE 6 and earlier need a little help with
					   z-indexes on centered menus */
}
html/* */:not([lang*=""]) div.rMenu-center ul.rMenu li a:hover {
	height: 100%;	/* for Netscape 6 */
}
html:/* */not([lang*=""])  div.rMenu-center ul.rMenu li a:hover {
	height: auto;	/* reset for Netscape 7 and better */
}

/*******************************************************************************
 * HACKS : Suckerfish w/Form Field Support (for IE 5.5 & 6.x)
 *
 * IE6 and earlier do not support the :hover pseudoclass and so javascript is 
 * used to add the "sfhover" class of any LI element that the mouse is currently 
 * over. This method is called suckerfish and you can read up on it at:
 * http://www.htmldog.com/articles/suckerfish/dropdowns/
 *
 * One problem with this approach is IE6 and earlier versions have a bug where
 * form fields appear over the dropdown menus regardless of z-index values.
 * The fix is to generate and stick an IFRAME element under the dropdown menus
 * as they pop. The JavaScript used to do this requires that we hide menus off
 * to the side of the screen ( left: -100000px; ), but normal rMenu operation
 * is to hide menus with the DISPLAY property ( display: none; ). So also
 * included in the set of rules below are rules to overwrite this original
 * functionality of rMenu and utilize the LEFT property to move menus off-
 * screen until needed. Any other rules that use the LEFT property in the
 * normal rMenu system will also have to be ovewriten here as well. This
 * includes the dropdown positions.
 *
 * NOTE: this allows for support of dropdown menus up to 3 levels deep. if you 
 *	 want to support greather menu depth you need to alter these selectors. 
 *	 read the above mentioned website for more info on how to do that.
 *
 *       The fix to get dropdowns to appear over form fields requires we 
 *       position menus off screen rather than simply hiding them with
 *       display:none. So you might think we should not be using the display
 *       property in the fields below. However we can because these display
 *       properties are only being set when a parent LI is being hovered, so
 *       the JavaScript used to operate on these LIs will already have the
 *       dimensions they need before these display rules are activated.
 */
* html ul.rMenu ul
{
	display: block;
	position: absolute;	/* ovewrite original functionality of hiding
				   element so we can hide these off screen */
}
* html ul.rMenu ul,
* html ul.rMenu-hor ul,
* html ul.sub-menu ul,
* html ul.rMenu-ver ul,
* html ul.rMenu-vRight ul,
* html ul.rMenu-hRight ul.sub-menu ul,
* html ul.rMenu-hRight ul.rMenu-ver ul,
* html ul.rMenu-hRight ul
{
	left: -10000px;		/* move menus off screen. note we're ovewriting
				   the dropdown position rules that use the 
				   LEFT property, thus all the selectors. */
}
* html ul.rMenu li.sfhover
{
	z-index: 999;		/* not totally needed, but keep the menu 
				   that pops above all other elements within
				   it's parent menu system */
}
* html ul.rMenu li.sfhover ul
{
	left: auto;		/* pull the menus that were off-screen back 
				   onto the screen */
}
* html ul.rMenu li.sfhover ul ul,
* html ul.rMenu li.sfhover ul ul ul
{ 
	display: none;		/* IE/Suckerfish alternative for browsers that
				   don't support :hover state on LI elements */
}
* html ul.rMenu li.sfhover ul,
* html ul.rMenu li li.sfhover ul,
* html ul.rMenu li li li.sfhover ul
{
	display: block;		/* ^ ditto ^ */
}

* html ul.sub-menu li.sfhover ul,
* html ul.rMenu-ver li.sfhover ul
{
	left: 60%;		/* dropdown positioning uses the left attribute
				   for horizontal positioning. however we can't
				   use this property until the menu is being
				   displayed.

				   note that all ULs beneath the menu item 
				   currently in the hover state will get this
				   value through inheritance. however all sub-
				   menus still won't display because
				   two rule sets up we're setting the 
				   DISPLAY property to none.
				 */
}
* html ul.rMenu-vRight li.sfhover ul,
* html ul.rMenu-hRight ul.sub-menu li.sfhover ul
* html ul.rMenu-hRight ul.rMenu-ver li.sfhover ul
{
	left: -60%;		/* ^ ditto ^ */
}
* html ul.rMenu iframe
{
	/* filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0); */
				/* the above rule is now applied in the 
				   javascript used to generate the IFRAME this
				   is applied to. it allows the CSS to validate
				   while keeping the original functionality. */
	position: absolute;
	left: 0;
	top: 0;
	z-index: -1;		/* this is the IFRAME that's placed behind
				   dropdown menus so that form elements don't
				   show through the menus. they are not set
				   programatically via javascript because
				   doing so generates some lag in the display
				   of the dropdown menu. */
}

/* ie6 fixes */

* html ul.rMenu {
	margin-left: 1px;
}

* html ul.rMenu ul, 
* html ul.rMenu ul ul,
* html ul.rMenu ul ul ul,
* html ul.rMenu ul ul ul ul {
	margin-left: 0;
}
	
<?php } ?>	

/* ------------------------------------------------------------------
---------- HACKS: Clearfix & others ---------------------------------
------------------------------------------------------------------ */

.clearfix:after 	{
    	content: "."; 
    	display: block; 
    	height: 0; 
    	clear: both; 
    	visibility: hidden;
	}
	
.clearfix {
	min-width: 0;		/* trigger hasLayout for IE7 */
	display: inline-block;
	/* \*/	display: block;	/* Hide from IE Mac */
	}
	
* html .clearfix {
	/* \*/  height: 1%;	/* Hide from IE Mac */ 
	}

/* Chrome and Safari don't like clearfix in some cases.
Also, adding height and font-size for IE6 */
.clearboth {
	clear: both;
	height: 1%;
	font-size: 1%;
	line-height: 1%;
	display: block;
	padding: 0;
	margin: 0;
	}


<?php 
bfa_incl('html_inserts_css'); 

if ( $bfa_ata['css_compress'] == "Yes" AND 
!($bfa_ata_debug==1 AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	ob_end_flush();
}	
if ( isset($bfa_ata_preview) OR $bfa_ata['css_external'] == "Inline" OR 
($bfa_ata_debug==1 AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	echo "</style>\n"; 
}
?>