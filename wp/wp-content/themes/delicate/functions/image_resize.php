<?php
// File and new size
$filename = $_GET['fn'];
$percent = 0.33;

// Content type
header('Content-type: image/png');

// Get new sizes
$size = getimagesize($filename);
$width = $size[0];
$height = $size[1];
$mime = $size['mime'];
$newwidth = $width * $percent;
$newheight = $height * $percent;

// Load
$thumb = imagecreatetruecolor($newwidth, $newheight);
// Transparent Background
imagesavealpha($thumb, true);
$trans_colour = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
imagefill($thumb, 0, 0, $trans_colour);

if ($mime == 'image/jpeg') {
	$source = imagecreatefromjpeg($filename);
}
if ($mime == 'image/png') {
	$source = imagecreatefrompng($filename);
}
if ($mime == 'image/gif') {
	$source = imagecreatefromgif($filename);
}

// Resize
imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

// Output
imagepng($thumb);
?>
