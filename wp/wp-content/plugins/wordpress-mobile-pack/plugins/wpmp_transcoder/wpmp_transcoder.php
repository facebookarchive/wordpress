<?php

/*
$Id: wpmp_transcoder.php 320356 2010-12-08 07:55:08Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_transcoder/wpmp_transcoder.php $

Copyright (c) 2009 James Pearce & friends, portions mTLD Top Level Domain Limited, ribot, Forum Nokia

Online support: http://wordpress.org/extend/plugins/wordpress-mobile-pack/

This file is part of the WordPress Mobile Pack.

The WordPress Mobile Pack is Licensed under the Apache License, Version 2.0
(the "License"); you may not use this file except in compliance with the
License.

You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed
under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
CONDITIONS OF ANY KIND, either express or implied. See the License for the
specific language governing permissions and limitations under the License.
*/

/*
Plugin Name: Mobile Transcoder
Plugin URI: http://wordpress.org/extend/plugins/wordpress-mobile-pack/
Description: Rewrites blog pages and posts for the mobile theme, to ensure compatibility with mobile devices
Version: 1.2.4
Author: James Pearce & friends
Author URI: http://www.assembla.com/spaces/wordpress-mobile-pack
*/

function wpmp_transcoder_activate() {
  if(!is_writable($dir = $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'c')) {
    update_option('wpmp_warning', sprintf(__('<strong>Transcoder will not be able to cache images</strong> to %s.', 'wpmp'), $dir) . ' ' . __('Please ensure that the web server has write-access to that directory.', 'wpmp'));
  }
}

function wpmp_transcoder_remove_media(&$content) {

	// in some cases we might know what the tag wants to do, so we can replace it
	// with something good like a link to the mobile site of YouTube no need to
	// replace vimeo as the embedding code already comes with a nice link in case
	// the object is not supported or removed
  $patterns_to_replace = array(
    '/<object.*movie\"\ value=\"http\:\/\/(www\.|m\.)?youtube\.com\/(watch\?v=|v\/)(\w+).*\">.*\/object>/i',
  );
  $replacements = array(
    '<a href="http://m.youtube.com/#/watch?v=${3}">YouTube video</a>',  // replace the youtube embedding object with a link to the mobile page
  );
  $content = preg_replace($patterns_to_replace, $replacements, $content);

  $remove_tags = array(
    "script"=>true,
    "object"=>false,
    "embed"=>false,
    "marquee"=>false,
    "frame"=>false,
    "iframe"=>false,
  );

  $remove_attributes = array(
    "on[^=]*",
  );

  foreach($remove_tags as $remove_tag=>$and_inner) {
    if($and_inner) {
      $content = preg_replace("/\<$remove_tag.*\<\/$remove_tag"."[^>]*\>/Usi", "", $content);
    }
    $content = preg_replace("/\<\/?$remove_tag"."[^>]*\>/Usi", "", $content);
  }

  foreach($remove_attributes as $remove_attribute) {
    $content = preg_replace("/(\<[^>]*)(\s$remove_attribute=\\\".*\\\")/Usi", '$1', $content);
    $content = preg_replace("/(\<[^>]*)(\s$remove_attribute=\'.*\')/Usi", '$1', $content);
  }

}

function wpmp_transcoder_partition_pages(&$content) {
  global $wpmp_transcoder_is_last_page;
  $pages = wpmp_transcoder_weigh_paragraphs($content);
  if(!isset($_GET['wpmp_tp']) || !is_numeric($page = $_GET['wpmp_tp'])) {
    $page = 0;
  }
  if($page >= sizeof($pages)) {
    $page = sizeof($pages)-1;
  }
  if($page < 0) {
    $page = 0;
  }
  $pager = '';
  if(sizeof($pages)>1) {
    $pager = "<p>" . sprintf(__('Page %1$d of %2$d', 'wpmp'), $page+1, sizeof($pages));
    if ($page>0) {
      $previous .= "<a href='" . wpmp_transcoder_replace_cgi("wpmp_tp", $page-1) . "'>" . __('Previous page', 'wpmp') . "</a>";
    }
    if ($page<sizeof($pages)-1) {
      $next .= "<a href='" . wpmp_transcoder_replace_cgi("wpmp_tp", $page+1) . "'>" . __('Next page', 'wpmp') . "</a>";
      $wpmp_transcoder_is_last_page = false;
    } else {
      $wpmp_transcoder_is_last_page = true;
    }
    if($previous || $next) {
      $pager .= " | $previous";
      if($previous && $next) {
        $pager .= " | ";
      }
      $pager .= $next;
    }
    $pager .= "</p>";
  }
  $content = "<p>" . @implode("</p><p>", $pages[$page]) . "</p>$pager";
}


