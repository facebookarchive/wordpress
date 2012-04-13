<?php
/* 
Handle all media processing for content. Used for publish module and for base OpenGraph data.
This is an annoying and complex set of rules and functions and interdependancies. 
You have been warned.
*/

// main OpenGraph handler
add_filter('sfc_base_meta','sfc_media_handler', 10, 2);
function sfc_media_handler($og, $post) {
	
	// only add this sort of meta to single post pages
	if (!is_singular()) return $og;

	$options = get_option('sfc_options');
	
	// first we apply the filters to the content, just in case they're using shortcodes or oembed to display stuff
	$content = apply_filters('the_content', $post->post_content);

	// video handling
	$vids = sfc_media_find_video($post, $content);

	if ( !empty($vids) ) 
		$og = array_merge($og,$vids);
	
	// image handling
	$images = sfc_media_find_images($post, $content);
	
	if (!empty($images)) {
		foreach ($images as $image) {
			$og['og:image'][] = $image;
		}
	} else if (!empty($options['default_image'])) {
		$og['og:image'][] = $options['default_image'];
	}
	
	// audio handling	
	$auds = sfc_media_find_audio($post, $content);
	$og = array_merge($og,$auds);
	
	if (isset($og['og:audio'])) {
		// audio files on posts sometimes get misidentified as video, clear those out
		unset($og['og:video']);
		unset($og['og:video:height']);
		unset($og['og:video:width']);
		unset($og['og:video:type']);
	}

	return $og;
}

function sfc_media_find_images($post, $content='') {

	if (empty($content)) $content = apply_filters('the_content', $post->post_content);
	
	$images = array();
	
	// we get the post thumbnail, put it first in the image list
	if ( current_theme_supports('post-thumbnails') && has_post_thumbnail($post->ID) ) {
		$thumbid = get_post_thumbnail_id($post->ID);
		$att = wp_get_attachment_image_src($thumbid, 'full');
		if (!empty($att[0])) {
			$images[] = $att[0];
		}
	}
	
	if (is_attachment() && 	preg_match('!^image/!', get_post_mime_type( $post ))) {	
	    $images[] = wp_get_attachment_url($post->ID);
	}
	
	// now search for images in the content itself
	if ( preg_match_all('/<img\s+(.+?)>/i', $content, $matches) ) {
		foreach($matches[1] as $match) {
			foreach ( wp_kses_hair($match, array('http')) as $attr)
				$img[strtolower($attr['name'])] = $attr['value'];
			if ( isset($img['src']) ) {
				if ( !isset( $img['class'] ) || ( isset( $img['class'] ) && false === straipos( $img['class'], apply_filters( 'sfc_img_exclude', array( 'wp-smiley' ) ) ) ) ) { // ignore smilies
					if ( !in_array( $img['src'], $images ) 
						&& strpos( $img['src'], 'fbcdn.net' ) === false // exclude any images on facebook's CDN
						&& strpos( $img['src'], '/plugins/' ) === false // exclude any images put in from plugin dirs
						) {
						$images[] = $img['src'];
					}
				}
			}
		}
	}
	
	return $images;
}

