<?php
function bfa_ata_admin() {
    global $bfa_ata, $bfa_ata_version, $options, $templateURI;

    if ( isset($_REQUEST['saved']) ) echo '<div id="message" class="updated fade"><p><strong>Atahualpa settings saved.</strong></p></div>';
    if ( isset($_REQUEST['reset']) ) echo '<div id="message" class="updated fade"><p><strong>Atahualpa settings reset.</strong></p></div>';
?>
<table width="100%" cellpadding="2" cellspacing="0"><tr><td valign="middle" width="380"><h2 style="margin:0 30px 0 0; padding: 5px 0 5px 0;">
Atahualpa <?php echo $bfa_ata_version; ?></h2></td><td valign="middle">
<iframe src="http://wordpress.bytesforall.com/update.php?theme=Atahualpa&version=<?php 
echo $bfa_ata_version; ?>" width="98%" height="40" scrolling="no" frameborder="0"></iframe></td>
</tr></table>

<div class="wrap" style="margin: 0 10px 0 0">
	
<table>
<tr>
<td valign="top" width="200">

<ul id="bfaoptiontabs" class="shadetabs">
	<li><a href="#" rel="start-here" class="selected">START</a></li>
	
	<strong>Export/Import Settings</strong>
	<li><a href="#" rel="export-import">Export/Import Settings</a></li>
	
	<strong>Search Engine Optimization</strong>
	<li><a href="#" rel="seo">Configure SEO</a></li>
	
	<strong>Overall Style & Config.</strong>
	<li><a href="#" rel="body-font-links">Body, Text &amp; Links</a></li>
	<li><a href="#" rel="layout">Style & configure LAYOUT</a></li>
	<li><a href="#" rel="favicon">Add a FAVICON</a></li>
	
	<strong>Header Area</strong>
	<li><a href="#" rel="header">Style & edit HEADER AREA</a></li>
	<li><a href="#" rel="header-image">Header Image</a></li>
	<li><a href="#" rel="feed-links">RSS Settings</a></li>
	<li><a href="#" rel="page-menu-bar">MENU 1 (Page Menu)</a></li>
	<li><a href="#" rel="cat-menu-bar">MENU 2 (Category Menu)</a></li>
	
	<strong>Center Column</strong>
	<li><a href="#" rel="center">Style & edit CENTER COLUMN</a></li>
	<li><a href="#" rel="next-prev-nav">Next/Previous Navigation</a></li>
	
	<strong>Sidebars & Widgets</strong>
	<li><a href="#" rel="sidebars">Style & configure SIDEBARS</a></li>
	<li><a href="#" rel="widgets">Style WIDGETS</a></li>
	<li><a href="#" rel="widget-areas">Add new WIDGET AREAS</a></li>
	
	<strong>Post & Pages</strong>
	<li><a href="#" rel="postinfos">Edit POST/PAGE INFO ITEMS</a></li>
	<li><a href="#" rel="posts">Style POSTS & PAGES</a></li>
	<li><a href="#" rel="posts-or-excerpts">Configure EXCERPTS</a></li>
	<li><a href="#" rel="post-thumbnails">Post THUMBNAILS</a></li>
	<li><a href="#" rel="more-tag">"Read More" tag</a></li>
	
	<strong>Comments</strong>
	<li><a href="#" rel="comments">Style & configure COMMENTS</a></li>
	
	<strong>Footer</strong>
	<li><a href="#" rel="footer-style">Style & edit FOOTER</a></li>
	
	<strong>Various Content Items</strong>
	<li><a href="#" rel="tables">Style TABLES</a></li>
	<li><a href="#" rel="forms">Style FORMS</a></li>
	<li><a href="#" rel="blockquotes">Style BLOCKQUOTES</a></li>
	<li><a href="#" rel="images">Style IMAGES</a></li>
	<li><a href="#" rel="html-inserts">Add HTML/CSS Inserts</a></li>
	
	<strong>Archives Page</strong>
	<li><a href="#" rel="archives-page">Create ARCHIVES PAGE</a></li>
	
	<strong>CSS & Javascript</strong>
	<li><a href="#" rel="css-javascript">Configure CSS & JS</a></li>
</ul>

</td>

<td valign="top" width="100%">

<div id="start-here" class="tabcontent">
<!-- opening the first tab content div, first option should be start-here, in the options array above //-->
<?php foreach ($options as $value) {     // start the options loop, check first, if we need to switch to another tab = option category

// open/close category tab divs . All categories except first category "start-here" get an opening form tag. "start-here" has no value "switch"
if ( isset($value['switch'])) echo '</div><div id="'.$value['category'].'" class="tabcontent"><form method="post">'; 

// extra info for some categories

// "Postinfo" instructions
if($value['category'] == "postinfos" AND isset($value['switch'])) { ?>
	<div class="bfa-container">
		<div class="bfa-container-full">
			<img src="<?php echo $templateURI; ?>/options/images/post-structure.gif" 
			style="float: right; margin: 40px 0 15px 15px;">
			<label for="Post Info Items">Post Info Items</label>
			Configure a <strong>Kicker</strong>, a <strong>Byline</strong> and a <strong>Footer</strong> 
			for posts and pages by arranging these <strong>Post Info Items</strong>. 
			<br /><br />
			Some of these post info items have one or several <strong>parameters</strong>: 
			<ul>
				<li>
					You can leave parameters empty but do not remove their single quotes, even if the parameter is empty.
				</li>
				<li>
					Replace the parameter <code>delimiter</code> with what you want to put between the list 
					items of the tag or category list, i.e. a comma.
				</li>
				<li>
					Replace the parameters <code>before</code> and <code>after</code> with what you want to 
					display before or after that info item. If an item has these "before/after" parameters, use 
					them instead of hard coding text before and after that item: Example: Use 
					<br />
					<code>%tags-linked('<i>Tags: </i>', '<i>, </i>', '<i> - </i>')%</code>
					<br />
					instead of<br />
					<code>Tags: %tags-linked('', '<i>, </i>', '')% - </code>
				</li>
				<li>
					Replace the parameter <code>linktext</code> with the link text for that item.
				</li>
			</ul>
			HTML and <strong>icons</strong> can be used, inside of parameters, too, just not inside the date item:
			<ul>
				<li>
					<code>&lt;image(someimage.gif)&gt;</code> to include an image. 
					<em>Note: The image item doesn't have quotes</em>
				</li>
				<li>
					To use your own images, upload them to /[theme-folder]/images/icons/
				</li>
			</ul>
			
			<h3>Icons</h3>
			<strong>Currently available images (Once you uploaded yours they will be listed here):</strong>
			<br /><br />
			<?php if ($handle = opendir( TEMPLATEPATH . '/images/icons/')) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") $files[] = $file;
				}
				closedir($handle);
			}
			sort($files);
			foreach ($files as $key => $file) {
				echo '<span style="float:left; width: 280px; margin-right: 10px; height: 22px;"><img src="' . 
				$templateURI . '/images/icons/'.$file.'" /> &nbsp;<code>&lt;image('.$file.')&gt;</code></span>';
			} ?>

			<div style="clear:left">&nbsp;</div>
			<h3>Examples</h3>
			Examples for <strong>Post Bylines</strong>:
			<ul>
				<li><code>By %author%, on %date('<i>F jS, Y</i>')%</code></li>
				<li><code>&lt;strong&gt;%author-linked%&lt;/strong&gt; posted this in &lt;strong&gt;%categories-linked('<i>, </i>')%
				&lt;/strong&gt; on &lt;em&gt;%date('<i>F jS, Y</i>')%&lt;/em&gt;</code></li>
				<li><code>&lt;image(user.gif)&gt; %author-linked% &lt;image(date.gif)&gt; %date('<i>l, jS #of F Y #a#t h:i:s A</i>')%</code></li>
			</ul>
			Examples for <strong>Post Footers</strong>:
			<ul>
				<li><code>%tags-linked('<i>&lt;strong&gt;Tags:&lt;/strong&gt; </i>', '<i>, </i>', '<i> &amp;mdash; </i>')% 
				&lt;strong&gt;Categories:&lt;/strong&gt; %categories-linked('<i>, </i>')% &amp;mdash; 
				%comments('<i>Nobody has commented yet, kick it off...</i>', '<i>One comment so far</i>', '<i>% people had their say - be the next!</i>', 
				'<i>Sorry, but comments are closed</i>')% &amp;mdash;  
				%wp-print% &amp;mdash; %wp-email% &amp;mdash; %sociable% &amp;mdash; %wp-postviews%</code></li>
			</ul>
			<h3>Post Info Items</h3>
			List of available post info items:
			<hr><code>%author%</code> - Displays the value in the user's Display name publicly as field.
			<hr><code>%modified-author%</code> - For <strong>WordPress 2.8</strong> and newer <strong>ONLY</strong>: 
			Displays the value in the Display name publicly as field, of the author who last modified a post. 
			This will not work in WordPress 2.7 and older.
			<hr><code>%author-description%</code> - Displays the contents of the About yourself field in an 
			author's profile (Administration > Profile > Your Profile).
			<hr><code>%author-login%</code> - Displays the login name of the author of a post. The login is also 
			referred to as the Username an author uses to gain access to a WordPress blog.
			<hr><code>%author-firstname%</code> - Displays the first name for the author of a post. The First Name 
			field is set in the user's profile (Administration > Profile > Your Profile).
			<hr><code>%author-lastname%</code> - Displays the last name for the author of a post. The Last Name field 
			is set in the user's profile (Administration > Profile > Your Profile).
			<hr><code>%author-nickname%</code> - Displays the nickname for the author of a post. The Nickname field 
			is set in the user's profile (Administration > Profile > Your Profile).
			<hr><code>%author-id%</code> - Displays the unique numeric user ID for the author of a post; the ID is 
			assigned by WordPress when a user account is created.
			<hr><code>%author-email-clear%</code> - Displays the email address for the author of a post. The E-mail 
			field is set in the user's profile (Administration > Profile > Your Profile). Note that the address 
			is not encoded or protected in any way from harvesting by spambots. For this, see <code>%author-email%</code>. 
			<hr><code>%author-email%</code> - Displays the email address for the author of a post, obfuscated by using 
			the internal function antispambot() to encode portions in HTML character 
			entities (these are read correctly by your browser).
			<hr><code>%author-url%</code> - Displays the URL (not a full link) to the Website for the author of a post. The Website field 
			is set in the user's profile (Administration > Profile > Your Profile).
			<hr><code>%author-link%</code> - Displays a link to the Website for the author of a post. The Website field is 
			set in the user's profile (Administration > Profile > Your Profile). The text for the link is the author's 
			Profile Display name publicly as field.
			<hr><code>%author-posts-link%</code> - Displays a link to all posts by an author. The link text is the user's 
			Display name publicly as field.
			<hr><code>%author-post-count%</code> - Displays the total number of posts an author has published. Drafts and 
			private posts aren't counted.
			<hr><code>%author-aim%</code> - Displays the AOL Instant Messenger screenname for the author of a post. 
			The AIM field is set in the user's profile (Administration > Profile > Your Profile).
			<hr><code>%author-yim%</code> - Displays the Yahoo IM ID for the author of a post. The Yahoo IM field is 
			set in the user's profile (Administration > Profile > Your Profile).
			<hr><code>%date('F jS, Y')%</code> - Display the date and/or time the post was published at. Many configuration 
			options at <a href='http://www.php.net/date'>PHP Date</a>. Because most letters of the alphabet represent 
			a certain PHP date/time function, you need to <strong>escape</strong> each letter that you want to 
			display LITERALLY. To <strong>escape</strong> a letter, put a hash character <strong>#</strong> right before that letter. 
			(Note that this is different from the original "PHP way" of escaping with backslashes <code>\</code>. The theme 
			needs the hash character <code>#</code>). The hash will tell the theme that you mean the actual letter and not the 
			corresponding PHP date/time function.<br /><br />
			<strong>How to escape literal letters</strong>
			<ul>
				<li>on -> <code>#o#n</code></li>
				<li>of -> <code>#of</code> &nbsp;&nbsp;(Note how the the lowercase <strong>f</strong> didn't get a <code>#</code>. 
				That's because <strong>f</strong> is one of the letters of the alphabet that does <strong>not</strong> represent a 
				PHP date function)</li>
				<li>at -> <code>#a#t</code></li>
				<li>the -> <code>#t#h#e</code></li>
				<li>The arrows just illustrate how to change a word to display it literally inside a date function, don't use them</li>
			</ul>
			<strong>Examples:</strong>
			<ul>
				<li><code>%date('<i>F j, Y, #a#t g:i a</i>')%</code> displays: December 10, 2008, at 5:16 pm <br />
				Note how the letters <strong>a</strong> and <strong>t</strong> of the word <strong>at</strong> are 
				<strong>escaped</strong> with <code>#</code> 
				to display them literally instead of interpreting them as PHP date/time function.
				</li>
				<li><code>%date('<i>F j, Y, g:i a</i>')%</code> displays: December 10, 2008, 5:16 pm
				</li>
				<li><code>%date('<i>m.d.y</i>')%</code> displays: 10.12.08
				</li>
			</ul>
			<hr>
			<code>%date-modified('F jS, Y')%</code> - Same as above but displays the post's last modfied date instead of its creation date.
			<hr><code>%tags-linked('before', 'delimiter', 'after')%</code> - Displays the tags associated with 
			the current post. The name of each tag will be linked to the relevant 'tag' page. 
			<br /><strong>Example:</strong> <code>%tags-linked('<i>Tagged with: </i>', '<i> - </i>', '<i>. </i>')%</code>
			<hr><code>%tags-cats-linked('before', 'delimiter', 'after')%</code> - Same as above but if no tags 
			are associated with the current post, the associated category is displayed instead.
			<br /><strong>Example:</strong> <code>%tags-linked('<i>Filed under: </i>', '<i> - </i>', '<i>. </i>')%</code>
			<hr><code>%tags('before', 'delimiter', 'after')%</code> - Displays the tags associated with 
			the current post. Tags are not linked.
			<br /><strong>Example:</strong> <code>%tags-linked('<i>Tags: </i>', '<i> - </i>', '<i>. </i>')%</code>
			<hr><code>%category%</code> - Displays the name of the <strong>first</strong> category a post belongs to.
			<hr><code>%category-linked%</code> - Displays a link to the <strong>first</strong> category a post belongs to.
			<hr><code>%categories('delimiter')%</code> - Displays the names of all categories a post belongs to, separated 
			by delimiter.
			<br /><strong>Example:</strong> <code>%categories('<i>, </i>')%</code>
			<hr><code>%categories-linked('delimiter')%</code> - Displays links to all categories a post belongs to, separated 
			by delimiter.
			<br /><strong>Example:</strong> <code>%categories-linked('<i> | </i>')%</code>
			<hr><code>%comments('No comments', '1 comment', '% comments', 'Comments closed')%</code> - Displays a 
			link to the comment section of the post. The link text depends on the comment count & status (open/closed). 
			<br /><br />
			<strong>When using this item, provide 4 text strings for the 4 possible comment states:</strong>
			<ul>
				<li>Replace <code>'No Comments'</code> with your link text for posts that have no comments yet</li>
				<li>Replace <code>'1 comment'</code> with your text for posts with 1 comment</li>
				<li>Replace <code>'% comments'</code> with your text for posts with 2 or more comments. The <code>%</code> (percent) character 
				will be replaced with the comment count. Use that character in your own text, too, unless you do not want to display the comment count.</li>
				<li>Replace <code>'Comments closed'</code> with your text for posts where comments are closed. Replace 'Comments closed' with 'dontshow' 
				to display nothing if comments are closed (instead of displaying something like 'Comments closed')</li>
			</ul>
			<br /><strong>Example:</strong> 
			<code>%comments('<i>Leave your comment</i>', '<i>One comment so far</i>', '<i>% people had their say - chime in!</i>', 
			'<i>Sorry, but comments are closed</i>')%</code>
			<br /><strong>Example 2:</strong> <code>%comments('<i>Comments</i>', '<i>Comments (1)</i>', '<i>Comments (%)</i>', '<i>dontshow</i>')%</code>
			<br /><br />NOTE: On single post pages the <code>%comments('...')%</code> link won't display anything because the comments are on the same page. 
			If you still 
			want to link to the comments, the comment section and the comment form start with named anchors, so you use something like this:<br /> 
			<code>&lt;?php echo '&lt;a href="'.getH().'comments"&gt;Skip to comments&lt;/a&gt;'; ?&gt;</code> or <code>&lt;?php echo 
			'&lt;a href="'.getH().'commentform"&gt;Skip to comments form&lt;/a&gt;'; ?&gt;</code>
			<hr><code>%comments-rss('linktext')%</code> - Displays the comment feed link for a post, with linktext as the link text.
			<hr><code>%trackback%</code> - Displays the trackback URL for the current post.
			<hr><code>%trackback-linked('linktext')%</code> - Displays a link to the trackback URL, with linktext as the link text.
			<hr><code>%trackback-rdf%</code> - Displays the trackback RDF information for a post. This information is not displayed 
			in a browser. Its use is partly intended for auto-detection of the trackback URI to a post, which can be "trackbacked" 
			by some blogging and RDF tools. Use this tag if you want to enable auto-discovery of the trackback URI for a post. 
			Without it, people who wish to send a trackback to one of your posts will have to manually search for the trackback URI. 
			<hr><code>%permalink%</code> - Displays the URL for the permalink to the post.
			<hr><code>%post-id%</code> - Displays the numeric ID of the post.
			<hr><code>%post-title%</code> - Displays the title of the post.
			<hr><code>%post-title-encoded%</code> - Displays the title of the post. It somewhat duplicates the functionality of 
			&post-title%, but provides a 'clean' version of the title by stripping HTML tags and converting certain characters 
			(including quotes) to their character entity equivalent. Use this i.e. for the title tag of a link, example:<br />
			&lt;a href="..." title="<code><i>%post-title-encoded%</i></code>"&gt;
			<hr><code>%edit('before', 'linktext', 'after')%</code> - Displays a direct edit link for the post, IF the current viewer 
			is permitted to edit posts, with <em>linktext</em> as the link text.
			<br /><strong>Example:</strong> <code>%edit('<i> - </i>', '<i>Edit This Post</i>', '')%</code>
			<hr><code>%print('linktext')%</code> - Displays a link with <em>linktext</em> as the link text, which, when clicked, 
			will start printing the content of the center column of the current page, without header, sidebars and footer.
			<br /><strong>Example:</strong> <code>%print('<i>Print this Page</i>')%</code>
			<hr><code>%wp-print%</code> - Displays a link to a print preview page of the post. A configurable alternative to the 
			theme's own basic print function (which prints right away, without preview page). Requires the plugin 
			<a href="http://wordpress.org/extend/plugins/wp-print/">WP-Print</a>. 
			After you installed this plugin, customize the output at the <a title="If this link doesn't work, go to 'Settings' (top right) -> 'Print'"  
			href="options-general.php?page=wp-print/print-options.php">WP-Print Options Page</a>.  
			<hr><code>%wp-email%</code> - Displays a link to a form where visitors can e-mail the post to others. Requires 
			the plugin <a href="http://wordpress.org/extend/plugins/wp-email/">WP-Email</a>. 
			After you installed this plugin, customize the output at the <a title="If this link doesn't work, click on 'E-Mail' 
			at the top of the current page, then 'E-Mail Options'" href="admin.php?page=wp-email/email-options.php">WP-Email Options Page</a>.<br />
			<strong>Suggested settings:</strong>
			<ul>
				<li>Change settings in the section "E-Mail Styles" to customize the output of this item</li>
				<li>Make other changes as you see fit</li>
				<li>Click "Save Changes"</li>
			</ul>
			<hr><code>%wp-postviews%</code> - Displays how many times the post was viewed. Requires the plugin 
			<a href="http://wordpress.org/extend/plugins/wp-postviews/">WP-PostViews</a>. 
			After you installed this plugin, customize the output at the <a title="If this link doesn't work, go to 'Settings' (top right) -> 'Post Views'" 
			href="options-general.php?page=wp-postviews/postviews-options.php">WP-PostViews Options Page</a>.<br />
			<strong>Suggested settings:</strong>
			<ul>
				<li>Change "Views Template" to customize the output of this item</li>
				<li>Make other changes as you see fit</li>
				<li>Click "Save Changes"</li>
			</ul>
			<hr><code>%wp-postratings%</code> - Displays stars or other graphics showing the vote/rating of a post, and lets visitors rate the post.
			Requires the plugin <a href="http://wordpress.org/extend/plugins/wp-postratings/">WP-PostRatings</a>. 
			After you installed this plugin, customize the output at the <a title="If this link doesn't work, click on 'Ratings' at the top of the current page" 
			href="admin.php?page=wp-postratings/postratings-templates.php">WP-PostRatings Options Page</a>.<br />
			<strong>Suggested settings:</strong>
			<ul>
				<li>Delete <code>&lt;br /&gt;%RATINGS_TEXT%</code> from the bottom of the textarea named "Ratings Vote Text:"</li>
				<li>Delete <code>&lt;br /&gt;%RATINGS_TEXT%</code> from the bottom of the textarea named "Ratings None:"</li>
				<li>Make other changes as you see fit</li>
				<li>Click "Save Changes"</li>
			</ul>
			<hr><code>%sociable%</code> - Displays little icons, linking the post to social bookmark sites. Requires the plugin 
			<a href="http://wordpress.org/extend/plugins/sociable/">Sociable</a>. Customize the output at the 
			<a title="If this link doesn't work, go to 'Settings' (top right) -> 'Sociable'" href="options-general.php?page=Sociable">
			Sociable Options Page</a>.<br />
			<strong>Suggested settings:</strong>
			<ul>
				<li>"Tagline:" - Will be ignored</li><li>"Position:" - Uncheck all boxes</li><li>"Use CSS:" - Uncheck this</li>
				<li>"Open in new window:" - Check or uncheck, will be used</li><li>Click "Save Changes"</li>
			</ul>
			<hr><code>%share-this%</code> - Displays little icons, linking the post to social bookmark sites. Requires the plugin 
			<a href="http://wordpress.org/extend/plugins/share-this/">Share This</a>. 
			<hr><code>%meta%</code> - Displays all custom fields and their values as unordered list 
			&lt;ul&gt;&lt;li&gt;..&lt;/li&gt;&lt;li&gt;..&lt;/li&gt;&lt;/ul&gt;
			<hr><code>%meta('fieldname')%</code> - Displays the value of the custom field with the field name "fieldname".
			<br /><strong>Example:</strong> <code>%meta('<i>price</i>')%</code>
			<!--
			<hr><code>&lt;?php echo "Hello world!"; ?&gt;</code> - In regular WordPress (but not in WPMU), you can use PHP code, too. 
			Write the PHP code in the usual way, with opening and closing PHP tags.
			<br /><strong>Example:</strong> 
			<code>&lt;?php if ( is_page('17') ) { if ( function_exists('some_plugin_function') ) { some_plugin_function(); } } ?&gt;</code>
			-->
		</div>
	</div>
<?php } // End of "Postinfo" instructions



