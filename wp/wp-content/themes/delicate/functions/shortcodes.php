<?php 
function notice_shortcode( $atts, $content = null ) {
   return '<div class="notice">'.$content.'</div>';
}
add_shortcode('notice', 'notice_shortcode');

function alert_shortcode( $atts, $content = null ) {
   return '<div class="alert">'.$content.'</div>';
}
add_shortcode('alert', 'alert_shortcode');

// Text features
function button_shortcode( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'link' => '#',
      ), $atts ) );  
   return '<a class="shortcode_button" href="' .esc_attr($link). '"><span>' .$content. '</span></a>';
}
add_shortcode('button', 'button_shortcode');

function dropcap_shortcode( $atts, $content = null ) {
   return '<p class="dropcap"><span class="dropcap">'.$content.'</span></p>';
}
add_shortcode('dropcap', 'dropcap_shortcode');

function highlight_shortcode( $atts, $content = null ) {
   return '<span class="highlight">'.$content.'</span>';
}
add_shortcode('highlight', 'highlight_shortcode');

function highlightbold_shortcode( $atts, $content = null ) {
   return '<span class="highlight-bold">'.$content.'</span>';
}
add_shortcode('highlightbold', 'highlightbold_shortcode');

function green_shortcode( $atts, $content = null ) {
   return '<span class="highlight-green">'.$content.'</span>';
}
add_shortcode('green', 'green_shortcode');

function red_shortcode( $atts, $content = null ) {
   return '<span class="highlight-red">'.$content.'</span>';
}
add_shortcode('red', 'red_shortcode');

function yellow_shortcode( $atts, $content = null ) {
   return '<span class="highlight-yellow">'.$content.'</span>';
}
add_shortcode('yellow', 'yellow_shortcode');

function blue_shortcode( $atts, $content = null ) {
   return '<span class="highlight-blue">'.$content.'</span>';
}
add_shortcode('blue', 'blue_shortcode');

function toggle_shortcode( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'title' => '',
      ), $atts ) );        
   return '<div class="toggle">'.esc_attr($title).'</div><div class="toggle_content"><div class="tc">'.$content.'</div></div>';
}
add_shortcode('toggle', 'toggle_shortcode');

// Text boxes
function insetright_shortcode( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'title' => 'Caption',
      ), $atts ) );  
   return '<span class="inset-right"><span class="inset-right-title">'.esc_attr($title).'</span>'.$content.'</span>';
}
add_shortcode('insetright', 'insetright_shortcode');

function insetleft_shortcode( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'title' => 'Caption',
      ), $atts ) );  
   return '<span class="inset-left"><span class="inset-left-title">'.esc_attr($title).'</span>'.$content.'</span>';
}
add_shortcode('insetleft', 'insetleft_shortcode');

function important_shortcode( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'title' => '',
      ), $atts ) );        
   return '<div class="important"><span class="important-title">'.esc_attr($title).'</span>'.$content.'</div> ';
}
add_shortcode('important', 'important_shortcode');

// Layout shortcodes
function col4_shortcode( $atts, $content = null ) {
   return '<div class="column4">'.$content.'</div>';
}
add_shortcode('col4', 'col4_shortcode');

function col3_shortcode( $atts, $content = null ) {
   return '<div class="column3">'.$content.'</div>';
}
add_shortcode('col3', 'col3_shortcode');

function col2_shortcode( $atts, $content = null ) {
   return '<div class="column2">'.$content.'</div>';
}
add_shortcode('col2', 'col2_shortcode');


// Misc
function clear_shortcode( $atts, $content = null ) {
   return '<div class="clear"></div>';
}
add_shortcode('clear', 'clear_shortcode');

function divider_shortcode( $atts, $content = null ) {
   return '<div class="divider top-shortcode"><a href="#">Top</a></div>';
}
add_shortcode('divider', 'divider_shortcode');

function t_show_shortcodes() {
 echo '<h3>Available Shortcodes</h3><p>Shortcodes are a very easy way to display lot of things on your blog posts and sidebars by inserting a very simple code. Its easy to use. Just paste a shortcode [your_shortcode] while editing a page(post). Here is the list of supported shortcodes:</p>
 <ul class="short-codes">
 <li><strong>Messages:</strong>
    <ul>
      <li><pre>[notice]Your text...[/notice]</pre></li>
      <li><pre>[alert]Your text...[/alert]</pre></li>
    </ul>
 </li>
 <li><strong>Text features:</strong>
    <ul>
        <li><pre>[button link="http://yourlink.com"]Button text[/button]</pre></li>
        <li><pre>[dropcap]Y[/dropcap]</pre></li>
        <li><pre>[highlight]Your text...[/highlight]</pre></li>
        <li><pre>[highlightbold]Your text...[/highlightbold]</pre></li>
        <li><pre>[green]Your text...[/green]</pre></li>
        <li><pre>[red]Your text...[/red]</pre></li>
        <li><pre>[yellow]Your text...[/yellow]</pre></li>
        <li><pre>[blue]Your text...[/blue]</pre></li>
        <li><pre>[toggle title="Toggle Title"]Your text...[/toggle]</pre></li>
    </ul>
 </li>
 <li><strong>Text boxes:</strong>
    <ul>
      <li><pre>[insetleft title="help"]Hello this is content[/insetleft]</pre></li>
      <li><pre>[insetright title="help"]Hello this is content[/insetright]</pre></li>
      <li><pre>[important title="help"]Hello this is content[/important]</pre></li>
    </ul>
 </li> 
 <li><strong>Layout:</strong>
    <ul>
      <li><pre>[col2]...content...[/col2]</pre></li>
      <li><pre>[col3]...content...[/col3]</pre></li>
      <li><pre>[col4]...content...[/col4]</pre></li>
    </ul>
 </li> 
 <li><strong>Misc:</strong>
    <ul>
      <li><pre>[clear]</pre></li>
      <li><pre>[divider]</pre></li>
    </ul>
 </li> 
 </ul><p><a href="http://support.nattywp.com/index.php?act=article&code=view&id=37">Using Shortcodes tutorial</a></p>';
}
?>