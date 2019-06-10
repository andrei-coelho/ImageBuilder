<?php 

/**
 * Resize multiples rabbits 
 */

require "../ImageBuilder.php";

try {
    $image = new ImageBuilder("../images/rabbit.png");
    $image -> setPath("../img");
    $image -> setName("rabbit_copy_simple");
    $image -> resize("650x*",  "*x100", "50x50");
    $image -> destroy();
} catch (Exception $e){
    echo $e -> getMessage();
}


