<?php 
/**
* @version		v.1.0
* @copyright	Copyright (C) 2008 NattyWP. All rights reserved.
* @author		Dave Miller
*
*/

// format definitions
define("GENERAL_WIDTH", "285");

define("YOUTUBE_HEIGHT", floor(GENERAL_WIDTH*14/17));
define("GOOGLE_HEIGHT", floor(GENERAL_WIDTH*14/17));
define("MYVIDEO_HEIGHT", floor(GENERAL_WIDTH*367/425));
define("CLIPFISH_HEIGHT", floor(GENERAL_WIDTH*95/116));
define("SEVENLOAD_HEIGHT", floor(GENERAL_WIDTH*408/500));
define("REVVER_HEIGHT", floor(GENERAL_WIDTH*49/60));
define("METACAFE_HEIGHT", floor(GENERAL_WIDTH*69/80));
define("YAHOO_HEIGHT", floor(GENERAL_WIDTH*14/17));
define("IFILM_HEIGHT", floor(GENERAL_WIDTH*365/448));
define("MYSPACE_HEIGHT", floor(GENERAL_WIDTH*173/215));
define("BRIGHTCOVE_HEIGHT", floor(GENERAL_WIDTH*206/243));
define("QUICKTIME_HEIGHT", floor(GENERAL_WIDTH*3/4));
define("VIDEO_HEIGHT", floor(GENERAL_WIDTH*3/4));
define("ANIBOOM_HEIGHT", floor(GENERAL_WIDTH*93/112));
define("FLASHPLAYER_HEIGHT", floor(GENERAL_WIDTH*93/112));
define("VIMEO_HEIGHT", floor(GENERAL_WIDTH*3/4));
define("GUBA_HEIGHT", floor(GENERAL_WIDTH*72/75));
define("DAILYMOTION_HEIGHT", floor(GENERAL_WIDTH*334/425));
define("GARAGE_HEIGHT", floor(GENERAL_WIDTH*289/430));
define("GAMEVIDEO_HEIGHT", floor(GENERAL_WIDTH*3/4));
define("VSOCIAL_HEIGHT", floor(GENERAL_WIDTH*40/41));
define("VEOH_HEIGHT", floor(GENERAL_WIDTH*73/90));
define("GAMETRAILERS_HEIGHT", floor(GENERAL_WIDTH*392/480));

