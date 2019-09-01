<?php 

/**
 *
 * @author Andrei Coelho
 * @version 0.2
 *
 */


                                                                         
/*-------------------------------------------------------------------------|
|                                BuildImage                                |
|--------------------------------------------------------------------------|
|                                                                          |
|    This is the main class. Use this class for create amazing Images!     |
|                                                                          |
|--------------------------------------------------------------------------*/

namespace ImageBuilder;

use ImageBuilder\ImageBuilderException as ImageBuilderException;
use ImageBuilder\Build as Build;
use ImageBuilder\Image as Image;

 class BuildImage {


	/**
	* The list os Image instances 
    *
	* @var array
	*
	*/
 	private $images = [];

	/**
	* Here are the copied image sizes from $from
    *
	* @var array
	*
	*/
	private static $source;

	/**
	* This is a default instance of the Image.
	* Is used to create other new Images
    *
	* @var Image
	*
	*/
	private $copy;



    private function __construct(string $from, $alias = 0)
    {
    	if (!extension_loaded('gd'))
            throw new ImageBuilderException(0, $from);

        if (!(@$info = getimagesize($from)))
            throw new ImageBuilderException(1, $from);

        self::$source = $info;

        // $res = @imagecreatetruecolor($info[0], $info[1]);

        //if(gettype($res) !== 'resource') throw new ImageBuilderException(0, $from);

        $this -> copy = new Image(self::read_path($from, self::generate_mime($info['mime'])), [$info[0], $info[1]]);
        $this -> images[$alias] = Clone $this -> copy;

        return $this;
    }


	/**
	* Create a new instance of BuildImage
	*
	* @param  string  $from
	* @param  array   $alias
	* @return BuildImage
	*/
    public static function from(string $from, string $alias = null) : BuildImage
    {
    	return $alias ? new BuildImage($from, $alias) : new BuildImage($from);
    }


	/**
	* Create more copies 
	*
	* @param  mixed (array or integer)  $argument
	* @return this   object
	*/
    public function copies($argument)
    {	
    	# if argument is integer, create new copies with number sent
    	if(is_int($argument))
    	{
	    	for ($i=0; $i < $argument; $i++) 
	    		$this -> images[] = Clone $this -> copy;
    	} 
    	# if the argument is array, create new copies using aliased value
    	else if(is_array($argument))
    	{
    		foreach ($argument as $alias)
    			if($alias) $this -> images[$alias] = Clone $this -> copy;
    	}

    	return $this;
    }


	/**
	* Create more single copy 
	*
	* @param  string   $alias
	* @return this     object
	*/
    public function copy(string $alias = null)
    {
    	$alias ? $this -> images[$alias] = $this -> copy : $this -> images[] = $this -> copy;
    	return $this;
    }


	/**
	* Set new size of all Images 
	*
	* @param  string   $size
	* @return this     object
	*/
    public function resize(string $size)
    {	
    	foreach ($this->images as $img)
    		$this -> resize_image($img, $size);

    	return $this;
    }


    private function resize_image(Image $image, $size)
    {
    	if(self::is_not_size($size)) throw new ImageBuilderException(3, $size);

    	$vars = self::generate_width_height(explode("x", strtolower($size)));

    	$image -> resize($vars[1], $vars[2]);
    }

    
	/**
	* Change the path where the images will be saved
	*
	* @param  string   $size
	* @return this     object
	*/
    public function path_as(string $path)
    {
    	foreach ($this->images as $img)
    		$this -> path_as_image($img, $path);

    	return $this;
    }

    private function path_as_image(Image $image, string $path)
    {
    	if(!($info = self::read_path($path))) throw new ImageBuilderException(6);

    	$image -> change_info($info);
    }


    /**
	* Use this method for change especific Images
	*
	* @param  string   $method
	* @param  array    $vars
	* @return this     object
	*/
    public function each_image(string $method, array $vars)
    {
    	$call = strtolower($method."_image");

    	if(!method_exists($this, $call)) throw new Exception($method, 6);
    	
    	foreach ($vars as $key => $value)
    		if(isset($this -> images[$key]) && is_string($value))
    			$this -> $call($this -> images[$key], $value);

    	return $this;

    }

    public function save()
    {
    	foreach ($this -> images as $alias => $image)
    	{
    		Build::image(
    			$image, 
				$this -> copy,
				$alias
    		);
    		$image -> done();
    	}

    	# close all resources
    }

	public function getClones()
    {
    	return $this -> images;
    }

	/*                       *
	*------------------------*
	*       AUX METHODS      *
	*------------------------*
	*                        */

	private static function read_path(string $path, $mi = false)
	{
		preg_match('/[^\s]+[^\s\/]+\.[\w]{3}/', $path, $result);

		if(count($result) == 0) return false;

		$vars = explode('/', $result[0]);
		$name = array_pop($vars);
		$mime = $mi === false ? @end(explode('.', $name)) : $mi;
		$path = implode('/', $vars).'/';

		return [$path, $name, $mime];

	}

	private static function is_not_size(string $size)
    {
    	return !preg_match('/((^\d{2,}x\d{2,}$)|(^\*x\d{2,}$)|(^\d{2,}x\*$))/i', trim($size));
    }

    private static function is_url(string $str)
    {
        return preg_match('/(https?:\/\/(?:www\.|(?!www))
        [a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|
        www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $from);
    }

    private static function generate_width_height(array $sizes)
    {
        if ($sizes[0] == "*"){
            $multipl = $sizes[1] / self::$source[1];
            $h = $sizes[1];
            $w =  (int)(self::$source[0] * $multipl);
            $s = "_x".$h;
        } else 
        if ($sizes[1] == "*"){
            $multipl = $sizes[0] / self::$source[0];
            $w = $sizes[0];
            $h =  (int)(self::$source[1] * $multipl);
            $s = $w."x_";
        } else {
            $h = $sizes[1];
            $w = $sizes[0];
            $s = $w."x".$h;
        }
        return [$s,$w,$h];
    }
 	
    private static function generate_mime($type)
    {
    	switch($type){
			case "image/jpeg":
			case "image/jpg":
				return "jpg";
			case "image/png":
				return "png";
			default: return "jpg";
		}
    }

}