function wpmp_transcoder_is_last_page() {
  global $wpmp_transcoder_is_last_page;
  if(isset($wpmp_transcoder_is_last_page)) {
    return $wpmp_transcoder_is_last_page;
  }
  return true;
}

function wpmp_transcoder_shrink_images(&$content) {
  if(!function_exists('imagecreatetruecolor')) {
    return;
  }
  $content = preg_replace("/\<\/img*\>/Usi", "", $content);
  preg_match_all("/\<img.* src=((?:'[^']*')|(?:\"[^\"]*\")).*\>/Usi", $content, $images);
  foreach($images[0] as $img_index=>$image) {
    $src = $images[1][$img_index];
    $new_src = trim($src, "'\"");
    $new_src = wpmp_transcoder_url_join('http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], $new_src);
    $height = "";
    $width = "";
    $new_image = $image;
    preg_match_all("/(width|height)[=:'\"\s]*(\d+)(?:px|[^\d])/Usi", $image, $dimensions);
    foreach($dimensions[0] as $dimension_index=>$dimension_clause) {
      $$dimensions[1][$dimension_index] = $dimensions[2][$dimension_index];
      $new_image = str_replace($dimension_clause, "", $new_image);
    }
    if(!$height || !$width) { //er, where did these come from anyway? the magic of $$ ;-)
      wpmp_transcoder_fetch_image($new_src, $width, $height, $type, $location);
    }
    $max_width = wpmp_transcoder_max_screen_width();
    if($width>$max_width) {
      $height = floor($height * $max_width / $width);
      $width = $max_width;
    }
    $max_height = wpmp_transcoder_max_screen_height();
    if($height>$max_height) {
      $width = floor($width * $max_height / $height);
      $height = $max_height;
    }
    $new_src = wpmp_transcoder_convert_image($new_src, $width, $height);
    $new_image = str_replace($src, "'$new_src'", $new_image);
    $new_image = "<img width='$width' height='$height'" . substr($new_image, 4);
    $content = str_replace($image, $new_image, $content);
  }
}


function wpmp_transcoder_simplify_styling(&$content) {

  $remove_attributes = array(
    "align",
    "background",
    "bgcolor",
    "border",
    "cellpadding",
    "cellspacing",
    "class",
    "color",
    "height",
    "style",
    "width",
  );

  $remove_tags = array(
    "center",
    "font",
    "span",
    "style"
  );

  $remove_empty_tags = array(
    "\w*",
  );

  foreach($remove_attributes as $remove_attribute) {
    $content = preg_replace("/(\<[^>]*)(\s$remove_attribute=\\\".*\\\")/Usi", '$1', $content);
    $content = preg_replace("/(\<[^>]*)(\s$remove_attribute=\'.*\')/Usi", '$1', $content);
  }

  foreach($remove_tags as $remove_tag) {
    $content = preg_replace("/\<\/?$remove_tag"."[^>]*\>/Usi", "", $content);
  }

  foreach($remove_empty_tags as $remove_empty_tag) {
    $content = preg_replace("/\<{$remove_empty_tag}\s*\>\<\/{$remove_empty_tag}\s*\>/Usi", "", $content);
  }
}



function wpmp_transcoder_replace_cgi($key, $new_value) {
  $new_get = array();
  foreach($_GET as $get=>$value) {
    if($get!=$key) {
      $new_get[$get] = urlencode($get) . "=" . urlencode(stripslashes($value));
    }
  }
  $new_get[$key] = urlencode($key) . "=" . urlencode($new_value);
  return array_shift(explode("?", $_SERVER['REQUEST_URI'])) . "?" . implode("&amp;", $new_get);
}

function wpmp_transcoder_weigh_paragraphs($content) {
  $contiguous_tags = array(
    "ul"=>false,
    "ol"=>false,
    "div"=>false,
    "code"=>false,
  );
  $content = trim($content);
  foreach($contiguous_tags as $contiguous_tag=>$save_breaks) {
    preg_match_all("/\<{$contiguous_tag}.*<\/{$contiguous_tag}[^>]*\>/Usi", $content, $blocks);
    foreach($blocks[0] as $block) {
      $new_block = wpmp_transcoder_normalise_breaks($block);
      if($save_breaks) {
        $new_block = str_replace("\n", "<wpmpbr />", $new_block);
      } else {
        $new_block = str_replace("\n", " ", $new_block);
      }
      $content = str_replace($block, $new_block, $content);
    }
  }

  $content = wpmp_transcoder_normalise_breaks($content);
  $content = explode("\n", $content);
  $weights = array();
  $total_weight = 0;
  $max_weight = wpmp_transcoder_max_paragraph_weight();
  $paragraphs = array();
  foreach($content as $paragraph) {
    $paragraph = trim($paragraph);
    $paragraph = balanceTags($paragraph, true);
    if ($paragraph!='') {
      $weight = strlen($paragraph);
      if (strpos(strtolower($paragraph), "<img")) {
        $weight += 300;
      }
      $total_weight += $weight;
      if($weight > $max_weight) {
        $max_weight = $weight;
      }
      $weights[] = $weight;
      $paragraphs[] = $paragraph;
    }
  }
  $pages = array();
  $page = 0;
  $page_weight = 0;
  foreach($paragraphs as $p=>$paragraph) {
    if($page_weight + $weights[$p] > $max_weight) {
      $page++;
      $page_weight = 0;
    }
    $pages[$page][] = str_replace("<wpmpbr />", "<br />", $paragraph);
    $page_weight += $weights[$p];
  }
  return $pages;
}

function wpmp_transcoder_normalise_breaks($content) {
  $content = preg_replace("/\r/Usi", "\n", $content);
  $content = preg_replace("/\<\/?p[^>]*\>/Usi", "\n", $content);
  $content = preg_replace("/\<\/?br[^>]*\>/Usi", "\n", $content);
  $content = preg_replace("/\n+/Usi", "\n", $content);
  $content = preg_replace("/[\x20\x09]+/Usi", " ", $content);
  return $content;
}


function wpmp_transcoder_max_paragraph_weight() {
  $default = 5000;
  if(function_exists('wpmp_deviceatlas_enabled') && wpmp_deviceatlas_enabled()) {
    $memory = wpmp_deviceatlas_property('memoryLimitMarkup');
    if(!is_numeric($memory)) {
      return $default;
    }
    if($memory==0) {
      return 10000;
    }
    if($memory<3000) {
      return $default;
    }
    if($memory>15000) {
      return 10000;
    }
    return floor($memory * 0.66);
  }
  return $default;
}
function wpmp_transcoder_max_screen_width() {
  $default = 124;
  if(function_exists('wpmp_deviceatlas_enabled') && wpmp_deviceatlas_enabled()) {
    $width = wpmp_deviceatlas_property('usableDisplayWidth');
    if(!is_numeric($width)) {
      return $default;
    }
    if($width<40) {
      return 40;
    }
    if($width>300) {
      return 300;
    }
    return $width - 4;
  }
  return $default;
}
function wpmp_transcoder_max_screen_height() {
  $default = 124;
  if(function_exists('wpmp_deviceatlas_enabled') && wpmp_deviceatlas_enabled()) {
    $height = wpmp_deviceatlas_property('usableDisplayHeight');
    if(!is_numeric($height)) {
      return $default;
    }
    if($height<40) {
      return 40;
    }
    if($height>300) {
      return 300;
    }
    return $height - 4;
  }
  return $default;
}


function wpmp_transcoder_url_is_dot($val) {
  return $val != '.';
}

function wpmp_transcoder_url_join($base, $url) {
  $base = parse_url($base);
  $url = parse_url($url);

  if ($url['scheme']) {
    return wpmp_transcoder_url_unparse($url);
  }

  if (!($url['path'] || $url['query'] || $url['fragment'])) {
    return wpmp_transcoder_url_unparse($base);
  }

  if (substr($url['path'], 0, 1) == '/') {
    $base['path'] = $url['path'];
    return wpmp_transcoder_url_unparse($base);
  }

  $base['query'] = $url['query'];
  $base['fragment'] = $url['fragment'];

  $segments = explode('/', $base['path']);
  array_pop($segments);
  $segments = array_merge($segments, explode('/', $url['path']));
  if ($segments[sizeof($segments) - 1] == '.') {
    $segments[sizeof($segments) - 1] = '';
  }

  $segments = array_filter($segments, 'wpmp_transcoder_url_is_dot');

  while (true) {
    $i = 1;
    $n = sizeof($segments) - 1;
    while ($i < $n) {
      if ($segments[$i] == '..' &&
        $segments[$i-1] != '' &&
        $segments[$i-1] != '..') {
        unset($segments[$i]);
        unset($segments[$i-1]);
        break;
      }
      $i ++;
    }
    if ($i >= $n) {
      break;
    }
  }
  $cnt = sizeof($segments);
  if ($cnt == 2 && $segments[0] == '' && $segments[1] == '..') {
    $segments[1] = '';
  } elseif ($cnt >= 2 && $segments[$cnt - 1] == '..') {
    unset($segments[$cnt - 1]);
    $segments[$cnt - 2] = '';
  }
  $base['path'] = implode('/', $segments);
  return wpmp_transcoder_url_unparse($base);
}

function wpmp_transcoder_url_unparse($url) {
  if($url['scheme']) {
    $result = $url['scheme'] . '://';
  }
  if (@$url['user'] || @$url['pass']) {
    $result .= $url['user'] . ':' . $url['pass'] . '@';
  }
  $result .= $url['host'] . $url['path'];
  if (@$url['query']) {
    $result .= '?' . $url['query'];
  }
  if (@$url['fragment']) {
    $result .= '#' . $url['fragment'];
  }
  return $result;
}


function wpmp_transcoder_fetch_image($url, &$width, &$height, &$type, &$location) {
  $location = "c" . DIRECTORY_SEPARATOR . md5($url);
  $full_location = dirname(__FILE__) . DIRECTORY_SEPARATOR . $location;
  if(file_exists($full_location)) {
    if(file_exists($meta = "$full_location.meta")) {
      include($meta);
      return;
    }
  } else {
    $data = "";
    if($handle = @fopen($url, 'r')) {
      while (!feof($handle)) {
        $data .= fread($handle, 8192);
      }
      fclose($handle);
    } elseif ($handle = @curl_init($url)) {
      curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
      $data = curl_exec($handle);
      curl_close($handle);
    }
    if(!$data) {
      return false;
    }
    @file_put_contents($full_location, $data);
    $data = "";
  }
  $info = @getimagesize($full_location);
  $width = $info[0];
  $height = $info[1];
  switch($info[2]) {
    case IMAGETYPE_GIF:
      $type='gif';
      break;
    case IMAGETYPE_PNG:
      $type='png';
      break;
    case IMAGETYPE_JPEG:
      $type='jpg';
      break;
  }
  @file_put_contents("$full_location.meta", "<?php $"."width='$width';$"."height='$height';$"."type='$type'; ?>");
}

if (!function_exists('file_put_contents')) {
  function file_put_contents($filename, $data) {
    $f = @fopen($filename, 'w');
    if (!$f) {
      return false;
    } else {
      $bytes = fwrite($f, $data);
      fclose($f);
      return $bytes;
    }
  }
}

function wpmp_transcoder_convert_image($url, $width, $height) {
  if (wpmp_transcoder_fetch_image($url, $_w, $_h, $type, $location)===false) {
    return;
  }
  $base = get_option('home') . "/wp-content/plugins/wordpress-mobile-pack/plugins/wpmp_transcoder/";
  if ($width==$_w && $height==$_h) {
    return "$base$location";
  }
  if(!file_exists($full_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . ($file = "$location.$width.$height.$type"))) {
    $source = @imagecreatefromstring(file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $location));
    @imagealphablending($source, true);
    @imagesavealpha($source, true);
    $image = @imagecreatetruecolor($width, $height);
    @imagealphablending($image, false);
    @imagesavealpha($image, true);
    @imagecopyresampled($image, $source, 0, 0, 0, 0, $width, $height, $_w, $_h);
    @imagealphablending($image, true);
    @imagedestroy($source);
    switch($type) {
      case 'gif':
        imagegif($image, $full_file);
        break;
      case 'jpg':
        imagejpeg($image, $full_file);
        break;
      case 'png':
        imagepng($image, $full_file);
        break;
    }
    @imagedestroy($image);
  }
  return "$base$file";
}


function wpmp_transcoder_purge_cache() {
  $count = 0;

  $dir_handle = opendir($dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'c');
  while($file = readdir($dir_handle)) {
    if($file[0]!=".") {
      if(@unlink($dir . DIRECTORY_SEPARATOR . $file)) {
        $count++;
      }
    }
  }
  closedir($dir_handle);
  return $count;
}
?>