// object targets and links
define("YOUTUBE_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.youtube.com/v/###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".YOUTUBE_HEIGHT."\"><param name=\"movie\" value=\"http://www.youtube.com/v/###VID###\" /><param name=\"autostart\" value=\"true\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("GOOGLE_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://video.google.com/googleplayer.swf?docId=###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".GOOGLE_HEIGHT."\"><param name=\"movie\" value=\"http://video.google.com/googleplayer.swf?docId=###VID###\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("MYVIDEO_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.myvideo.de/movie/###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".MYVIDEO_HEIGHT."\"><param name=\"movie\" value=\"http://www.myvideo.de/movie/###VID###\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("CLIPFISH_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.clipfish.de/videoplayer.swf?as=0&amp;videoid=###VID###&amp;r=1\" width=\"".GENERAL_WIDTH."\" height=\"".CLIPFISH_HEIGHT."\"><param name=\"movie\" value=\"http://www.clipfish.de/videoplayer.swf?as=0&amp;videoid=###VID###&amp;r=1\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("SEVENLOAD_TARGET", "<script type='text/javascript' src='http://sevenload.com/pl/###VID###/".GENERAL_WIDTH."x".SEVENLOAD_HEIGHT."'></script><br />");

define("REVVER_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://flash.revver.com/player/1.0/player.swf?mediaId=###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".REVVER_HEIGHT."\"><param name=\"movie\" value=\"http://flash.revver.com/player/1.0/player.swf?mediaId=###VID###\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("METACAFE_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.metacafe.com/fplayer/###VID###.swf\" width=\"".GENERAL_WIDTH."\" height=\"".METACAFE_HEIGHT."\"><param name=\"movie\" value=\"http://www.metacafe.com/fplayer/###VID###.swf\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("YAHOO_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://us.i1.yimg.com/cosmos.bcst.yahoo.com/player/media/swf/FLVVideoSolo.swf?id=###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".YAHOO_HEIGHT."\"><param name=\"movie\" value=\"http://us.i1.yimg.com/cosmos.bcst.yahoo.com/player/media/swf/FLVVideoSolo.swf?id=###VID###\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("IFILM_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.ifilm.com/efp?flvbaseclip=###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".IFILM_HEIGHT."\"><param name=\"movie\" value=\"http://www.ifilm.com/efp?flvbaseclip=###VID###\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("MYSPACE_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://lads.myspace.com/videos/vplayer.swf?m=###VID###&amp;type=video\" width=\"".GENERAL_WIDTH."\" height=\"".MYSPACE_HEIGHT."\"><param name=\"movie\" value=\"http://lads.myspace.com/videos/vplayer.swf?m=###VID###&amp;type=video\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("BRIGHTCOVE_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://admin.brightcove.com/destination/player/player.swf?initVideoId=###VID###&amp;servicesURL=http://services.brightcove.com/services&amp;viewerSecureGatewayURL=https://services.brightcove.com/services/amfgateway&amp;cdnURL=http://admin.brightcove.com&amp;autoStart=false\" width=\"".GENERAL_WIDTH."\" height=\"".BRIGHTCOVE_HEIGHT."\"><param name=\"movie\" value=\"http://admin.brightcove.com/destination/player/player.swf?initVideoId=###VID###&amp;servicesURL=http://services.brightcove.com/services&amp;viewerSecureGatewayURL=https://services.brightcove.com/services/amfgateway&amp;cdnURL=http://admin.brightcove.com&amp;autoStart=false\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("ANIBOOM_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://api.aniboom.com/embedded.swf?videoar=###VID###&amp;allowScriptAccess=sameDomain&amp;quality=high\" width=\"".GENERAL_WIDTH."\" height=\"".ANIBOOM_HEIGHT."\"><param name=\"movie\" value=\"http://api.aniboom.com/embedded.swf?videoar=###VID###&amp;allowScriptAccess=sameDomain&amp;quality=high\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("VIMEO_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.vimeo.com/moogaloop.swf?clip_id=###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".VIMEO_HEIGHT."\"><param name=\"movie\" value=\"http://www.vimeo.com/moogaloop.swf?clip_id=###VID###\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("GUBA_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.guba.com/f/root.swf?video_url=http://free.guba.com/uploaditem/###VID###/flash.flv&amp;isEmbeddedPlayer=true\" width=\"".GENERAL_WIDTH."\" height=\"".GUBA_HEIGHT."\"><param name=\"movie\" value=\"http://www.guba.com/f/root.swf?video_url=http://free.guba.com/uploaditem/###VID###/flash.flv&amp;isEmbeddedPlayer=true\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("DAILYMOTION_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.dailymotion.com/swf/###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".DAILYMOTION_HEIGHT."\"><param name=\"movie\" value=\"http://www.dailymotion.com/swf/###VID###\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("GARAGE_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.garagetv.be/v/###VID###/v.aspx\" width=\"".GENERAL_WIDTH."\" height=\"".GARAGE_HEIGHT."\"><param name=\"movie\" value=\"http://www.garagetv.be/v/###VID###/v.aspx\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("GAMEVIDEO_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://gamevideos.com:80/swf/gamevideos11.swf?embedded=1&amp;autoplay=0&amp;src=http://gamevideos.com:80/video/videoListXML%3Fid%3D###VID###%26adPlay%3Dfalse\" width=\"".GENERAL_WIDTH."\" height=\"".GAMEVIDEO_HEIGHT."\"><param name=\"movie\" value=\"http://gamevideos.com:80/swf/gamevideos11.swf?embedded=1&fullscreen=1&amp;autoplay=0&amp;src=http://gamevideos.com:80/video/videoListXML%3Fid%3D###VID###%26adPlay%3Dfalse\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("VSOCIAL_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://static.vsocial.com/flash/ups.swf?d=###VID###&a=0\" width=\"".GENERAL_WIDTH."\" height=\"".VSOCIAL_HEIGHT."\"><param name=\"movie\" value=\"http://static.vsocial.com/flash/ups.swf?d=###VID###&a=0\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("VEOH_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=###VID###&id=anonymous\" width=\"".GENERAL_WIDTH."\" height=\"".VEOH_HEIGHT."\"><param name=\"movie\" value=\"http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=###VID###&id=anonymous\" /><param name=\"autostart\" value=\"true\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

define("GAMETRAILERS_TARGET", "<object type=\"application/x-shockwave-flash\" data=\"http://www.gametrailers.com/remote_wrap.php?mid=###VID###\" width=\"".GENERAL_WIDTH."\" height=\"".GAMETRAILERS_HEIGHT."\"><param name=\"movie\" value=\"http://www.gametrailers.com/remote_wrap.php?mid=###VID###\" /><param name=\"autostart\" value=\"true\" /><param name=\"wmode\" value=\"transparent\" /></object><br />");

function t_show_video($pID){
	$video_id =  get_post_meta($pID, "video_id", $single = true);
	$video_type = get_post_meta($pID, "video_type", $single = true); 
	if($video_type == '' || $video_type == '#NONE#') { }
	else{
	switch ($video_type) {
			case "youtube": $output .= YOUTUBE_TARGET; break;
			case "google": $output .= GOOGLE_TARGET; break;
			case "myvideo": $output .= MYVIDEO_TARGET; break;
			case "clipfish": $output .= CLIPFISH_TARGET; break;
			case "sevenload": $output .= SEVENLOAD_TARGET; break;
			case "revver": $output .= REVVER_TARGET; break;
			case "metacafe": $output .= METACAFE_TARGET; break;
			case "yahoo": $output .= YAHOO_TARGET; break;
			case "ifilm": $output .= IFILM_TARGET; break;
			case "myspace": $output .= MYSPACE_TARGET; break;
			case "brightcove": $output .= BRIGHTCOVE_TARGET; break;
			case "aniboom": $output .= ANIBOOM_TARGET; break;
			case "vimeo": $output .= VIMEO_TARGET; break;
			case "guba": $output .= GUBA_TARGET; break;
			case "gamevideo": $output .= GAMEVIDEO_TARGET; break;
			case "vsocial": $output .= VSOCIAL_TARGET; break;
			case "dailymotion": $output .= DAILYMOTION_TARGET; break;
			case "garagetv": $output .= GARAGE_TARGET; break;
			case "veoh": $output .= VEOH_TARGET; break;
			case "gametrailers": $output .= GAMETRAILERS_TARGET; break;
	}
	
	$output = str_replace("###VID###", $video_id, $output);

	echo '<div style="float:left; margin:0px 10px 10px 0px;">'. $output .'</div>';
	}
}

$metabox_video = array(
		"video_type" => array (
			"name"		=> "video_type",
			"default" 	=> "",
			"type" 		=> "text",
			"desc"      => "Upload your image with 'Add Media' above post window, copy the url and paste it here."
		),
		"video_id" => array (
			"name"		=> "video_id",
			"default" 	=> "",
			"type" 		=> "text",
			"desc"      => "Upload your image with 'Add Media' above post window, copy the url and paste it here."
		),

	);
	
function video_meta_box_content() {
	global $post, $metabox_video;
	echo '<div id="postcustomstuff"><table id="list-table">'."\n";
	echo "\t".'<thead>';
	echo "\t".'<tr>';
	echo "\t".'<th class="left">Video Type</th>';
	echo "\t".'<th>Video ID</th>';
	echo "\t".'</tr>';
	echo "\t".'</thead>';
	echo "\t".'<tr>';
	echo "\t\t".'<td id="newmetaleft" class="left">';

	foreach ($metabox_video as $custom_metabox) {
		$metabox_value = get_post_meta($post->ID,$custom_metabox["name"],true);
		if ($metabox_value == "" || !isset($metabox_value)) {
			$metabox_value = $custom_metabox['default'];
		}
		
			if ($custom_metabox["name"] == 'video_type'){			
				$video_entries[] = array( "#NONE#", "- Select -" );
				$video_entries[] = array( "youtube", "Youtube" );
				$video_entries[] = array( "google", "Google" );
				$video_entries[] = array( "myvideo", "MyVideo" );
				$video_entries[] = array( "clipfish", "Clip Fish" );
				$video_entries[] = array( "sevenload", "Sevenload" );
				$video_entries[] = array( "revver", "Revver" );
				$video_entries[] = array( "metacafe", "Metacafe" );
				$video_entries[] = array( "yahoo", "Yahoo" );
				$video_entries[] = array( "ifilm", "iFilm" );
				$video_entries[] = array( "myspace", "Myspace" );
				$video_entries[] = array( "brightcove", "Brightcove" );
				$video_entries[] = array( "aniboom", "Aniboom" );
				$video_entries[] = array( "vimeo", "Vimeo" );
				$video_entries[] = array( "guba", "Guba" );
				$video_entries[] = array( "gamevideo", "Gamevideo" );
				$video_entries[] = array( "vsocial", "Vsocial" );
				$video_entries[] = array( "dailymotion", "Dailymotion" );
				$video_entries[] = array( "garagetv", "Garagetv" );
				$video_entries[] = array( "veoh", "Veoh" );
				$video_entries[] = array( "gametrailers", "Gametrailers" );
				

			
				ih_select( 'video_'.$custom_metabox["name"], $video_entries, $metabox_value, "" ); 			
			
				echo "\t".'</td>';
			}
			else {
	echo "\t\t".'<td><input size="70" type="'.$custom_metabox['type'].'" value="'.$metabox_value.'" name="video_'.$custom_metabox["name"].'" id="'.$custom_metabox.'"/></td>'."\n";
			}			
	}
	
	echo "\t".'</tr>';
	echo '</table></div>'."\n\n";
	echo '<p>Select Video Type and insert Video ID (e.g. http://www.youtube.com/watch?v=<strong>Y2HIK1lgb3U</strong>).</p>'."\n\n";
}

function video_metabox_insert($pID) {

	global $metabox_video;
	foreach ($metabox_video as $custom_metabox) {
		$var = "video_".$custom_metabox["name"];
		if (isset($_POST[$var])) {
			if( get_post_meta( $pID, $custom_metabox["name"] ) == "" )
					add_post_meta($pID, $custom_metabox["name"], $_POST[$var], true );				
			elseif($_POST[$var] != get_post_meta($pID, $custom_metabox["name"], true))				
					update_post_meta($pID, $custom_metabox["name"], $_POST[$var]);				
			elseif($_POST[$var] == "")
				delete_post_meta($pID, $custom_metabox["name"], get_post_meta($pID, $custom_metabox["name"], true));
		}
	}
}

function video_meta_box() {
	if ( function_exists('add_meta_box') ) {
		add_meta_box('video-settings',$GLOBALS['natty_themename'].' Video Settings','video_meta_box_content','post','normal');
		add_meta_box('video-settings',$GLOBALS['natty_themename'].' Video Settings','video_meta_box_content','page','normal');
	}
}

add_action('admin_menu', 'video_meta_box');
add_action('wp_insert_post', 'video_metabox_insert');
?>