function sfc_media_find_video($post, $content='') {
	if (empty($content)) $content = apply_filters('the_content', $post->post_content);

	$og=array();
	
	// look for iframes
	if ( preg_match('/<iframe\s+(.+?)>/i', $content, $matches) ) {
	
		// parse out the params
		foreach ( wp_kses_hair($matches[1], array('http')) as $attr) 
			$embed[strtolower($attr['name'])] = $attr['value'];
		
		if (!empty($embed['src'])) {
			
			$save = false;
			
			// first, check post meta, maybe we already did this URL once
			$hash = md5($embed['src']);
			$meta = get_post_meta($post->ID, '_sfc_embed_'.$hash, true);
			if ( !empty( $meta ) ) {
				// we have done this before, use the saved data instead of reprocessing
				$og = $meta;
			}

			else

			// youtube iframes have srcs that start with http://www.youtube.com/embed/(id)
			if ( preg_match('@http://[^/]*?youtube\.com/embed/([^?&#]+)@i', $embed['src'], $matches ) ) {	
				// this is what youtube's own opengraph data looks like
				$og['og:video'] = 'http://www.youtube.com/v/'.$matches[1].'?version=3&amp;autohide=1';
				$og['og:video:height'] = 224;
				$og['og:video:width'] = 398;
				$og['og:video:type'] = "application/x-shockwave-flash";
				$og['og:image'][] = "http://img.youtube.com/vi/{$matches[1]}/0.jpg";
			} 

			else

			// vimeo iframes have srcs that start with http://player.vimeo.com/video/(id)
			if ( preg_match('@http://[^/]*?vimeo\.com/video/([^?&#]+)@i', $embed['src'], $matches ) ) {	
				// this is what vimeo's own opengraph data looks like
				$og['og:video'] = 'http://vimeo.com/moogaloop.swf?clip_id='.$matches[1];
				$og['og:video:height'] = 360;
				$og['og:video:width'] = 640;
				$og['og:video:type'] = "application/x-shockwave-flash";

				$resp = wp_remote_get("http://vimeo.com/api/v2/video/{$matches[1]}.json");
				if (!is_wp_error($resp) && 200 == wp_remote_retrieve_response_code( $resp )) {
					$data = json_decode(wp_remote_retrieve_body($resp), true);
					if (!empty($data[0]['thumbnail_large'])) {
						$thumb = $data[0]['thumbnail_large'];
					}
				}

				if (isset($thumb)) {
					$og['og:image'][] = $thumb;
					$save=true;
				}
			}

			else 
			
			// dailymotion iframe src's look like http://www.dailymotion.com/embed/video/(id)
			if ( preg_match('@http://[^/]*?dailymotion\.com/embed/video/([^?&#]+)@i', $embed['src'], $matches ) ) {	
				// this is what dailymotion's own opengraph data looks like
				$og['og:video'] = 'http://www.dailymotion.com/swf/video/'.$matches[1].'?autoPlay=1';
				$og['og:video:height'] = 720;
				$og['og:video:width'] = 1280;
				$og['og:video:type'] = "application/x-shockwave-flash";
				
				$resp = wp_remote_get("https://api.dailymotion.com/video/{$matches[1]}?fields=thumbnail_large_url", array('sslverify'=>false));
				if (!is_wp_error($resp) && 200 == wp_remote_retrieve_response_code( $resp )) {
					$data = json_decode(wp_remote_retrieve_body($resp), true);
					if (!empty($data['thumbnail_large_url'])) {
						$thumb = $data['thumbnail_large_url'];
					}
				}
				if (isset($thumb)) {
					$og['og:image'][] = $thumb;
					$save=true;
				}
			}
			
			else
			
			// blip.tv urls look like http://blip.tv/play/(id).html
			if ( preg_match('@http://[^/]*?blip\.tv/play/([^?&#]+).html@i', $embed['src'], $matches ) ) {	
				// this is what blip.tv's own opengraph data looks like
				$og['og:video'] = 'http://blip.tv/play/'.$matches[1];
				$og['og:video:type'] = "application/x-shockwave-flash";
				
				$resp = wp_remote_get("http://blip.tv/players/episode/{$matches[1]}?skin=json");
				if (!is_wp_error($resp) && 200 == wp_remote_retrieve_response_code( $resp )) {
					$body = wp_remote_retrieve_body($resp);
					if (preg_match("/blip_ws_results\((.*)\)\;/ms", $body, $matches)){
						$data = json_decode($matches[1], true);
						if (!empty($data[0]["Post"]["thumbnailUrl"])) {
							$thumb = $data[0]["Post"]["thumbnailUrl"];
						}
					}
				}
				if (isset($thumb)) {
					$og['og:image'][] = $thumb;
					$save=true;
				}
			}
			
			
			
			// TODO add new providers with weird stuff here as needed
						
			
			/* 
			
			Quick documentation for hackers who want to screw around with this:
			
			For Facebook to properly use video information, all four video fields *and* the image must be populated. 
			
			So if you're going to try to do some url magic, then you may need to make an API call somewhere to get the
			image for the video thumbnail (or other things, conceivably). If you do, then setting the $save=true like I 
			did above will make the below line of code save your results in post meta and then they won't have to be 
			re-retrieved from whatever API you're hitting. There's a call up at the top to handle retreiving those
			meta results and using them, thus bypassing all the parsing nonsense.
			
			But, ONLY set $save to true if you're making external API calls. If you can get the image info without making 
			an external hit, then leave it false so as to not waste space in the DB with the meta information. Notice how
			I don't save the YouTube info because it's not necessary. In actual fact, that image isn't the exact same as
			the one they use on their data, and I could make an oEmbed API call to get their image, but it's pointless
			since that image URL I'm generating always works anyway.
			
			*/
			
			// save whatever we found so we don't have to do it again
			if ($save) update_post_meta($post->ID, '_sfc_embed_'.$hash, $og);
		}
	}
	
	else
	
	// TODO: This is crap and it rarely works. Think harder.
	/*
	// look for an embed to add with video_src (simple, just add first embed)
	if ( preg_match('/<embed\s+(.+?)>/i', $content, $matches) ) {
		foreach ( wp_kses_hair($matches[1], array('http')) as $attr) 
			$embed[strtolower($attr['name'])] = $attr['value'];
	
		$embed['src'] = preg_replace('/&.*$/','', $embed['src']);
	
		if (preg_match('@http://[^/]*?youtube\.com/@i', $embed['src']) ) {
			$embed['src'] = preg_replace('/[?&#].*$/','', $embed['src']);
		}
		
		// it's amazing how often this works
		if (!empty($embed['flashvars'])) {
			$embed['src'] .= '&amp;'.$embed['flashvars'];
		}
	
		if ( isset($embed['src']) ) $og['og:video'] = $embed['src'];
		if ( isset($embed['height']) ) $og['og:video:height'] = $embed['height'];
		if ( isset($embed['width']) ) $og['og:video:width'] = $embed['width'];
		if ( isset($embed['type']) ) $og['og:video:type'] = $embed['type'];
	}
*/

/*
Veoh embed example for potential future use

<object width="410" height="341" id="veohFlashPlayer" name="veohFlashPlayer">
<param name="movie" value="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1311&permalinkId=v25812263H4aE9RgT&player=videodetailsembedded&videoAutoPlay=0&id=anonymous"></param>
<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
<embed src="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1311&permalinkId=v25812263H4aE9RgT&player=videodetailsembedded&videoAutoPlay=0&id=anonymous" 
type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" 
width="410" height="341" id="veohFlashPlayerEmbed" name="veohFlashPlayerEmbed"></embed>
</object>
<br /><font size="1">Watch <a href="http://www.veoh.com/watch/v25812263H4aE9RgT">
Dagli Brugsen Glud-Nørby opføres.</a> in <a href="http://www.veoh.com/browse/videos/category/entertainment">
Entertainment</a>  |  View More <a href="http://www.veoh.com">Free Videos Online at Veoh.com</a></font>
*/
	return $og;
}

function sfc_media_find_audio($post, $content='') {
	//if (empty($content)) $content = apply_filters('the_content', $post->post_content);
	
	$og = array();
	
	$enc = get_post_meta($post->ID, 'enclosure', true);

	if (!empty($enc)) {
		$enclosure = explode("\n", $enc);

		//only get the the first element eg, audio/mpeg from 'audio/mpeg mpga mp2 mp3'
		$t = preg_split('/[ \t]/', trim($enclosure[2]) );
		$type = $t[0];

		$og['og:audio'] = trim(htmlspecialchars($enclosure[0]));
		$og['og:audio:type'] = esc_attr($type);
		$og['og:audio:title'] = esc_attr(get_the_title());
	}

	return $og;
}