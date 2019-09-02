<?php 

/**
 *
 * @author Andrei Coelho
 * @version 0.1
 *
 */


namespace ImageBuilder;

use ImageBuilder\ImageBuilderException as ImageBuilderException;

class Image {

	public $resource;
	public $actions = [];
	public $filters = [];
	public $modify  = false;
	
	/**
	* The informations of Image
    *
	* @var string $path, $name, $mime
	*
	*/
	public $path, $name, $mime;

	public $sizes = [];

	public function __construct($resource, array $info, array $sizes)
	{
		$this -> resource = $resource;
		$this -> path     = $info[0];
		$this -> name     = $info[1];
		$this -> mime     = $info[2];
		$this -> sizes    = $sizes;
	}

	public function change_info(array $info, bool $mod)
	{	
		$this -> path   = $info[0];
		$this -> name   = $info[1];
		$this -> mime   = $info[2];

		$this -> modify = $mod;
	}

	public function resize(string $size)
	{	
		$this -> actions['resize'] = $size;
	}

	public function crop(string $values){
		$this -> actions['crop'] = $values;
	}

	public function done()
	{
		# code...
	}

}