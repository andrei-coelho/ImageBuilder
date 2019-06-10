<?php 

/**
 * Resize multiples rabbits 
 */

require "../ImageBuilder.php";

try {
    $image = new ImageBuilder("../images/rabbit.png");
    $image -> setPath("../img");
    $image -> setName("rabbit_copy");
    $image -> copyResize("700x*", "*x100", "50x50");
    $image -> destroy();
} catch (Exception $e){
    echo $e -> getMessage();
}


