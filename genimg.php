<?php
header("Content-Type: image/png");
$w=$_REQUEST['w'];
$h=$_REQUEST['h'];
$im = imagecreate($w, $h);// or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im,0,0,$w,$h,$background_color);
if($_REQUEST['a']=="pingline") {
    $y1=25+$_REQUEST['pl0']*20;
    $x1=0;
    for($i=1;$i<9;$i++) {
        $y2=25+$_REQUEST['pl'.$i]*20;
        $x2=10+$i*10;
        $linecolor = imagecolorallocatealpha($im, 0, 255, 0, 0);
        imageline ($im , $x1, $y1, $x2, $y2, $linecolor);
        $y1=$y2;
        $x1=$x2;
    }
}
imagepng($im);
imagedestroy($im);
