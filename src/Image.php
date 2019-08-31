<?php 

/**
 *
 * @author Andrei Coelho
 * @version 0.1
 *
 */


namespace src;

use src\ImageBuilderException as ImageBuilderException;

class Image {

	private $resource;
	private $actions = [];

	public function __construct($resource)
	{
		if(!is_resource($resource) || get_resource_type($resource) !== 'gd') 
			throw new ImageBuilderException("", 5);
		
		$this->resource = $resource;
	}

	public function resize(int $width, int $height)
	{
		$this-> actions[]['resize'] = [$width, $height];
	}

	public function getResource()
	{
		return $this -> resource;
	}

	private function changeResource($resource)
	{
		# code...
	}


}