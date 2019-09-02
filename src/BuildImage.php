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
	* Is used to create other new Images
    *
	* @var string
	*
	*/
	private $from;

	/**
	* This is a control for alias
    *
	* @var boolean
	*
	*/
	private $mod = false;

    private function __construct(string $from, $alias = 0)
    {
    	if (!extension_loaded('gd'))
            throw new ImageBuilderException(0, $from);

		$this -> from = $from;
		$this -> images[$alias] = self::create_image($from);

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
    public function copy($argument)
    {	
    	# if argument is integer, create new copies with number sent
    	if(is_int($argument) && $argument > 0)
    	{
			$this -> mod = true;
	    	for ($i=0; $i < $argument; $i++) 
	    		$this -> images[] = self::create_image($this->from);
    	} 
    	# if the argument is array, create new copies using aliased value
    	else if(is_array($argument))
    	{
    		foreach ($argument as $alias)
    			if(!is_bool($alias)) {
					$this -> images[$alias] = self::create_image($this->from);
				}
    	}

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
		if(!self::is_size_values($size)) throw new ImageBuilderException(3, $size);

    	foreach ($this->images as $img)
    		$this -> resize_image($img, $size, true);

    	return $this;
    }


    private function resize_image(Image $image, $size, $type = false)
    {	
		if(!$type)
		if(!self::is_size_values($size)) throw new ImageBuilderException(3, $size);
		
    	$image -> resize($size);
    }


	/**
	* Set crop of all Images 
	*
	* @param  mix      $values
	* @return this     object
	*/
    public function crop($values)
    {	
		if(!self::is_crop_values($values)) throw new ImageBuilderException(3, $values);

    	foreach ($this->images as $img)
    		$this -> crop_image($img, $values, true);

    	return $this;
    }


    private function crop_image(Image $image, $values, $type = false)
    {	
		if(!$type)
    	if(!self::is_crop_values($values)) throw new ImageBuilderException(3, $values);

    	$image -> crop($values);
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

    	$image -> change_info($info, !$this -> mod);
    }


    /**
	* Use this method for change especific Images
	*
	* @param  string   $method
	* @param  array    $vars
	* @return this     object
	*/
    public function for_image(string $method, array $vars)
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
				$alias
    		);
    	}
		
    }


	/*                       *
	*------------------------*
	*       AUX METHODS      *
	*------------------------*
	*                        */

	private static function create_image(string $from)
	{
		if (!(@$info = getimagesize($from)))
			throw new ImageBuilderException(1, $from);

		$mime = self::generate_mime($info['mime']);

        return new Image(self::create_resource($from, $mime), self::read_path($from, $mime), [$info[0], $info[1]]);
	}

	private static function create_resource(string $from, string $mime)
	{
		switch ($mime) {
			case 'jpg':
				return imagecreatefromjpeg($from);

			case 'png':
					$res = imagecreatefrompng($from);
					imagealphablending($res, false);
					imagesavealpha($res, true);
				return $res;

			default:
				return false;
		}
	}

	private static function read_path(string $path, $mi = false)
	{
		preg_match('/[^\s]+[^\s\/]+\.[\w]{3}/', $path, $result);

		if(count($result) == 0) return false;

		$vars = explode('/', $result[0]);
		$full = array_pop($vars);
		$name = substr($full, 0, -4);
		$mime = $mi === false ? @end(explode('.', $full)) : $mi;
		$path = implode('/', $vars).'/';
	
		return [$path, $name, $mime];

	}

	private static function is_size_values(string $size)
    {
		return preg_match('/((^\d{2,}x\d{2,}$)|
		(^\*x\d{2,}$)|(^\d{2,}x\*$)|(^\_x\d{2,}$)|
		(^\d{2,}x\_$))/i', trim($size));
    }

	private static function is_crop_values(string $info)
	{
		return preg_match('/(center|left|right|top|bottom|\d{2,})\s(center|left|right|top|bottom|\d{2,})\s((\d{2,}x\d{2,})|(\*x\d{2,})|(\d{2,}x\*)|(\_x\d{2,})|(\d{2,}x\_))/', trim($info));
	}

    private static function is_url(string $from)
    {
        return preg_match('/(https?:\/\/(?:www\.|(?!www))
        [a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|
        www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $from);
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