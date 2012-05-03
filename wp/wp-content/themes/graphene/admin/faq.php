<?php
/**
 * This function generates the theme's FAQ page in WordPress administration.
 *
 * @package Graphene
 * @since Graphene 1.1.3
*/
function graphene_faq(){ ?>
	<div class="wrap">
    	<div class="icon32" id="icon-themes"><br /></div>
        <h2><?php _e("Graphene's Frequently Asked Questions", 'graphene'); ?></h2>
        <ol>
        	<li>
            	<p><strong><?php _e("Where should I go for the theme's support?", 'graphene'); ?></strong></p>
                <p><?php _e("Please direct all support requests for the theme at the theme's <a href=\"http://forum.khairul-syahir.com/graphene-wordpress-theme/\">Support Forum</a>.", 'graphene'); ?></p>
            </li>
            <li>
            	<p><strong><?php _e("The post's featured image is replacing my header image. Help!", 'graphene'); ?></strong></p>
                <p><?php _e("This is actually one of the theme's features, based on the feature in the default TwentyTen theme. Any featured image that has a size of greater than or equal to the theme's header image size (960 x 198 pixels) will replace the header image when the post/page that featured image is assigned to is being displayed. It enables you to have different header image for different posts and/or pages.", 'graphene'); ?></p>
                <p><?php _e("If you want to disable this feature, simply tick the <em>Disable Featured Image replacing header image</em> option in the <a href=\"themes.php?page=graphene_options&tab=display\">Graphene Options</a> page, under Display &gt; Header Display Options.", 'graphene'); ?></p>
            </li>
        	<li>
            	<p><strong><?php _e("Can I modify Graphene to my heart's content without paying anything?", 'graphene'); ?></strong></p>
                <p><?php _e("The Graphene WordPress theme, along with all the other themes in the WordPress.org Official Free Themes Directory, is released under the GNU General Public License (GPL) Version 3. The full text of that licence is included with the theme in the <code>licence.txt</code> file in the theme's folder. Releasing the theme under that licence means, among others, that you are <em>free to modify the theme in any way for any purpose (including commercial)</em>. However, if you decide to redistribute the theme, the licence dictates that you must release the theme under the same licence, GPLv3, and attributes the copyright of the original theme to the original author.", 'graphene'); ?></p>
                <p><?php _e("But of course, the author would always appreciate <a href=\"themes.php?page=graphene_options\">donations</a> to support ongoing and future developments of the theme.", 'graphene'); ?></p>
            </li>
            <li>
            	<p><strong><?php _e("If the theme is released under GPLv3, what is this Creative Commons licence in the theme's footer?", 'graphene'); ?></strong></p>
                <p><?php _e("The Creative Commons licence is a popular licence nowadays that are used by a lot of web-based content authors to licence their work such that it protects their intellectual property but in the same time allows its free distribution. It is included with the theme simply to make it easy for the theme's users to make use of the licence for the content they publish. Theme users can remove it altogether via the theme's Options page should they wish not to use it.", 'graphene'); ?></p>
                <p><?php _e("Put simply, <em>it is not the licence that is applied for the theme itself</em>, but just for the website's content should the theme user wants to use it.", 'graphene'); ?></p>
            </li>
            <li>
            	<p><strong><?php _e("Is the theme compatible with this plugin or that plugin?", 'graphene'); ?></strong></p>
                <p><?php _e("I don't know. With so many plugins available for WordPress, there's no way that I (or anybody else for that matter) can test for compatibility for all of them. Having said that, the theme is built with all the necessary WordPress components included with it, so chances are most plugins will be compatible with the theme.", 'graphene'); ?></p>
                <p><?php _e("My suggestion is to just install the plugin and try it. If you stumble into problem, ask for support from the plugin author first. If the plugin author says that it's a problems with the theme, you know where to find support.", 'graphene'); ?></p>
            </li>
        </ol>
    </div>
<?php
}
?>