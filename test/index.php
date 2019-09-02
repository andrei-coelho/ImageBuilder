<?php 

require "../autoload.php";

use ImageBuilder\BuildImage as BuildImage;

$images = BuildImage::
from('images/rabbit.png')
->copy(3)
->path_as('img/coelho.png')
->each_image('path_as', [1 => 'images/coelho_ao_lado.png'])
->each_image('resize', [2 => '200x*'])
->save();
