<?php
if (! isset($_GET['f'])) {
	error_log('image.php: file name parameter not found');
	exit;
}
$fname = $_GET['f'];
if (! is_file("img/$fname")) {
	error_log('image.php: requested file ' . $fname . ' does not exist');
	exit;
}

$img = null;
if (preg_match('/\.jpg$/i', $fname) || preg_match('/\.jpeg$/i', $fname)) $img = imagecreatefromjpeg("img/$fname");
if (preg_match('/\.gif$/i', $fname)) $img = imagecreatefromgif("img/$fname");
if (preg_match('/\.png$/i', $fname)) $img = imagecreatefrompng("img/$fname");

list($width, $height, $type, $attr) = getimagesize("img/$fname");
if ($width > 1280) {
	$newWidth = 1280;
	$newHeight = $height * 1280 / $width;
	$thumb = imagecreatetruecolor($newWidth, $newHeight);
	imagecopyresized($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
	$img = $thumb;
}
header('Content-type: image/jpeg');
imagejpeg($img);
?>