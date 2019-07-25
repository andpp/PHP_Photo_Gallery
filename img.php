<?php

// Include PHP Image Magician library
    require_once('php_image_magician.php');

    $name = $_GET["f"];
    $extensions    = array(".jpg",".png",".gif",".JPG",".PNG",".GIF");
    $ext = strrchr($name, '.');
    if(in_array($ext, $extensions)) {

        // Open JPG image
        $magicianObj = new imageLib($name);

        // Resize to best fit then crop
        $magicianObj -> resizeImage(1300, 1000, 'auto');

	$exifArray = $magicianObj -> getExif();
//  1: 'rotate(0deg)',
//  3: 'rotate(180deg)',
//  6: 'rotate(90deg)',
//  8: 'rotate(270deg)'

        switch ($exifArray['orientation']) {
            case 1:
                 // $magicianObj -> rotate(0);
                 break;
            case 3:
                 $magicianObj -> rotate(180);
                 break;
            case 6:
                 $magicianObj -> rotate(90);
                 break;
            case 8:
                 $magicianObj -> rotate(270);
                 break;
        }

        // Save resized image as a PNG
        $magicianObj -> displayImage('jpg', 85) ;
   } else {
        http_response_code(404);
   }

?>
