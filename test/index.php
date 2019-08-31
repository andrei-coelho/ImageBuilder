<?php 

require "../autoload.php";

BuildImage::
from('images/rabbit.png', 'alias_um')
->copies(['alias_dois', 'alias_tres']);
