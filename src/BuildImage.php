<?php 

/**
 *
 * @author Andrei Coelho
 * @version 0.2
 *
 */

use src\ImageBuilderException as ImageBuilderException;
use src\Image as Image;

 class BuildImage {

 	private $images = [];
	private static $sizes;
	private $copy;

    private function __construct(string $from, $alias = 0)
    {
    	if(!extension_loaded('gd')){
            throw new ImageBuilderException($from, 0);
        }

        if(!($sizes = getimagesize($from))){
            throw new ImageBuilderException($from, 1);
        }

        self::$sizes = $sizes;

        $res = @imagecreatetruecolor($sizes[0], $sizes[1]);

        if(gettype($res) !== 'resource'){
        	throw new ImageBuilderException($from, 0);
        }

        $this->copy = $this->images[$alias] = new Image($res); 

        return $this;
    }

    public static function from(string $from, string $alias = null){

    	return $alias ? new BuildImage($from, $alias) : new BuildImage($from);

    }

    public function copies($argument)
    {	
    	$copy = $this->copy;

    	if(is_int($argument)){
    		$total = $argument - count($this->images);
	    	for ($i=0; $i < $total; $i++) { 
	    		$this->images[] = $copy;
	    	}
    	} else 
    	if(is_array($argument)){
    		foreach ($argument as $alias) {
    			if($alias){
    				$this->images[$alias] = $copy;
    			}
    		}
    	}

    	return $this;
    }

    public function copy(string $alias = null)
    {
    	$alias ? $this->images[$alias] = $this->copy : $this->images[] = $this->copy;
    	return $this;
    }

    public function resize(string $size)
    {	
    	if(self::is_not_size($size)){
    		throw new ImageBuilderException($size, 3);
    	}

    	$vars = self::generate_width_height(explode("x", strtolower($size)));
    	
    	foreach ($this->images as $img) {
    		$img->resize($vars[1], $vars[2]);
    	}

    	return $this;
    }

    private function resize_image(Image $image, $size)
    {
    	
    }

    public function each_image(string $method, array $vars)
    {
    	$call = strtolower($method."_image");

    	if(!method_exists($this, $call)) throw new Exception($method, 6);
    	
    	foreach ($vars as $key => $value) {
    		if(isset($this->images[$key])){
    			$this->$call($this->images[$key], $value);
    		}
    	}

    	return $this;

    }

    public function save(string $path)
    {
    	
    }


    /**
    * AUX METHODS
    */

    private static function is_not_size(string $size)
    {
    	$size = trim($size);
    	return !preg_match('/((^\d{2,}x\d{2,}$)|(^\*x\d{2,}$)|(^\d{2,}x\*$))/i', $size);
    }

    private static function is_url(string $str){
        return preg_match('/(https?:\/\/(?:www\.|(?!www))
        [a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|
        www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $from);
    }

    private static function generate_width_height(array $sizes)
    {
        if ($sizes[0] == "*"){
            $multipl = $sizes[1] / self::$sizes[1];
            $h = $sizes[1];
            $w =  (int)($this->source[0] * $multipl);
            $s = "_x".$h;
        } else 
        if ($sizes[1] == "*"){
            $multipl = $sizes[0] / self::$sizes[0];
            $w = $sizes[0];
            $h =  (int)(self::$sizes[1] * $multipl);
            $s = $w."x_";
        } else {
            $h = $sizes[1];
            $w = $sizes[0];
            $s = $w."x".$h;
        }
        return [$s,$w,$h];
    }
 	
 }