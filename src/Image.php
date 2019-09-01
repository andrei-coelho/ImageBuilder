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

	private $resource;
	private $actions = [];
	private $filters = [];
	private $modify  = false;
	
	/**
	* The informations of Image
    *
	* @var string $path, $name, $mime
	*
	*/
	private $path, $name, $mime;

	public function __construct($resource, array $info)
	{
		if(!is_resource($resource) || get_resource_type($resource) !== 'gd') 
			throw new ImageBuilderException(5);
		
		$this -> resource = $resource;
		$this -> path     = $info[0];
		$this -> name     = $info[1];
		$this -> mime     = $info[2];
	}

	public function change_info(array $info)
	{	
		$this -> path   = $info[0];
		$this -> name   = $info[1];
		$this -> mime   = $info[2];

		$this -> modify = true;
	}

	public function resize(int $width, int $height)
	{
		$this -> actions[]['resize'] = [$width, $height];
	}

	public function getMime()
	{
		return $this -> mime;
	}

	public function getFrom()
	{
		return $this -> path . $this -> name;
	}

	public function getInfo():array
	{
		return [
			'path'     => $this->path,
			'name'     => $this->name,
			'mime'     => $this->mime,
			'resource' => $this->resource,
			'actions'  => $this->actions,
			'filters'  => $this->filters,
			'modify'   => $this->modify			
		];
	}

	public function done()
	{
		# code...
	}

}