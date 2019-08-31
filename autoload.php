<?php 

function __autoload($name) {

    $file = $name == 'ImageBuilderNew' 
    ? __DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR.$name. ".php"
    : __DIR__.DIRECTORY_SEPARATOR.$name. ".php";

    if (file_exists($file) && is_readable($file)) {
        require $file;
        return;
    }

    throw new Exception("This class {$name} not exists", 1);

}