#####################################################################
#     TEXT                                                                                                     
#####################################################################

if ($value['type'] == "text") { 

	echo '<div class="bfa-container"><div class="bfa-container-left">
	<label for="' . $value['id'] . '">' . $value['name'] . '</label><input ';
	
	if ( isset($value['size'])) 
		echo "size=" . $value['size'] . ($value['size'] > 20 ? ' style="width: 95%;"' : ' '); 
	
//	Note: eregi() is depreciated in php 5.3
//	echo ( eregi("color", $value['id']) ? 'class="color" ' : '' ) . 
	echo ( preg_match("/"."color"."/i", $value['id']) ? 'class="color" ' : '' ) . 
	'name="' . $value['id'] . '" id="' . $value['id'] . '" type="' . $value['type'] . '" value="';
	 
	if ( isset($bfa_ata[ $value['id'] ]) ) 
		echo ( isset($value['editable']) ? 
		stripslashes(format_to_edit( $bfa_ata[ $value['id'] ] )) : 
		$bfa_ata[ $value['id'] ]  ); 
	else  
		echo ( isset($value['editable']) ? 
		stripslashes(format_to_edit($value['std'])) : 
		$value['std'] ); 

	echo '" /><br />Default: <strong>';

	if ($value['std'] == "") 
		echo "blank"; 
	else  
		echo ( isset($value['editable']) ? 
		stripslashes(format_to_edit($value['std'])) : 
		$value['std'] ); 
	
	echo '</strong></div><div class="bfa-container-right">' . $value['info'] . '</div>
  	<div style="clear:both"></div></div>';
	
} 



