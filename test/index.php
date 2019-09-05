<?php 

require "../autoload.php";

use ImageEditor\EditImage as EditImage;

EditImage::
    from('images/rabbit.png', 'h')
    ->copy(['v', 'b', 'n'])
    ->flip('horizontal', 'h')
    ->flip('vertical', 'v')
    ->flip('both', 'b')
    ->resize('600x*') // action
    ->crop('left bottom 300x500')
    ->save();



/**
 * filters:::::::::::::::
 * 
 * negate
 * grayscale
 * brightness
 * 
 */
