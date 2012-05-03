<?php

// http://example.com/home/wp-content/themes/atahualpa
$templateURI = get_template_directory_uri();

// http://example.com/home
$wordpress_base = home_url();

// /wp-content/themes/atahualpa/
$template_path = str_replace( $wordpress_base, '', $templateURI) . '/';

// example.com/home
$server_name_incl_wp_dir = str_replace("http://", "", $wordpress_base);

// /home
$wordpress_dir = str_replace($_SERVER['SERVER_NAME'], '', $server_name_incl_wp_dir);

// /home/wp-content/themes/atahualpa/
$css_img_path = $wordpress_dir . $template_path;

# $bfa_ata_widget_areas = get_option('bfa_widget_areas');
$bfa_ata = get_option('bfa_ata4');
if(isset($bfa_ata['bfa_widget_areas'])) $bfa_ata_widget_areas = $bfa_ata['bfa_widget_areas'];
else $bfa_ata_widget_areas = '';
$widget_form_string = '';
if (is_array($bfa_ata_widget_areas)) {
	foreach ($bfa_ata_widget_areas as $widget_area) {
		$widget_form_string .= '
		<input type="checkbox" name="delete_widget_areas" id="' . 
		$widget_area['name'] . '" value="' . $widget_area['name'] . 
		'" /><label class="widget_area_label" for="' . $widget_area['name'] . 
		'">' . $widget_area['name'] . '</label><br />';
	}
}

// Since 3.4.8, not activated yet, needs more code in css.php, js.php (3rd option), bfa_ata_admin.php ("New" tab) 
// and bfa_ata_add_admin.php (Save js and css files in WP Uploads with each "Save Changes")
/*
if (stristr(PHP_OS, 'WIN')) {
	$slash = '\\';
} else {
	$slash = '/';
}
$upload_path = str_replace( 'themes', '', get_theme_root() ) . 'uploads';
if(is_writable($upload_path)) { 
	$is_writable = "<span style='color:green;background:white'>Yes</span>"; 
} else {
	$is_writable = "<span style='color:red;background:white'>No</span>"; 
}
*/

/* make some of these variables global in functions in 3.5.0+ */

// different options text for WP and WPMU, because image upload works differently
$header_image_text_wp = "To add your own header image(s), upload one or several
images with any file names <code>anything.[jpg|gif|png]</code> i.e.
<code>hfk7wdfw8.gif</code>, <code>IMAGE_1475.jpg</code>, <code>bla.png</code>
to ". $template_path ."images/header/ through FTP. You will need a
\"FTP Client\" software such as <a href=\"http://filezilla-project.org/download.php\">
Filezilla</a> (free), the Firefox extension
<a href=\"https://addons.mozilla.org/de/firefox/addon/684\">FireFTP</a> (free)
or <a href=\"http://www.smartftp.com/download/\">SmartFTP</a> ($36.95).";