#####################################################################
#     WIDGET LIST ITEMS                                                                                                     
#####################################################################

elseif ($value['type'] == "widget-list-items") { 

# needed for multi array options
$current_options = $bfa_ata[ $value['id'] ];	
?>
<div class="bfa-container">
	<div class="bfa-container-full">
		<label for="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></label>
		<?php echo $value['info']; ?>
		<br />
		<br />
		<table class="bfa-optiontable" border="0" cellspacing="0">
		<thead>
		<tr>
			<td colspan="8">List items and links inside</td>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>Left Margin for whole Item</td><td>Left Border Width for Links</td><td>Left Border Color for Links</td>
			<td>Left Border Hover Color for Links</td><td>Left Padding for Links</td><td>Link Text Weight</td><td>Link Text Color</td>
			<td>Link Text Hover Color</td></tr>
		<tr>
			<td>
				<select name="<?php echo $value['id'] . '[li-margin-left]'; ?>" id="<?php echo $value['id'] . '[li-margin-left]'; ?>">
					<?php for ($i = 0; $i <= 20; $i++) { ?>
					<option<?php if ( $current_options['li-margin-left'] == $i) { echo ' selected="selected"'; } 
					elseif ( !isset($current_options['li-margin-left']) AND $i == $value['std']['li-margin-left']) { 
					echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<select name="<?php echo $value['id'] . '[link-border-left-width]'; ?>" id="<?php echo $value['id'] . '[link-border-left-width]'; ?>">
					<?php for ($i = 0; $i <= 20; $i++) { ?>
					<option<?php if ( $current_options['link-border-left-width'] == $i) { echo ' selected="selected"'; } 
					elseif ( !isset($current_options['link-border-left-width']) AND $i == $value['std']['link-border-left-width']) { 
					echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<input size="8" class="color" name="<?php echo $value['id'] . '[link-border-left-color]'; ?>" id="<?php echo $value['id'] . 
				'[link-border-left-color]'; ?>" type="text" value="<?php if ( $current_options['link-border-left-color'] != "") { 
				echo $current_options['link-border-left-color'] ; } else { echo $value['std']['link-border-left-color']; } ?>" />
			</td>
			<td>
				<input size="8" class="color" name="<?php echo $value['id'] . '[link-border-left-hover-color]'; ?>" id="<?php echo $value['id'] . 
				'[link-border-left-hover-color]'; ?>" type="text" value="<?php if ( $current_options['link-border-left-hover-color'] != "") { 
				echo $current_options['link-border-left-hover-color'] ; } else { echo $value['std']['link-border-left-hover-color']; } ?>" />
			</td>
			<td>
				<select name="<?php echo $value['id'] . '[link-padding-left]'; ?>" id="<?php echo $value['id'] . '[link-padding-left]'; ?>">
					<?php for ($i = 0; $i <= 20; $i++) { ?>
					<option<?php if ( $current_options['link-padding-left'] == $i) { echo ' selected="selected"'; } 
					elseif ( !isset($current_options['link-padding-left']) AND $i == $value['std']['link-padding-left']) { 
					echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<select name="<?php echo $value['id'] . '[link-weight]'; ?>" id="<?php echo $value['id'] . '[link-weight]'; ?>">
					<?php foreach (array('normal', 'bold') as $option) { ?>
					<option<?php if ( $current_options['link-weight'] == $option) { echo ' selected="selected"'; } 
					elseif ( !isset($current_options['link-weight']) AND $option == $value['std']['link-weight']) { 
					echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<input size="8" class="color" name="<?php echo $value['id'] . '[link-color]'; ?>" id="<?php echo $value['id'] . 
				'[link-color]'; ?>" type="text" value="<?php if ( $current_options['link-color'] != "") { 
				echo $current_options['link-color'] ; } else { echo $value['std']['link-color']; } ?>" />
			</td>
			<td>
				<input size="8" class="color" name="<?php echo $value['id'] . '[link-hover-color]'; ?>" id="<?php echo $value['id'] . 
				'[link-hover-color]'; ?>" type="text" value="<?php if ( $current_options['link-hover-color'] != "") { 
				echo $current_options['link-hover-color'] ; } else { echo $value['std']['link-hover-color']; } ?>" />
			</td>
		</tr>
		</tbody>
		</table>	
		<div style="clear:both"></div>
	</div>
</div>
<?php } 



#####################################################################
#     DISPLAY ON                                                                                                     
#####################################################################

elseif ($value['type'] == "displayon") { 

	# special for checkboxes, if  checkbox is unchecked then there won't be any key/value  pair for that checkbox in the options table.
	if ( isset($bfa_ata[ $value['id'] ]))  
		$current_options = $bfa_ata[ $value['id'] ]; 
	else 
		$current_options = $value['std']; 

	echo '<div class="bfa-container"><div class="bfa-container-left"><label for="' . 
	$value['name'] . '">' . $value['name'] . '</label>
	<table align="right" class="bfa-optiontable" border="0" cellspacing="0" cellpadding="5">
	<tbody><tr><td style="vertical-align: top">
	<input type="checkbox" name="' . $value['id'] . '[homepage]" ' . 
	(isset($current_options['homepage']) ? 'checked="checked"' : '' ) . ' /> Homepage<br /> 
	<input type="checkbox" name="' . $value['id'] . '[frontpage]" ' . 
	(isset($current_options['frontpage']) ? 'checked="checked"' : '' ) . ' /> Front Page (*)<br /> 
	<input type="checkbox" name="' . $value['id'] . '[single]" ' . 
	(isset($current_options['single']) ? 'checked="checked"' : '' ) . ' /> Single Posts<br /> 
	<input type="checkbox" name="' . $value['id'] . '[page]" ' . 
	(isset($current_options['page']) ? 'checked="checked"' : '' ) . ' /> "Page" pages<br /> 
	<input type="checkbox" name="' . $value['id'] . '[category]" ' . 
	(isset($current_options['category']) ? 'checked="checked"' : '' ) . ' /> Category Pages<br /> 
	<input type="checkbox" name="' . $value['id'] . '[date]" ' . 
	(isset($current_options['date']) ? 'checked="checked"' : '' ) . ' /> Archive Pages
	</td><td style="vertical-align: top"> 
	<input type="checkbox" name="' . $value['id'] . '[tag]" ' . 
	(isset($current_options['tag']) ? 'checked="checked"' : '' ) . ' /> Tag Pages<br /> 
	<input type="checkbox" name="' . $value['id'] . '[taxonomy]" ' . 
	(isset($current_options['taxonomy']) ? 'checked="checked"' : '' ) . ' /> Cust.Tax. (**)<br /> 
	<input type="checkbox" name="' . $value['id'] . '[search]" ' . 
	(isset($current_options['search']) ? 'checked="checked"' : '' ) . ' /> Search Results<br /> 
	<input type="checkbox" name="' . $value['id'] . '[author]" ' . 
	(isset($current_options['author']) ? 'checked="checked"' : '' ) . ' /> Author Pages<br /> 
	<input type="checkbox" name="' . $value['id'] . '[404]" ' . 
	(isset($current_options['404']) ? 'checked="checked"' : '' ) . ' /> "Not Found"<br /> 
	<input type="checkbox" name="' . $value['id'] . '[attachment]" ' . 
	(isset($current_options['attachment']) ? 'checked="checked"' : '' ) . ' /> Attachments<br /> 
	<input type="hidden" name="' . $value['id'] . '[check-if-saved-once]" value="saved">
	</td></tr></tbody>
	</table>
	</div><div class="bfa-container-right">' . $value['info'] . '
	</div><div style="clear:both"></div></div>';
	
} 



#####################################################################
#     TEXTAREA                                                                                                    
#####################################################################

elseif ($value['type'] == "textarea") { 

	echo '<div class="bfa-container clearfix">
    <div class="bfa-container-left"><label for="' . $value['id'] . '">' . 
	$value['name'] . '</label><textarea name="' . $value['id'] . '" id="' .
	$value['id'] . '" class="growing">';
	
	if ( isset($bfa_ata[ $value['id'] ]) )  
		echo ( $value['editable'] == "yes" ? 
		stripslashes(format_to_edit( $bfa_ata[ $value['id'] ] )) : 
		$bfa_ata[ $value['id'] ] ) ; 
	else 
		echo ( $value['editable'] == "yes" ? 
		stripslashes(format_to_edit($value['std'])) : 
		$value['std'] ); 

	echo '</textarea><br />Default: <strong>';

	if ($value['std'] == "") 
		echo "blank"; 
	else  
		echo "<br /><code>" . ( $value['editable'] == "yes" ? 
		str_replace("\n", "<br />", htmlentities($value['std'], ENT_QUOTES)) : 
		str_replace("\n", "<br />", $value['std']) ) . "</code>";  

	echo '</strong></div><div class="bfa-container-right">' . $value['info'] . 
	'</div><div style="clear:both"></div></div>';

} 



#####################################################################
#     POSTINFOS & LARGE TEXTAREAS
#####################################################################

elseif ($value['type'] == "postinfos" OR $value['type'] == "textarea-large") { 

	echo '<div class="bfa-container">
	<div class="bfa-container-full"><label for="' . $value['name'] . '">' . 
	$value['name'] . '</label>' . $value['info'].'<br /><div class="mooarea"><textarea name="' .
#	$value['id'] . '" id="' . $value['id'] . '" class="growing">';
$value['id'] . '" id="' . $value['id'] . '" class="growing" 
cols="60" rows="1" style="overflow: hidden; height: 16px; line-height: 16px; ">';
/*
	if ( get_option( $value['id'] ) !== FALSE) 
		echo format_to_edit(get_option( $value['id'] )); 
	else 
		echo format_to_edit($value['std']); 
*/
	if ( isset($bfa_ata[ $value['id'] ]) ) 
		echo format_to_edit( $bfa_ata[ $value['id'] ] ); 
	else 
		echo format_to_edit( $value['std'] ); 
		
	echo "</textarea></div>Default: <strong>";
	
	if ($value['std'] == "")  
		echo "blank"; 
	else  
		echo format_to_edit( $value['std'] ); 
	
	echo '</strong></div></div>';

} 



#####################################################################
#     INFO                                                                                                     
#####################################################################

elseif ($value['type'] == "info") { 

	echo '<div class="bfa-container"><div class="bfa-container-full">
	<label for="' . $value['name'] . '">' . $value['name'] . '</label>' .
	$value['info'] . '</div></div>';
	
} 



#####################################################################
#     SELECT                                                                                                     
#####################################################################

elseif ($value['type'] == "select") { 

	echo '<div class="bfa-container"><div class="bfa-container-left">
	<label for="' . $value['name'] . '">' . $value['name'] . '</label>
	<select name="' . $value['id'] . '" id="' . $value['id'] . '">';
	
	foreach ($value['options'] as $option) { 
	
	    echo '<option';
		
		if ( $bfa_ata[ $value['id'] ] == $option) 
			echo ' selected="selected"'; 
		elseif ( $bfa_ata[ $value['id'] ] == '' AND $option == $value['std'])  
			echo ' selected="selected"'; 
 
		echo '>'.$option.'</option>';
		
	} 
	
	echo '</select><br />Default: <strong>' . 
# changed in 3.3.4:
#	( $value['std'] == "" ? "blank" : $value['std'] ) .
	$value['std'] .
	'</strong></div><div class="bfa-container-right">' . $value['info'] . 
	'</div><div style="clear:both"></div></div>';

}



#####################################################################
#     SAVE and RESET OPTIONS                                                                                                     
#####################################################################

	// all categories except first category "start-here" get closing form tags and buttons
	if ( $value['category'] != "start-here" AND isset($value['lastoption']) ) {  
		echo '<div id="submit-container" class="bfa-container" style="background: none; border: none;">	
		<p class="submit">
		<input class="save-tab" name="save" type="submit" value="" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="category" value="' . $value['category'] . '" />
		<span style="font-weight: bold; font-size: 22px; color: #018301">Save settings of current page</span>
		</p><br />
		</form>
		<form method="post">
		<p class="submit">
		<input class="reset-tab" name="reset" type="submit" value="" onClick="return confirmPageReset()" />
		<input type="hidden" name="action" value="reset" />
		<input type="hidden" name="category" value="' . $value['category'] . '" />
		<span style="font-weight: bold; font-size: 13px; color: #ab0000">Reset settings of current page</span>
		</p>
		</form>
		</div>';
	}

} 

// options loop END

?>
</div> <!-- closing the last tab content div //-->

<!-- "reset all" button -->
<br /><br />
	<form method="post">
	<p class="submit">
	<input class="reset-all" name="reset" type="submit" value="Reset ALL theme options" onClick="return confirmSubmit()"/>
	<input type="hidden" name="action" value="reset" />
	<input type="hidden" name="category" value="reset-all" /><br />
	<span style="color: #c00;"><strong>WARNING</strong> - this will reset ALL 200+ theme options!</span><br />Clicking this button will...<br />
	(1) remove all Atahualpa options from the WordPress options table<br />
	(2) reset all Atahualpa options to the default values<br />
	</p>
	</form>
	
</td>
</tr>
</table>

</div><!-- / class=wrap -->


<?php
}
?>