<?php 

require "../autoload.php";

use ImageBuilder\BuildImage as BuildImage;

$images = BuildImage::
from('images/rabbit.png')
->copy(3)
->save();


/*
$img = getimagesize("http://lounge.obviousmag.org/por_tras_do_espelho/2012/10/as-imagens-de-sonho-de-anja-stiegler.html.jpg?v=20190825090142");
var_dump($img);

$im = imagecreatetruecolor(100, 100);

// Set alphablending to on
imagealphablending($im, true);

// Draw a square
imagefilledrectangle($im, 30, 30, 70, 70, imagecolorallocate($im, 255, 0, 0));

// Output
header('Content-Type: image/png');

imagepng($im);
imagedestroy($im);

*/