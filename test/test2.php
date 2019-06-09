<?php 

/**
 * Resize Image Test PNG Rabbit in transparence
 */

require "../ImageBuilder.php";

try {
    $image = new ImageBuilder("../images/rabbit.png");
    $image -> setPath("../img");
    $image -> setName("copy_rabbit");
    $image -> resize("700x*");
    $image -> resize("200x*");
    $image -> destroy();
} catch (Exception $e){
    echo $e -> getMessage();
}

