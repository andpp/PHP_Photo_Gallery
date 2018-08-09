<?php

// Include PHP Image Magician library
    require_once('php_image_magician.php');

    $name = $_GET["f"];

    // Open JPG image
    $magicianObj = new imageLib($name);

    // Resize to best fit then crop
    $magicianObj -> resizeImage(200, 150, 'auto');

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
    $magicianObj -> displayImage('jpg',80) ;



    $th_name = str_replace("albums","thumbs",$name);
    $th_name = str_replace("//","/",$th_name);
//    echo $th_name;
    if (!file_exists(dirname($th_name))) {
        mkdir(dirname($th_name), 0777, true);
    }
    $magicianObj -> saveImage($th_name,85);


?>
