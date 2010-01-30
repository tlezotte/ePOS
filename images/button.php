<?php
$label = $_GET['l'];
$c = $_GET['c'];
$image = $_GET['i'];

$im = imagecreatefrompng($image);

// Set color
switch ($c) {
	case warn:
	case red:
		$color = imagecolorallocate($im, 255, 0, 0);
		break;
	case blue:
		$color = imagecolorallocate($im, 0, 0, 255);
		break;		
	case orange:
		$color = imagecolorallocate($im, 255, 128, 0);
		break;		
	case ok:
	case green:
		$color = imagecolorallocate($im, 0, 128, 0);
		break;
	case grey:
	case gray:
		$color = imagecolorallocate($im, 153, 153, 153);
		break;			
	case tan:
		$color = imagecolorallocate($im, 99, 99, 66);
		break;					
	case black:					
	default:
		$color = imagecolorallocate($im, 0, 0, 0);
		break;
}

$px = (imagesx($im) - 7 * strlen($label)) / 2;
imagestring($im, 3, $px, 4, $label, $color);

header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
?> 
