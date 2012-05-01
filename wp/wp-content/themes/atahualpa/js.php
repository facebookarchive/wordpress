<?php 
# global $bfa_ata; if ($bfa_ata == "") include_once (TEMPLATEPATH . '/functions/bfa_get_options.php'); 
if ( isset($bfa_ata_preview) OR $bfa_ata['javascript_external'] == "Inline" OR 
( isset($bfa_ata_debug) AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	echo '<script type="text/javascript">'; 
} else { 
	header("Content-type: application/x-javascript"); 
}
// Currently not used. Enable in bfa_theme_options as well
/*
if ( $bfa_ata['javascript_compress'] == "Yes" AND 
!( $bfa_ata_debug == 1 AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	ob_start("bfa_compress_js");
}
*/

function bfa_compress_js($buffer) {
	/* remove comments */
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	/* remove tabs, spaces, newlines, etc. */
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", "  ", "   ", "    "), '', $buffer);
	$buffer = str_replace(array(": ", " :"), ":", $buffer);
	$buffer = str_replace(array(" {", "{ "), "{", $buffer);
	$buffer = str_replace(array(" }", "} "), "}", $buffer);
	$buffer = str_replace(array(" (", "( "), "(", $buffer);
	$buffer = str_replace(array(" )", ") "), ")", $buffer);
	$buffer = str_replace(array(", ", " ,"), ",", $buffer);
	$buffer = str_replace(array("; ", " ;"), ";", $buffer);
	$buffer = str_replace(array("= ", " ="), "=", $buffer);
	return $buffer;
}


# if (function_exists('sociable_html')) {
# include (WP_PLUGIN_DIR.'/sociable/wists.js');
# }

?>

//<![CDATA[

<?php if (strpos($bfa_ata['configure_header'],'%image')!== FALSE AND 
$bfa_ata['header_image_javascript'] != "0" AND $bfa_ata['crossslide_fade'] == "0") { ?>
	var HeaderImages = new Array(<?php echo implode(",", bfa_rotating_header_images()); ?>);
	var t; var j = 0
	var p = HeaderImages.length
	<?php if ($bfa_ata['header_image_javascript_preload'] == "Yes") { ?>
	var PreLoadImages = new Array()
	for (i = 0; i < p; i++){
		PreLoadImages[i] = new Image()
		PreLoadImages[i].src = HeaderImages[i]
	}
	<?php } ?>
	function RotateHeaderImages(){
		if (document.body){
			HeaderImageContainer = document.getElementById('imagecontainer');
			HeaderImageContainer.style.background = 'url(' + HeaderImages[j] + ') <?php echo $bfa_ata['headerimage_alignment']; ?> no-repeat';
			j = j + 1
			if (j > (p-1)) j=0
			t = setTimeout('RotateHeaderImages()', <?php echo $bfa_ata['header_image_javascript']; ?>000)
		}
	}
	window.onload = RotateHeaderImages;
<?php } ?>


/* JQUERY */
jQuery(document).ready(function(){ 
<?php if ( $bfa_ata['animate_page_menu_bar'] == "Yes" AND strpos($bfa_ata['configure_header'],'%page')!== FALSE ) { ?>
  jQuery("#rmenu2 li.rMenu-expand").hover(function(){
    jQuery(this).find('ul.rMenu-ver:first').css({"display":"block","position":"absolute"});
    jQuery(this).find('ul.rMenu-ver:first li').css({"display":"none"}).slideDown(500);	
  },function() {
    jQuery(this).find('ul.rMenu-ver:first').css("display","block");
    jQuery(this).find('ul.rMenu-ver:first li').css("display","block").slideUp(300);
	jQuery(this).find('ul.rMenu-ver:first').slideUp(300);
   });
<?php } ?>    
   
<?php if ( $bfa_ata['animate_cat_menu_bar'] == "Yes" AND strpos($bfa_ata['configure_header'],'%cat')!== FALSE ) { ?>
  jQuery("#rmenu li.rMenu-expand").hover(function(){
    jQuery(this).find('ul.rMenu-ver:first').css({"display":"block","position":"absolute"});
    jQuery(this).find('ul.rMenu-ver:first li').css({"display":"none"}).slideDown(500);	
  },function() {
    jQuery(this).find('ul.rMenu-ver:first').css("display","block");
    jQuery(this).find('ul.rMenu-ver:first li').css("display","block").slideUp(300);
	jQuery(this).find('ul.rMenu-ver:first').slideUp(300);
   });
<?php } ?>
  
<?php if (strpos($bfa_ata['configure_header'],'%image')!== FALSE AND 
$bfa_ata['header_image_javascript'] != "0" AND $bfa_ata['crossslide_fade'] != "0") { ?>
	jQuery('div#imagecontainer')
	.crossSlide({sleep: <?php echo $bfa_ata['header_image_javascript']; ?>,fade: <?php echo $bfa_ata['crossslide_fade']; ?>},[
		{ src: <?php echo implode( " },\n{ src: ", bfa_rotating_header_images() ); ?> }
	]);
	/*	
	.crossSlide({fade: <?php echo $bfa_ata['crossslide_fade']; ?>},[
		<?php echo "{ src: " . implode( ", from: '40% 40%', to: '60% 60%', time: 3 },\n{ src: ", bfa_rotating_header_images() ) . ", 
		from: '40% 40%', to: '60% 60%', time: 3
		}\n ]);"; ?>
	*/
<?php } ?>

	/* jQuery('ul#rmenu').superfish(); */
	/* jQuery('ul#rmenu').superfish().find('ul').bgIframe({opacity:false}); */
 
	/* For IE6 */
	if (jQuery.browser.msie && /MSIE 6\.0/i.test(window.navigator.userAgent) && !/MSIE 7\.0/i.test(window.navigator.userAgent) && !/MSIE 8\.0/i.test(window.navigator.userAgent)) {

		/* Max-width for images in IE6 */		
		var centerwidth = jQuery("td#middle").width(); 
		
		/* Images without caption */
		jQuery(".post img").each(function() { 
			var maxwidth = centerwidth - 10 + 'px';
			var imgwidth = jQuery(this).width(); 
			var imgheight = jQuery(this).height(); 
			var newimgheight = (centerwidth / imgwidth * imgheight) + 'px';	
			if (imgwidth > centerwidth) { 
				jQuery(this).css({width: maxwidth}); 
				jQuery(this).css({height: newimgheight}); 
			}
		});
		
		/* Images with caption */
		jQuery("div.wp-caption").each(function() { 
			var captionwidth = jQuery(this).width(); 
			var maxcaptionwidth = centerwidth + 'px';
			var captionheight = jQuery(this).height();
			var captionimgwidth =  jQuery("div.wp-caption img").width();
			var captionimgheight =  jQuery("div.wp-caption img").height();
			if (captionwidth > centerwidth) { 
				jQuery(this).css({width: maxcaptionwidth}); 
				var newcaptionheight = (centerwidth / captionwidth * captionheight) + 'px';
				var newcaptionimgheight = (centerwidth / captionimgwidth * captionimgheight) + 'px';
				jQuery(this).css({height: newcaptionheight}); 
				jQuery("div.wp-caption img").css({height: newcaptionimgheight}); 
				}
		});
		
		/* sfhover for LI:HOVER support in IE6: */
		jQuery("ul li").
			hover( function() {
					jQuery(this).addClass("sfhover")
				}, 
				function() {
					jQuery(this).removeClass("sfhover")
				} 
			); 

	/* End IE6 */
	}
	
<?php if ($bfa_ata['table_hover_rows'] == "Yes") { ?>
	jQuery(".post table tr").
		mouseover(function() {
			jQuery(this).addClass("over");
		}).
		mouseout(function() {
			jQuery(this).removeClass("over");
		});
<?php } else { ?>
	jQuery(".post table.hover tr").
		mouseover(function() {
			jQuery(this).addClass("over");
		}).
		mouseout(function() {
			jQuery(this).removeClass("over");
		});	
<?php } ?>

	
<?php if ($bfa_ata['table_zebra_stripes'] == "Yes") { ?>
	jQuery(".post table tr:even").
		addClass("alt");
<?php } else { ?>
	jQuery(".post table.zebra tr:even").
		addClass("alt");	
<?php } ?>

	
<?php if ($bfa_ata['highlight_forms'] == "Yes") { ?>
	jQuery("input.text, input.TextField, input.file, input.password, textarea").
		focus(function () {  
			jQuery(this).addClass("highlight"); 
		}).
		blur(function () { 
			jQuery(this).removeClass("highlight"); 
		})
<?php } ?>
	
	jQuery("input.inputblur").
		focus(function () {  
			jQuery(this).addClass("inputfocus"); 
		}).
		blur(function () { 
			jQuery(this).removeClass("inputfocus"); 
		})

		
<?php if (function_exists('lmbbox_comment_quicktags_display')) { ?>
	jQuery("input.ed_button").
		mouseover(function() {
			jQuery(this).addClass("ed_button_hover");
		}).
		mouseout(function() {
			jQuery(this).removeClass("ed_button_hover");
		});
<?php } ?>

	
	jQuery("input.button, input.Button, input#submit").
		mouseover(function() {
			jQuery(this).addClass("buttonhover");
		}).
		mouseout(function() {
			jQuery(this).removeClass("buttonhover");
		});

	/* toggle "you can use these xhtml tags" */
	jQuery("a.xhtmltags").
		click(function(){ 
			jQuery("div.xhtml-tags").slideToggle(300); 
		});

	/* For the Tabbed Widgets plugin: */
	jQuery("ul.tw-nav-list").
		addClass("clearfix");

		
<?php if ( $bfa_ata['sticky_layout_footer'] == "Yes" ) { ?>
	/* Strech short pages to full height, keep footer at bottom. */
	
	/* Set a compensation value to fix browser differences and an overall 
	misalignment with this method */
	if (jQuery.browser.msie || jQuery.browser.safari) { 
		var bfacompensate = 41; 
	} else { 
		var bfacompensate = 21; 
	}
	
	/* Fix a jQuery/Opera 9.5+ bug with determining the window height */
	var windowheight = jQuery.browser.opera && jQuery.browser.version > "9.5" &&
    jQuery.fn.jquery <= "1.2.6" ? document.documentElement["clientHeight"] : jQuery(window).height();
	
	/* Top and bottom padding may have been set on the BODY */
	var paddingtop = parseInt(jQuery("body").css("padding-top"));
	var paddingbottom = parseInt(jQuery("body").css("padding-bottom"));
	
	/* Get the height of the header, footer, and the layout as a whole */
	var headerheight = jQuery("td#header").height();
	var footerheight = jQuery("td#footer").height();
	var layoutheight = jQuery("div#wrapper").height();
	
	/* Adjust height of middle column if (layout height + body padding-top + body padding-bottom) is smaller than 
	height of browser viewport */
	if ( windowheight > (layoutheight + paddingtop + paddingbottom) ) {
		var newmiddleheight = windowheight - paddingtop - headerheight - footerheight - paddingbottom - bfacompensate;
		jQuery("td#middle").css({height: newmiddleheight + "px"});
	}  
<?php } ?>
	
});

//]]>
<?php 

#if ( function_exists('wp_list_comments') AND $bfa_ata['include_wp_comment_reply_js'] == "Yes" ) 
#	include (ABSPATH . '/wp-includes/js/comment-reply.js'); 

// Currently not used. Enable in bfa_theme_options as well
/*
if ( $bfa_ata['javascript_compress'] == "Yes" AND 
!( $bfa_ata_debug == 1 AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	ob_end_flush(); 
}
*/
if ( isset($bfa_ata_preview) OR $bfa_ata['javascript_external'] == "Inline" OR 
( isset($bfa_ata_debug) AND $bfa_ata['allow_debug'] == "Yes" ) ) {
	echo "</script>\n"; 
}
?>