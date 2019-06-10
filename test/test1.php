<?php 

/**
 * Resize Image Test JPG turtle
 */

require "../ImageBuilder.php";

try {
    $image = new ImageBuilder("../images/sea-turtle-960x541.jpg");
    $image -> setPath("../img");
    $image -> setName("copy");
    $image -> resize("700x*");
    $image -> destroy();
} catch (Exception $e){
    echo $e -> getMessage();
}