$header_image_text_wpmu = "To upload your own header images, you'll need to prepare your header image(s) 
on your harddrive first. Rename your header images to <code>atahualpa_header_X.[jpg|gif|png|bmp]</code> 
(Example: <code>atahualpa_header_1.jpg</code>, <code>atahualpa_header_3.png</code>, 
<code>atahualpa_header_182.gif</code>) and then, upload them to your WordPress site through the WordPress Editor</strong>. 
<br /><br />There may be no \"upload\" tab in the admin area though. In that case, start as if you were going to 
add an image to a post: Go to Admin -> Manage -> Posts, and click on the title of an existing post to open the editor. 
Click on the \"Add Media\" link, and in the next window click on the \"Choose files to upload\" button. 
That will open a window on your local computer where you can find and select the header image 
(which you've already renamed as described before) on your local harddrive. Select \"Full Size\" and, 
do NOT click on \"Insert into Post\" but click on \"Save all changes\" instead. Now reload your Homepage 
and the new header image should appear. If you want more than one header image (to have them rotate) simply 
repeat all these steps. Atahualpa will autmatically recognize all images that are named 
<code>atahualpa_header_X.[jpg|png|gif]</code>. If there's only one image, then it'll be your single, 
\"static\" header image. If there's more than one image, then Atahualpa will rotate them with every pageview.";

$logo_icon_text_wp = "To show your own graphic, upload an image to <strong>". $template_path ."images/</strong>
and put the file name of the image into this field. <br /><br /><strong>Example:</strong><br /><code>myownlogo.gif</code>
<br /><br />Make sure you have <strong>no spaces</strong> or exotic characters in the image file name. 
Your Windows or Mac Computer may display them but the hosting server probably won't. The image file can 
have a .gif, .jpg, .jpeg, .png or .bmp extension.";

$logo_icon_text_wpmu = "To show your own graphic, upload an image through the WordPress Editor. 
There may be no \"upload\" tab in your WordPress version. To upload the image start as if you were going to 
add an image to a post: Go to Admin -> Manage -> Posts, and click on the title of an existing post to open 
the editor. Click on the \"Add Media\" link, and in the next window click on the \"Choose files to upload\" button. 
That will open a window on your local computer where you can select the image on your local harddrive. 
After you've selected the image, choose \"Full Size\" and, instead of clicking on \"Insert into Post\", 
click on \"Save all changes\". Then put the file name of your image into this field, i.e. <code>my-new-logo.jpg</code> 
and click \"Save changes\" at the bottom fo this page. Now reload your Homepage and your new logo should appear 
instead of the default one.<br /><br />Make sure you have no spaces or exotic characters in the image file name. 
Your Windows or Mac Computer may display them but the hosting server probably won't. The image file can have 
a .gif, .jpg, .jpeg, .png or .bmp extension.";

if (defined('ABSPATH')) { 
	if (file_exists(ABSPATH."/wpmu-settings.php")) {
		$header_image_text = $header_image_text_wpmu; 
		$logo_icon_text = $logo_icon_text_wpmu; 
	} else { 
		$header_image_text = $header_image_text_wp; 
		$logo_icon_text = $logo_icon_text_wp;
	}
}

// different options text for different WP versions
if( function_exists('wp_list_comments') ) {
	// WP 2.7+
	$go_to_pages = "go to Site Admin -> Pages -> Edit";
	$go_to_cats = "go to Site Admin -> Posts -> Categories";
	$path_to_widgets = "Appearance";
} else {
	// WP 2.6 and older
	$go_to_pages = "go to Site Admin -> Manage -> Pages";
	$go_to_cats = "go to Site Admin -> Manage -> Categories";
	$path_to_widgets = "Design (\"Presentation\" in WP 2.3 and older)";
}

// array of theme options starts here. Set the category of the first option of every new option category to "category_name", except for the very first option, which will be hard coded in functions.php
$options1 = array(

    array(    "name" => "Thank you for using Atahualpa",
    	    "category" => "start-here",
            "id" => "start_here",
            "type" => "info",
			"lastoption" => "yes", 
            "info" => "<br />Since 3.6.6: <ul><li>Import settings by copying &amp; pasting the content of the settings file into the textarea in the theme options -  instead of uploading the settings file directly.</li>
			<li>PHP code cannot be put into the textareas in the theme options anymore.</li>
			</ul>
			This is due to changed rules for themes listed at wordpress.org (No file operations, no processing of dynamic PHP code).
			<h3 class='infohighlight-header'>Join for free:</h3>
			<div class='infohighlight'>
			<img src=\"" . $templateURI . "/images/comment_icon.png\" style=\"float: left; margin: 0px 10px 5px 0;\">

			<a href='http://forum.bytesforall.com/register.php'>Become a member</a> of our active <a href='http://forum.bytesforall.com/'>Forum</a> and discuss Atahualpa with 1000's of other Atahualpa users.   
			Get new styles and language files, ask or answer questions and more.</div> 
			<h3 class='infohighlight-header'>Please donate if you can:</h3>
			<div class='infohighlight'>
			<img src=\"" . $templateURI . "/images/awardsmall.gif\" style=\"float: left; margin: 0px 10px 5px 0;\">				
			<a href='http://forum.bytesforall.com/awc_ds.php?do=donation'>Please donate</a> to support the Atahualpa/BytesForAll team. Your financial help will serve to maintain, 
			improve and support Atahualpa. <strong>Thank you</strong> <img src=\"" . $templateURI . "/images/heart.png\" style=\"\">
			&nbsp; <em>BytesForAll Team</em>
			</div>
			<h3 class='infohighlight-header'>Or go even further:</h3>
			<div class='infohighlight'>
			<img src=\"" . $templateURI . "/images/award1small.gif\" style=\"float: left; margin: 0px 10px 5px 0;\">
			<img src=\"" . $templateURI . "/images/diamondsmall.gif\" style=\"float: left; margin: 0px 10px 5px 0;\">
			<a href='http://forum.bytesforall.com/awc_ds.php?do=donation'>Donate $20</a> or more to become a 
			<strong>Gold</strong> or <strong>Diamond</strong> member  
			for additonal benefits such as extra styles and tutorials and preferred 
			attention from developers and moderators.</div> 
			<br /><br />
			"),

// New category: Export/Import Settings

	array(	
	"name" 		=> "Export Atahualpa settings as file",
	"category" 	=> "export-import",
	"switch" 	=> "yes",
    "id" => "export_settings",
	"type" 		=> "info",
	"info" 		=> "<br />Export the current Atahualpa settings and download them as a text file. This text file can be imported into this or another 
					Atahualpa 3.4.7+ installation:<br /><br /><a class='button' href='" . $wordpress_base . "/?bfa_ata_file=settings-download' id='settings-download'><strong>Export &amp; Download</strong> Atahualpa Settings File</a>
					<br /><br />The file will be named <code>ata-" . str_replace('.','', $_SERVER['SERVER_NAME']) . "-" . date('Y') . date('m') . date('d') . ".txt</code>. After you downloaded it, 
					you can (but don't need to) rename the file to something more meaningful.
					"
),

	array(	
	"name" 		=> "Import Atahualpa settings <span style='text-decoration:line-through'>file</span>",
	"category" 	=> "export-import",
    "id" => "import_settings",
	"type" 		=> "info",
	"info" 		=> "<br /><span style='text-decoration:line-through'>Upload a Atahualpa settings file from your desktop computer and import it:</span><br />SINCE 3.6.5: <strong>Paste the content of a settings file here</strong> 
	into this textarea and click 'Import'<br /><br />
	PHP file functions cannot be used anymore in themes listed on wordpress.org, so we 
			had to remove file operations from Atahualpa (Upload isn't enough, Atahualpa needs to read the file, too). Instead of uploading a settings file you now need to copy the content of the settings file instead, and paste it into the textarea below, finally click 'Import Settings'.   
			<br />
				<br /><span style='color:red;font-weight:bold'>THIS WILL OVERLAY ANY EXISTING SETTINGS YOU ALREADY HAVE</span>
				<br /><textarea style='border:solid 1px black;' name='import-textarea' cols='80' rows='10' id='import-textarea'></textarea><br />
				<br /><a class='button' href='#' id='import-settings'><strong>Import Settings</strong></a>
				<br /><br />
					<div id='settingsimported'></div>
					<div class='infohighlight'>4 styles are included in the Atahualpa download, inside <code>/atahualpa/styles/: <code>ata-round.txt</code>, <code>ata-classic.txt</code>, <code>ata-default.txt</code> and  <code>ata-adsense.txt</code> 
					</code> To use one of them open the file, select all (Ctrl+A), copy (Ctrl+C) and paste it (Ctrl+V) here. (Different keys on MAC)</div>"
),

	array(	
	"name" 		=> "Delete the option 'bfa_ata4'",
	"category" 	=> "export-import",
    "id" => "delete_bfa_ata4",
	"type" 		=> "info",
	"lastoption" => "yes", 
	"info" 		=> "<div class='infohighlight'>Since 3.4.7, the theme options are stored in one single option named <code>bfa_ata4</code>. If you had issues with Atahulpa 3.4.7, 3.4.8 or 3.4.9 you may have a corrupted option <code>bfa_ata4</code> in the database.<br /><br /><span style='color:red;font-weight:bold'>WARNING</span> - Pressing this button <span style='color:red;font-weight:bold'>WILL RESET ALL THEME OTPTIONS</span> to their default</div>
					<br /><a class='button' href='#' id='delete_bfa_ata4'><strong>Delete 'bfa_ata4'</strong></a>, then reload a page on your site. This will create a new <code>bfa_ata4</code> entry in the database.<div id='bfa_ata4_deleted'></div>"
),

// New category: seo

    array(    "name" => "Use Post / Page Options?",
    	    "category" => "seo",
			"switch" => "yes",
            "id" => "page_post_options",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "This adds a few option fields to the 'Write' panel of Wordpress, allowing you to set different titles 
			for posts or pages based on where the title appears (Meta Tag, Body on single post pages, Body on multi post pages)
			<br /><br /><em>This used to be turned on by default in 3.4.6 and was removed in 3.4.7 - 3.4.9. It is an option that can be turned on 
			since 3.5.0.</em>"),
			
    array(    "name" => "Use Bytes For All SEO options?",
    	    "category" => "seo",
            "id" => "use_bfa_seo",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "<strong>Leave this at \"No\" if you're using ANY SEO plugin</strong> such as \"All-in-one-SEO\", 
			or any plugin that deals with meta tags in some way. If both a SEO plugin and Atahualpa's SEO functions are 
			activated, the meta tags of your site may get messed up, which might affect your search engine rankings. 
			<br /><br />If you leave this at \"No\", the next SEO options (except the last one, \"Nofollow RSS...\") 
			will become obsolete, you may just skip them. <br /><br /><em>Note: Even if you set this to \"Yes\", the
			SEO functions listed below (except \"Nofollow RSS...\" and \"Make Post Titles H1\") will NOT be activated 
			IF Atahualpa recognizes that a SEO plugin is activated.</em>"),

    array(    "name" => "Homepage Meta Description",
    	    "category" => "seo",
            "id" => "homepage_meta_description",
            "std" => "",
            "type" => "textarea-large",
			"escape" => "yes", 
            "info" => "Type 1-3 sentences, about 20-30 words total. Will be used
            as Meta Description for (only) the homepage. If left blank, no Meta
            Description will be added to the homepage.<br /><br />HTML: No<br />
			Single and double quotes: Yes"),    

    array(    "name" => "Homepage Meta Keywords",
    	    "category" => "seo",
            "id" => "homepage_meta_keywords",
            "std" => "",
            "type" => "textarea-large",
			"escape" => "yes", 
            "info" => "Type 5-30 words or phrases, separated by comma. Will be used as the Meta Keywords for (only) 
			the homepage. If left blank, no Meta Keywords will be added to the homepage.<br /><br />HTML: No<br />
			Single and double quotes: Technically, Yes, but search engines might object to it. Probably better to avoid 
			quotes here."),

    array(    "name" => "Meta Title Tag format",
    	    "category" => "seo",
            "id" => "add_blogtitle",
            "type" => "select",
            "std" => "Page Title - Blog Title",
            "options" => array("Page Title - Blog Title", "Blog Title -
            Page Title", "Page Title"),
            "info" => "Show the blog title in front of or after the page title,
            in the meta title tag of every page? Or, show only the page title?"),

    array(    "name" => "Meta Title Tag Separator",
    	    "category" => "seo",
            "id" => "title_separator_code",
            "type" => "select",
            "std" => "1",
            "options" => array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10",
            "11", "12", "13"),
            "info" => "If you chose to include the blog title in the meta title
            (the option above), choose here what to put <strong>between</strong>
            the page and the blog title (or vice versa):<br /><br /> 1<code> &#171;
			</code> &nbsp;  &nbsp;  2<code> &#187; </code> &nbsp;  &nbsp;
            3<code> &#58; </code> &nbsp;  &nbsp; 4<code>&#58; </code> &nbsp;  &nbsp;
            5<code> &#62; </code> &nbsp;  &nbsp;  6<code> &#60; </code> &nbsp; &nbsp;
            7<code> &#45; </code><br /><br />8<span style='background: white; padding: 0 15px'>&lsaquo;</span>  &nbsp;  &nbsp;
            9<span style='background: white; padding: 0 15px'>&#8250;</span> &nbsp;  &nbsp;  
			10<span style='background: white; padding: 0 15px'>&#8226;</span>
            &nbsp; &nbsp;  11<code> &#183; </code> &nbsp; &nbsp;  12<span style='background: white; padding: 0 15px'>
			&#151;</span> &nbsp; &nbsp;  13<code> &#124;&nbsp;</code>"),
 
    array(    "name" => "Noindex Date Archive Pages?",
    	    "category" => "seo",
            "id" => "archive_noindex",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Include meta tag \"noindex, follow\" into date based
            archive pages? The purpose is to keep search engines from spidering
            duplicate content from your site."),

    array(    "name" => "Noindex Category pages?",
    	    "category" => "seo",
            "id" => "cat_noindex",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Include meta tag \"noindex, follow\" into category pages?
            Same purpose as above."),

    array(    "name" => "Noindex Tag pages?",
    	    "category" => "seo",
            "id" => "tag_noindex",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Include meta tag \"noindex, follow\" into tag pages?
            Same purpose as above."),

    array(    "name" => "Make Post/Page Titles H1?",
    	    "category" => "seo",
            "id" => "h1_on_single_pages",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Make the Post/Page Titles H1 instead of H2, and the blog
            title H2 instead of H1, on Single Post Pages and static \"Page\"
            pages?<br /><em>This gives the actual content more weight
			on their dedicated pages and downplays the weight of the blog title.
            On all multi post pages (such as homepage, category pages, etc.),
            the post titles will still be H2 and the blog title will be H1.</em>"),
			
    array(    "name" => "Nofollow RSS, trackback & admin links?",
    	    "category" => "seo",
            "id" => "nofollow",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
			"lastoption" => "yes", 
            "info" => "Make RSS, trackback & admin links \"nofollow\"?
            Same purpose as above."),

// New category: body-font-links

    array(    "name" => "Body Style",
    	    "category" => "body-font-links",
			"switch" => "yes",
            "id" => "body_style",
            "std" => "font-family: tahoma, arial, sans-serif;\nfont-size: 0.8em;\ncolor: #000000;\nbackground: #ffffff;",
            "type" => "textarea-large",
            "info" => "The styles you set here will apply to everything that doesn't get its own style. <br /><br />
			<strong>Examples:</strong> <br /><br />Setting a background image for the body:<br /><code>background: 
			url(". $css_img_path ."images/backgr.gif) repeat top left;</code><br />To use your own image upload it to <code>
			". $css_img_path ."images/</code><br /><br />To put space above and below the layout:<br />
			<code>padding-top: 20px; padding-bottom: 20px;</code><br /><br />Set padding here, instead of margin on the 
			layout container (see tab \"Layout\") because that won't work for the bottom, in Internet Explorer."),
			
    array(    "name" => "Link Default Color",
    	    "category" => "body-font-links",
            "id" => "link_color",
            "std" => "666666",
            "type" => "text",
            "info" => "All hex color codes."),

    array(    "name" => "Link Hover Color",
    	    "category" => "body-font-links",
            "id" => "link_hover_color",
            "std" => "cc0000",
            "type" => "text",
            "info" => "Color of links when \"hovering\" over them with the mouse
            pointer. All hex color codes."),

    array(    "name" => "Link Default Decoration",
    	    "category" => "body-font-links",
            "id" => "link_default_decoration",
            "type" => "select",
            "std" => "none",
            "options" => array("none", "underline"),
            "info" => "Underline links or not, in their default state?"),

    array(    "name" => "Link Hover Decoration",
    	    "category" => "body-font-links",
            "id" => "link_hover_decoration",
            "type" => "select",
            "std" => "underline",
            "options" => array("underline", "none"),
            "info" => "When the mouse pointer hovers over a link, underline it or not?"),        

    array(    "name" => "Link Text Bold or Not",
    	    "category" => "body-font-links",
            "id" => "link_weight",
            "type" => "select",
            "std" => "bold",
            "options" => array("bold", "normal"),
			"lastoption" => "yes", 
            "info" => "Make link text bold or not?"),

// New category: layout

    array(    "name" => "Layout WIDTH and type (FLUID or FIXED)",
    	    "category" => "layout",
			"switch" => "yes",
            "id" => "layout_width",
            "std" => "99%",
            "type" => "text",
			"size" => "7",
            "info" => "This setting must contain either <code>%</code> (percent)
            or <code>px</code> after the number. <br /><br /><strong>Examples
            </strong><ul><li><code>990px</code> Fixed width of 990 pixels</li>
            <li><code>92%</code> Fluid width of 92%</li><li><code>100%</code>
            Fluid width spanning the whole browser viewport</li></ul>"),

    array(    "name" => "Layout MIN width",
    	    "category" => "layout",
            "id" => "layout_min_width",
            "std" => "",
            "type" => "text",
			"size" => "5",
            "info" => "OPTIONAL, and for FLUID layouts only: You may set a
            MINIMUM width (in pixels) for fluid layouts, to limit the resizing
            behaviour.<br /><br /><strong>Example:</strong> <code>770</code>"),

    array(    "name" => "Layout MAX width",
    	    "category" => "layout",
            "id" => "layout_max_width",
            "std" => "",
            "type" => "text",
			"size" => "5",
            "info" => "OPTIONAL, and for FLUID layouts only: You may set a
            MAXIMUM width (in pixels) for fluid layouts, to limit the resizing
            behaviour.<br /><br /><strong>Example:</strong> <code>1250</code>"),

	array(    "name" => "Layout Container Style",
    	    "category" => "layout",
            "id" => "layout_style",
            "std" => "padding: 0;",
            "type" => "textarea-large",
            "info" => "Style the layout container here. The layout container
            holds the whole page including header, sidebars, center column and
            footer. <ul><li>Don't use <code>margin</code> here. margin-left and
            margin-right are needed to center the layout container. There's
            also no real need for left/right margin. You can get space on the
            left and right of the layout with a layout-width such as 98%.
            And instead of <code>margin-top</code> and <code>margin-bottom</code>
            use padding on the body (see menu tab \"Body, Text & Links\")</li>
			<li>Left/Right padding must be set separately in the next option.
            It will be ignored (set to 0) here.</li></ul>
            <strong>Example:</strong><br /><br /><code>border: solid 2px #cccccc;
            <br />padding: 10px; /*This effectively only affects top/bottom
            padding */</code><br /><code>background: #ffffff;<br />-moz-border-radius:10px;
            <br />-khtml-border-radius: 10px;<br />-webkit-border-radius:10px;<br />
            border-radius: 10px;</code><br /><br />NOTE: The rounded corners
            won't be round in Internet Explorer."),

	array(    "name" => "Layout Container Padding Left/Right",
    	    "category" => "layout",
            "id" => "layout_style_leftright_padding",
            "std" => "0",
            "type" => "text",
			"size" => "4",
            "info" => "If you want left/right padding on the layout container,
            put the pixel value here. Atahualpa needs this as a separate style,
            in order to include it in the min/max width calculation.
            <strong>Example:</strong> <code>20</code>"),

	array(    "name" => "IE Document Type",
    	    "category" => "layout",
            "id" => "IEDocType",
            "type" => "select",
            "std" => "None",
            "options" => array("None", "EmulateIE7", "EmulateIE8", "IE8", "IE9", "Edge"),
            "lastoption" => "yes", 
            "info" => "Set this option to force Internet Explorer to use a particular rendering mode (supported by Internet Explorer 8 and newer)"),
            
// New category: favicon

	array(    "name" => "Favicon",
    	    "category" => "favicon",
			"switch" => "yes",
            "id" => "favicon_file",
            "std" => "new-favicon.ico",
            "type" => "text",
			"size" => "30",
			"lastoption" => "yes", 
            "info" =>  "<img src=\"" . $templateURI .
            "/options/images/favicon-locations.gif\" style=\"float: right;
            margin: 0 0 10px 10px;\">" . "Put the file name of the favicon here,
            i.e. <code>fff-sport_soccer.ico</code>. To use your own graphic,
            upload a <code>your-file-name.ico</code> to
            <strong>". $css_img_path ."images/favicon/</strong><br /><br />
            Leave blank to show no favicon.<br /><br />
            <em>If the icon doesn't show: In some browsers such as IE6 you might
            have to clear cache and history and restart the browser</em><br />
            <br /><em>1-favicon.ico - 44-favicon.ico are available as big
            .png files (up to 128x128) at
            <a href=\"http://www.icon-king.com/projects/nuvola/\">Nuvola Icon Set</a>
            if you want to create a matching logo.</em><br /><br />
            <em>NOTE: If you create your own favicon: Simply renaming a .gif,
            .png or jpg file won't work in Internet Explorer. <code>.ico</code>
            is an actual file format. Create a 32 bit .png (optional: with
            transparent background) and a size of 16x16 pixels and convert it
            into an <code>.ico</code> file with a software such as
            <a href=\"http://www.towofu.net/soft-e/\">@Icon Sushi</a></em>
            <img src=\"" . $templateURI . "/options/images/favicons.gif\"
            style=\"display: block; margin: 10px;\">"),

// New category: header

    array(    "name" => "Configure Header Area",
    	    "category" => "header",
			"switch" => "yes",
            "id" => "configure_header",
            "type" => "textarea-large",
            "editable" => "yes",
			"size" => "30",
            "std" => "%pages %logo %bar1 %image %bar2",
            "info" => "Choose from 10 header items to arrange a custom header area:
            <ul><li><code>%pages</code> - The horizontal drop down menu bar for
            \"Page\" pages</li><li><code>%page-center</code> - Like above but centered. 
			Note: There's no S in this tag. It's <strong>not</strong> %page<strong>S</strong>-center</li>
			<li><code>%page-right</code> - Like above but right-aligned. 
			Note: There's no S in this tag. It's <strong>not</strong> %page<strong>S</strong>-right</li>
			<li><code>%cats</code> - The horizontal drop down
            menu bar for categories</li><li><code>%cat-center</code> - Like above but centered. 
			Note: There's no S in this tag. It's <strong>not</strong> %cat<strong>S</strong>-center</li>
			<li><code>%cat-right</code> - Like above but right-aligned. 
			Note: There's no S in this tag. It's <strong>not</strong> %cat<strong>S</strong>-right</li>
			<li><code>%logo</code> - The logo area,
            including the logo icon, the blog title & description, the search
            box and the RSS/Email icons</li><li><code>%image</code> -
			The rotating (or static) header image with the (optional) opacity
            overlay left & right and an (optional) overlayed blog title & blog
            tagline</li><li><code>%bar1</code> - A horizontal bar, to be used as
            decoration on top, bottom of between header items. Can be used
            multiple times.</li><li><code>%bar2</code> - A second horizontal bar,
            that can be styled differently. Can be used multiple times.</li></ul>
            You can style and configure these header items individually further
            down on this page, and on the menu tabs <a href=\"javascript: myflowers.expandit('page-menu-bar-tab')\">Page Menu Bar</a> and
            <a href=\"javascript: myflowers.expandit('cat-menu-bar-tab')\">Category Menu Bar</a>. <br /><br />This section here is just for the
            overall configuration of the header area.<br /><br />List the header
            items you want to display, in the order you want to display them.
            <br /><br />Examples:<ul><li><code>%image %bar1 %logo %bar1 %pages</code>
            </li><li><code>%pages %image %cats</code></li>
			<li><code>%bar1 %logo %cats %bar2 %pages %bar1</code></li></ul>"),
		
	array(    "name" => "Logo Area: Styling",
    	    "category" => "header",
            "id" => "logoarea_style",
            "std" => "",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/logo-area.jpg\" style=\"float: right; 
			margin: 0 0 10px 10px;\">" . "Style the header's logo area. The logo area is the container that holds 
			the logo / logo icon, the blog title, the blog tagline, the search box and the RSS/Email icons. 
			The height of the logo area will be determined by its content. If you want more height, set the height here. 
			You can set the height, borders and the background. Avoid margin and padding for this container - it's a table.
			<br /><br /><strong>Example:</strong><br /><code>height: 150px;<br />background: #eeeeee;<br />
			border: solid 1px #000000;</code>"),

    array(    "name" => "Show Logo Image?",
    	    "category" => "header",
            "id" => "logo",
            "type" => "text",
            "std" => "logo.png",
            "info" => "Show a logo in the logo area? Leave blank to show no logo. To test this, put <code>huge-logo.gif</code> 
			here and set both \"Show Blog Title\" and \"Show Blog Tagline\" to \"No\" below. <br /><br />" . $logo_icon_text . 
			" Upload custom logo images to /atahualpa/images/."),

	array(    "name" => "Logo Image: Styling",
    	    "category" => "header",
            "id" => "logo_style",
            "std" => "margin: 0 10px 0 0;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/logo-style.gif\" style=\"float: right; 
			margin: 0 0 10px 10px;\">" . "Style the logo here, i.e. give it a border or move it around by applying margins.
			<br /><br /><strong>Example:</strong><br /><br /><code>margin: 30px 30px 30px 30px;</code>"),

    array(    "name" => "Show Blog Title?",
    	    "category" => "header",
            "id" => "blog_title_show",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "You can remove the blog title, i.e. if you want to have just a (bigger) graphical logo instead 
			of a small logo icon plus the blog title in HTML. If you set this to \"No\" you'll probably want to remove 
			the Blog Tagline as well (see below)"),
			
    array(    "name" => "Blog Title",
    	    "category" => "header",
            "id" => "blog_title_style",
            "std" => "margin: 0;\npadding: 0;\nletter-spacing: -1px;\nline-height: 1.0em;\nfont-family: tahoma, arial, sans-serif;\nfont-size: 240%;",
            "type" => "textarea-large",
            "info" => "Style the blog title font except the color and font-weight (= next options)."),

    array(    "name" => "Blog Title: Font Weight",
    	    "category" => "header",
            "id" => "blog_title_weight",
            "type" => "select",
            "std" => "bold",
            "options" => array("bold", "normal"),
            "info" => "Make blog title bold or not."),
			
    array(    "name" => "Blog Title Color",
    	    "category" => "header",
            "id" => "blog_title_color",
            "std" => "666666",
            "type" => "text",
            "info" => "The blog title default color."),
			
    array(    "name" => "Blog Title Color: Hover",
    	    "category" => "header",
            "id" => "blog_title_color_hover",
            "std" => "000000",
            "type" => "text",
            "info" => "The blog title hover color."),

    array(    "name" => "Show Blog Tagline?",
    	    "category" => "header",
            "id" => "blog_tagline_show",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "You can remove the blog tagline here. The blog tagline is
            the short blog description under the blog title. It can be set at
            Settings -> General -> Tagline."),
			
    array(    "name" => "Blog Tagline",
    	    "category" => "header",
            "id" => "blog_tagline_style",
            "std" => "margin: 0;\npadding: 0;\nfont-size: 1.2em;\nfont-weight: bold;\ncolor: #666666;",
            "type" => "textarea-large",
            "info" => "Style the blog tagline."),
			
    array(    "name" => "Show search box?",
    	    "category" => "header",
            "id" => "show_search_box",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "You can remove the search box from the header here.<br />
            <br /><em>To put a search box into one of the sidebars, go to
            Site Admin -> " . $path_to_widgets . " -> Widgets, and add the \"Search\"
			widget to one of the sidebars.</em>"),

	array(    "name" => "Search box",
    	    "category" => "header",
            "id" => "searchbox_style",
            "std" => "border: 1px dashed #cccccc;\nborder-bottom: 0;\nwidth: 200px;\nmargin: 0;\npadding: 0;",
            "type" => "textarea-large",
            "info" => "Style the searchbox in the header."),

	array(    "name" => "Text in header search box",
    	    "category" => "header",
            "id" => "searchbox_text",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "Show pre-filled text in the header search box, such as
            <code>Type + Enter to search</code> ?"),

    array(    "name" => "Horizontal Bar 1: Styling",
    	    "category" => "header",
            "id" => "horbar1",
            "std" => "height: 5px;\nbackground: #ffffff;\nborder-top: dashed 1px #cccccc;",
            "type" => "textarea-large",
            "info" => "2 (empty) horizontal bars are available, both of which you can style differently and use once or
			multiple times as additional styling elements for the header area. These bars will span the whole layout width. 
			You can style their background color, height and all 4 borders (top, right, bottom, left)."),

    array(    "name" => "Horizontal Bar 2: Styling",
    	    "category" => "header",
            "id" => "horbar2",
            "std" => "height: 5px;\nbackground: #ffffff;\nborder-bottom: dashed 1px #cccccc;",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "Style the 2nd horizontal bar here. You can use each one of these bars multiple times (or not at all)."),
			
// New category: header-image

    array(    "name" => "Header Images",
    	    "category" => "header-image",
			"switch" => "yes",
            "id" => "header_image_info",
            "type" => "info",
            "info" => "<br />All header images are located in <code>". $css_img_path ."images/header/</code>. All images in that directory will
			be rotated. If you don't want rotating header images, leave only one image in that directory. <ul><li>If you 
			chose a fixed width layout, the image(s) should be as wide as your <a href=\"javascript: myflowers.expandit('layout-tab')\">layout width</a>.</li><li>If you chose a fluid layout, 
			the images should be as wide as your \"max width\" setting.</li><li>If you chose no \"max-width\" setting, your 
			images should be as wide as the widest screen resolution (of your visitors) you want to cater for. 1280 pixels is 
			common today, so the images should be that wide or wider. The next common screen widths are 1440, 1600, 1680 and 
			1920 pixels. </li></ul>" . $header_image_text),

    array(    "name" => "Rotate header images with Javascript?",
    	    "category" => "header-image",
            "id" => "header_image_javascript",
            "type" => "select",
            "std" => "0",
            "options" => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30),
            "info" => "Select amount of seconds between rotation. Set to 0 to not use Javascript for rotation. In this case  
			a new random image from <code>". $css_img_path ."images/header/</code> will be used on each page view, but the images won't rotate while the page is being viewed."),

    array(    "name" => "Sort or Shuffle header images?",
    	    "category" => "header-image",
            "id" => "header_image_sort_or_shuffle",
            "type" => "select",
            "std" => "Sort",
            "options" => array("Sort", "Shuffle"),
            "info" => "Setting this to <strong>Sort</strong> will sort the images by filename. Selecting <strong>Shuffle</strong> will randomly present the images."),

    array(    "name" => "Fade in/out header images with Javascript?",
    	    "category" => "header-image",
            "id" => "crossslide_fade",
            "type" => "select",
            "std" => "0",
            "options" => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30),
            "info" => "Select duration of fade effect in seconds. Set to 0 to not fade images. In this case  
			the images will be rotated abruptly."),

    array(    "name" => "Preload header images for Javascript rotation?",
    	    "category" => "header-image",
            "id" => "header_image_javascript_preload",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Preload the header images for Javascript rotation? Should probably be set to \"No\" if you have many header 
			images."),
			
    array(    "name" => "Make header image clickable?",
    	    "category" => "header-image",
            "id" => "header_image_clickable",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Select \"Yes\" to make the header image clickable and to link it to the homepage."),

    array(    "name" => "Header Image: Height",
    	    "category" => "header-image",
            "id" => "headerimage_height",
            "std" => "150",
            "type" => "text",
			"size" => "5",
            "info" => "<img src=\"" . $templateURI . "/options/images/header-image-height.jpg\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Visible height of the header image(s), <strong>in pixels</strong>. 
			Change this value to show a taller or less tall area of the header image(s). <br /><br /><em>This value 
			does not need to match the actual height of your header image(s). In fact, all your header images could 
			have different (actual) heights. Only the top XXX (= value that you set here) pixels of each image will 
			be shown, the rest will be hidden. </em>"),

    array(    "name" => "Header Image: Alignment",
    	    "category" => "header-image",
            "id" => "headerimage_alignment",
            "type" => "select",
            "std" => "top center",
            "options" => array("top center", "top left", "top right", "center left", "center center", "center right", 
			"bottom left", "bottom center", "bottom right"),
            "info" => "The aligned edge or end of the image will be the fixed part, and the image will be cut off from 
			the opposite edge or end if it doesn't fit into the visitor's browser viewport. <br /><br />
			<strong>Example:</strong> If you choose \"Top Left\" as the alignment, then the image(s) will be cut off 
			from the opposite edge, which would be \"Bottom Right\" in this case."),

    array(    "name" => "Opacity LEFT: Value",
    	    "category" => "header-image",
            "id" => "header_opacity_left",
            "std" => "40",
            "type" => "select",
			"options" => array("0", "5", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55", "60", "65", 
			"70", "75", "80", "85", "90", "95"),
            "info" => "<img src=\"" . $templateURI . "/options/images/opacity.jpg\" style=\"float: right; 
			margin: 0 0 10px 10px;\">" . "Opacity overlay for the LEFT hand side of the header image. 
			Choose 0 to remove the Opacity."),

    array(    "name" => "Opacity LEFT: Width",
    	    "category" => "header-image",
            "id" => "header_opacity_left_width",
            "std" => "200",
            "type" => "text",
			"size" => "5",
            "info" => "<img src=\"" . $templateURI . "/options/images/opacity-left-width.jpg\" style=\"float: right; 
			margin: 0 0 10px 10px;\">" . "Width of the Opacity overlay for the LEFT hand side of the header image, 
			<strong>in pixels</strong>. To match this to the left sidebar's width, add up the left sidebar's width plus 
			its left and right paddings, if you've set any."),

    array(    "name" => "Opacity LEFT: Color",
    	    "category" => "header-image",
            "id" => "header_opacity_left_color",
            "std" => "ffffff",
            "type" => "text",
            "info" => "Color of the Opacity overlay for the LEFT hand side of the header image."),
			
    array(    "name" => "Opacity RIGHT: Value",
    	    "category" => "header-image",
            "id" => "header_opacity_right",
            "std" => "40",
            "type" => "select",
			"options" => array("0", "5", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55", "60", "65", 
			"70", "75", "80", "85", "90", "95"),
            "info" => "<img src=\"" . $templateURI . "/options/images/opacity.jpg\" style=\"float: right; 
			margin: 0 0 10px 10px;\">" . "Opacity overlay for the RIGHT hand side of the header image. 
			Choose 0 to remove the Opacity."),

    array(    "name" => "Opacity RIGHT: Width",
    	    "category" => "header-image",
            "id" => "header_opacity_right_width",
            "std" => "200",
            "type" => "text",
			"size" => "5",
            "info" => "<img src=\"" . $templateURI . "/options/images/opacity-right-width.jpg\" style=\"float: right; 
			margin: 0 0 10px 10px;\">" . "Width of the Opacity overlay for the RIGHT hand side of the header image, 
			<strong>in pixels</strong>. To match this to the right sidebar's width, add up the left sidebar's width plus 
			its left and right paddings, if you've set any."),

    array(    "name" => "Opacity RIGHT: Color",
    	    "category" => "header-image",
            "id" => "header_opacity_right_color",
            "std" => "ffffff",
            "type" => "text",
            "info" => "Color of the Opacity overlay for the RIGHT hand side of the header image."),

    array(    "name" => "Overlay Blog TITLE over Header Image(s)?",
    	    "category" => "header-image",
            "id" => "overlay_blog_title",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "An alternative location for the blog title."),

    array(    "name" => "Overlay Blog TAGLINE over Header Image(s)?",
    	    "category" => "header-image",
            "id" => "overlay_blog_tagline",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "An alternative location for the blog tagline."),

    array(    "name" => "Overlayed Blog Title/Tagline Style",
    	    "category" => "header-image",
            "id" => "overlay_box_style",
            "std" => "margin-top: 30px;\nmargin-left: 30px;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/header-overlay.jpg\" style=\"float: right; 
			margin: 0 0 10px 10px;\">" . "The overlayed blog title and blog tagline will be in a div container. 
			Move that container around by changing the <code>margin-top</code> and <code>margin-left</code> values. 
			To right-align the overlayed container, add <code>float: right;</code> and replace <code>margin-left</code> 
			with <code>margin-right</code>. To center it, add <code>float:none; margin-left:auto; margin-right:auto; 
			text-align:center;</code> and, instead of adding margin-top here, add padding-top to 
			the parent container, via <a href=\"javascript: myflowers.expandit('html-inserts-tab')\">HTML/CSS Inserts</a> -> CSS Inserts: 
			<code>div.header-image-container { padding-top: 30px; height: XXXpx; }</code> with XXX = desired image height - 
			padding-top value.<br />
			<br />You can add background color, borders and padding, too.
			<br /><br /><strong>Example (as shown in the image):</strong><br /><br /><code>margin-top: 30px;<br />margin-left: 30px;<br />
			width: 300px;<br />padding: 7px;<br />background: #ffffff;<br />border: solid 2px #000000;<br />
			filter: alpha(opacity=60);<br />-moz-opacity:.60;<br />opacity:.60;<br />-moz-border-radius: 7px;<br />
			-khtml-border-radius: 7px;<br />-webkit-border-radius: 7px;<br />border-radius: 7px;</code><br /><br />
			Leave <code>width: ...;</code> out to let the box adjust to the width of the blog title or tagline, whichever is longer.<br />
			<br />To change the styles of the blog title or the blog tagline individually, see the menu tab \"Header\"."),        

    array(    "name" => "<span style='background:white;color:red'>NEW</span> Overlay Header Image",
    	    "category" => "header-image",
            "id" => "overlay_header_image",
            "std" => "",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "The Overlay Header Image area allows you to put in HTML which will overlay the header image. You could use this code to put buttons for links on top of the header image. 
			<span style='text-decoration:line-through'>You can also mix in PHP code in this area. This would allow you to point to an image in the wp-content folder and not have to worry about it's location during theme upgrades.</span><br /><br />
			For example, suppose you want to put buttons to link to your Twitter and FaceBook sites. You put the images (facebook.jpg and twitter.jpg) in a folder in wp-contents ('wp-content/my-images'). 
			You could add the following to this option<br /><br />
			<code>&lt;div id=\"header_image_sociable\"&gt;<br />&nbsp;&nbsp;&lt;ul&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href=\"http://www.facebook.com/myid\"&gt;<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;img src=\"<span style='text-decoration:line-through'>&lt;?php echo site_url(); ?&gt;</span>/wp-content/images/facebook.jpg\" alt=\"Facebook\" /&gt;&lt;/a&gt;&lt;/li&gt;<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href=\"http://www.twitter.com/myid\"&gt;<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;img src=\"<span style='text-decoration:line-through'>&lt;?php echo site_url(); ?&gt;</span>/wp-content/images/twitter.jpg\" alt=\"Twitter\" /&gt;&lt;/a&gt;&lt;/li&gt;<br />
			&nbsp;&nbsp;&lt;/ul&gt;<br />&nbsp;&lt;/div&gt;</code>
			<br /><br />Since 3.6.5 <span style='color:red'>PHP code cannot be used</span> anymore. The sample code above should still work, with the striked-through PHP code removed, if your WP installation is in the root of your domain. If it's in a subdirectory such as blog, 
			the image paths would begin with <code>/blog/wp-content/...</code> instead of <code>/wp-content/</code>
			<br /><br />If you wanted to remove the bullets from the items and position the items on the right side of the image, you could put the following in the CSS Inserts<br /><br /><code>#header_image_sociable {position: absolute; right:40px; top: 20px;}<br />#header_image_sociable ul {list-style-type: none;}</code>"),        

// New category: feed-links

    array(    "name" => "RSS settings",
    	    "category" => "feed-links",
			"switch" => "yes",
            "id" => "rss_settings_info",
            "type" => "info",
            "info" => "<br />Choose from 4 types of RSS links:<ul><li>Subscribe to the Posts feed</li><li>Subscribe to 
			the Comments feed</li><li>Subscribe by Email, via Feedburner</li><li>Subscribe to the comments of a single
			post</li></ul>There are 4 different locations to place these RSS links:<ul><li>On the right hand side of 
			the logo area: Small buttons and/or text links.<br />Configuration: Menu tab \"RSS Settings\" (the page you're 
			looking at right now)</li><li>In a sidebar, via the widget \"BFA Subscribe\". Bigger buttons and text, plus
			a Email form field.<br />Configuration: WP Admin -> Appearance (\"Design\" or \"Presentation\" in older WP versions) -> <a href='widgets.php'>Widgets</a>
			-> BFA Subscribe.</li><li>In the footer area: Text links.<br />Configuration: Menu tab 
			<a href=\"javascript: myflowers.expandit('footer-style-tab')\">Style & edit FOOTER</a> -> Footer: Content.</li><li>
			Above or below a post: Buttons and/or Text links<br />Configuration: Menu tab 
			'Edit POST/PAGE INFO ITEMS' -> any of the 12 text areas.</li></ul>
			After you've configured everything...<ul><li>... the \"Subscribe by Email\" link will go to Feedburner</li><li>... 
			the \"Subscribe by Email\" form field will be submitted to Feedburner</li><li>... but all Posts and Comments
			Feed links will still go to the default WordPress RSS links</li></ul><strong>If you want to redirect all the 
			Posts and Comments RSS links to Feedburner, you will need to install the
			<a href=\"http://www.google.com/support/feedburner/bin/answer.py?answer=78483&topic=13252\">Feedburner Feedsmith</a> 
			plugin.</strong> <em>Note: When copying and pasting your Feedburner feed URL from your Feedburner account into 
			the options page of Feedsmith here on your blog, make sure there is <strong>no space</strong> at the end of the 
			URL or Feedburner account name.</em><br /><br />Atahualpa does not send RSS subscribers (other than Email subscribers) 
			straight away to Feedburner, because...<ul><li>... that would not cover existing subscribers</li><li>... 
			Feedsmith is a global solution that covers each and any RSS link on your site, including those a third party 
			plugin may add</li><li>... with a Feedsmith redirection readers subscribe to the URLs that you control 
			(http://www.yourdomain.com/feed/). Should you ever want to stop using Feedburner, simply uninstall the Feedsmith plugin 
			and you'll keep all your subscribers - they just won't be redirected anymore.</li></ul>"),

	array(    "name" => "RSS Box Width",
    	    "category" => "feed-links",
            "id" => "rss_box_width",
            "std" => "280",
            "type" => "text",
            "info" => "<img src=\"" . $templateURI . "/options/images/rss-box.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"Give the box containing the RSS buttons/links a fixed width, <strong>in pixels</strong>, to keep them in 
			one line, to avoid early wrapping. You shouldn't make this wider than needed for the given content."),		
			
    array(    "name" => "Show Post Feed icon?",
    	    "category" => "feed-links",
            "id" => "show_posts_icon",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "<img src=\"" . $templateURI . "/options/images/show_posts_icon.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"Show the Post RSS Feed icon on the right hand side of the logo area?"),

	array(    "name" => "Post Feed Link Text",
    	    "category" => "feed-links",
            "id" => "post_feed_link",
            "std" => __("Posts","atahualpa"),
            "type" => "text",
            "info" => "<img src=\"" . $templateURI . "/options/images/post_feed_link.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"Leave blank to show no Post Feed Text Link in the logo area."),		

	array(    "name" => "Post Feed Link \"Title\"",
    	    "category" => "feed-links",
            "id" => "post_feed_link_title",
            "std" => __("Subscribe to the POSTS feed","atahualpa"),
            "type" => "text",
			"size" => "30", 
            "info" => "<img src=\"" . $templateURI . "/options/images/post_feed_link_title.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"This is not the link anchor text (that was one option above), but the link \"title\", a text that will pop up when the mouse points at the link."),		

    array(    "name" => "Show Comment Feed icon?",
    	    "category" => "feed-links",
            "id" => "show_comments_icon",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "<img src=\"" . $templateURI . "/options/images/show_comments_icon.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" .
			"Show the Comment RSS Feed icon on the right hand side of the logo area?"),

	array(    "name" => "Comment Feed Link Text",
    	    "category" => "feed-links",
            "id" => "comment_feed_link",
            "std" => __("Comments","atahualpa"),
            "type" => "text",
            "info" => "<img src=\"" . $templateURI . "/options/images/comment_feed_link.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" .
			"Leave blank to show no Comment Feed Text Link in the logo area."),

	array(    "name" => "Comment Feed Link \"Title\"",
    	    "category" => "feed-links",
            "id" => "comment_feed_link_title",
            "std" => __("Subscribe to the COMMENTS feed","atahualpa"),
            "type" => "text",
			"size" => "30", 
            "info" => "<img src=\"" . $templateURI . "/options/images/comment_feed_link_title.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" .
			"This is not the link anchor text (that was one option above), but the link \"title\", a text that will pop up when the mouse points at the link."),		

    array(    "name" => "Show Feedburner Email icon?",
    	    "category" => "feed-links",
            "id" => "show_email_icon",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "<img src=\"" . $templateURI . "/options/images/show_email_icon.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"Show a Feedburner \"Subscribe by Email\" icon on the right hand side of the logo area?"),

	array(    "name" => "Feedburner Email Link Text",
    	    "category" => "feed-links",
            "id" => "email_subscribe_link",
            "std" => __("By Email","atahualpa"),
            "type" => "text",
            "info" => "<img src=\"" . $templateURI . "/options/images/email_subscribe_link.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"Leave blank to show no \"Subscribe by Email\" Text Link in the logo area"),		

	array(    "name" => "Feedburner Email Link \"Title\"",
    	    "category" => "feed-links",
            "id" => "email_subscribe_link_title",
            "std" => __("Subscribe by EMAIL","atahualpa"),
            "type" => "text",
			"size" => "30", 
            "info" => "<img src=\"" . $templateURI . "/options/images/email_subscribe_link_title.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"This is not the link anchor text (that was one option above), but the link \"title\", a text that will pop up when the mouse points at the link."),		
			
    array(    "name" => "Feedburner ID for this site?",
    	    "category" => "feed-links",
            "id" => "feedburner_email_id",
            "type" => "text",
            "std" => "",
			"size" => "25", 
            "info" => "If you chose to show the Feedburner \"Subscribe by Email\" link, put the ID of the Feedburner feed for this site here. 
			<br /><br />The ID will be a number (around 7 digits) if you have an OLD account at feedburner.com. If you have a NEW account 
			at feedburner.google.com, the ID will not be a number but a string that probably resembles your site name but without spaces.
			<br /><br />Log in your Feedburner account, click \"My Feeds\" -> \"[Title of the feed/site in question]\" 
			-> \"Publicize\" -> \"Email Subscriptions\". (If you have not activated the Email subscription yet do it now and proceed with the 
			next step afterwards). Now check out the two textareas.<br /><br /><strong>If you have a feedburner.google.com account:</strong> 
			The smaller one of the two textareas, the one at the bottom, will contain something like this:  
			<code>feedburner.google.com/fb/a/mailverify?uri=<i>bytesforall/lzoG</i>&amp;loc=en_US</code> The highlighted 
			text is your Google/Feedburner ID. Note: <strong>bytesforall/lzoG</strong> will NOT be your ID. This is just a sample to show you where the 
			ID starts and where it ends. It starts after <code>?uri=</code> and it ends before <code>&amp;loc=</code><br /><br /><strong>
			If you have an (old, original) feedburner.com account:</strong> With an old feedburner account, that is not transferred to google yet, 
			the smaller one of the two textareas, the one at the bottom, will contain something like this: 
			<code>www.feedburner.com/fb/a/emailverifySubmit?feedId=<i>1234567</i>&amp;loc=en_US</code> The highlighted number is 
			your (old, original) Feedburner.com ID. Note: <strong>1234567</strong> will NOT be your ID. This is just a sample to show you where the 
			ID starts and where it ends. It starts after <code>?feedId=</code> and it ends before <code>&amp;loc=</code><br /><br />	
			Now that you got your (new Google/Feedburner OR old Feedburner.com) ID put it into this field here"),

    array(    "name" => "OLD or NEW Feedburner account?",
    	    "category" => "feed-links",
            "id" => "feedburner_old_new",
            "type" => "select",
            "std" => "New - at feedburner.google.com",
			"lastoption" => "yes", 
            "options" => array("New - at feedburner.google.com", "Old - at feedburner.com"),
            "info" => "Whether your account qualifies as old or new does not depend on whether you log in at feedburner.com or at feedburner.google.com. 
			See one option above to determine whether your account is OLD or NEW."),

// New category: page-menu-bar

    array(    "name" => "Animate Page Menu Bar",
    	    "category" => "page-menu-bar",
			"switch" => "yes",
            "id" => "animate_page_menu_bar",
            "std" => "No",
            "type" => "select",
            "options" => array("No", "Yes"),
            "info" => "Animate the page menu bar with Javascript?"),
			
    array(    "name" => "Home link in Page Menu Bar",
    	    "category" => "page-menu-bar",
            "id" => "home_page_menu_bar",
            "std" => __("Home","atahualpa"),
            "type" => "text",
            "info" => "<ul><li>Leave this blank to have no \"Home\" link in the page menu bar</li><li>Or, put text here 
			to include a link to your homepage into the page menu bar</li><li>The text doesn't have to be \"Home\", it 
			can be anything</li></ul>"),

    array(    "name" => "Exclude pages from Page Menu Bar?",
    	    "category" => "page-menu-bar",
            "id" => "exclude_page_menu_bar",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "<ul><li>Leave blank to include all pages in the page menu bar</li><li>To exclude certain pages from the 
			page menu bar, put their ID's into this field, separated by comma</li></ul><strong>Example:</strong> <code>13,29,102,117</code>
			<br /><br />To get the ID of a page, go to WP Admin -> Pages -> <a href=\"edit-pages.php\">Edit</a>, point your mouse at the title of the page 
			in question, and watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&post=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the page."),

    array(    "name" => "Depth of Page Menu Bar",
    	    "category" => "page-menu-bar",
            "id" => "levels_page_menu_bar",
            "std" => "0",
            "type" => "select",
            "options" => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
            "info" => "<ul><li>Choose 0 to include ALL levels of pages (top level, sub pages, sub sub pages...) in the page 
			menu bar</li><li>Choose a number between 1 and 10 to include only the respective amount of page levels</li></ul>"),

    array(    "name" => "Sorting order of Page Menu Bar",
    	    "category" => "page-menu-bar",
            "id" => "sorting_page_menu_bar",
            "type" => "select",
            "std" => "menu_order",
            "options" => array("menu_order", "post_title"),
            "info" => "<ul><li><code>menu_order</code> - Sort the pages chronologically, as you created them (Change the page 
			order at Manage -> Pages -> Click on page title -> Page Order)</li><li><code>post_title</code> - alphabetically</li></ul>"),

    array(    "name" => "Title tags in Page Menu Bar",
    	    "category" => "page-menu-bar",
            "id" => "titles_page_menu_bar",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Include a \"title\" tag for each item in the page menu? These will pop up when hovering over a menu item."),

    array(    "name" => "Don't link first level parent items in Page Menu Bar?",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_1st_level_not_linked",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Set this to No to replace the link target of first level parent items with &lt;a href=\"#\"&gt; 
			This will basically unlink the first level menu items that have children. The submenus drop down when pointing 
			the mouse at the link, but clicking the link doesn't do anything. This is for users that want to organize their 
			pages in main pages and sub pages but don't have content on the main pages. \"Yes\" means \"Yes, don't link\"."),
			
    array(    "name" => "Border around all menu items",
    	    "category" => "page-menu-bar",
            "id" => "anchor_border_page_menu_bar",
            "std" => "dashed 1px #cccccc",
            "type" => "text",
            "info" => "Every item of the menu bar, plus the menu bar itself, will be wrapped into this border. 
			To have no borders in the first level, give it the same color as the background color for first level items. 
			Don't use semicolons here.<br /><br />Note: Leave the border width at 1px, match colors if you want to make 
			it dissapear."),

    array(    "name" => "Background color",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_bar_background_color",
            "std" => "ffffff",
            "type" => "text",
            "info" => "Background color for menu items in their default state and the menu bar itself."),


    array(    "name" => "Background color: Hover",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_bar_background_color_hover",
            "std" => "eeeeee",
            "type" => "text",
            "info" => "Background color for menu items in hover and current state. <strong>Use fffffe instead of ffffff</strong> due to a \"White Background Hover Bug\" in IE7/IE8. http://haslayout.net/css/Hover-White-Background-Ignore-Bug. "),

    array(    "name" => "Background color: Parent",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_bar_background_color_parent",
            "std" => "dddddd",
            "type" => "text",
            "info" => "Background color for parent menu item while hovering over its sub menu. <strong>Use fffffe instead of ffffff</strong> due to a \"White Background Hover Bug\" in IE7/IE8. http://haslayout.net/css/Hover-White-Background-Ignore-Bug."),

    array(    "name" => "Font Size &amp; Face",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_font",
            "std" => "11px Arial, Verdana, sans-serif",
            "type" => "text",
			"size" => "30", 
			"editable" => "yes", 
            "info" => "Set both the font size and the font face for the menu items. Enclose font face names with a 
			space in quotes, i.e.:<br /><code>12px \"comic sans ms\", \"courier new\", arial, sans-serif</code><br />
			<br />Don't use semicolons here."),

    array(    "name" => "Link Color",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_bar_link_color",
            "std" => "777777",
            "type" => "text",
            "info" => "Color of the link text."),

    array(    "name" => "Link Color: Hover",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_bar_link_color_hover",
            "std" => "000000",
            "type" => "text",
            "info" => "Color of the link text in hover state."),
	
    array(    "name" => "Transform text in Page Menu Bar?",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_transform",
            "type" => "select",
            "std" => "uppercase",
            "options" => array("uppercase", "lowercase", "capitalize", "none"),
            "info" => "You can transform the link titles in the page menu bar."),

    array(    "name" => "White or Black Arrows as Sub Menu Indicator?",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_arrows",
            "type" => "select",
            "std" => "black",
            "options" => array("black", "white"),
            "info" => "If a menu item has sub menus, it will be indicated with down/right arrows. Choose the color for these arrows."),

    array(    "name" => "Width of Sub Menus",
    	    "category" => "page-menu-bar",
            "id" => "page_menu_submenu_width",
            "type" => "select",
            "std" => "11",
			"lastoption" => "yes", 
			"options" => array("7", "7.5", "8", "8.5", "9", "9.5", "10", "10.5", "11", "11.5", "12", "12.5", "13", 
			"13.5", "14", "14.5", "15", "15.5", "16", "16.5", "17", "17.5", "18", "18.5", "19", "19.5", "20", "20.5", 
			"21", "21.5", "22", "22.5", "23", "23.5", "24", "24.5", "25"),
            "info" => "The width of top level items will adjust to the width of the links inside, but the sub menus 
			need a defined width, <strong>in \"em\"</strong>."),
			
// New category: cat-menu-bar

    array(    "name" => "Animate Category Menu Bar",
    	    "category" => "cat-menu-bar",
			"switch" => "yes",
            "id" => "animate_cat_menu_bar",
            "std" => "No",
            "type" => "select",
            "options" => array("Yes", "No"),
            "info" => "Animate the category menu bar with Javascript?"),
			
    array(    "name" => "Home link in Category Menu Bar",
    	    "category" => "cat-menu-bar",
            "id" => "home_cat_menu_bar",
            "std" => "",
            "type" => "text",
            "info" => "<ul><li>Leave this blank to have no \"Home\" link in the category menu bar</li><li>Or, put text 
			here to include a link to your homepage into the category menu bar</li><li>The text doesn't have to be \"Home\", 
			it can be anything</li></ul>"),

    array(    "name" => "Exclude categories from Category Menu Bar?",
    	    "category" => "cat-menu-bar",
            "id" => "exclude_cat_menu_bar",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "<ul><li>Leave blank to include all categories in the category menu bar</li><li>To exclude certain 
			categories put their ID into this field, separated by comma</li></ul><strong>Example:</strong> <code>13,29,102,117</code>
			<br /><br />To get the ID of a category, go to WP Admin -> Posts -> <a href=\"categories.php\">Categories</a>, point your mouse at the 
			title of the category in question, and watch your browser's status bar (it's at the bottom) for an URL ending 
			on \"...action=edit&cat_ID=<strong>XX</strong>\". <strong>XX</strong> is the ID of the category."),

    array(    "name" => "Depth of Category Menu Bar",
    	    "category" => "cat-menu-bar",
            "id" => "levels_cat_menu_bar",
            "std" => "0",
            "type" => "select",
            "options" => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
            "info" => "<ul><li>Choose 0 to include ALL levels of categories (top level, sub cats, sub sub cats...) in the 
			category menu bar</li><li>Choose a number between 1 and 10 to include only the respective amount of category levels</li></ul>"),

    array(    "name" => "Sort Category Menu Bar by:",
    	    "category" => "cat-menu-bar",
            "id" => "sorting_cat_menu_bar",
            "type" => "select",
            "std" => "ID",
            "options" => array("ID", "name", "count", "order"),
            "info" => '<ul><li><code>ID</code> - Sort the categories chronologically, as you created them</li>
			<li><code>name</code> - alphabetically</li><li><code>count</code> - by number of posts</li>
			<li><code>order</code> - individually, as set on the options page of the plugin 
			<a href="http://wordpress.org/extend/plugins/my-category-order/">My Category Order</a> (requires this 
			plugin to be installed)</li></ul>'),

    array(    "name" => "Sorting order Category Menu Bar",
    	    "category" => "cat-menu-bar",
            "id" => "order_cat_menu_bar",
            "type" => "select",
            "std" => "ASC",
            "options" => array("ASC", "DESC"),
            "info" => "Sort categories in ascending (ASC) or descending (DESC) order?"),		
			
    array(    "name" => "Title tags in Category Menu Bar",
    	    "category" => "cat-menu-bar",
            "id" => "titles_cat_menu_bar",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Include a \"title\" tag for each item in the category menu bar? Title tags are the little 
			boxes that pop up when hovering over a menu item. Setting this to yes makes sense if you've set a 
			\"description\" for each category (Manage -> Categories -> Click on category name). Otherwise the 
			title tag will just repeat the category name."),

    array(    "name" => "Add category description to menu bar tabs",
    	    "category" => "cat-menu-bar",
            "id" => "add_descr_cat_menu_links",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "This will add a line break &lt;br /&gt; followed by the \"category description\" (which you can set at 
			Site Admin -> Posts -> Categories -> Description) to the link text of each category's button in the category menu bar. 
			This category description, which is now visible inside the menu buttons, can be styled through HTML/CSS Inserts -> 
			CSS Insert, i.e.: <code>span.cat-descr { font-size: 90%; text-tranform: none; }</code>"),

    array(    "name" => "Default Category description text",
    	    "category" => "cat-menu-bar",
            "id" => "default_cat_descr_text",
            "std" => "View all posts filed under<br />%category%",
            "type" => "text",
			"size" => "30", 
			"editable" => "yes",
            "info" => "If you chose to include the category description in the menu bar's link texts (one option above), then set 
			a default text here for categories that have no description. <code>%category%</code> is a placeholder and will be 
			replaced with the Category Title. HTML allowed."),
			
    array(    "name" => "Border around all menu items",
    	    "category" => "cat-menu-bar",
            "id" => "anchor_border_cat_menu_bar",
            "std" => "solid 1px #000000",
            "type" => "text",
            "info" => "Every item of the menu bar, plus the menu bar itself, will be wrapped into this border. 
			To have no borders in the first level, give it the same color as the background color for first level items. 
			Don't use semicolons here. <br /><br />Note: Leave the border width at 1px, match colors if you want to make it dissapear."),

    array(    "name" => "Background color",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_bar_background_color",
            "std" => "777777",
            "type" => "text",
            "info" => "Background color for menu items in their default state and the menu bar itself."),

    array(    "name" => "Background color: Hover",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_bar_background_color_hover",
            "std" => "cc0000",
            "type" => "text",
            "info" => "Background color for menu items in hover and current state. <strong>Use fffffe instead of ffffff</strong> due to a \"White Background Hover Bug\" in IE7/IE8. http://haslayout.net/css/Hover-White-Background-Ignore-Bug."),

    array(    "name" => "Background color: Parent",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_bar_background_color_parent",
            "std" => "000000",
            "type" => "text",
            "info" => "Background color for parent menu item while hovering over its sub menu. <strong>Use fffffe instead of ffffff</strong> due to a \"White Background Hover Bug\" in IE7/IE8. http://haslayout.net/css/Hover-White-Background-Ignore-Bug."),
			
    array(    "name" => "Font for Category Menu Bar",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_font",
            "std" => "11px Arial, Verdana, sans-serif",
            "type" => "text",
			"size" => "30", 
            "editable" => "yes", 
            "info" => "Set both the font size and the font face for the menu items. Enclose font face names 
			with a space in quotes, i.e.:<br /><code>12px \"comic sans ms\", \"courier new\", arial, sans-serif</code>
			<br /><br />Don't use semicolons here."),

    array(    "name" => "Link Color",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_bar_link_color",
            "std" => "ffffff",
            "type" => "text",
            "info" => "Color of the link text in default state."),

    array(    "name" => "Link Color: Hover",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_bar_link_color_hover",
            "std" => "ffffff",
            "type" => "text",
            "info" => "Color of the link text in hover state."),
			
    array(    "name" => "Transform text in Category Menu Bar?",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_transform",
            "type" => "select",
            "std" => "uppercase",
            "options" => array("uppercase", "lowercase", "capitalize", "none"),
            "info" => "You can transform the link titles in the category menu bar."),
			
    array(    "name" => "White or Black Arrows as Sub Menu Indicator?",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_arrows",
            "type" => "select",
            "std" => "white",
            "options" => array("white", "black"),
            "info" => "If a menu item has sub menus, it will be indicated with down/right arrows. 
			Choose the color for these arrows."),

    array(    "name" => "Width of Sub Menus",
    	    "category" => "cat-menu-bar",
            "id" => "cat_menu_submenu_width",
            "type" => "select",
            "std" => "11",
			"lastoption" => "yes", 
			"options" => array("7", "7.5", "8", "8.5", "9", "9.5", "10", "10.5", "11", "11.5", "12", "12.5", 
			"13", "13.5", "14", "14.5", "15", "15.5", "16", "16.5", "17", "17.5", "18", "18.5", "19", "19.5", "20", 
			"20.5", "21", "21.5", "22", "22.5", "23", "23.5", "24", "24.5", "25"),
            "info" => "The width of top level items will adjust to the width of the links inside, but the sub menus 
			need a defined width, <strong>in \"em\"</strong>."),


// NEW since 3.6.5: category: center	

	array(    "name" => "Center column style",
    	    "category" => "center",
			"switch" => "yes",
            "id" => "center_column_style",
            "std" => "padding: 10px 15px;",
            "type" => "textarea-large",

            "info" => "Style the center column here. The center column is the container for everything in the middle: 
			All posts (including \"page\" posts) and the next/prev navigation."),

    array(    "name" => "Where are the 'Content ABOVE Loop', 'The LOOP' etc. options?",
    	    	"category" => "center",
            "id" => "widget_areas_reset",
            "type" => "info",
			"lastoption" => "yes",
            "info" => "<strong>They are now 'hardcoded' into Atahualpa's &lt;code&gt;index.php&lt;/code&gt;. To edit the loop or to add custom code before/after the loop, manually edit &lt;code&gt;index.php&lt;/code&gt;.</strong><br /><br />Since 3.6.5 custom PHP code isn't possible anymore in the Atahualpa Theme Options. Atahualpa was one of few themes, if not the only theme, to allow custom PHP code to be inserted through theme options. 
			The philosophy as to what themes should or should not be able to do (i.e. which PHP functions they can use) has been tightened up quite a bit lately by WordPress, so in order to stay listed on wordpress.org we 
			had to remove this 'custom PHP' feature. Check wordpress.bytesforall.com and forum.bytesforall.com for possible workarounds or even alternative theme versions, if you cannot live without custom PHP."),

			
// new category: center
/*
	array(    "name" => "Center column style",
    	    "category" => "center",
			"switch" => "yes",
            "id" => "center_column_style",
            "std" => "padding: 10px 15px;",
            "type" => "textarea-large",
            "info" => "Style the center column here. The center column is the container for everything in the middle: 
			All posts (including \"page\" posts) and the next/prev navigation."),

	array(    "name" => "Content ABOVE the LOOP",
    	    "category" => "center",
            "id" => "content_above_loop",
            "std" => "<?php bfa_next_previous_page_links('Top'); ?>",
            "type" => "textarea-large",
            "info" => "Edit/add/remove content above THE LOOP. The Loop is the content that Wordpress outputs for a particular page. This 
can be a list of posts (thus the name \"Loop\"), or a single 
post or a static page. You can use HTML and <strong>PHP</strong> here and in the other text areas below."),	

	array(    "name" => "The LOOP",
    	    "category" => "center",
            "id" => "content_inside_loop",
            "std" => "<?php bfa_next_previous_post_links('Top'); ?>

<?php 
if ( function_exists('post_class') ) { ?>
<div <?php if ( is_page() ) { post_class('post'); } else { post_class(\"\$odd_or_even\"); } ?> id=\"post-<?php the_ID(); ?>\">
<?php } else { ?>
<div class=\"<?php echo ( is_page() ? 'page ' : '' ) . \$odd_or_even . ' post\" id=\"post-'; the_ID(); ?>\">
<?php } ?>

<?php bfa_post_kicker('<div class=\"post-kicker\">','</div>'); ?>

<?php bfa_post_headline('<div class=\"post-headline\">','</div>'); ?>

<?php bfa_post_byline('<div class=\"post-byline\">','</div>'); ?>

<?php bfa_post_bodycopy('<div class=\"post-bodycopy clearfix\">','</div>'); ?>

<?php bfa_post_pagination('<p class=\"post-pagination\"><strong>'.__('Pages:','atahualpa').'</strong>','</p>'); ?>

<?php bfa_post_footer('<div class=\"post-footer\">','</div>'); ?>

</div><!-- / Post -->",
            "type" => "textarea-large",
            "info" => "Here you can add content in between single posts, or edit THE LOOP as such. To put something after post number X 
you can use the variable <code>\$bfa_ata['postcount']</code>. It contains the number of the current post in the loop.
<br /><br /><strong>Example:</strong><br /><br />
<code>&lt;?php if ( \$bfa_ata['postcount'] == 1 ) { ?&gt;<br /><br />
The HTML, Javascript or PHP that you put here will be displayed after the first post.<br /><br />
&lt;?php } ?&gt;</code>
<br /><br /><strong>Example 2:</strong><br /><br />
<code>&lt;?php if ( is_front_page() AND \$bfa_ata['postcount'] == 3 ) { ?&gt;<br /><br />
This here will be displayed after the 3rd post on (only) the homepage.<br /><br />
&lt;?php } ?&gt;</code>"),	
						
	array(    "name" => "Content BELOW the LOOP",
    	    "category" => "center",
            "id" => "content_below_loop",
            "std" => "<?php bfa_next_previous_post_links('Middle'); ?>

<?php bfa_get_comments(); ?>

<?php bfa_next_previous_post_links('Bottom'); ?>
		
<?php bfa_archives_page('<div class=\"archives-page\">','</div>'); ?>
			
<?php bfa_next_previous_page_links('Bottom'); ?>",
            "type" => "textarea-large",
            "info" => "Add/remove/edit the content below the LOOP here."),		

	array(    "name" => "Content if NOT FOUND",
    	    "category" => "center",
            "id" => "content_not_found",
            "std" => "<h2><?php _e('Not Found','atahualpa'); ?></h2>
<p><?php _e(\"Sorry, but you are looking for something that isn't here.\",\"atahualpa\"); ?></p>",
            "type" => "textarea-large",
				"lastoption" => "yes", 
            "info" => "Add/edit/remove the content here that is displayed on \"404 Not Found\" pages."),		
*/           
            
// New category: next/prev navigation
			
    array(    "name" => "NEWER / OLDER orientation",
    	    "category" => "next-prev-nav",
			"switch" => "yes",
            "id" => "next_prev_orientation",
            "std" => "Newer Left, Older Right",
            "type" => "select", 
			"options" => array("Newer Left, Older Right", "Older Left, Newer Right"), 
            "info" => "Show the link to the NEWER post/page on the LEFT or the RIGHT hand side of the navigation bar(s)?"),
			
    array(    "name" => "Home link in Nav. on MULTI post pages?",
    	    "category" => "next-prev-nav",
            "id" => "home_multi_next_prev",
            "std" => "",
            "type" => "text",
            "info" => "On multi post pages, show a \"Home\" link between the 2 links pointing to the previous and 
			the next page?<ul><li>Leave blank to show no \"Home\" link</li><li>Or, put any text here to use it as 
			the text for the link to the homepage</li><li>If you use the WP-PageNavi plugin, this setting becomes 
			obsolete as then the page numbers of WP-PageNavi will be displayed instead of the default next/prev links</li></ul>"),

    array(    "name" => "Home link in Nav. on SINGLE post pages?",
    	    "category" => "next-prev-nav",
            "id" => "home_single_next_prev",
            "std" => "",
            "type" => "text",
            "info" => "On single post pages, show a \"Home\" link between the 2 links pointing to the previous and 
			the next post?<ul><li>Leave blank to show no \"Home\" link</li><li>Or, put any text here to use it as 
			the text for the link to the homepage</li></ul>"),

    array(    "name" => "\"Newer Page\" link on MULTI post pages",
    	    "category" => "next-prev-nav",
            "id" => "multi_next_prev_newer",
            "std" => __("&laquo; Newer Entries","atahualpa"),
            "type" => "text",
			"size" => "30", 
			"editable" => "yes", 
            "info" => "You can use single and double quotes, and HTML. Examples:<ul><li><code>&lt;br /&gt;</code> 
			for line breaks</li><li><code>&lt;strong&gt; ... &lt;/strong&gt;</code> to make text <strong>bold</strong></li>
			<li><code>&lt;em&gt; ... &lt;/em&gt;</code> to make text <em>italic</em></li><li><code>&amp;nbsp;</code> to 
			include a non-breaking space</li><li><code>&amp;raquo;</code> for a right angle quote 
			<span style=\"font-size: 25px\">&raquo;</span></li><li><code>&amp;laquo;</code> for a left angle quote 
			<span style=\"font-size: 25px\">&laquo;</span></li><li><code>&amp;rsaquo;</code> for a right single angle quote 
			<span style=\"font-size: 25px\">&rsaquo;</span></li><li><code>&amp;lsaquo;</code> for a left single angle quote 
			<span style=\"font-size: 25px\">&lsaquo;</span></li><li><code>&amp;rarr;</code> for a right arrow 
			<span style=\"font-size: 25px\">&rarr;</span></li><li><code>&amp;larr;</code> for a left arrow 
			<span style=\"font-size: 25px\">&larr;</span></li></ul>
			<em>NOTE: If you use WP-PageNavi then this and the next setting become obsolete</em>"), 

    array(    "name" => "\"Older Page\" link on MULTI post pages",
    	    "category" => "next-prev-nav",
            "id" => "multi_next_prev_older",
            "std" => __("Older Entries &raquo;","atahualpa"),
            "type" => "text",
			"size" => "30", 
			"editable" => "yes", 
            "info" => "See above for HTML examples."), 

    array(    "name" => "\"Newer Post\" link on SINGLE post pages",
    	    "category" => "next-prev-nav",
            "id" => "single_next_prev_newer",
            "std" => "&laquo; %link",
            "type" => "text",
			"size" => "30", 
			"editable" => "yes", 
            "info" => "See above for HTML examples.<br /><br />To include the linked title of the newer post, use <code>%link</code>"), 
			
    array(    "name" => "\"Older Post\" link on SINGLE post pages",
    	    "category" => "next-prev-nav",
            "id" => "single_next_prev_older",
            "std" => "%link &raquo;",
            "type" => "text",
			"size" => "30", 
			"editable" => "yes", 
            "info" => "See above for HTML examples.<br /><br />To include the linked title of the older post, use <code>%link</code>"),

    array(    "name" => "Keep next/prev. links on SINGLE post pages to same post category?",
    	    "category" => "next-prev-nav",
            "id" => "single_next_prev_same_cat",
            "std" => "No",
            "type" => "select", 
			"options" => array("No", "Yes"), 
            "info" => "Limit the Next and Previous links on single post pages to the category of the 
			current post."),
	
);

$options2 = array(

    array(    "name" => "\"Newer Comments\" link for COMMENTS navigation",
    	    "category" => "next-prev-nav",
            "id" => "comments_next_prev_newer",
            "std" => __("Newer Comments &raquo;","atahualpa"),
            "type" => "text",
			"size" => "30", 
			"editable" => "yes", 
            "info" => "See above for HTML examples. If you choose to show page numbers (see below) the 
			\"Newer Comments\" link will be on the left hand side of the page numbers."),
			
    array(    "name" => "\"Older Comments\" link for COMMENTS navigation",
    	    "category" => "next-prev-nav",
            "id" => "comments_next_prev_older",
            "std" => __("&laquo; Older Comments","atahualpa"),
            "type" => "text",
			"size" => "30", 
			"editable" => "yes", 
            "info" => "See above for HTML examples. If you choose to show page numbers (see below) the 
			\"Older Comments\" link will be on the right hand side of the page numbers."),

    array(    "name" => "Location of Paged COMMENTS Navigation",
    	    "category" => "next-prev-nav",
            "id" => "location_comments_next_prev",
            "std" => "Above and Below Comments",
            "type" => "select", 
			"options" => array("Above Comments", "Below Comments", "Above and Below Comments"),
            "info" => "Show the Next/Previous comments navigation above or below the comments?<br /><br />
			"),

    array(    "name" => "Style the COMMENTS ABOVE Box",
    	    "category" => "next-prev-nav",
            "id" => "next_prev_style_comments_above",
            "std" => "margin: 0 0 10px 0;\npadding: 5px 0 5px 0;",
            "type" => "textarea-large",
            "info" => "Style the box that contains the next/previous navigation for comments, when it is above the comments."),

    array(    "name" => "Style the COMMENTS BELOW Box",
    	    "category" => "next-prev-nav",
            "id" => "next_prev_style_comments_below",
            "std" => "margin: 0 0 10px 0;\npadding: 5px 0 5px 0;",
            "type" => "textarea-large",
            "info" => "Style the box that contains the next/previous navigation for comments, when it is below the comments."),

    array(    "name" => "Show Page Numbers (Pagination) for COMMENTS Navigation",
    	    "category" => "next-prev-nav",
            "id" => "next_prev_comments_pagination",
            "std" => "Yes",
            "type" => "select", 
			"options" => array("Yes", "No"), 
            "info" => "Instead of the regular Newer Comments / Older Comments links you can show the page numbers plus
			previous/next links. Your settings for \"Newer Comments\" link and \"Older Comments\" link from above
			will be used as the next/previous link texts."),

);

$options3 = array(
			
    array(    "name" => "Location of Next/Previous Page Navigation on MULTI Post Pages",
    	    "category" => "next-prev-nav",
            "id" => "location_multi_next_prev",
            "std" => "Bottom",
            "type" => "select", 
			"options" => array("Top", "Bottom", "Top and Bottom", "None"), 
            "info" => "On multi post pages, show the Next/Previous navigation on top (above all posts), at the bottom 
			(below all posts), or on top AND at the bottom?"),
			
    array(    "name" => "Location of Next/Previous Post Navigation on SINGLE Post Pages",
    	    "category" => "next-prev-nav",
            "id" => "location_single_next_prev",
            "std" => "Top",
            "type" => "select", 
			"options" => array("Top", "Middle", "Bottom", "Top and Middle", "Top and Bottom", "Middle and Bottom", 
			"Top, Middle and Bottom", "None"), 
            "info" => "On single post pages, show the Next/Previous navigation on top, in the middle 
			(between the post and the comments), or at the bottom?"),

    array(    "name" => "Style the Navigation TOP Box",
    	    "category" => "next-prev-nav",
            "id" => "next_prev_style_top",
            "std" => "margin: 0 0 10px 0;\npadding: 0 0 10px 0;\nborder-bottom: dashed 1px #cccccc;",
            "type" => "textarea-large",
            "info" => "Style the box that contains the next/previous navigation, when it is at the top."),

	array(    "name" => "Style the Navigation MIDDLE Box",
    	    "category" => "next-prev-nav",
            "id" => "next_prev_style_middle",
            "std" => "margin: 10px 0 20px 0;\npadding: 10px 0 10px 0;\nborder-top: dashed 1px #cccccc;\nborder-bottom: dashed 1px #cccccc;",
            "type" => "textarea-large",
            "info" => "Style the box that contains the next/previous navigation, when it is in the middle."),
			
    array(    "name" => "Style the Navigation BOTTOM Box",
    	    "category" => "next-prev-nav",
            "id" => "next_prev_style_bottom",
            "std" => "margin: 20px 0 0 0;\npadding: 10px 0 0 0;\nborder-top: dashed 1px #cccccc;",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "Style the box that contains the next/previous navigation, when it is at the bottom."),
			
// New category: sidebars

    array(    "name" => "LEFT sidebar: Display on:",
    	    "category" => "sidebars",
			"switch" => "yes",
            "id" => "leftcol_on",
            "std" => array ("homepage" => "on", 
								"frontpage" => "on", 
								"single" => "on", 
								"page" => "on", 
								"category" => "on", 
								"date" => "on", 
								"tag" => "on", 
								"taxonomy" => "on",
								"search" => "on", 
								"author" => "on", 
								"404" => "on", 
								"attachment" => "on", 
								"check-if-saved-once" => FALSE),
            "type" => "displayon",
			"stripslashes" => "no",
            "info" => "(*) \"Front Page\" will only affect WP 2.5 and newer: For those newer WP versions, 
			IF you select a static \"Page\" page as the home page, then \"Front Page\" becomes the actual homepage, 
			while the \"Homepage\" will be the home page for Posts (but not the whole blog). If no static front page 
			is selected, Homepage and Front Page will be the same.<br />(**) Custom Taxonomy pages. Custom taxonomies don't exist by default. 
			You'd have to create them manually or with a plugin such as http://wordpress.org/extend/plugins/custom-taxonomies/"),

    array(    "name" => "LEFT sidebar: Don't display on Pages:",
    	    "category" => "sidebars",
            "id" => "left_col_pages_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the left sidebar on <strong>individual</strong> pages, put the ID's of 
			those pages here, separated by comma<br /><br /><strong>Example:</strong><br /><code>29,8,111</code><br /><br />
			To get the ID of a page, " . $go_to_pages . ", point your mouse at the title of the page in question, and 
			watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&post=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the page."), 

    array(    "name" => "LEFT sidebar: Don't display on Categories:",
    	    "category" => "sidebars",
            "id" => "left_col_cats_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the left sidebar on <strong>individual</strong> category pages, put the ID's of 
			those categories here, separated by comma<br /><br /><strong>Example:</strong><br /><code>13,5,87</code><br /><br />
			To get the ID of a category, " . $go_to_cats . ", point your mouse at the title of the category in question, 
			and watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&cat_ID=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the category.<br /><br />Note: This will turn on/off sidebars on category pages 
			(pages that list the posts in the given category), but not on \"all single post pages of posts in category XX\"."), 

    array(    "name" => "LEFT INNER sidebar: Display on:",
    	    "category" => "sidebars",
            "id" => "leftcol2_on",
            "std" => array ("check-if-saved-once" => FALSE),
            "type" => "displayon",
			"stripslashes" => "no",
            "info" => "(*) \"Front Page\" will only affect WP 2.5 and newer: For those newer WP versions, 
			IF you select a static \"Page\" page as the home page, then \"Front Page\" becomes the actual homepage, 
			while the \"Homepage\" will be the home page for Posts (but not the whole blog). If no static front page 
			is selected, Homepage and Front Page will be the same.<br />(**) Custom Taxonomy pages. Custom taxonomies don't exist by default. 
			You'd have to create them manually or with a plugin such as http://wordpress.org/extend/plugins/custom-taxonomies/"),

    array(    "name" => "LEFT INNER sidebar: Don't display on Pages:",
    	    "category" => "sidebars",
            "id" => "left_col2_pages_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the left sidebar on <strong>individual</strong> pages, put the ID's of 
			those pages here, separated by comma<br /><br /><strong>Example:</strong><br /><code>29,8,111</code><br /><br />
			To get the ID of a page, " . $go_to_pages . ", point your mouse at the title of the page in question, and 
			watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&post=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the page."), 

    array(    "name" => "LEFT INNER sidebar: Don't display on Categories:",
    	    "category" => "sidebars",
            "id" => "left_col2_cats_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the left sidebar on <strong>individual</strong> category pages, put the ID's of 
			those categories here, separated by comma<br /><br /><strong>Example:</strong><br /><code>13,5,87</code><br /><br />
			To get the ID of a category, " . $go_to_cats . ", point your mouse at the title of the category in question, 
			and watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&cat_ID=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the category.<br /><br />Note: This will turn on/off sidebars on category pages 
			(pages that list the posts in the given category), but not on \"all single post pages of posts in category XX\"."), 
						
	array(    "name" => "RIGHT sidebar: Display on:",
    	    "category" => "sidebars",
            "id" => "rightcol_on",
            "std" => array ("homepage" => "on", 
								"frontpage" => "on", 
								"single" => "on", 
								"page" => "on", 
								"category" => "on", 
								"date" => "on", 
								"tag" => "on", 
								"taxonomy" => "on",
								"search" => "on", 
								"author" => "on", 
								"404" => "on", 
								"attachment" => "on", 
								"check-if-saved-once" => FALSE),
            "type" => "displayon",
			"stripslashes" => "no",
            "info" => "(*) \"Front Page\" will only affect WP 2.5 and newer: For those newer WP versions, IF you 
			select a static \"Page\" page as the home page, then \"Front Page\" becomes the actual homepage, while the 
			\"Homepage\" will be the home page for Posts (but not the whole blog). If no static front page is selected, 
			Homepage and Front Page will be the same.<br />(**) Custom Taxonomy pages. Custom taxonomies don't exist by default. 
			You'd have to create them manually or with a plugin such as http://wordpress.org/extend/plugins/custom-taxonomies/"),

    array(    "name" => "RIGHT sidebar: Don't display on Pages:",
    	    "category" => "sidebars",
            "id" => "right_col_pages_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the right sidebar on <strong>individual</strong> pages, put the ID's of 
			those pages here, separated by comma<br /><br /><strong>Example:</strong><br /><code>29,8,111</code><br /><br />
			To get the ID of a page, " . $go_to_pages . ", point your mouse at the title of the page in question, and 
			watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&post=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the page."), 

    array(    "name" => "RIGHT sidebar: Don't display on Categories:",
    	    "category" => "sidebars",
            "id" => "right_col_cats_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the right sidebar on <strong>individual</strong> categories, put the ID's of 
			those categories here, separated by comma<br /><br /><strong>Example:</strong><br /><code>13,5,87</code><br /><br />
			To get the ID of a category, " . $go_to_cats . ", point your mouse at the title of the category in question, and 
			watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&cat_ID=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the category.<br /><br />Note: This will turn on/off sidebars on category pages 
			(pages that list the posts in the given category), but not on \"all single post pages of posts in category XX\""), 

	array(    "name" => "RIGHT INNER sidebar: Display on:",
    	    "category" => "sidebars",
            "id" => "rightcol2_on",
            "std" => array ("check-if-saved-once" => FALSE),
            "type" => "displayon",
			"stripslashes" => "no",
            "info" => "(*) \"Front Page\" will only affect WP 2.5 and newer: For those newer WP versions, IF you 
			select a static \"Page\" page as the home page, then \"Front Page\" becomes the actual homepage, while the 
			\"Homepage\" will be the home page for Posts (but not the whole blog). If no static front page is selected, 
			Homepage and Front Page will be the same.<br />(**) Custom Taxonomy pages. Custom taxonomies don't exist by default. 
			You'd have to create them manually or with a plugin such as http://wordpress.org/extend/plugins/custom-taxonomies/"),

    array(    "name" => "RIGHT INNER sidebar: Don't display on Pages:",
    	    "category" => "sidebars",
            "id" => "right_col2_pages_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the right sidebar on <strong>individual</strong> pages, put the ID's of 
			those pages here, separated by comma<br /><br /><strong>Example:</strong><br /><code>29,8,111</code><br /><br />
			To get the ID of a page, " . $go_to_pages . ", point your mouse at the title of the page in question, and 
			watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&post=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the page."), 

    array(    "name" => "RIGHT INNER sidebar: Don't display on Categories:",
    	    "category" => "sidebars",
            "id" => "right_col2_cats_exclude",
            "std" => "",
            "type" => "text",
			"size" => "30", 
            "info" => "To turn off the right sidebar on <strong>individual</strong> categories, put the ID's of 
			those categories here, separated by comma<br /><br /><strong>Example:</strong><br /><code>13,5,87</code><br /><br />
			To get the ID of a category, " . $go_to_cats . ", point your mouse at the title of the category in question, and 
			watch your browser's status bar (it's at the bottom) for an URL ending on \"...action=edit&cat_ID=<strong>XX</strong>\". 
			<strong>XX</strong> is the ID of the category.<br /><br />Note: This will turn on/off sidebars on category pages 
			(pages that list the posts in the given category), but not on \"all single post pages of posts in category XX\""), 
			
    array(    "name" => "LEFT sidebar WIDTH",
    	    "category" => "sidebars",
            "id" => "left_sidebar_width",
            "std" => "200",
            "type" => "text", 
			"size" => "6", 
            "info" => "Width of the left sidebar in pixels. <strong>Example:</strong> <code>165</code>"),

    array(    "name" => "LEFT INNER sidebar WIDTH",
    	    "category" => "sidebars",
            "id" => "left_sidebar2_width",
            "std" => "200",
            "type" => "text", 
			"size" => "6", 
            "info" => "Width of the left sidebar in pixels. <strong>Example:</strong> <code>165</code>"),
            
    array(    "name" => "RIGHT sidebar WIDTH",
    	    "category" => "sidebars",
            "id" => "right_sidebar_width",
            "std" => "200",
            "type" => "text", 
			"size" => "6", 
            "info" => "Width of the right sidebar in pixels. <strong>Example:</strong> <code>220</code>"),

    array(    "name" => "RIGHT INNER sidebar WIDTH",
    	    "category" => "sidebars",
            "id" => "right_sidebar2_width",
            "std" => "200",
            "type" => "text", 
			"size" => "6", 
            "info" => "Width of the right sidebar in pixels. <strong>Example:</strong> <code>220</code>"),
            			
    array(    "name" => "LEFT sidebar style",
    	    "category" => "sidebars",
            "id" => "left_sidebar_style",
            "std" => "border-right: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;",
            "type" => "textarea-large",
            "info" => "Style the LEFT sidebar here. Usually all content in a sidebar would be inside of widgets,
			so there should be no need to set text styles for the sidebar. The widgets can be styled separately, 
			see the menu tabs above."),

    array(    "name" => "LEFT INNER sidebar style",
    	    "category" => "sidebars",
            "id" => "left_sidebar2_style",
            "std" => "border-right: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;",
            "type" => "textarea-large",
            "info" => "Style the LEFT sidebar here. Usually all content in a sidebar would be inside of widgets,
			so there should be no need to set text styles for the sidebar. The widgets can be styled separately, 
			see the menu tabs above."),
			
    array(    "name" => "RIGHT sidebar style",
    	    "category" => "sidebars",
            "id" => "right_sidebar_style",
            "std" => "border-left: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;",
            "type" => "textarea-large",
            "info" => "Style the RIGHT sidebar here."),

    array(    "name" => "RIGHT INNER sidebar style",
    	    "category" => "sidebars",
            "id" => "right_sidebar2_style",
            "std" => "border-left: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "Style the RIGHT sidebar here."),
            
// New category: widgets

    array(    "name" => "Widget Container",
    	    "category" => "widgets",
			"switch" => "yes",
            "id" => "widget_container",
            "std" => "margin: 0 0 15px 0;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/widget.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"The widget container contains the \"Widget Title\" (-Box) and the \"Widget Content\" (-Box), 
			both of which you can style independently."),

    array(    "name" => "Widget Title Box",
    	    "category" => "widgets",
            "id" => "widget_title_box",
            "std" => "",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/widget-title-box.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"The Widget Title box contains the widget title. Text, calendar and search widgets may have no 
			title if you chose none. In that case there will be no Widget Title box in the widget container."),	

    array(    "name" => "Widget Title",
    	    "category" => "widgets",
            "id" => "widget_title",
            "std" => "font-size: 1.6em;\nfont-weight: bold;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/widget-title.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style the Widget Title Font."),	

    array(    "name" => "Widget Content Box",
    	    "category" => "widgets",
            "id" => "widget_content",
            "std" => "",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/widget-content.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "The Widget Content Box is gone since Atahualpa 3.4. for better 
			plugin compatibility. It was a propretiary Atahualpa feature that provided extra styling possibilites but didn't play well with 
			all plugins because some of them rely heavily on widgets having the same structure as in the \"Default\" theme (which does 
			not have this extra DIV inside each widget).  			
			Instead of \"div.widget-container\" the styles that you put here will be applied on \"div.widget ul, div.textwidget\". 
			That will cover all text widgets and all widgets that consists of unordered lists, which should be the majority of all widgets.  
			Otherwise look into the source code of a browser rendered page of your site to see which DIV or other HTML element wraps around 
			the body content of the widget that was not covered by this, and add a CSS Insert such as: <code>div.some-class { margin: 10px }</code>."),

    array(    "name" => "Widget List Items",
    	    "category" => "widgets",
            "id" => "widget_lists",
       			"std" => array  (
       								"li-margin-left" => 0, 
       								"link-weight" => "normal", 
       								"link-padding-left" => 5, 
       								"link-border-left-width" => 7,
       								"link-color" => "666666", 
       								"link-hover-color" => "000000",  
       								"link-border-left-color" => "cccccc", 
       								"link-border-left-hover-color" => "000000"),
            "type" => "widget-list-items",
			"stripslashes" => "no",
            "info" => "<img src=\"" . $templateURI . "/options/images/widget-list-items-1.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "List items and links in widgets."),

    array(    "name" => "Widget List Items, 2nd level",
    	    "category" => "widgets",
            "id" => "widget_lists2",
       			"std" => array  (
       								"li-margin-left" => 5, 
       								"link-weight" => "normal",
       								"link-padding-left" => 5, 
       								"link-border-left-width" => 7,
       								"link-color" => "666666", 
       								"link-hover-color" => "000000",  
       								"link-border-left-color" => "cccccc", 
       								"link-border-left-hover-color" => "000000"),
            "type" => "widget-list-items",
			"stripslashes" => "no",
            "info" => "<img src=\"" . $templateURI . "/options/images/widget-list-items-2.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Second level list items and links in widgets."),

    array(    "name" => "Widget List Items, 3rd and lower level",
    	    "category" => "widgets",
            "id" => "widget_lists3",
				"std" => array  (
       								"li-margin-left" => 5, 
       								"link-weight" => "normal", 
       								"link-padding-left" => 5, 
       								"link-border-left-width" => 7,
       								"link-color" => "666666", 
       								"link-hover-color" => "000000",  
       								"link-border-left-color" => "cccccc", 
       								"link-border-left-hover-color" => "000000"),
            "type" => "widget-list-items",
			"stripslashes" => "no",
            "info" => "<img src=\"" . $templateURI . "/options/images/widget-list-items-3.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Third and lower level list items and links in widgets."),

    array(    "name" => "Category Widget Display Type",
    	    "category" => "widgets",
            "id" => "category_widget_display_type",
            "std" => "inline",
            "type" => "select", 
            "options" => array("inline", "block"),
            "info" => "The category widget needs this extra setting because it is the only widget that can 
			have both a link and a non-linked text (the \"post count\") inside a single list item AND be 
			hierarchical AND be too long to fit into a single line. For the most pleasing result across 
			browsers, choose... <ul><li><code>inline</code> if you are displaying the post count</li>
			<li><code>block</code> if you're <strong>not</strong> displaying the post count</li></ul>"),

    array(    "name" => "Adjust SELECT menu font size",
    	    "category" => "widgets",
            "id" => "select_font_size",
            "std" => "Default",
            "type" => "select", 
            "options" => array("Default", "12px", "11px", "10px", "9px"),
			"lastoption" => "yes", 
            "info" => "<img src=\"" . $templateURI . "/options/images/select-cutoff.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "In <strong>Internet Explorer</strong>, 
			\"Select\" drop down menus will be cut off if one or more of the select menu items (in this 
			case: category titles) are too long. <br /><br />To avoid this, set a (small) fixed pixel font 
			size for the select menu items here, such as <strong>11px</strong> (11 pixels) if you feel 
			(or see, because you use IE) that your select menus might be too wide for the set sidebar 
			width. (OR: Make your sidebar wider)"),
			
// New category: widget-areas

    array(    "name" => "Delete custom Widget Areas",
    	    	"category" => "widget-areas",
				"switch" => "yes",
            "id" => "widget_areas_reset",
            "type" => "info",
            "info" => "1) Delete the <code>&lt;?php bfa_widget_area(...) ?&gt;</code> code from whichever text area here in the theme options you placed it in.
            <br />2) Select the widget areas that you want to delete and click the link below. <br />Note: The widget areas will be re-created until you deleted its associated code (see step 1). <br />
<form action=\"\" method=\"\" id=\"widgetarea-form\">" . $widget_form_string . "</form>
<a style='display:block; background:#C6D9E9;width:250px;margin-top:10px; padding:5px 10px;' id='reset_widget_areas' href='#'>Delete checked Widget Areas</a><span style='color:green;font-weight:bold;float:left;padding-left:30px' id='formstatus'></span><br />"),

    array(    "name" => "Add new Widget Areas",
    	    	"category" => "widget-areas",
            "id" => "widget_areas_info",
            "type" => "info",
				"lastoption" => "yes", 
            "info" => "<strong>Note: After you've added new widget areas, you'll need to <span style='color:red'>reload a front end page</span> 
            1-2 times before they get created and start appearing on the front end. And you'll need to <span style='color:red'>reload WP->Presentation->Widgets</span> 
            1-2 times before you can see the new widget areas there and start adding widgets.</strong><br /><br />In addition to the existing widget areas (the sidebars) you can add additional widget areas, i.e. in the header 
area, the center column or the footer area. This page here only explains how to use this feature. To actually add a new widget area 
you'll have to go to one of the following menu tabs:
<ul><li><a href=\"javascript: myflowers.expandit('header-tab')\">Style & edit HEADER AREA</a>: Put the code into the text area named \"Configure Header Area\".</li>
<li><a href=\"javascript: myflowers.expandit('center-tab')\">Style & edit CENTER COLUMN</a>: Put the code into ANY of the text areas EXCEPT the first one, named \"Center column style\".</li>
<li><a href=\"javascript: myflowers.expandit('footer-style-tab')\">Style & edit FOOTER</a>: Put the code into the text area named \"Footer: Content\".</li>
<li>Technically, you could also hard code widget areas into one of the Atahualpa files such as index.php, header.php or footer.php (by using the code as shown below). 
You should avoid this though, because you'd have to re-do these file edits whenever you upgrade to a new version of Atahualpa. Most of the time it will be unnecessary anyway as there 
most likely is a text area here in the theme option pages where you can insert your code. Code that you insert in the theme options will be saved in the 
database and automatically carried over to new Atahualpa versions when you upgrade.</li></ul>

<h3>Usage</h3>
The code to create new widget areas is a PHP function with parameters:<br />
<code>&lt;?php bfa_widget_area('parameter=value&#38;paramater=value&#38;paramater=value'); ?&gt;</code>

<h3>Min. required paramaters</h3>
This is the shortest and most basic way you can create a widget area.<br />
<code>&lt;?php bfa_widget_area('name=My new widget area'); ?&gt;</code><br /><br />
In this case a simple DIV container will be created. In the source code of your site, it will look like this:<br />
<code>&lt;div id=\"my_new_widget_area\" class=\"bfa_widget_area\"&gt; Widgets will go here &lt;/div&gt;</code><br />
(If you choose several cells, a table will be created instead, see below).

<h3>Example:</h3>
This example uses more parameters. It creates a widget area spanning the whole available width (like all widget areas), with 4 widget area cells (default: 1). Each widget area cell is a widget area in its own right. You can 
specify an alignment and a width for all or particular widget area cells. Finally, you can specify opening and closing HTML tags for the widgets that will be placed in 
these new widget area cells.<br />
<code>&lt;?php bfa_widget_area('name=My widget area&cells=4&align=1&align_2=9&align_3=7&width_4=700&before_widget=&lt;div id=\"%1\$s\" class=\"header-widget %2\$s\"&gt;&after_widget=&lt;/div&gt;'); ?&gt;</code><br /><br />
Because these are multiple cells side by side, it will create a table instead of a DIV. Doing this with floating DIV's would not only be very fragile, it would also require more code 
than the table consists of. 


<h3>Available parameters:</h3>
<table>
<tr>
<td class='bfa-td' colspan='2' style='border-top-style: solid'>
<strong>Mandatory:</strong>
</td>
</tr><tr>
<td class='bfa-td'><code>name</code></td>
<td class='bfa-td'>Name under which all cells of this widget area will be listed at Site Admin -> Appearance -> <a href='widgets.php'>Widgets</a> (see drop down select menu at top right).<br /><br />
							<em>A widget area with 3 cells and a name of \"My widget area\" creates 3 widget cells which will be listed as
							\"My widget area 1\", \"My widget area 2\" and \"My widget area 3\" at Site Admin -> Appearance -> Widgets, 
							with the CSS ID's \"my_widget_area_1\", \"my_widget_area_2\" and \"my_widget_area_3\". </em>
</td>
</tr><tr>
<td class='bfa-td' colspan='2' style='border-top-style: solid'>		
<strong>Optional:</strong>
</td>
</tr><tr>
<td class='bfa-td'><code>cells</code></td>
<td class='bfa-td'>Amount of (table) cells. Each cell is a new widget area. Default: 1</td>
</tr><tr>
<td class='bfa-td'><code>align</code></td>
<td class='bfa-td'><img src=\"" . $templateURI . "/options/images/widget-area-alignment.gif\" 
			style=\"float: left; margin: 0 10px 10px 0;\">Default alignment for all cells.<br />Default: <code>2</code> (= center top). <code>1</code> = center middle, <code>2</code> = center top, 
<code>3</code> = right top, <code>4</code> = right middle, <code>5</code> = right bottom, <code>6</code> = center bottom, 
<code>7</code> = left bottom, <code>8</code> = left middle, <code>9</code> = left top.</td>
</tr><tr>
<td class='bfa-td'><code>align_1</code>, <code>align_2</code>, <code>align_3</code> etc.</td>
<td class='bfa-td'><img src=\"" . $templateURI . "/options/images/widget-area-alignment.gif\" 
			style=\"float: left; margin: 0 10px 10px 0;\">Alignment for a particular widget area cell. If not defined, widget area cells get the default value of the global parameter <code>align</code>, which, if not defined, is <code>2</code> (= center top).</td>
</tr><tr>
<td class='bfa-td'><code>width_1</code>, <code>width_2</code>, <code>width_3</code> etc.</td>	
<td class='bfa-td'>Width of a particular widget area cell. If not defined, widget area cells get an equal share of the remaining width of the whole widget area table.</td>
</tr>
<tr>
<td class='bfa-td' colspan='2' style='border-top-style: solid'>		
<strong>Very Optional:</strong><br />Use these only if you want to apply different opening and closing HTML tags to the widgets that you 
put into the new widget areas. By default the widgets will get the same opening/closing tags as the widgets in the sidebars. The default tags 
are shown below. <br /><br /><em>Note: These are the HTML tags that will be wrapped around each single widget in this particular widget area. 
The purpose is to be able to wrap a widget into different HTML tags depending on the widget area it was placed in. If you just want different styling for 
a widget based on the widget area it was placed in, then you could usually achieve this with CSS alone, by adressing the widget through the ID or class of its parent (= widget area it was placed in):<br />
</em><code>div#my_widget_area div.widget { border: solid 1px black}</code><br /><em>Use these opening/closing HTML tags only  
if CSS alone is not enough, because you need different HTML tags before or after the widget or widget title, in a particular widget area.</em>
</td>
</tr><tr>
<td class='bfa-td'><code>before_widget</code></td>
<td class='bfa-td'>HTML before each widget in any cell of this widget area. <br />Default:  <code>&lt;div id=\"%1\$s\" class=\"widget %2\$s\"&gt;</code><br />
<code>%1\$s</code> and <code>%2\$s</code> will be replaced with individual names, which the widget will provide.</td>
</tr><tr>
<td class='bfa-td'><code>after_widget</code></td>
<td class='bfa-td'>HMTL after each widget ... <br />Default: <code>&lt;/div&gt;</code></td>
</tr><tr>
<td class='bfa-td'><code>before_title</code></td>
<td class='bfa-td'>HTML before the title of each widget in any cell of this widget area: <br />Default: <code>&lt;div class=\"widget-title\"&gt;&lt;h3&gt;</code></td>
</tr><tr>
<td class='bfa-td'><code>after_title</code></td>
<td class='bfa-td'>HMTL after the title ... <br />Default: <code>&lt;/h3&gt;&lt;/div&gt;</code></td>
</tr>
</table>
"),

// New category: postinfos

    array(    "name" => "KICKER: Homepage",
    	    "category" => "postinfos",
			"switch" => "yes",
            "id" => "post_kicker_home",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no kicker on posts on the homepage.<br /><strong>Example:
			</strong> <code>%category%</code>"),

	array(    "name" => "KICKER: Multi Post Pages",
    	    "category" => "postinfos",
            "id" => "post_kicker_multi",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no kicker on posts on multi post pages.<br /><strong>Example:
			</strong> <code>%category-linked%</code>"),

	array(    "name" => "KICKER: Single Post Pages",
    	    "category" => "postinfos",
            "id" => "post_kicker_single",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no kicker on posts on single post pages.<br /><strong>Example:
			</strong> <code>%category-linked%</code>"),

	array(    "name" => "KICKER: \"Page\" Pages",
    	    "category" => "postinfos",
            "id" => "post_kicker_page",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no kicker on \"page\" pages.<br /><em>NOTE: \"Page\" 
			pages don't have categories or tags</em>"),

	array(    "name" => "BYLINE: Homepage",
    	    "category" => "postinfos",
            "id" => "post_byline_home",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no byline on posts on the homepage.<br /><strong>Example:
			</strong> <code>By %author%, on %date('<i>F jS, Y</i>')%</code>"),

	array(    "name" => "BYLINE: Multi Post Pages",
    	    "category" => "postinfos",
            "id" => "post_byline_multi",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no byline on posts on multi post pages.<br /><strong>Example:
			</strong> <code>By %author%, on %date('<i>F jS, Y</i>')%</code>"),

	array(    "name" => "BYLINE: Single Post Pages",
    	    "category" => "postinfos",
            "id" => "post_byline_single",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no byline on posts on single post pages.<br /><strong>Example:
			</strong> <code>By %author%, on %date('<i>F jS, Y</i>')%</code>"),

	array(    "name" => "BYLINE: \"Page\" Pages",
    	    "category" => "postinfos",
            "id" => "post_byline_page",
            "type" => "postinfos",
            "std" => "",
            "info" => "Leave blank to display no byline on \"page\" pages.<br /><em>NOTE: \"Page\" 
			pages don't have categories or tags</em>"),

	array(    "name" => "FOOTER: Homepage",
    	    "category" => "postinfos",
            "id" => "post_footer_home",
            "type" => "postinfos",
            "std" => "%date('F jS, Y')% | %tags-linked('" . __('Tags: ','atahualpa') . "', ', ', ' | ')% " . 
__('Category:','atahualpa') . " %categories-linked(', ')% | %comments('" . __('Leave a comment','atahualpa') . 
"', '" . __('One comment','atahualpa') . "', '" . __('% comments','atahualpa') . "', '" . __('Comments are closed','atahualpa') . 
"')% %edit(' | ', '" . __('Edit this post','atahualpa') . "', '')%",
            "info" => "Leave blank to display no footer on posts on the homepage.<br /><strong>Example:</strong> 
			<code>%tags-linked('<i>&lt;strong&gt;Tags:&lt;/strong&gt; </i>', '<i>, </i>', '<i> - </i>')% 
			&lt;strong&gt;Categories:&lt;/strong&gt; %categories-linked('<i>, </i>')%&lt;br /&gt;
			%comments('<i>Leave a comment</i>', '<i>One comment so far</i>', '<i>% comments - be the next!</i>', 
			'<i>Comments are closed</i>')% %edit(' | ', 'Edit', '')%</code>"),

	array(    "name" => "FOOTER: Multi Post Pages",
    	    "category" => "postinfos",
            "id" => "post_footer_multi",
            "type" => "postinfos",
            "std" => "%date('F jS, Y')% | %tags-linked('" . __('Tags: ','atahualpa') . "', ', ', ' | ')% " . 
__('Category:','atahualpa') . " %categories-linked(', ')% | %comments('" . __('Leave a comment','atahualpa') . 
"', '" . __('One comment','atahualpa') . "', '" . __('% comments','atahualpa') . "', '" . __('Comments are closed','atahualpa') . 
"')% %edit(' | ', '" . __('Edit this post','atahualpa') . "', '')%",
            "info" => "Leave blank to display no footer on posts on multi post pages.<br /><strong>Example:</strong> 
			<code>%tags-linked('<i>&lt;strong&gt;Tags:&lt;/strong&gt; </i>', '<i>, </i>', '<i> - </i>')% &lt;strong&gt;
			Categories:&lt;/strong&gt; %categories-linked('<i>, </i>')%&lt;br /&gt;%comments('<i>Leave a comment</i>', 
			'<i>One comment so far</i>', '<i>% comments - be the next!</i>', '<i>Comments are closed</i>')% %edit(' | ', 'Edit', '')%</code>"),

	array(    "name" => "FOOTER: Single Post Pages",
    	    "category" => "postinfos",
            "id" => "post_footer_single",
            "type" => "postinfos",
            "std" => "%date('F jS, Y')% | %tags-linked('" . __('Tags: ','atahualpa') . "', ', ', ' | ')% " . 
__('Category:','atahualpa') . " %categories-linked(', ')% %edit(' | ', '" . __('Edit this post','atahualpa') . "', '')%",
            "info" => "Leave blank to display no footer on posts on single post pages.<br /><strong>Example:</strong> 
			<code>%tags-linked('<i>&lt;strong&gt;Tags:&lt;/strong&gt; </i>', '<i>, </i>', '<i> - </i>')% &lt;strong&gt;
			Categories:&lt;/strong&gt; %categories-linked('<i>, </i>')% | &lt;a href=\"#comments\"&gt;Skip to comments&lt;/a&gt; %edit(' | ', 'Edit', '')%</code>"),

	array(    "name" => "FOOTER: \"Page\" Pages",
    	    "category" => "postinfos",
            "id" => "post_footer_page",
            "type" => "postinfos",
            "std" => "",
			"lastoption" => "yes", 
            "info" => "Leave blank to have no footer on \"page\" pages.<br /><em>NOTE: \"Page\" pages 
			don't have categories or tags</em>"),

// New category: posts

    array(    "name" => "POST Container",
    	    "category" => "posts",
			"switch" => "yes",
            "id" => "post_container_style",
            "std" => "margin: 0 0 30px 0;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-container.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the container</strong> 
			that contains the whole post/page."),

    array(    "name" => "POST Container: STICKY",
    	    "category" => "posts",
            "id" => "post_container_sticky_style",
            "std" => "background: #eee url(" . $templateURI .
            "/images/sticky.gif) 99% 5% no-repeat;\nborder: dashed 1px #cccccc;\npadding: 10px;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-container.gif\"
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Additional styles for <strong>the container
			</strong> when it is <strong>STICKY</strong>. This works only in WP 2.7 and newer. 
			In WP 2.7, posts can be marked as \"sticky\" which will make them stay on the top of the homepage."),

    array(    "name" => "KICKER Box",
    	    "category" => "posts",
            "id" => "post_kicker_style",
            "std" => "margin: 0 0 5px 0;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-kicker.gif\"
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the box</strong> that 
			contains the post/page \"kicker\", <strong>and the text</strong> inside, except the links."),

    array(    "name" => "KICKER Box: Links",
    	    "category" => "posts",
            "id" => "post_kicker_style_links",
            "std" => "color: #000000;\ntext-decoration: none;\ntext-transform: uppercase;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-kicker-links.gif\"
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the links</strong> in the kicker box."),			

	array(    "name" => "KICKER Box: Links: Hover",
    	    "category" => "posts",
            "id" => "post_kicker_style_links_hover",
            "std" => "color: #cc0000;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-kicker-links-hover.gif\"
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the links</strong> in the 
			kicker box, in their <strong>hover</strong> state."),			
			
    array(    "name" => "HEADLINE Box",
    	    "category" => "posts",
            "id" => "post_headline_style",
            "std" => "",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-headline.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the box</strong> that contains 
			the post/page title. The text inside (= the post/page title) will be styled in the next section."),

    array(    "name" => "HEADLINE Box: Text",
    	    "category" => "posts",
            "id" => "post_headline_style_text",
            "std" => "padding: 0;\nmargin: 0;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-headline-text.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the post/page titles, when 
			they are NOT links</strong>, but regular text (= on single post pages and \"page\" pages)."),
			
    array(    "name" => "HEADLINE Box: Links",
    	    "category" => "posts",
            "id" => "post_headline_style_links",
            "std" => "color: #666666;\ntext-decoration: none;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-headline-links.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the post/page titles, when 
			they ARE links</strong> (= on multi post pages such as home, archive, category, tag, search...). 
			\"Page\" page titles are usually never links, but they might become links, i.e. if you expand 
			WordPress' search capabilities with a plugin."),			

	array(    "name" => "HEADLINE Box: Links: Hover",
    	    "category" => "posts",
            "id" => "post_headline_style_links_hover",
            "std" => "color: #000000;\ntext-decoration: none;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-headline-links-hover.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style the <strong>hover</strong> state of 
			<strong>post/page titles</strong>, when they are links."),			

    array(    "name" => "BYLINE Box",
    	    "category" => "posts",
            "id" => "post_byline_style",
            "type" => "textarea-large",
            "std" => "margin: 5px 0 10px 0;",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-byline.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the box</strong> that contains 
			the post/page byline, <strong>and the text</strong> inside, except the links."),

	array(    "name" => "BYLINE Box: Links",
    	    "category" => "posts",
            "id" => "post_byline_style_links",
            "type" => "textarea-large",
            "std" => "",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-byline-links.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the links</strong> in 
			the byline box."),

	array(    "name" => "BYLINE Box: Links: Hover",
    	    "category" => "posts",
            "id" => "post_byline_style_links_hover",
            "type" => "textarea-large",
            "std" => "",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-byline-links-hover.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the links</strong> in the 
			byline box, in their <strong>hover</strong> state."),

    array(    "name" => "BODY Box",
    	    "category" => "posts",
            "id" => "post_bodycopy_style",
            "type" => "textarea-large",
            "std" => "",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-body.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the box</strong> that 
			contains the post/page main text (= the \"body copy\"). The text and links in 
			the post/page main text box can be styled on the main tab \"Text & Link Styling\"."),
		
	array(    "name" => "FOOTER Box",
    	    "category" => "posts",
            "id" => "post_footer_style",
            "type" => "textarea-large",
            "std" => "margin: 0;\npadding: 5px;\nbackground: #eeeeee;\ncolor: #666;\nline-height: 18px;",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-footer.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the box</strong> that 
			contains the post/page footer, <strong>and the text</strong> inside, except the links."),

	array(    "name" => "FOOTER Box: Links",
    	    "category" => "posts",
            "id" => "post_footer_style_links",
            "type" => "textarea-large",
            "std" => "color: #333;\nfont-weight: normal;\ntext-decoration: none;",
            "info" => "<img src=\"" . $templateURI . "/options/images/post-footer-links.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the links</strong> in the footer box."),

	array(    "name" => "FOOTER Box: Links: Hover",
    	    "category" => "posts",
            "id" => "post_footer_style_links_hover",
            "type" => "textarea-large",
            "std" => "color: #333;\nfont-weight: normal;\ntext-decoration: underline;",
			"lastoption" => "yes", 
            "info" => "<img src=\"" . $templateURI . "/options/images/post-footer-links-hover.gif\" 
			style=\"float: right; margin: 0 0 10px 10px;\">" . "Style <strong>the links</strong> in the 
			footer box, in their <strong>hover</strong> state."),

			
// New category: posts-or-excerpts

    array(    "name" => "Excerpt length",
    	    "category" => "posts-or-excerpts",
			"switch" => "yes",
            "id" => "excerpt_length",
            "type" => "text",
            "size" => 6,
            "std" => 55,
            "info" => "Length of excerpts (= number of words)."),

     array(    "name" => "Don't strip these tags",
			"escape" => "yes",
    	    "category" => "posts-or-excerpts",
            "id" => "dont_strip_excerpts",
            "type" => "text",
            "size" => 40,
            "std" => "<p>",
            "info" => "By default, WordPress strips most HTML tags from excerpts, except &lt;p&gt;. Put the HTML tags here 
            that you don't want to have stripped by WordPress. Put only the opening tag here, without spaces or commas. 
            <br /><br /><strong>Example:</strong><br /><br />
            <code>&lt;p>&lt;a>&lt;span>&lt;em></code><br />...means \"don't strip &lt;p&gt;, &lt;/p&gt;, &lt;a href=\"...\">, &lt;/a&gt;, 
            &lt;span class=\"...\"&gt;, &lt;/span&gt;, &lt;em&gt; and &lt;/em&gt; from excerpts\"."),   
            
    array(    "name" => "Custom read more",
    	    "category" => "posts-or-excerpts",
            "id" => "custom_read_more",
            "type" => "textarea-large",
            "std" => "[...]",
            "info" => "By default, WordPress puts an ellipsis <code>[...]</code> at the bottom of excerpts. You can customize this with 
            HTML & PHP. <br /><br /><strong>Example</strong><br /><br />
            <code>&lt;p&gt;Continue reading &lt;a href=\"%permalink%\"&gt;%title%&lt;/a&gt;&lt;/p&gt;</code><br /><br />
            <code>%permalink%</code> will be replaced with the URL of the post<br />
            <code>%title%</code> will be replaced with the title of the post"),                   
                
    array(    "name" => "Posts or excerpts on HOME page?",
    	    "category" => "posts-or-excerpts",
            "id" => "excerpts_home",
            "type" => "select",
            "std" => "Full Posts",
            "options" => array("Only Excerpts", "Full Posts"),
            "info" => "Show full posts or only excerpts, on the Homepage?"),

    array(    "name" => "Show the first X posts on HOME page as full posts?",
    	    "category" => "posts-or-excerpts",
            "id" => "full_posts_homepage",
            "type" => "select",
            "std" => 0,
            "options" => array(0,1,2,3,4,5,6,7,8,9,10),
            "info" => "By setting a number here and setting the option above (Posts or excerpts on HOME page?) to 
			\"Only Excerpts\" you can show X full posts on top of the Homepage, followed by excerpts."),
			
    array(    "name" => "Posts or excerpts on CATEGORY pages?",
    	    "category" => "posts-or-excerpts",
            "id" => "excerpts_category",
            "type" => "select",
            "std" => "Only Excerpts",
            "options" => array("Only Excerpts", "Full Posts"),
            "info" => "Show full posts or only excerpts, on Category pages?"),
            
    array(    "name" => "Posts or excerpts on ARCHIVE pages?",
    	    "category" => "posts-or-excerpts",
            "id" => "excerpts_archive",
            "type" => "select",
            "std" => "Only Excerpts",
            "options" => array("Only Excerpts", "Full Posts"),
            "info" => "Show full posts or only excerpts, on (date based) Archive pages?"),

    array(    "name" => "Posts or excerpts on TAG pages?",
    	    "category" => "posts-or-excerpts",
            "id" => "excerpts_tag",
            "type" => "select",
            "std" => "Only Excerpts",
            "options" => array("Only Excerpts", "Full Posts"),
            "info" => "Show full posts or only excerpts, on Tag pages?"),
            
    array(    "name" => "Posts or excerpts on SEARCH RESULT pages?",
    	    "category" => "posts-or-excerpts",
            "id" => "excerpts_search",
            "type" => "select",
            "std" => "Only Excerpts",
            "options" => array("Only Excerpts", "Full Posts"),
            "info" => "Show full posts or only excerpts, on Search Result pages?"),

    array(    "name" => "Posts or excerpts on AUTHOR pages?",
    	    "category" => "posts-or-excerpts",
            "id" => "excerpts_author",
            "type" => "select",
            "std" => "Only Excerpts",
            "options" => array("Only Excerpts", "Full Posts"),
			"lastoption" => "yes", 
            "info" => "Show full posts or only excerpts, on Author pages?"),

// New category: posts-or-excerpts

    array(    "name" => "Post Thumbnail Width",
    	    "category" => "post-thumbnails",
			"switch" => "yes",
            "id" => "post_thumbnail_width",
            "type" => "text",
            "size" => 6,
            "std" => 150,
            "info" => "Width of Post Thumbnails in pixels.<br><br><span style='color:red'>Note: Post Thumbnails are only displayed on multi post pages, which 
			have been set to display 'Only Excerpts'. See menu tab 'Configure Excerpts'.</span>"),

    array(    "name" => "Post Thumbnail Height",
    	    "category" => "post-thumbnails",
            "id" => "post_thumbnail_height",
            "type" => "text",
            "size" => 6,
            "std" => 150,
            "info" => "Height of Post Thumbnails in pixels."),

    array(    "name" => "Crop Post Thumbnails?",
    	    "category" => "post-thumbnails",
            "id" => "post_thumbnail_crop",
            "type" => "select",
            "std" => "No",
            "options" => array("Yes", "No"),
            "info" => "Example: Original image is 600x400. The settings above are at 150/150. Then the post thumbnail has a size of...<ul><li>150x150 if 'Yes' (Crop)</li>
			<li>150x100 if 'No' (Don't Crop')</li></ul>
			With cropping you get the same size for all post thumbnails, but something will be cut off from the image (unless the image is square sized). "),

    array(    "name" => "Post Thumbnail CSS",
    	    "category" => "post-thumbnails",
            "id" => "post_thumbnail_css",
            "type" => "textarea-large",
            "std" => "float: left;\nborder: 0;\npadding: 0;\nbackground: none;\nmargin: 0 10px 5px 0;\n",
			"lastoption" => "yes", 
            "info" => "Style the Post Thumbnail. It is placed inside the 
			'bodycopy' of a post, right before the first paragraph of the excerpt:<br />
			&lt;div class='post-bodycopy'&gt;<br />&nbsp;&nbsp;&nbsp;<strong>&lt;img POST THUMBNAIL HERE class='wp-post-image' /&gt;</strong><br />&nbsp;&nbsp;&nbsp;&lt;p&gt;Post Excerpt starts here...&lt;/p&gt;<br />&lt;/div&gt;"),

			
// New category: more-tag

    array(    "name" => "Read More",
    	    "category" => "more-tag",
			"switch" => "yes",
           "id" => "more_tag",
            "std" => __("Continue reading %post-title%","atahualpa"),
            "type" => "text",
			"size" => "30", 
			"editable" => "yes",
			"lastoption" => "yes", 
            "info" => "Configure the \"Read More\" text here. The text you put here will be displayed whenever you use
			<code>&lt;!--more--&gt;</code> in a post, either by manually inserting that tag into a post or by using the 
			more button (see images below). This is a more fine-grained method of generating post excerpts than setting 
			the post display type to \"Excerpts\" (see menu tab \"Posts or Excerpts\"). <br /><br />Whenever you insert 
			<code>&lt;!--more--&gt;</code> into a post, only the text before that tag will be displayed on 
			multi-post-pages while the whole post will be displayed on its dedicated single post page. <br /><br />
			Use <code>%post-title%</code> to include the post title in the \"More\" text.<br /><br />
			Example:<br /> <code>Continue reading \"&lt;strong&gt;%post-title%&lt/strong&gt;\" &amp;raquo;</code><br /><br />
			You can use single and double quotes, and HTML. Examples:<ul><li><code>&lt;br /&gt;</code> for line breaks</li>
			<li><code>&lt;strong&gt; ... &lt;/strong&gt;</code> to make text <strong>bold</strong></li>
			<li><code>&lt;em&gt; ... &lt;/em&gt;</code> to make text <em>italic</em></li><li><code>&amp;nbsp;</code> to 
			include a non-breaking space</li><li><code>&amp;raquo;</code> for a right angle quote 
			<span style=\"font-size: 25px\">&raquo;</span></li><li><code>&amp;laquo;</code> for a left angle quote 
			<span style=\"font-size: 25px\">&laquo;</span></li><li><code>&amp;rsaquo;</code> for a right single angle quote 
			<span style=\"font-size: 25px\">&rsaquo;</span></li><li><code>&amp;lsaquo;</code> for a left single angle quote 
			<span style=\"font-size: 25px\">&lsaquo;</span></li><li><code>&amp;rarr;</code> for a right arrow 
			<span style=\"font-size: 25px\">&rarr;</span></li><li><code>&amp;larr;</code> for a left arrow 
			<span style=\"font-size: 25px\">&larr;</span></li></ul>The WordPress editor buttons to insert the 
			\"Read More\" tag into a post or page. They look different depending on whether you're in Visual or HTML mode.<br /><br >" .
			"<img src=\"" . $templateURI . "/options/images/readmore1.gif\" /><br /><br />
			<img src=\"" . $templateURI . "/options/images/readmore2.gif\" />"),
 
// New category: comments
                                                                
    array(    "name" => "Highlight Author comments?",
    	    "category" => "comments",
			"switch" => "yes",
            "id" => "author_highlight",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Highlight author (blog owner) comments with a different background color?"),

    array(    "name" => "Color for Author comment highlighting",
    	    "category" => "comments",
           "id" => "author_highlight_color",
            "std" => "ffecec",
            "type" => "text",
            "info" => "If you chose Yes above, set the background color for author comments here."),

    array(    "name" => "Border color for 2nd or lower level Author comments",
    	    "category" => "comments",
           "id" => "author_highlight_border_color",
            "std" => "ffbfbf",
            "type" => "text",
            "info" => "If you chose Yes above, set the border color for author comments in the 2nd 
			or lower level = direct reply to another comment"),
			
    array(    "name" => "Regular Comment Background Color",
    	    "category" => "comments",
           "id" => "comment_background_color",
            "std" => "ffffff",
            "type" => "text",
            "info" => "Background color for comments"),

    array(    "name" => "Alternating Comment Background Color",
    	    "category" => "comments",
           "id" => "comment_alt_background_color",
            "std" => "eeeeee",
            "type" => "text",
            "info" => "Background color for every second comment. Choose the same color as one option above if 
			you want the same background color for all comments."),

    array(    "name" => "Border between single comments",
    	    "category" => "comments",
           "id" => "comment_border",
            "std" => "dotted 1px #cccccc",
            "type" => "text",
            "info" => "Style the line that separates every comment from the next. No semicolon here."),

    array(    "name" => "Comment Author Name Size",
    	    "category" => "comments",
            "id" => "comment_author_size",
            "type" => "select",
            "std" => "110%",
            "options" => array("100%", "105%", "110%", "115%", "120%", "125%", "130%", "135%", "140%", "145%", "150%"),
            "info" => "Font size of comment author names relative to base font size."),

    array(    "name" => "Comment Reply link text",
    	    "category" => "comments",
           "id" => "comment_reply_link_text",
            "std" => __(" &middot; Reply", "atahualpa"),
            "type" => "text",
            "editable" => "yes",
            "info" => "The text for the \"Reply\" link for each comment."),

    array(    "name" => "Comment Edit link text",
    	    "category" => "comments",
           "id" => "comment_edit_link_text",
            "std" => __(" &middot; Edit", "atahualpa"),
            "type" => "text",
            "editable" => "yes",
            "info" => "The text for the \"Edit\" link for each comment. This text is only visible to users who are allowed
            to edit a comment."),

    array(    "name" => "Comment In Moderation text",
    	    "category" => "comments",
           "id" => "comment_moderation_text",
            "std" => __("Your comment is awaiting moderation.", "atahualpa"),
            "type" => "text",
            "editable" => "yes",
            "info" => "The text to show after a comment was submitted, if you've set your comments to be moderated before being
            published."),

    array(    "name" => "Comments Are Closed text",
    	    "category" => "comments",
           "id" => "comments_are_closed_text",
            "std" => __("<p>Comments are closed.</p>", "atahualpa"),
            "type" => "text",
            "editable" => "yes",
            "info" => "The text to show below a post or page if you've closed comments for that post or page."),

    array(    "name" => "Allow comments on \"Page\" pages, too?",
    	    "category" => "comments",
            "id" => "comments_on_pages",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Set to Yes to have a comment form (and comments if any) on \"Page\" pages, too, and not only on Post pages."),

    array(    "name" => "Separate trackbacks/pings from comments?",
    	    "category" => "comments",
            "id" => "separate_trackbacks",
            "type" => "select",
            "std" => "No",
            "options" => array("Yes", "No"),
            "info" => "For WP 2.6 and older: List comments, trackbacks and pings in the order they come in, or put all 
			trackbacks and pings below the comments?<br /><br /><em>Note: This works well with Atahualpa's own functions 
			but not if you use the plugin <a href=\"http://wordpress.org/extend/plugins/paged-comments/\">Paged Comments</a> 
			or Wordpress 2.7</em>"),	

    array(    "name" => "Avatar Size",
    	    "category" => "comments",
           "id" => "avatar_size",
            "type" => "select",
            "std" => "55",
            "options" => array("0", "20", "25", "30", "35", "40", "45", "50", "55", "60", "65", "70", "75", "80"),
            "info" => "The size of avatars, in pixels. 55 means 55 x 55 pixels. Choose 0 here to show no avatars 
			(or turn them off in the WordPress admin panel if your WP version has built in avatar support)."),			

    array(    "name" => "Avatar Style",
    	    "category" => "comments",
           "id" => "avatar_style",
            "std" => "margin: 0 8px 1px 0;\npadding: 3px;\nborder: solid 1px #ddd;\nbackground-color: #f3f3f3;\n-moz-border-radius: 3px;\n-khtml-border-radius: 3px;\n-webkit-border-radius: 3px;\nborder-radius: 3px;",
            "type" => "textarea-large",
            "info" => "Style avatars. The lines with \"radius\" create rounded corners in Firefox and Safari."), 

    array(    "name" => "Show XHTML tags?",
    	    "category" => "comments",
           "id" => "show_xhtml_tags",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Show the \"You can use these HTML tags\" info below the comment form?"),			

    array(    "name" => "Comment Form Style",
    	    "category" => "comments",
           "id" => "comment_form_style",
            "std" => "margin: 25px 0;\npadding: 25px;\nbackground: #eee;\n-moz-border-radius: 8px;\n-khtml-border-radius: 8px;\n-webkit-border-radius: 8px;\nborder-radius: 8px;",
            "type" => "textarea-large",
            "info" => "Style the comment form area = Box that contains the Name, Email, Website input fields, the comment textarea and the submit button."), 
			
    array(    "name" => "Submit Button Style",
    	    "category" => "comments",
           "id" => "submit_button_style",
            "std" => "padding: 4px 10px 4px 10px;\nfont-size: 1.2em;\nline-height: 1.5em;\nheight: 36px;",
            "type" => "textarea-large",
            "info" => "Style the comment submit button, i.e. give it margin to move it around. This section here is 
			specifically for the <strong>comment</strong> submit button. Additionally, default button styles will be applied, see
			menu tab \"Forms\", options \"Submit Buttons: Default Style\" and \"Submit Buttons: Hover Style\"."), 
			
    array(    "name" => "Comment display order",
    	    "category" => "comments",
            "id" => "comment_display_order",
            "type" => "select",
            "std" => "Oldest on top",
            "options" => array("Oldest on top", "Newest on top"),
			"lastoption" => "yes", 
            "info" => "For WP 2.6 and older: To list comments in reverse order choose \"Newest on top\". 
			In WP 2.7+ you can set this at Settings -> Discussion."),	
			
// New category: footer-style (don't name this "footer", Wordpress already uses that for their own footer in the admin area)

    array(    "name" => "Footer Style",
    	    "category" => "footer-style",
			"switch" => "yes",
           "id" => "footer_style",
            "std" => "background-color: #ffffff;\nborder-top: dashed 1px #cccccc;\npadding: 10px;
text-align: center;\ncolor: #777777;\nfont-size: 95%;",
            "type" => "textarea-large",
            "info" => "Style the footer box and the text inside."), 

    array(    "name" => "Footer Style: Links",
    	    "category" => "footer-style",
           "id" => "footer_style_links",
            "std" => "text-decoration: none;\ncolor: #777777;\nfont-weight: normal;",
            "type" => "textarea-large",
            "info" => "Style the links in the footer."), 

    array(    "name" => "Footer Style: Links: Hover",
    	    "category" => "footer-style",
           "id" => "footer_style_links_hover",
            "std" => "text-decoration: none;\ncolor: #777777;\nfont-weight: normal;",
            "type" => "textarea-large",
            "info" => "Style the links in the footer in hover state."), 

    array(    "name" => "Footer: Content",
    	    "category" => "footer-style",
           "id" => "footer_style_content",
            "std" => __("Copyright &copy; %current-year% %home% - All Rights Reserved","atahualpa"),
            "type" => "textarea-large",
			"editable" => "yes", 
            "info" => "Content in the footer area. You can use PHP (in WordPress, but not in WPMU), (X)HTML 
			and these placeholders ...
			<ul><li><code>%current-year%</code> to display the current year</li>
			<li><code>%page-XX%</code> to display the full link for a specific page. Replace XX with the ID of the page 
			you want to display the link for.</li><li><code>%home%</code> to display a full link to the homepage.</li>
			<li><code>%loginout%</code> to display a full Login/Logout link</li><li><code>%admin%</code> to display a 
			full link to the admin area. (Will only be displayed for logged in users.)</li>
			<li><code>%register%</code> to display a full register link</li><li><code>%rss%</code> to display (only) 
			the URL for the RSS feed. This is not a full link, just the URL. Use something like 
			<code>&lt;a href=\"%rss%\" rel=\"nofollow\"&gt;Posts Feed&lt;/a&gt;</code></li>
			<li><code>%comments-rss%</code> to display (only) the URL for the Comments RSS feed. This is not a full link, 
			just the URL. Use something like <code>&lt;a href=\"%comments-rss%\" rel=\"nofollow\"&gt;Comments Feed&lt;/a&gt;</code>. 
			(The BFA SEO option \"Nofollow RSS\" will not work here - nofollow would have to be included manually as 
			shown in these examples).</li>
			<li>In HTML, <span style=\"font-size:24px\">&copy;</span> can be displayed with <code>&amp;copy;</code>, 
			<span style=\"font-size:24px\">&trade;</span> with <code>&amp;trade;</code> and 
			<span style=\"font-size:24px\">&reg;</span> with <code>&amp;reg;</code></li></ul>"), 

	array(    "name" => "Sticky footer on short pages?",
    	    "category" => "footer-style",
            "id" => "sticky_layout_footer",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes"),
            "info" => "Make the layout footer \"stick\" at the bottom if the page is shorter than the browser viewport?"),
			
    array(    "name" => "Show number of queries &amp; timer?",
    	    "category" => "footer-style",
            "id" => "footer_show_queries",
            "type" => "select",
            "std" => "No",
            "options" => array("No", "Yes - visible", "Yes - in source code"),
			"lastoption" => "yes", 
            "info" => "Show the amount of database queries and the time required to render the given page, 
			at the bottom of every page? This can be useful to see how certain settings or plugins add to 
			the page rendering time."),

// New category: tables

    array(    "name" => "Table Style",
    	    "category" => "tables",
			"switch" => "yes",
            "id" => "table",
            "std" => "border-collapse: collapse;\nmargin: 10px 0;",
            "type" => "textarea-large",
            "info" => "Style the table as a whole <code>&lt;table&gt;</code> ... <code>&lt;/table&gt;</code>"),
			
    array(    "name" => "Table Caption Style",
    	    "category" => "tables",
            "id" => "table_caption",
            "std" => "background: #eeeeee;\nborder: #999999;\npadding: 4px 8px;\ncolor: #666666;",
            "type" => "textarea-large",
            "info" => "The table caption (if you use any) is (usually) the first row in a table.<br /><br />
			<strong>Example:</strong><br /><code>&lt;table&gt;<br /><i>&lt;caption&gt;Results May 2008&lt;/caption&gt;</i><br />
			&lt;thead&gt;&lt;tr&gt;&lt;th&gt;Name&lt;/th&gt;&lt;th&gt;Address&lt;/th&gt;&lt;/tr&gt;&lt;/thead&gt;<br />
			&lt;tfoot&gt;&lt;tr&gt;&lt;td&gt;Previous&lt;/td&gt;&lt;td&gt;Next&lt;/td&gt;&lt;/tr&gt;&lt;/tfoot&gt;<br />
			&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;John&lt;/td&gt;&lt;td&gt;Smallville&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;<br />
			&lt;/table&gt;</code><br /><br /><em>Note how the table footer <code>tfoot</code> comes <strong>before</strong> 
			the body <code>tbody</code></em>"),

    array(    "name" => "Table Header Cells",
    	    "category" => "tables",
            "id" => "table_th",
            "std" => "background: #888888;\ncolor: #ffffff;\nfont-weight: bold;\nfont-size: 90%;\npadding: 4px 8px;\n
			border: solid 1px #ffffff;\ntext-align: left;",
            "type" => "textarea-large",
            "info" => "Style the table header cells <code>&lt;th&gt;</code> ... <code>&lt;/th&gt;</code>"),

    array(    "name" => "Table Body Cells",
    	    "category" => "tables",
            "id" => "table_td",
            "std" => "padding: 4px 8px;\nbackground-color: #ffffff;\nborder-bottom: 1px solid #dddddd;\ntext-align: left;",
            "type" => "textarea-large",
            "info" => "Style the regular table cells <code>&lt;td&gt;</code> ... <code>&lt;/td&gt;</code>"),

    array(    "name" => "Table Footer Cells",
    	    "category" => "tables",
            "id" => "table_tfoot_td",
            "std" => "",
            "type" => "textarea-large",
            "info" => "You can style the table footer cells individually. <em>Or else they'll get the
			same style as the Table Body Cells.</em>"),
			
    array(    "name" => "Zebra stripe all tables?",
    	    "category" => "tables",
            "id" => "table_zebra_stripes",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Add a different style to every second row in <strong>all</strong> tables in posts and pages?
			Alternatively, set this to \"No\" and add the class <code>zebra</code> to individual tables that you want to 
			zebra stripe.<br /><br /><strong>Example:</strong><br /><code>&lt;table class=\"zebra\"&gt; ... &lt;/table&gt;</code>"),

    array(    "name" => "Zebra row TD style",
    	    "category" => "tables",
            "id" => "table_zebra_td",
            "std" => "background: #f4f4f4;",
            "type" => "textarea-large",
            "info" => "If you chose to zebra stripe tables, set the style for the cells in every second row here."),
			
    array(    "name" => "Hover effect for all tables?",
    	    "category" => "tables",
            "id" => "table_hover_rows",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Change the style of table rows when the mouse pointer hovers over them, for <strong>all</strong> 
			tables in posts and pages? Alternatively, set this to \"No\" and add the class <code>hover</code>
			to individual tables that you want to apply the hover effect on.<br /><br /><strong>Example:</strong><br />
			<code>&lt;table class=\"hover\"&gt; ... &lt;/table&gt;</code><br /><br />Multiple classes can be added, too, 
			i.e. to add both the zebra and the hover effect to an individual table:<br /><br />
			<code>&lt;table class=\"zebra hover\"&gt; ... &lt;/table&gt;</code>"),

    array(    "name" => "Hover row TD style",
    	    "category" => "tables",
            "id" => "table_hover_td",
            "std" => "background: #e2e2e2;",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "If you chose to use a hover efect for table rows, set the style for the cells in hovered table rows here."),

// New category: forms

    array(    "name" => "Form fields: Style",
    	    "category" => "forms",
			"switch" => "yes",
            "id" => "form_input_field_style",
            "std" => "color: #000000;\nborder-top: solid 1px #333333;\nborder-left: solid 1px #333333;\nborder-right: solid 1px #999999;\nborder-bottom: solid 1px #cccccc;",
            "type" => "textarea-large",
            "info" => "Style the text input fields and textareas in forms. "),

    array(    "name" => "Form fields: Background image",
    	    "category" => "forms",
           "id" => "form_input_field_background",
            "std" => "inputbackgr.gif",
            "type" => "text", 
			"size" => "35", 
            "info" => "The \"shadow\" inside of text fields and texareas. Other available shadows are <code>inputbackgr-red.gif</code>,
			<code>inputbackgr-green.gif</code> and <code>inputbackgr-blue.gif</code>. Or, upload your own image to <code>" .
            $css_img_path . "images/</code>. Leave blank to have no background image in form input fields."),
			
    array(    "name" => "Highlight form fields?",
    	    "category" => "forms",
            "id" => "highlight_forms",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Highlight form input fields when they get focus (when someone clicks into the field)?"),

    array(    "name" => "Highlight form fields: Style",
    	    "category" => "forms",
            "id" => "highlight_forms_style",
            "std" => "background: #e8eff7;\nborder-color: #37699f;",
            "type" => "textarea-large",
            "info" => "If you chose \"Yes\" above, style the highlighted state of input fields here."),

    array(    "name" => "Submit Buttons: Default Style",
    	    "category" => "forms",
            "id" => "button_style",
            "std" => "background-color: #777777;\ncolor: #ffffff;\nborder: solid 2px #555555;\nfont-weight: bold;",
            "type" => "textarea-large",
            "info" => "Style submit buttons in their <strong>default</strong> state."),

    array(    "name" => "Submit Buttons: Hover Style",
    	    "category" => "forms",
            "id" => "button_style_hover",
            "std" => "background-color: #6b9c6b;\ncolor: #ffffff;\nborder: solid 2px #496d49;",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "Style submit buttons in their <strong>hover</strong> state."),

// New category: blockquotes

    array(    "name" => "Blockquotes: Style",
    	    "category" => "blockquotes",
			"switch" => "yes",
            "id" => "blockquote_style",
            "std" => "color: #555555;\npadding: 1em 1em;\nbackground: #f4f4f4;\nborder: solid 1px #e1e1e1;",
            "type" => "textarea-large",
            "info" => "<img src=\"" . $templateURI . "/options/images/blockquotes.gif\" style=\"float: right; margin: 0 0 10px 10px;\">" . 
			"Style blockquotes. <br /><br /><strong>Example:</strong><br /><code>font: italic 1.1em georgia, serif;<br />color: #336699;<br />
			padding: 0 1em;<br />background: #c9dbed;<br />border: dashed 5px #336699;</code><br /><br />Example Screenshot is from IE7. 
			It will look different on non-IE browsers."),
			
    array(    "name" => "Blockquotes in blockquotes: Style",
    	    "category" => "blockquotes",
            "id" => "blockquote_style_2nd_level",
            "std" => "color: #444444;\npadding: 1em 1em;\nbackground: #e1e1e1;\nborder: solid 1px #d3d3d3;",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "Style blockquotes inside of blockquotes."),

// New category: images

    array(    "name" => "Images in Posts",
    	    "category" => "images",
			"switch" => "yes",
            "id" => "post_image_style",
            "std" => "padding: 5px;\nborder: solid 1px #dddddd;\nbackground-color: #f3f3f3;\n-moz-border-radius: 3px;\n-khtml-border-radius: 3px;\n-webkit-border-radius: 3px;\nborder-radius: 3px;",
            "type" => "textarea-large",
            "info" => "Style images in posts, when they have no caption. The lines with \"radius\" create
			rounded corners in Firefox and Safari. To remove the border around images, delete everything in this box."),
			
    array(    "name" => "Images in Posts: Caption Style",
    	    "category" => "images",
            "id" => "post_image_caption_style",
            "std" => "border: 1px solid #dddddd;\ntext-align: center;\nbackground-color: #f3f3f3;\npadding-top: 4px;\nmargin: 10px 0 0 0;\n-moz-border-radius: 3px;\n-khtml-border-radius: 3px;\n-webkit-border-radius: 3px;\nborder-radius: 3px;",
            "type" => "textarea-large",
            "info" => "Style the caption box for images in posts, that have a caption. The lines with \"radius\"
			create rounded corners in Firefox and Safari. To remove the border around images with caption, delete everything in this box."),

    array(    "name" => "Caption Text: Style",
    	    "category" => "images",
            "id" => "image_caption_text",
            "std" => "font-size: 0.8em;\nline-height: 13px;\npadding: 2px 4px 5px;\nmargin: 0;\ncolor: #666666;",
            "type" => "textarea-large",
			"lastoption" => "yes", 
            "info" => "Style the caption text."),

// New category: html-inserts

    array(    "name" => "HTML Inserts: Header",
    	    "category" => "html-inserts",
			"switch" => "yes",
            "id" => "html_inserts_header",
            "std" => "",
            "type" => "textarea-large",
			"editable" => "yes", 
            "info" => "Add code here (JavaScript, CSS, certain type of HTML) that you want to put into the header section of the website, 
			between <code>&lt;head&gt;</code> and <code>&lt;/head&gt;</code>. <strong>Note:</strong> Any HTML you put here shouldn't be
			\"visible\" HTML such as a table or a DIV container. If you put HTML here, then it would be machine parsable code, something like a 
			meta tag, such as:<br /><code>&lt;meta name=\"author\" content=\"John W. Doe\" /&gt;</code>.
			<br /><br />Since 3.6.5 <span style='color:red'>PHP code cannot be used anymore</span> in HTML/CSS Inserts.
			<strong>Google Analytics code</strong> would go here. '. 
			"),

    array(    "name" => "HTML Inserts: Body Tag",
    	    "category" => "html-inserts",
            "id" => "html_inserts_body_tag",
            "std" => "",
            "type" => "textarea-large",
			"editable" => "yes", 
            "info" => "Add code here (usually Javascript) that you want to add to the opening body tag <code>&lt;body&gt;</code> of the website.<br /><br />
			<strong>Example:</strong><br /><code>onLoad=\"alert('The page is loading... now!')\"</code> would result
			in an output of <code>&lt;body <i>onLoad=\"alert('The page is loading... now!')\"</i>&gt;</code> instead
			of the regular <code>&lt;body&gt;</code>.
			<br /><br />Since 3.6.5 <span style='color:red'>PHP code cannot be used anymore</span> in HTML/CSS Inserts."),

    array(    "name" => "HTML Inserts: Body Top",
    	    "category" => "html-inserts",
            "id" => "html_inserts_body_top",
            "std" => "",
            "type" => "textarea-large",
			"editable" => "yes", 
            "info" => "Add code here (JavaScript, HTML, CSS) that you want to put into the body section of the website, between 
			<code>&lt;body&gt;</code> and <code>&lt;/body&gt;</code>, right after <code>&lt;body&gt;</code>.
			<br /><br />Since 3.6.5 <span style='color:red'>PHP code cannot be used anymore</span> in HTML/CSS Inserts."),

    array(    "name" => "HTML Inserts: Body Bottom",
    	    "category" => "html-inserts",
            "id" => "html_inserts_body_bottom",
            "std" => "",
            "type" => "textarea-large",
			"editable" => "yes", 
            "info" => "Add code here (JavaScript, HTML, CSS) that you want to put into the body section of the website, 
			between <code>&lt;body&gt;</code> and <code>&lt;/body&gt;</code>, right before <code>&lt;/body&gt;</code>.
			<br /><br />Since 3.6.5 <span style='color:red'>PHP code cannot be used anymore</span> in HTML/CSS Inserts."),

    array(    "name" => "CSS Inserts",
    	    "category" => "html-inserts",
            "id" => "html_inserts_css",
            "std" => "",
            "type" => "textarea-large",
			"editable" => "yes", 
			"lastoption" => "yes", 
            "info" => "Add CSS code here that you want to append to your theme's CSS file.<br /><br /><strong>Example</strong><br />
			<code>.newclass {<br />color: #123456;<br />border: solid 1px #000000;<br />
			font-family: arial, \"comic sans ms\", sans-serif;<br />background: url(". $css_img_path ."images/myimage.gif);
            <br />}</code>
			<br /><br /><strong>Example 2</strong><br /><code>h1 { font-size: 34px; line-height: 1.2; margin: 0.3em 0 10px; }<br />
h2 { font-size: 28px; line-height: 1.3; margin: 1em 0 .2em; }<br />
h3 { font-size: 24px; line-height: 1.3; margin: 1em 0 .2em; }<br />
h4 { font-size: 19px; margin: 1.33em 0 .2em; }<br />
h5 { font-size: 1.3em; margin: 1.67em 0; font-weight: bold; }<br />
h6 { font-size: 1.15em; margin: 1.67em 0; font-weight: bold; }<br /></code><br />Since 3.6.5 <span style='color:red'>PHP code cannot be used anymore</span> in HTML/CSS Inserts."),


// New category: Archives page

    array(    "name" => "Archives Page ID",
    	    "category" => "archives-page",
			"switch" => "yes",
           "id" => "archives_page_id",
            "std" => "",
            "type" => "text",
			"size" => "5",
            "info" => "Atahualpa has no Archives page by default but you can create a custom one:<ul>
			<li>Put the ID of an existing page into this field to make that page the Archives page.</li>
			<li>This can be an empty page or a page with content.</li><li>If the page has content, the archives 
			will be appended at the bottom.</li></ul>An Archives page is a \"Page\" page listing the links to 
			(usually: monthly) archives and the categories, similar to a sitemap, but usually without a list 
			of \"Page\" pages. The difference to the archive links or select menu in the sidebar is that the 
			links will be displayed as regular content in the middle column"),

    array(    "name" => "Show Archives by Date?",
    	    "category" => "archives-page",
           "id" => "archives_date_show",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Show archives by date?"),
			
    array(    "name" => "Archives by Date: Title",
    	    "category" => "archives-page",
           "id" => "archives_date_title",
            "std" => __("Archives by Month","atahualpa"),
            "type" => "text",
            "info" => "The headline for the yearly/monthly/daily/postbypost archives"),

    array(    "name" => "Archives by Date: Type",
    	    "category" => "archives-page",
           "id" => "archives_date_type",
            "type" => "select",
            "std" => "monthly",
            "options" => array("yearly", "monthly", "weekly", "daily", "postbypost"),
            "info" => "List the date based archives by year, month, week, day or post by post?"),

    array(    "name" => "Archives by Date: Limit",
    	    "category" => "archives-page",
           "id" => "archives_date_limit",
            "std" => "",
            "type" => "text",
            "info" => "Optional: Limit the amount of date based archive links. Leave blank for no limit. 
			<strong>Example:</strong> <code>30</code>"),

    array(    "name" => "Archives by Date: Show post count?",
    	    "category" => "archives-page",
           "id" => "archives_date_count",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Show the post count for each date based archive link? Won't be used if you chose \"postbypost\" above."),

    array(    "name" => "Show Archives by Category?",
    	    "category" => "archives-page",
           "id" => "archives_category_show",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Show archives by category?"),
			
    array(    "name" => "Archives by Category: Title",
    	    "category" => "archives-page",
           "id" => "archives_category_title",
            "std" => __("Archives by Category","atahualpa"),
            "type" => "text",
            "info" => "The headline for the category archives"),

    array(    "name" => "Archives by Category: Show post count?",
    	    "category" => "archives-page",
           "id" => "archives_category_count",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "Display the post count after each category link?"),

    array(    "name" => "Archives by Category: Depth",
    	    "category" => "archives-page",
           "id" => "archives_category_depth",
            "type" => "select",
            "std" => "0",
            "options" => array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"),
            "info" => "Limit the depth of category levels to be displayed. Choose 0 to display all categories (= no depth limit)"),

    array(    "name" => "Archives by Category: Order by",
    	    "category" => "archives-page",
           "id" => "archives_category_orderby",
            "type" => "select",
            "std" => "name",
            "options" => array("ID", "name", "count"),
            "info" => "Sort the category archive links by <ul><li><strong>ID</strong> - chronologically</li>
			<li><strong>name</strong> - alphabetically</li><li><strong>count</strong> - post count</li></ul>"),

    array(    "name" => "Archives by Category: Order",
    	    "category" => "archives-page",
           "id" => "archives_category_order",
            "type" => "select",
            "std" => "ASC",
            "options" => array("ASC", "DESC"),
            "info" => "Sort the category list<ul><li><strong>ASC</strong> - ascending</li>
			<li><strong>DESC</strong> - descending</li></ul>"),	

    array(    "name" => "Archives by Category: Feed Link",
    	    "category" => "archives-page",
           "id" => "archives_category_feed",
            "type" => "select",
            "std" => "No",
            "options" => array("Yes", "No"),
			"lastoption" => "yes", 
            "info" => "Show a linked RSS icon after each category link?"),			
            
            
// New category: css-javascript

    array(    "name" => "CSS: External file or inline?",
    	    "category" => "css-javascript",
			"switch" => "yes",
           "id" => "css_external",
            "type" => "select",
            "std" => "Inline",
            # "options" => array("External & Static", "External", "Inline"),
            "options" => array("External", "Inline"),
            "info" => "Should the CSS code be in an external file, or inline in the header of each page?<br /><br /> 
            You might want to choose inline if your average page view per visitor is close to 1. In that case 
            inline CSS might actually be faster than an external CSS file."),
/*
			<br /><br /><span style='background:white;color:red'>NEW</span> Since 3.4.8 you can create a static CSS file inside 
			the WP Uploads directory (<code>" . $upload_path . "</code> - Writable? " . $is_writable . ") , which will be updated each time you click the huge green 'Save Changes' button. 
			For better site performance you should choose the '<strong>External & Static</strong>' option, unless your WP Uploads 
			directory isn't writable and you cannot make it writable."),
*/

    array(    "name" => "Javascript: External file or inline?",
    	    "category" => "css-javascript",
           "id" => "javascript_external",
            "type" => "select",
            "std" => "Inline",
            # "options" => array("External & Static", "External", "Inline"),
            "options" => array("External", "Inline"),
            "info" => "Should the Javascript code be in an external file, or inline in the header of each page? 
            Same considerations apply as above."),
/*			
			<br /><br /><span style='background:white;color:red'>NEW</span> Since 3.4.8 you can create a static Javascript file inside 
			the WP Uploads directory, which will be updated each time you click the huge green 'Save Changes' button. 
			For better site performance you should choose the '<strong>External & Static</strong>' option, unless your WP Uploads 
			directory isn't writable and you cannot make it writable."),
*/

    array(    "name" => "IE6 PNG Fix CSS Selectors",
    	    "category" => "css-javascript",
           "id" => "pngfix_selectors",
            "type" => "textarea-large",
            "std" => "",
            "info" => "If you're using transparent PNG images, put the CSS selectors here that contain tranparent PNG images, so 
			a IE6 PNG tranparency fix can be applied on those elements. Separate selectors with commas. <br>For example you would use <strong>a.posts-icon, a.comments-icon, a.email-icon, img.logo</strong>"),
			
    array(    "name" => "CSS: Compress?",
    	    "category" => "css-javascript",
           "id" => "css_compress",
            "type" => "select",
            "std" => "Yes",
            "options" => array("Yes", "No"),
            "info" => "By choosing to compress the CSS  
            you'll end up with an additional file size of ~7 Kbyte IF you have mod_deflate or gzip running on your server. 
            CSS file sizes: No Atahualpa Compression / No Gzip: ~ 60-70 KByte. With Atahualpa Compression: ~ 35 KByte. With Atahualpa 
            Compression plus Gzip/mod_defalte: ~ 7 KByte."),

    array(    "name" => "Allow debugging?",
    	    "category" => "css-javascript",
           "id" => "allow_debug",
            "type" => "select",
            "std" => "Yes",
            "lastoption" => "yes",
            "options" => array("Yes", "No"),
            "info" => "Setting this to <strong>Yes</strong> will show inline, uncompressed 
			CSS and Javascript in the source code of your site, regardless of your settings above, <strong>IF</strong> <code>?bfa_debug=1</code> was added to the URL. Additionally, the Wordpress and Atahualpa versions will be displayed as meta tags, and a meta robots tag \"noindex,follow\" will be inserted, to avoid duplicate content 
			issues in search engines.<br /><br /><strong>Set this to <code>YES</code> before you ask someone at i.e. forum.bytesforall.com to have a look at the code of your site</strong>.")
			
/*                                
    array(    "name" => "Javascript: Compress?",
    	    "category" => "css-javascript",
           "id" => "javascript_compress",
            "type" => "select",
            "std" => "Yes",
            	"lastoption" => "yes",
            "options" => array("Yes", "No"),
            "info" => "Turn this off whenever you want someone to look at the Javascript code of your site. The compressed Javascript will be almost 
            unreadable.<br /><br /><em>The compressed Javascript may not work properly if lines in the code, i.e. code 
            that you added, were not finished with a semicolon. If you experience weird Javascript related behavior, turn this option here off to see if it's 
            caused by the compression.</em>"),
*/

/* 
    array(    "name" => "Remove comment-reply.js?",
    	    "category" => "css-javascript",
           "id" => "include_wp_comment_reply_js",
            "type" => "select",
            "std" => "No",
			"lastoption" => "yes", 
            "options" => array("Yes", "No"),
            "info" => "For WP 2.7+: Include WP's \"comment-reply.js\" in Atahualpa's js.php and remove it from the header? This will save 1 HTTP request to the web server."),
*/
                     
);

// Merge arrays to get different options sets for WP 2.7+ (with new paged comments settings) and WP 2.6 and older 
if (function_exists('wp_list_comments')) {
	$options = array_merge($options1, $options2, $options3);  // WP 2.7 and newer
} else {
	$options = array_merge($options1, $options3);  // WP 2.6 and older
}

?>