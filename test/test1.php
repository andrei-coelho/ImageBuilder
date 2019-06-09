<?php 

/**
 * Resize Image Test
 */

require "../ImageBuilder.php";

try {
    $image = new ImageBuilder("images/sea-turtle-960x541.jpg");
    $image -> setPath("img");
    $image -> setName("copy");
    $image -> resize("700x*");
    $image -> resize("500x*");
    $image -> resize("300x*");
} catch (Exception $e){
    echo $e -> getMessage();
}


