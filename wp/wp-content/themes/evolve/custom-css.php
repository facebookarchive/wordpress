    <?php $options = get_option('evolve');
    $template_url = get_template_directory_uri();  
  if ($options['evl_header_slider'] == "disable" || $options['evl_header_slider'] == "") {
   
  $evolve_css_data .= 'ul.slides li.slide {display:block !important; }';
 } else { } 
 
   if ($options['evl_pos_sidebar'] == "right") { 
  
} if ($options['evl_pos_sidebar'] == "left") { 
  
  $evolve_css_data .= '/**
 * Basic 2 column (aside)(content) fixed layout
 * 
 * @package WPEvoLve
 * @subpackage Layouts
 * @beta
 */

.container { width: 960px; margin: 20px auto; }
#wrapper {width:990px;}
.hfeed { width: 620px; float: right; }
.aside { width: 320px; float: left; }
.footer { clear: both; }';
   
  } if ($options['evl_pos_sidebar'] == "left" && $options['evl_sidebar_num'] == "two") { 
  
 $evolve_css_data .= '/**
 * Basic 3 column (aside)(aside)(content) fixed layout
 * 
 * @package WPEvoLve
 * @subpackage Layouts
 * @beta
 */

.container { width: 960px; margin: 20px auto; }
.hfeed { width: 506px; float: right; }
.aside { width: 210px; float: left;margin-right:15px; }
.footer { clear: both; }
.entry-content img {max-width: 492px;}
.widget:after {content: url('.$template_url.'/library/media/images/widget-shadow.png);margin-left:-105px;}';
  
  
 } if ($options['evl_pos_sidebar'] == "right" && $options['evl_sidebar_num'] == "two") { 
 
 $evolve_css_data .= '/**
 * Basic 3 column (aside)(aside)(content) fixed layout
 * 
 * @package WPEvoLve
 * @subpackage Layouts
 * @beta
 */

.container { width: 960px; margin: 20px auto; }
.hfeed { width: 506px; float: left; }
.aside { width: 210px; float: right;margin-left:15px; }
.footer { clear: both; }
.entry-content img {max-width: 492px;}
.widget:after {content: url('.$template_url.'/library/media/images/widget-shadow.png);margin-left:-105px;}'; 
  
  
} if ($options['evl_pos_sidebar'] == "left_right" && $options['evl_sidebar_num'] == "two") { 

 $evolve_css_data .= '/**
 *  3 columns (aside)(content)(aside) fixed layout
 * 
 */

.container { width: 960px; margin: 20px auto; }
.hfeed { width: 506px; float: right; }
#wrapper {width:990px;}
#secondary { width: 210px; float: left;margin-right:15px; }
#secondary-2 { width: 210px; float: right;margin-left:15px; }
.footer { clear: both; }
.entry-content img {max-width: 492px;}
.widget:after {content: url('.$template_url.'/library/media/images/widget-shadow.png);margin-left:-105px;}';
  
  
} if ($options['evl_width_layout'] == "fluid") { 

 $evolve_css_data .= '/**
 * Basic 1 column (content)(aside) fluid layout
 * 
 * @package WPEvoLve
 * @subpackage Layouts
 * @beta
 */

.container { min-width:960px;max-width:2400px;width:95%;margin:20px 3%;}
#wrapper {margin:0;width:100%;}
.hfeed { min-width:620px; width: 65%; }
.aside { min-width:320px; width: 34%; }

#slide_holder {min-width:620px;width: 65%;}
.slide-container {min-width:620px;width: 100%;}

.widgets-back {margin:10px 0 0 0 !important;}
.widgets-back-inside {min-width:960px;max-width:2400px;width:95%;margin:20px 3%;}



#righttopcolumn {min-width:320px; width: 34%;}

#search-text-box {min-width:220px; width: 69%;}
#search-button-box {min-width:90px; width: 30%;}

.space-2 {width:90%;}';
 
  
} if ($options['evl_width_layout'] == "fluid" && $options['evl_sidebar_num'] == "two") {

 $evolve_css_data .= '/**
 * Basic 1 column (content)(aside) fluid layout
 * 
 * @package WPEvoLve
 * @subpackage Layouts
 * @beta
 */

.container { min-width:960px;max-width:2400px;width:95%;margin:20px 3%;}
#wrapper {min-width:990px;max-width:2400px;width:95%;}
.hfeed { width: 57%;min-width:506px; }
.aside { width: 20%;min-width:210px;  }

#slide_holder {min-width:526px;width: 57%;}
.slide-container {min-width:526px;width: 100%;}



#righttopcolumn {min-width:315px; width: 41%;}

#search-text-box {min-width:50px; width: 69%;}
#search-button-box {min-width:35px; width: 30%;}';

} if ($options['evl_width_layout'] == "fluid" && $options['evl_sidebar_num'] == "two" && $options['evl_pos_sidebar'] == "left_right") { 

 $evolve_css_data .= '/**
 * 3 columns (aside)(content)(aside) fluid layout
 * 
 */

.container { min-width:960px;max-width:2400px;width:95%;margin:20px 3%;}
.hfeed { width: 57%;min-width:506px; }
.home .hfeed, .archive .hfeed, .single .hfeed, .page .hfeed {margin-right:10px;}
#secondary, #secondary-2 { width: 20%;min-width:210px; }

#slide_holder {min-width:526px;width: 57%;}
.slide-container {min-width:526px;width: 100%;}

#righttopcolumn {min-width:315px; width: 41%;}

#search-text-box {min-width:50px; width: 69%;}
#search-button-box {min-width:35px; width: 30%;}';


 } if ($options['evl_sidebar_num'] == "disable") { 
 
 $evolve_css_data .= '/**
 * 1 column (content) fixed layout
 * 
 * @package WPEvoLve
 * @subpackage Layouts
 * @beta
 */

.container { width: 960px; margin: 20px auto; }
.hfeed { width: 960px; }
.footer { clear: both; }'; 

} if ($options['evl_sidebar_num'] == "disable" && $options['evl_width_layout'] == "fluid") { 

 $evolve_css_data .= '/**
 * 1 column (content) fluid layout
 * 
 */

.container { min-width:960px;max-width:2400px;width:95%;margin:20px 3%;}
.hfeed { width: 100%;min-width:960px; }';


 } if ($options['evl_content_back'] == "dark") { 
 
 
 $evolve_css_data .= '/**
 * Dark content
 * 
 */

body {color:#fff;}

a, .entry-content a:link, .entry-content a:active, .entry-content a:visited {color:#3d9dff;}

.entry-content {text-shadow:0 1px 0px #000;}

.content { background-image:url('.$template_url.'/library/media/images/dark/divider-dark.png);} 
.entry-title, .entry-title a {color:#ccc;text-shadow:0 1px 0px #000;}
.entry-title, .entry-title a:hover { color: #fff; }

input[type="text"], input[type="password"], textarea {border:1px solid #111!important;}


.entry-content img, .entry-content .wp-caption {background:#444;border: 2px solid #404040;}

#slide_holder .featured-title a, #slide_holder .twitter-title {color:#ddd;}
#slide_holder .featured-title a:hover {color:#fff;}
#slide_holder .featured-title, #slide_holder .twitter-title, #slide_holder p {text-shadow:0 1px 1px #333;}

#slide_holder img {background:#444;border: 2px solid #404040;}



#slide_holder .carousel-trans {background-image:url('.$template_url.'/library/media/images/dark/carousel-trans-dark.png);}

var, kbd, samp, code, pre {background-color:#333;}
pre {border-color:#111;}

.twitter-time {color:#aaa;}
.twitter-status, .post-more {border-color: #222; border-bottom-color: #111;text-shadow: 0 1px 0 #111;
   color: #aaa;
    background: #505050;               
    background: -webkit-gradient(linear,left top,left bottom,color-stop(.2, #505050),color-stop(1, #404040));
    background: -moz-linear-gradient(center top,#505050 20%,#404040 100%);
    background: -o-linear-gradient(top, #505050,#404040) !important;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#505050\', endColorstr=\'#404040\');
    -webkit-box-shadow:  0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
    -moz-box-shadow:   0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
    box-shadow:   0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
}
a.post-more:hover {color:#fff;}


#search-button {
    border-color: #222; border-bottom-color: #111;text-shadow: 0 1px 0 #111;
    color: #aaa;
    background: #505050;               
    background: -webkit-gradient(linear,left top,left bottom,color-stop(.2, #505050),color-stop(1, #404040));
    background: -moz-linear-gradient(center top,#505050 20%,#404040 100%);
    background: -o-linear-gradient(top, #505050,#404040) !important;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#505050\', endColorstr=\'#404040\');
    -webkit-box-shadow:  0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
    -moz-box-shadow:   0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
    box-shadow:   0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);}
    
#search-button:hover {color:#fff!important;}    

.social-title, #reply-title {color:#fff;text-shadow:0 1px 0px #222;}

.social-trans-left {background-image:url('.$template_url.'/library/media/images/dark/social-trans-left-dark.png);}
.social-trans-right {background-image:url('.$template_url.'/library/media/images/dark/social-trans-right-dark.png);}


.menu-back {border-top-color:#515151;}

.page-title {text-shadow:0 1px 2px #111;}

.comment-count, .home .hentry .comment-count a, .archive .hentry .comment-count a, .search .hentry .comment-count a {
    background-image: url('.$template_url.'/library/media/images/dark/comment-dark.png)!important;
}

.hentry .entry-header .comment-count a { background:none !important;-moz-box-shadow:none !important;}

.content-bottom {background:#353535;border-color:#303030; }

.entry-header {border-color:#333; 
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#505050\', endColorstr=\'#404040\');
 background: -moz-linear-gradient(center top , #505050 20%, #404040 100%) repeat scroll 0 0 transparent !important;
    background: -webkit-gradient(linear,left top,left bottom,color-stop(.2, #505050),color-stop(1, #404040)) !important;
    background: -o-linear-gradient(top, #505050,#404040) !important;     
     box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 2px rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    -box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 2px rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    -webkit-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 2px rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    -moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
}

.entry-header a {color:#eee;}

.entry-meta {text-shadow:0 1px 0 #111;}

.edit-post a {-moz-box-shadow:0 0 2px #333;color:#333;text-shadow:0 1px 0 #fff;}

.hentry {background-image:url('.$template_url.'/library/media/images/dark/divider-tile-dark.png);}

.entry-footer a:hover {color:#fff;}

.widget-content {  
  background: #404040;
    border-color: #222;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
    -box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
    -webkit-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
     -moz-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
    color: #FFFFFF;
}

.author.vcard .avatar {border-color:#222;}



.tipsy-inner {-moz-box-shadow:0 0 2px #111;}


#secondary a:hover, #secondary-2 a:hover {border-bottom-color:#aaa;color:#fff;text-shadow:0 1px 1px #000;}

ul, ul ul, ul ul ul {list-style-image:url('.$template_url.'/library/media/images/dark/list-style-dark.gif);}

.widgets-back, .menu-container {background-image: url('.$template_url.'/library/media/images/dark/main-bg-dark.jpg);}  

.widgets-back h3 {color:#fff !important;text-shadow:0 1px 3px #444 !important;}
.widgets-back ul, .widgets-back ul ul, .widgets-back ul ul ul {list-style-image:url('.$template_url.'/library/media/images/dark/list-style-dark.gif) !important;}  

.widgets-back a:hover {color:orange}

.widgets-holder a {
    text-shadow: 0 1px 0 #000 !important;
}

.widgets-back .widget-title a {color:#fff !important;text-shadow:0 1px 3px #444 !important;}

.comment, .trackback, .pingback {text-shadow:0 1px 0 #000;background: #555;
    border-color: #333 #333 #444;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    -box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    -webkit-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    -moz-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
}

.avatar {  background:#444444;border-color: #404040;}

#leave-a-reply {text-shadow:0 1px 1px #333333;}

.entry-content .read-more a, #page-links a {border-color: #222; border-bottom-color: #111;text-shadow: 0 1px 0 #111;
    color: #aaa;
    background: #505050;               
    background: -webkit-gradient(linear,left top,left bottom,color-stop(.2, #505050),color-stop(1, #404040));
    background: -moz-linear-gradient(center top,#505050 20%,#404040 100%);
    background: -o-linear-gradient(top, #505050,#404040);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#505050\', endColorstr=\'#404040\');
    -webkit-box-shadow:  0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
    -moz-box-shadow:   0 1px 0 rgba(255, 255, 255, 0.3) inset,0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
    box-shadow:   0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);}

.share-this a { text-shadow:0 1px 0px #111; }
.share-this a:hover {color:#fff;}
.share-this strong {color:#999;border:1px solid #222;text-shadow:0 1px 0px #222;background:#505050;
background:-moz-linear-gradient(center top , #505050 20%, #404040 100%) repeat scroll 0 0 transparent;
   background: -webkit-gradient(linear,left top,left bottom,color-stop(.2, #505050),color-stop(1, #404040)) !important;
    background: -o-linear-gradient(top, #505050,#404040) !important;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#505050\', endColorstr=\'#404040\');
-webkit-box-shadow: 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
-moz-box-shadow: 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
-box-shadow: 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
box-shadow: 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
}
.share-this:hover strong {color:#fff;}

.page-navigation .nav-next, .single-page-navigation .nav-next, .page-navigation .nav-previous, .single-page-navigation .nav-previous {color:#777;}
.page-navigation .nav-previous a, .single-page-navigation .nav-previous a, .page-navigation .nav-next a, .single-page-navigation .nav-next a {background:rgba(0, 0, 0, 0.06);color:#999999;text-shadow:0 1px 0px #333;}
.page-navigation .nav-previous a:hover, .single-page-navigation .nav-previous a:hover, .page-navigation .nav-next a:hover, .single-page-navigation .nav-next a:hover {background:#333;color:#eee;}

/* Page Navi */

.wp-pagenavi a, .wp-pagenavi span {-moz-box-shadow:0 1px 2px #333;background:#555;color:#999999;text-shadow:0 1px 0px #333;}
.wp-pagenavi a:hover, .wp-pagenavi span.current {background:#333;color:#eee;}


#page-links a:hover {background:#333;color:#eee;}

blockquote {background:url('.$template_url.'/library/media/images/dark/quote-dark.gif) no-repeat 10px 15px #505050;color:#bbb;text-shadow:0 1px 0px #000;
    border-color: #555 #444 #444 #555;
    -webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) inset;
    -moz-box-shadow:0 0 10px rgba(0, 0, 0, 0.1) inset;
    box-shadow:0 0 10px rgba(0, 0, 0, 0.1) inset;}

table {
background: rgba(0, 0, 0, 0.3);
-webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
-moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
-box-shadow:0 0 10px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.1) !important;
border-color:#222;    
}
thead, thead th, thead td {background:rgba(0, 0, 0, 0.15);color:#FFFFFF;text-shadow:0 0 2px #000;}
th, td {border-bottom: 1px solid #222;border-top: 1px solid #444;}    

table#wp-calendar th, table#wp-calendar tbody tr td {color:#555;text-shadow:0 1px 0px #111;}
table#wp-calendar tbody tr td {border-right:1px solid #333;border-top:1px solid #3c3c3c;}
table#wp-calendar th {color:#fff;text-shadow:0 1px 0px #111;}
table#wp-calendar tbody tr td a {text-shadow:0 1px 0px #111;}




/* Ads Spaces */

.ad-space {border:2px solid #f9f9f9;-moz-box-shadow:0 1px 4px #333;text-shadow:0 1px 1px #fff;color:#999;background:#f1f1f1;}
.ad-space h3 {color:#555 !important;text-shadow:0 1px 1px #fff !important;}
.ad-space:hover {background:#fff;color:#333;}';




  } if ($options['evl_menu_back'] == "dark") { 
  

$evolve_css_data .= '.nav a {color:#fff;text-shadow:0 1px 0px #333; }

.nav li.nav-hover ul { background: #505050; }

.nav ul li a {border-bottom: 1px solid #444;border-top: 1px solid #666;}

.nav ul li:hover > a, .nav li.current-menu-item > a, .nav li.current-menu-ancestor > a  {border-top-color:#666!important;}

.nav li.current-menu-ancestor li.current-menu-item > a, .nav li.current-menu-ancestor li.current-menu-parent > a {border-top-color:#666; }

.nav ul {border: 1px solid #444; border-bottom:0;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 2px rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 2px rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
-moz-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 2px rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
-webkit-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 2px rgba(255, 255, 255, 0.3) inset, 0 0 10px rgba(0, 0, 0, 0.1) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
}

.nav li {border-left-color: #444;border-right-color:  #666;}

.menu-header {background:#505050;
   background:-moz-linear-gradient(center top , #606060 20%, #505050 100%) repeat scroll 0 0 transparent;
   background: -webkit-gradient(linear,left top,left bottom,color-stop(.2, #606060),color-stop(1, #505050)) !important;
    background: -o-linear-gradient(top, #606060,#505050) !important;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#606060\', endColorstr=\'#505050\');
    -webkit-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);-moz-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset, 0 0 5px rgba(0, 0, 0, 0.3) inset, 0 1px 2px rgba(0, 0, 0, 0.29);
    color:#fff;text-shadow:0 1px 0px #000;
    border-color:#222;  
} 

.nav li:hover > a, .nav li.current-menu-item > a, .nav li.current-menu-ancestor > a,
.nav li.current-menu-ancestor li.current-menu-item > a, .nav li.current-menu-ancestor li.current-menu-parent > a { 
-webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
-moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
-box-shadow: 0 0 10px rgba(0, 0, 0, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.1);
box-shadow:  0 0 10px rgba(0, 0, 0, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.1);}

.nav li.current-menu-item, .nav li.current-menu-ancestor, .nav li:hover {border-right-color:#666!important;}

.nav .sf-sub-indicator {background-image:	url('.$template_url.'/library/media/images/arrow-superfish-dark.png);}
.nav li ul .sf-sub-indicator {background-image:url('.$template_url.'/library/media/images/arrow-superfish-right-dark.png);}';


 } if ($options['evl_main_color'] == "light_grey_blue") { 
  
  $evolve_css_data .= '/**
 * Light grey + blue
 * 
 */
 

.header {background:#dedede url('.$template_url.'/library/media/images/light-grey-blue/header-footer.jpg) repeat;border-bottom: 1px solid #ccc;}

#tagline {color:#aaa;text-shadow:0 1px 0 #eee;}

#logo a, .footer a, .widgets-back .widgets-holder a, .widgets-holder a { text-shadow:0 1px 0px #ddd;}
.widgets-back .widgets-holder a, .widgets-holder a {text-shadow:none;}

.footer {background:#dedede url('.$template_url.'/library/media/images/light-grey-blue/footer-light.jpg) repeat;color:#333;border-top: 1px solid #ccc;}

p#copyright {text-shadow:0 1px 0px #fff;}';
  
  
     } if ($options['evl_main_color'] == "light_grey_blue" && $options['evl_content_back'] == "dark" ) { 
     
    $evolve_css_data .= '/**
 * Light grey + blue + dark
 * 
 */
 
.widgets-holder a:hover {color:orange!important;}'; 

} if ($options['evl_main_color'] == "green_yellow") { 

$evolve_css_data .= '/**
 * Green + yellow
 * 
 */
 
 
a, .entry-content a:link, .entry-content a:active, .entry-content a:visited, .footer .widgets-holder a {color:#519f22;} 
.entry-content a:hover, .footer .widgets-holder a:hover, #secondary a:hover, #secondary-2 a:hover {color:#3e7a1a;}

.header {background:#7eda08 url('.$template_url.'/library/media/images/green-yellow/header-footer.jpg) repeat;border-bottom: 1px solid #80b140;}

#tagline {color:#729e39;text-shadow:0 1px 0 #85e708;}

#logo a, .footer a { color:yellow;text-shadow:0 1px 1px #2e5e0b;}
#logo a:hover, .footer a:hover { color:#fff;}

.footer {background:#7eda08 url('.$template_url.'/library/media/images/green-yellow/header-footer.jpg);border-top: 1px solid #80b140;}
p#copyright {text-shadow:0 1px 0 #85e708;color:#729e39;}';


} if ($options['evl_main_color'] == "green_yellow" && $options['evl_content_back'] == "dark" ) { 

$evolve_css_data .= '/**
 * Green + yellow + dark
 * 
 */
 
 
a, .entry-content a:link, .entry-content a:active, .entry-content a:visited {color:#6fdb2e;} 

#secondary a:hover, #secondary-2 a:hover, .widgets-holder a:hover {color:#fff!important;}

.widgets-holder a {text-shadow: 0 1px 0 #000 !important;}';

  
} if ($options['evl_main_color'] == "red_yellow") { 

$evolve_css_data .= '/**
 * Red + Yellow
 * 
 */
 
 
a, .entry-content a:link, .entry-content a:active, .entry-content a:visited, .footer .widgets-holder a {color:#ff4a0b;} 
.entry-content a:hover, .footer .widgets-holder a:hover, #secondary a:hover, #secondary-2 a:hover {color:#a12f07;}

.header {background:#f0501f url('.$template_url.'/library/media/images/red-yellow/header-footer.jpg) repeat;border-bottom: 1px solid #b93f19;}

#tagline {color:#c8441b;text-shadow:0 1px 0 #f47b55;}

#logo a, .footer a { color:yellow;text-shadow:0 1px 1px #471b0d;}
#logo a:hover, .footer a:hover { color:#fff;}

.footer {background:#f0501f url('.$template_url.'/library/media/images/red-yellow/header-footer.jpg) repeat;border-top: 1px solid #c8441b;}
p#copyright {text-shadow:0 1px 0 #f47b55;color:#b93f19;}';

} if ($options['evl_main_color'] == "red_yellow" && $options['evl_content_back'] == "dark") { 

$evolve_css_data .= '/**
 * Red + yellow + dark
 * 
 */
 
 
a, .entry-content a:link, .entry-content a:active, .entry-content a:visited {color:#ff5418;} 

#secondary a:hover, #secondary-2 a:hover,.widgets-holder a:hover {color:#fff!important;}';

  
} if ($options['evl_main_color'] == "pink_purple") { 

$evolve_css_data .= '/**
 * Pink + Purple
 * 
 */
 
 
a, .entry-content a:link, .entry-content a:active, .entry-content a:visited, .footer .widgets-holder a {color:#e555e0;} 
.entry-content a:hover, .footer .widgets-holder a:hover, #secondary a:hover, #secondary-2 a:hover {color:#963693;}

.header {background:#ed81f1 url('.$template_url.'/library/media/images/pink-purple/header-footer.jpg) repeat;border-bottom: 1px solid #c26ec5;}

#tagline {color:#b768b9;text-shadow:0 1px 0 #fbbbf8;}

#logo a, .footer a { color:#831da0;text-shadow:0 1px 0px #fbbbf8;}
#logo a:hover, .footer a:hover { color:#333;}

.footer {background:#ed81f1 url('.$template_url.'/library/media/images/pink-purple/header-footer.jpg) repeat;color:#333;border-top: 1px solid #c26ec5;}
p#copyright {text-shadow:0 1px 0px #fbbbf8;color:#b768b9;}';



} if ($options['evl_main_color'] == "pink_purple" && $options['evl_content_back'] == "dark" ) {

$evolve_css_data .= '/**
 * Pink + Purple + Dark
 * 
 */
 
 
a, .entry-content a:link, .entry-content a:active, .entry-content a:visited {color:#e076ff;} 

#secondary a:hover, #secondary-2 a:hover, .widgets-holder a:hover {color:#fff!important;}';

} if ($options['evl_main_color'] == "light_blue") { 

$evolve_css_data .= '/**
 * Light Blue + Blue
 * 
 */
 
 
.header {background:#81c0f1 url('.$template_url.'/library/media/images/light-blue/header-footer.jpg) repeat;border-bottom: 1px solid #6290b3;}

#tagline {color:#6290b3;text-shadow:0 1px 0 #b6d8fd;}

#logo a, .footer a { color:#1f68b3;text-shadow:0 1px 0px #b6d8fd;}
#logo a:hover, .footer a:hover { color:#333;}

.footer {background:#81c0f1 url('.$template_url.'/library/media/images/light-blue/header-footer.jpg) repeat;color:#333;border-top: 1px solid #6290b3;}

p#copyright {text-shadow:0 1px 0px #B6D8FD;color:#6290b3;}';


} if ($options['evl_main_color'] == "light_blue" && $options['evl_content_back'] == "dark" ) { 


$evolve_css_data .= '/**
 * Light Blue + Blue + Dark
 * 
 */
 

.widgets-holder a:hover {color:orange!important}';

 } if ($options['evl_main_color'] == "brown_yellow") { 
 
 
 $evolve_css_data .= '/**
 * Brown + Yellow
 * 
 */

a, .entry-content a:link, .entry-content a:active, .entry-content a:visited, .footer .widgets-holder a {color:#846430;} 
.entry-content a:hover, .footer .widgets-holder a:hover, #secondary a:hover, #secondary-2 a:hover {color:#634b24;}
 
.header {background:#8e6525 url('.$template_url.'/library/media/images/brown-yellow/header-footer.jpg) repeat;border-bottom:1px solid #5e441b;}

#tagline {color:#6b4d1f;text-shadow:0 1px 0 #a7772c;}

#logo a, .footer a { color:yellow;text-shadow:0 1px 0px #18130b;}
#logo a:hover, .footer a:hover { color:#fff;}

       
.footer {background:#8e6525 url('.$template_url.'/library/media/images/brown-yellow/header-footer.jpg) repeat;border-top:1px solid #5e441b;}
p#copyright {color:#6b4d1f;text-shadow:0 1px 0 #a7772c;}';


} if ($options['evl_main_color'] == "brown_yellow" && $options['evl_content_back'] == "dark" ) { 

$evolve_css_data .= '/**
 * Brown + Yellow + Dark
 * 
 */

a, .entry-content a:link, .entry-content a:active, .entry-content a:visited {color:#a78441;}  

#secondary a:hover, #secondary-2 a:hover, .widgets-holder a:hover {color:#fff!important;}';

 } if ($options['evl_post_layout'] == "two") { 
  
  $evolve_css_data .= '/**
 * Posts Layout
 * 
 */
 
 
.home .hentry, .archive .hentry, .search .hentry {width:48%;float:left;margin-right:19px;padding-bottom:12px;}
.home .hentry .entry-content, .archive .hentry .entry-content, .search .hentry .entry-content {font-size:13px;}
.entry-content {margin-top:25px;}
.home .odd0, .archive .odd0, .search .odd0{clear:both;}
.home .odd1, .archive .odd1, .search .odd1{margin-right:0px;}
.home .entry-title, .entry-title a, .archive .entry-title, .search .entry-title {font-size:24px;letter-spacing:-1px;line-height:23px;}
.home .hentry img, .archive .hentry img, .search .hentry img{float:left;margin-right:10px;max-width:100px;max-height:150px;padding:3px;width:expression(document.body.clientWidth < 742? \'100px\' : document.body.clientWidth > 1000? \'100px\' : \'auto\');}
.home .entry-header, .archive .entry-header, .search .entry-header{font-size:12px;}
.home .published strong, .archive .published strong,  .search .published strong{font-size:15px;line-height:15px;}
.home .hentry .comment-count a, .archive .hentry .comment-count a, .search .hentry .comment-count a  {background:url('.$template_url.'/library/media/images/comment.png) 0 9px no-repeat;text-decoration:none;position:relative;bottom:-9px;border:none;padding:8px 10px 8px 22px;}
.home .hfeed, .archive .hfeed, .single .hfeed, .page .hfeed {margin-right:0px;}
.home .hentry .entry-footer, .archive .hentry .entry-footer, .search .hentry .entry-footer {float:left;width:100%}
.home .hentry .comment-count, .archive .hentry .comment-count, .search .hentry .comment-count {background:none!important;padding-right:0;}';
  
 } if ($options['evl_post_layout'] == "three") {
  
$evolve_css_data .= '/**
 * Posts Layout
 * 
 */
 
 
.home .hentry, .archive .hentry, .search .hentry {width:31%;float:left;margin-right:19px;padding-bottom:12px;}
.home .hentry .entry-content, .archive .hentry .entry-content, .search .hentry .entry-content {font-size:13px;}
.entry-content {margin-top:25px;}
.home .odd0, .archive .odd0, .search .odd0 {clear:both;}
.home .odd2, .archive .odd2, .search .odd2 {margin-right:0px;}
.home .entry-title, .entry-title a, .archive .entry-title, .search .entry-title {font-size:24px;letter-spacing:-1px;line-height:23px;}
.home .hentry img, .archive .hentry img, .search .hentry img {float:left;margin-right:10px;max-width:100px;max-height:150px;padding:3px;width:expression(document.body.clientWidth < 742? \'100px\' : document.body.clientWidth > 1000? \'100px\' : \'auto\');}
.home .entry-header, .archive .entry-header, .search .entry-header {font-size:12px;}
.home .published strong, .archive .published strong, .search .published strong {font-size:15px;line-height:15px;}
.home .hentry .comment-count a, .archive .hentry .comment-count a, .search .hentry .comment-count a  {background:url('.$template_url.'/library/media/images/comment.png) 0 9px no-repeat;text-decoration:none;position:relative;bottom:-9px;border:none;padding:8px 10px 8px 22px;}
.home .hentry .comment-count, .archive .hentry .comment-count, .search .hentry .comment-count {background:none!important;padding-right:0;}';

} if ($options['evl_title_font'] == "tahoma") { 
  $evolve_css_data .= '#logo, #logo a {font-family:Tahoma, Geneva, Verdana;font-weight:bold;letter-spacing:-2px;}';
 } if ($options['evl_title_font'] == "georgia") { 
 $evolve_css_data .= '#logo, #logo a {font-family:Georgia, Palatino, Palatino Linotype, Times, Times New Roman, serif;font-weight:bold;letter-spacing:-2px;}';
 } if ($options['evl_title_font'] == "arial") { 
 $evolve_css_data .= '#logo, #logo a {font-family:Arial Black, Arial, Helvetica Neue, Helvetica, sans-serif;font-weight:bold;letter-spacing:-5px;}';
 } if ($options['evl_title_font'] == "calibri") { 
 $evolve_css_data .= '#logo, #logo a {font-family:Calibri,Segoe UI,Myriad Pro,Myriad,Trebuchet MS,Helvetica,Arial,sans-serif;font-weight:bold;letter-spacing:-2px;}';
 } if ($options['evl_content_font'] == "arial") { 
 $evolve_css_data .= 'body, input, textarea {font-family: Arial, Helvetica Neue, Helvetica, sans-serif;}';
 } if ($options['evl_content_font'] == "georgia") { 
 $evolve_css_data .= 'body, input, textarea {font-family: Georgia, Palatino, Palatino Linotype, Times, Times New Roman, serif;}';
 } if ($options['evl_content_font'] == "courier") { 
 $evolve_css_data .= 'body, input, textarea {font-family: "Courier New", Courier, monospace;}';
 } if ($options['evl_content_font'] == "calibri") { 
 $evolve_css_data .= 'body, input, textarea {font-family:Calibri,Segoe UI,Myriad Pro,Myriad,Trebuchet MS,Helvetica,Arial,sans-serif;}';
 } if ($options['evl_pos_logo'] == "right") { 
 $evolve_css_data .= '#logo-image {float:right;margin:0 0 0 20px;}';
 } if ($options['evl_pos_button'] == "left") { 
 $evolve_css_data .= '#backtotop {left:3%;margin-left:0;}';
 } if ($options['evl_pos_button'] == "right") { 
 $evolve_css_data .= '#backtotop {right:3%;}';
 } if ($options['evl_pos_button'] == "middle" || $options['evl_pos_button'] == "") {
 $evolve_css_data .= '#backtotop {left:50%;}';
   
 } if ($options['evl_widgets_header'] == "two") {   

$evolve_css_data .= '.widgets-holder .header-1, .widgets-holder .header-2 {float:left;width:473px;margin-right:10px;}
.widgets-holder .header-2 {margin-right:0;}';
  
 } if ($options['evl_widgets_header'] == "three") { 
 
 $evolve_css_data .= '.widgets-holder .header-1, .widgets-holder .header-2, .widgets-holder .header-3 {float:left;width:313px;margin-right:10px;}
.widgets-holder .header-3 {margin-right:0;}';  

 } if ($options['evl_widgets_header'] == "four") { 
 
 $evolve_css_data .= '.widgets-holder .header-1, .widgets-holder .header-2, .widgets-holder .header-3, .widgets-holder .header-4 {float:left;width:232px;margin-right:10px;}
.widgets-holder .header-4 {margin-right:0;}';  

 } if ($options['evl_widgets_header'] == "two" && $options['evl_width_layout'] == "fluid") { 
 
 $evolve_css_data .= '.widgets-holder .header-1, .widgets-holder .header-2 {float:left;width:49%;margin-right:10px;}
.widgets-holder .header-2 {margin-right:0;}';

} if ($options['evl_widgets_header'] == "three" && $options['evl_width_layout'] == "fluid") { 

$evolve_css_data .= '.widgets-holder .header-1, .widgets-holder .header-2, .widgets-holder .header-3 {float:left;width:32%;margin-right:10px;}
.widgets-holder .header-3 {margin-right:0;}';

} if ($options['evl_widgets_header'] == "four" && $options['evl_width_layout'] == "fluid") {

$evolve_css_data .= '.widgets-holder .header-1, .widgets-holder .header-2, .widgets-holder .header-3, .widgets-holder .header-4 {float:left;width:24%;margin-right:10px;}
.widgets-holder .header-4 {margin-right:0;}';


 } if ($options['evl_widgets_num'] == "two") {
 
 $evolve_css_data .= '.widgets-holder .footer-1, .widgets-holder .footer-2 {float:left;width:473px;margin-right:10px;}
.widgets-holder .footer-2 {margin-right:0;}';  


 } if ($options['evl_widgets_num'] == "three") { 
 
 $evolve_css_data .= '.widgets-holder .footer-1, .widgets-holder .footer-2, .widgets-holder .footer-3 {float:left;width:313px;margin-right:10px;}
.widgets-holder .footer-3 {margin-right:0;}';

 } if ($options['evl_widgets_num'] == "four") {   

 $evolve_css_data .= '.widgets-holder .footer-1, .widgets-holder .footer-2, .widgets-holder .footer-3, .widgets-holder .footer-4 {float:left;width:232px;margin-right:10px;}
.widgets-holder .footer-4 {margin-right:0;}';


} if ($options['evl_widgets_num'] == "two" && $options['evl_width_layout'] == "fluid") {


$evolve_css_data .= '.widgets-holder .footer-1, .widgets-holder .footer-2 {float:left;width:49%;margin-right:10px;}
.widgets-holder .footer-2 {margin-right:0;}';


} if ($options['evl_widgets_num'] == "three" && $options['evl_width_layout'] == "fluid") {



$evolve_css_data .= '.widgets-holder .footer-1, .widgets-holder .footer-2, .widgets-holder .footer-3 {float:left;width:32%;margin-right:10px;}
.widgets-holder .footer-3 {margin-right:0;}';


} if ($options['evl_widgets_num'] == "four" && $options['evl_width_layout'] == "fluid") {

$evolve_css_data .= '.widgets-holder .footer-1, .widgets-holder .footer-2, .widgets-holder .footer-3, .widgets-holder .footer-4 {float:left;width:24%;margin-right:10px;}
.widgets-holder .footer-4 {margin-right:0;}';


} if ($options['evl_back_images'] == "1") { 


$evolve_css_data .= '.header, .menu-back, .content, .content-top, .content-bottom, .footer-top, .footer, .hentry,
.wmiddle, .wmiddle-right, .wbottom, .wright, .wtop-top, .wtop-left, .wtop-right, .wtop-middle, .wtopmiddle-left, .wtopmiddle-right,
.entry-header, .published, .widgets-back, .menu-container, .share-this, .wmiddle-left, .wmiddle-right, .wsbottom-left, .wsbottom-right, #respond,
.comment, .trackback, .pingback {background-image:none;}
.menu-container {background-color:#eeeff3;}';



} if ($options['evl_back_images'] == "1" && $options['evl_content_back'] == "dark") {  

$evolve_css_data .= '.menu-container {background-color:#5c5c5c;}';   


} if ($options['evl_back_images'] == "1" && $options['evl_menu_back'] == "dark") { 

$evolve_css_data .= '.menu-top-left, .menu-top-right, .menu-bottom-left, .menu-bottom-right, .menu-middle-left, .menu-middle-right, .menu-top,
.menu-bottom, .menu-middle {background:#565656;}';


} if ($options['evl_custom_background'] == "1") {

$evolve_css_data .= '#wrapper {margin:0 auto 30px auto !important;background:#fff;box-shadow:0 0 15px rgba(0,0,0,.2);}';

} ?>