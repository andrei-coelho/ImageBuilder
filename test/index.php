<?php 

require "../autoload.php";

use ImageBuilder\BuildImage as BuildImage;

$images = BuildImage::
from('images/rabbit.png')
->path_as('img/coelho_crop.png')
->crop('right center 800x*')
->resize('300x*')
->save();
