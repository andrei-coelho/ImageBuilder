<?php 

spl_autoload_register(function($name) {
	
	$prefix = 'ImageEditor\\';

	if(strpos($name, $prefix) !== 0){
		// its not a ImageEditor classes
		return;
	}

	// remove prefix
	$nameFile = str_replace($prefix, "", $name);

	$file = __DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR.$nameFile.".php";

    if (file_exists($file) && is_readable($file)) {
        include $file;
        return;
    }

    throw new Exception("This class {$name} not exists", 1);

});
